<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Exports\RecapExport;
use App\Mail\SendRecapEmail;
use App\Mail\SendReportEmail;
use App\Models\CategoryMenu;
use App\Models\KitchenReport;
use App\Models\meja_billiard;
use App\Models\Menu;
use App\Models\order_items;
use App\Models\orders;
use App\Models\Report;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    /**
     * Helper: Add shift filter to query
     * - Super admin: no filter (lihat semua shift)
     * - Regular admin: filter by their shift_id
     * - Kitchen: has separate dashboard
     */
    private function applyShiftFilter($query, $user)
    {
        // Super admin lihat semua data (no filter)
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Regular admin filter by their shift
        if ($user->shift_id) {
            return $query->where('shift_id', $user->shift_id);
        }

        // No shift assigned - return empty
        return $query->whereRaw('1=0');
    }

    /**
     * Helper: Check if user can access order
     * - Super admin: can access any
     * - Regular admin: only their shift
     * - Kitchen: separate access control
     */
    private function canAccessOrder($user, $order)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $order->shift_id === $user->shift_id;
    }

    public function store(Request $request)
    {
        $idempotencyKey = $request->header('Idempotency-Key') ?: $request->input('idempotency_key');
        if (is_string($idempotencyKey)) {
            $idempotencyKey = trim($idempotencyKey);
            if ($idempotencyKey === '') {
                $idempotencyKey = null;
            } elseif (strlen($idempotencyKey) > 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Idempotency key tidak valid',
                    'error' => 'invalid_idempotency_key',
                ], 422);
            }
        } else {
            $idempotencyKey = null;
        }

        if ($idempotencyKey) {
            $existingOrder = orders::where('idempotency_key', $idempotencyKey)->first();
            if ($existingOrder) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order berhasil dibuat',
                    'order_id' => $existingOrder->id,
                    'redirect_url' => route('orders.show', $existingOrder->id),
                ]);
            }
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number' => 'required|string|max:255',
            'room' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cash,qris,transfer',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.image' => 'nullable|string',
        ]);

        // Get active shift based on current time
        $activeShift = Shift::getActiveShift();

        // Validasi: Order hanya bisa dibuat jika ada active shift
        if (! $activeShift) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak bisa dibuat di luar jam operasional. Silakan cek jam buka restoran.',
                'error' => 'no_active_shift',
            ], 422);
        }

        $totalPrice = 0;
        foreach ($request->items as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        try {
            $order = DB::transaction(function () use ($request, $totalPrice, $activeShift, $idempotencyKey) {
                $order = orders::create([
                    'customer_name' => $request->customer_name,
                    'table_number' => $request->table_number,
                    'room' => $request->room,
                    'total_price' => $totalPrice,
                    'payment_method' => $request->payment_method,
                    'status' => 'processing',
                    'shift_id' => $activeShift->id,
                    'idempotency_key' => $idempotencyKey,
                ]);

                foreach ($request->items as $item) {
                    order_items::create([
                        'order_id' => $order->id,
                        'menu_name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'image' => $item['image'] ?? null,
                    ]);
                }

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat',
                'order_id' => $order->id,
                'redirect_url' => route('orders.show', $order->id),
            ]);
        } catch (QueryException $e) {
            if ($idempotencyKey) {
                $existingOrder = orders::where('idempotency_key', $idempotencyKey)->first();
                if ($existingOrder) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Order berhasil dibuat',
                        'order_id' => $existingOrder->id,
                        'redirect_url' => route('orders.show', $existingOrder->id),
                    ]);
                }
            }

            throw $e;
        }
    }

    /**
     * Tampilkan halaman order detail untuk customer
     */
    public function show($id)
    {
        $order = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->findOrFail($id);

        // Hanya bisa lihat order yang masih processing atau pending
        if (! in_array($order->status, ['pending', 'processing'])) {
            return redirect()->route('orders.create')->with('error', 'Order sudah selesai dan tidak dapat diedit.');
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Get order data as JSON (for loading into cart)
     */
    public function getOrderData($id)
    {
        $order = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'table_number' => $order->table_number,
                'room' => $order->room,
                'total_price' => $order->total_price,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'created_at' => $order->created_at->toISOString(),
                'updated_at' => $order->updated_at->toISOString(),
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->menu_name,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'image' => $item->image,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Update order (tambah/kurang quantity item)
     */
    public function update(Request $request, $id)
    {
        $order = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->findOrFail($id);

        // Hanya bisa edit order yang masih processing atau pending
        if (! in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order sudah selesai dan tidak dapat diedit.',
            ], 400);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalPrice = 0;

            foreach ($request->items as $itemData) {
                $orderItem = order_items::where('id', $itemData['id'])
                    ->where('order_id', $order->id)
                    ->firstOrFail();

                $orderItem->quantity = $itemData['quantity'];
                $orderItem->save();

                $totalPrice += $orderItem->price * $orderItem->quantity;
            }

            // Update total price
            $order->total_price = $totalPrice;
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil diupdate',
                'order' => $order->load('orderItems'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate order: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tambah item ke order yang sudah ada
     */
    public function addItem(Request $request, $id)
    {
        $order = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->findOrFail($id);

        // Hanya bisa edit order yang masih processing atau pending
        if (! in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order sudah selesai dan tidak dapat diedit.',
            ], 400);
        }

        $request->validate([
            'menu_name' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'image' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah item sudah ada di order
            $existingItem = order_items::where('order_id', $order->id)
                ->where('menu_name', $request->menu_name)
                ->first();

            if ($existingItem) {
                // Update quantity jika item sudah ada
                $existingItem->quantity += $request->quantity;
                $existingItem->save();
            } else {
                // Tambah item baru
                order_items::create([
                    'order_id' => $order->id,
                    'menu_name' => $request->menu_name,
                    'price' => $request->price,
                    'quantity' => $request->quantity,
                    'image' => $request->image ?? null,
                ]);
            }

            // Recalculate total price
            $totalPrice = order_items::where('order_id', $order->id)
                ->get()
                ->sum(function ($item) {
                    return $item->price * $item->quantity;
                });

            $order->total_price = $totalPrice;
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan',
                'order' => $order->load('orderItems'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan item: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus item dari order
     */
    public function removeItem($id, $itemId)
    {
        $order = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->findOrFail($id);

        // Hanya bisa edit order yang masih processing atau pending
        if (! in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order sudah selesai dan tidak dapat diedit.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $orderItem = order_items::where('id', $itemId)
                ->where('order_id', $order->id)
                ->firstOrFail();

            $orderItem->delete();

            // Recalculate total price
            $totalPrice = order_items::where('order_id', $order->id)
                ->get()
                ->sum(function ($item) {
                    return $item->price * $item->quantity;
                });

            $order->total_price = $totalPrice;
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus',
                'order' => $order->load('orderItems'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel order
     */
    public function cancel($id)
    {
        $order = orders::findOrFail($id);

        // Hanya bisa cancel order yang masih pending atau processing
        if (! in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order sudah selesai dan tidak dapat dibatalkan.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Hapus order items
            $order->orderItems()->delete();

            // Hapus order
            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibatalkan',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan order: '.$e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        // Get current user's shift
        $user = Auth::user();
        $userShiftId = $user->shift_id;

        // Get active shift based on time (with caching untuk performa)
        $activeShift = cache()->remember('active_shift', 60, function () {
            return Shift::getActiveShift();
        });

        // Optimasi query: hanya ambil kolom yang diperlukan dan eager load orderItems dengan select
        $query = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'created_at', 'updated_at', 'shift_id')
            ->with(['orderItems' => function ($query) {
                $query->select('id', 'order_id', 'menu_name', 'price', 'quantity', 'image');
            }])
            ->where('status', 'processing');

        // Filter by user's shift - WAJIB filter berdasarkan shift user
        if ($userShiftId) {
            $query->where('shift_id', $userShiftId);
        } else {
            // Jika user belum di-assign shift, tampilkan kosong
            return view('dapur.dapur', [
                'orders' => collect([]),
                'activeShift' => $activeShift,
            ])->with('error', 'Anda belum di-assign ke shift. Silakan hubungi administrator.');
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return view('dapur.dapur', compact('orders', 'activeShift'));
    }

    /**
     * Admin: List all orders for management
     *
     * Optimization:
     * - Eager load orderItems (prevent N+1 queries)
     * - Filter by shift_id with authorization check
     * - Super admin sees all shifts, regular admin sees only their shift
     */
    public function adminIndex()
    {
        $user = Auth::user();

        // Authorization check: user must have order.view permission
        if (! $user->hasPermissionTo('order.view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat daftar order.');
        }

        // Only regular admin (NOT super_admin) without shift_id gets blocked
        // Super admin ALWAYS has access to all shifts data regardless of shift assignment
        if (! $user->hasRole('super_admin') && ! $user->shift_id) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda belum di-assign ke shift. Silakan hubungi administrator.');
        }

        // Eager load orderItems to prevent N+1 queries (optimized)
        // Apply shift filter: super_admin gets all orders from all shifts, regular admin gets only their shift
        $query = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->orderByRaw("CASE 
                WHEN status = 'processing' THEN 1 
                WHEN status = 'completed' THEN 2 
                WHEN status = 'rejected' THEN 3 
                WHEN status = 'pending' THEN 4 
                ELSE 5 
            END")
            ->orderBy('created_at', 'desc')
            ->limit(100);

        $allOrders = $this->applyShiftFilter($query, $user)->get();

        // Eager load tables with select to minimize data transfer
        $tables = meja_billiard::select('id', 'name', 'room', 'slug', 'qrcode')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.orders.index', compact('allOrders', 'tables'));
    }

    /**
     * Check for new orders or status changes (for auto refresh)
     */
    public function checkNewOrders(Request $request)
    {
        $user = Auth::user();
        $lastOrderId = $request->get('last_order_id', 0);
        $lastCheckTime = $request->get('last_check_time');

        // Super admin bisa cek orders dari semua shift, regular admin harus punya shift_id
        if (! $user->hasRole('super_admin') && ! $user->shift_id) {
            return response()->json([
                'has_changes' => false,
                'has_new_orders' => false,
                'has_status_changes' => false,
                'new_orders_count' => 0,
                'status_changed_count' => 0,
                'active_orders_count' => 0,
                'has_active_orders' => false,
                'latest_order_id' => 0,
                'current_time' => Carbon::now('Asia/Jakarta')->toISOString(),
            ]);
        }

        // Check for new orders (order baru langsung processing, jadi tidak perlu check pending)
        $query = orders::where('id', '>', $lastOrderId)
            ->where('status', 'processing');

        if (! $user->hasRole('super_admin')) {
            $query->where('shift_id', $user->shift_id);
        }

        $newOrdersCount = $query->count();

        // Check for status changes (orders that were updated after last check)
        $statusChangedCount = 0;
        if ($lastCheckTime) {
            $statusQuery = orders::where('updated_at', '>', $lastCheckTime)
                ->where(function ($query) {
                    $query->where('status', 'completed')
                        ->orWhere('status', 'rejected')
                        ->orWhere('status', 'processing');
                });

            if (! $user->hasRole('super_admin')) {
                $statusQuery->where('shift_id', $user->shift_id);
            }

            $statusChangedCount = $statusQuery->count();
        }

        $latestQuery = orders::orderBy('id', 'desc');
        if (! $user->hasRole('super_admin')) {
            $latestQuery->where('shift_id', $user->shift_id);
        }

        $latestOrder = $latestQuery->first();
        $latestOrderId = $latestOrder ? $latestOrder->id : 0;

        // Check if there are any active orders (pending or processing)
        $activeQuery = orders::whereIn('status', ['pending', 'processing']);
        if (! $user->hasRole('super_admin')) {
            $activeQuery->where('shift_id', $user->shift_id);
        }

        $activeOrdersCount = $activeQuery->count();

        // Check if there are any changes (new orders or status changes)
        $hasChanges = $newOrdersCount > 0 || $statusChangedCount > 0;

        // Get new orders details if there are new orders (optimized)
        $newOrders = [];
        if ($newOrdersCount > 0) {
            $newOrders = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'created_at', 'updated_at', 'shift_id')
                ->with('orderItems:id,order_id,menu_name,price,quantity,image')
                ->where('shift_id', $user->shift_id)
                ->where('id', '>', $lastOrderId)
                ->where('status', 'processing')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => $order->customer_name,
                        'table_number' => $order->table_number,
                        'room' => $order->room,
                        'total_price' => $order->total_price,
                        'payment_method' => $order->payment_method,
                        'created_at' => Carbon::parse($order->created_at)->utc()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'order_items' => $order->orderItems->map(function ($item) {
                            return [
                                'menu_name' => $item->menu_name,
                                'price' => $item->price,
                                'quantity' => $item->quantity,
                            ];
                        }),
                    ];
                });
        }

        return response()->json([
            'has_changes' => $hasChanges,
            'has_new_orders' => $newOrdersCount > 0,
            'has_status_changes' => $statusChangedCount > 0,
            'new_orders_count' => $newOrdersCount,
            'status_changed_count' => $statusChangedCount,
            'active_orders_count' => $activeOrdersCount,
            'has_active_orders' => $activeOrdersCount > 0,
            'latest_order_id' => $latestOrderId,
            'new_orders' => $newOrders,
            'current_time' => Carbon::now('Asia/Jakarta')->toISOString(),
        ]);
    }

    /**
     * Admin: Approve order (change status from pending to processing)
     *
     * Authorization:
     * - Only approve orders from the user's own shift
     * - Validation: Order status must be 'pending'
     */
    public function approve($id)
    {
        $user = Auth::user();

        // Check permission
        if (! $user->hasPermissionTo('order.update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengapprove order.');
        }

        // Eager load to minimize queries
        $order = orders::findOrFail($id);

        // Authorization: Check if order belongs to user's shift (or super_admin)
        if (! $this->canAccessOrder($user, $order)) {
            return back()->with('error', 'Anda tidak memiliki akses ke order ini.');
        }

        // Validation: Only pending orders can be approved
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order ini tidak dapat diapprove karena statusnya bukan pending.');
        }

        // Update status
        $order->update(['status' => 'processing']);

        return back()->with('success', 'Order berhasil diapprove dan akan muncul di dapur.');
    }

    /**
     * Admin: Reject order
     *
     * Authorization:
     * - Only reject orders from the user's own shift
     * - Validation: Order status must be 'pending'
     */
    public function reject($id)
    {
        $user = Auth::user();

        // Check permission
        if (! $user->hasPermissionTo('order.update')) {
            abort(403, 'Anda tidak memiliki akses untuk menolak order.');
        }

        // Eager load to minimize queries
        $order = orders::findOrFail($id);

        // Authorization: Check if order belongs to user's shift (or super_admin)
        if (! $this->canAccessOrder($user, $order)) {
            return back()->with('error', 'Anda tidak memiliki akses ke order ini.');
        }

        // Validation: Only pending orders can be rejected
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order ini tidak dapat direject karena statusnya bukan pending.');
        }

        // Update status
        $order->update(['status' => 'rejected']);

        return back()->with('success', 'Order berhasil direject.');
    }

    /**
     * Admin: Delete order
     *
     * PENTING: Hanya bisa menghapus order dengan status pending.
     * Order yang sudah completed, processing, atau rejected TIDAK BOLEH DIHAPUS
     * untuk mencegah kecurangan saat tutup buku akhir bulan/tahun.
     *
     * Authorization:
     * - Only delete orders from the user's own shift
     * - Only delete pending orders
     */
    public function destroy($id)
    {
        $user = Auth::user();

        // Check permission
        if (! $user->hasPermissionTo('order.delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus order.');
        }

        // Eager load orderItems for deletion (optimized - hanya kolom yang diperlukan)
        $order = orders::select('id', 'status', 'shift_id')
            ->with('orderItems:id,order_id')
            ->findOrFail($id);

        // Authorization: Check if order belongs to user's shift (or super_admin)
        if (! $this->canAccessOrder($user, $order)) {
            return back()->with('error', 'Anda tidak memiliki akses ke order ini.');
        }

        // Validation: Only pending orders can be deleted
        // Orders yang sudah completed, processing, atau rejected tidak bisa dihapus
        if (! $order->canBeDeleted()) {
            $statusLabels = [
                'completed' => 'SELESAI',
                'processing' => 'SEDANG DIPROSES',
                'rejected' => 'DITOLAK',
            ];
            $statusLabel = $statusLabels[$order->status] ?? strtoupper($order->status);

            return back()->with('error', 'Order dengan status '.$statusLabel.' tidak dapat dihapus. Data penjualan harus tersimpan permanen untuk keperluan tutup buku dan audit.');
        }

        // Delete order items first (cascade delete)
        $order->orderItems()->delete();

        // Delete order
        $order->delete();

        return back()->with('success', 'Order berhasil dihapus.');
    }

    /**
     * Start cooking order (change status from pending to processing)
     *
     * Actions:
     * - Update order status from 'pending' to 'processing'
     *
     * Authorization:
     * - Only start cooking orders from the user's own shift
     */
    public function startCooking($id)
    {
        $user = Auth::user();

        // Validation: User must be assigned to a shift or be super_admin
        if (! $user->hasRole('super_admin') && ! $user->shift_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum di-assign ke shift. Silakan hubungi administrator.',
            ], 403);
        }

        $order = orders::findOrFail($id);

        // Authorization: Check if order belongs to user's shift (or super_admin)
        if (! $user->hasRole('super_admin') && $order->shift_id !== $user->shift_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke order ini.',
            ], 403);
        }

        // Validation: Only pending orders can be started
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order ini tidak dapat dimulai karena statusnya bukan pending.',
            ], 400);
        }

        // Update status
        $order->update(['status' => 'processing']);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dimulai.',
            'order' => $order->fresh(),
        ]);
    }

    /**
     * Mark order as completed
     *
     * Actions:
     * - Update order status to 'completed'
     * - Save to kitchen_reports for persistence
     * - Return order data for realtime update
     *
     * Authorization:
     * - Only complete orders from the user's own shift
     */
    public function complete($id)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi',
                ], 401);
            }

            // Eager load orderItems for data manipulation (optimized)
            $order = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
                ->with('orderItems:id,order_id,menu_name,price,quantity,image')
                ->findOrFail($id);

            // Authorization: Check if order belongs to user's shift or super_admin
            if (! $user->hasRole('super_admin') && $order->shift_id !== $user->shift_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke order ini.',
                ], 403);
            }

            // Update order status
            $order->update(['status' => 'completed']);

            // Save to kitchen_reports for persistence (prevent data loss)
            try {
                // Check if already saved (prevent duplicates)
                $existingReport = KitchenReport::where('order_id', $order->id)->first();

                if (! $existingReport) {
                    // Prepare order items data
                    $orderItems = $order->orderItems->map(function ($item) {
                        return [
                            'menu_name' => $item->menu_name,
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                            'image' => $item->image,
                        ];
                    })->toArray();

                    // Create kitchen report
                    KitchenReport::create([
                        'order_id' => $order->id,
                        'customer_name' => $order->customer_name,
                        'table_number' => $order->table_number,
                        'room' => $order->room,
                        'total_price' => $order->total_price,
                        'payment_method' => $order->payment_method,
                        'order_items' => json_encode($orderItems),
                        'order_date' => $order->created_at->format('Y-m-d'),
                        'completed_at' => Carbon::now('Asia/Jakarta'),
                    ]);
                }
            } catch (\Exception $e) {
                // Log error but don't fail the operation
                Log::error('Error saving to kitchen_reports', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Return order data for realtime update
            return response()->json([
                'success' => true,
                'message' => 'Order telah diselesaikan',
                'order' => [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'table_number' => $order->table_number,
                    'room' => $order->room,
                    'total_price' => $order->total_price,
                    'payment_method' => $order->payment_method,
                    'created_at' => $order->created_at->format('d M Y H:i').' WIB',
                    'updated_at' => $order->updated_at->format('d M Y H:i').' WIB',
                    'order_items' => $order->orderItems->map(function ($item) {
                        return [
                            'menu_name' => $item->menu_name,
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active (processing) orders for live refresh on kitchen page
     *
     * Optimization:
     * - Eager load orderItems to prevent N+1 queries
     * - Filter by user's shift and status = 'processing'
     * - Super admin sees all shifts
     * - Select only needed columns
     */
    public function activeOrders()
    {
        $user = Auth::user();

        // Validation: User must be assigned to a shift or be super_admin
        if (! $user->hasRole('super_admin') && ! $user->shift_id) {
            return response()->json(['orders' => []]);
        }

        // Eager load orderItems with specific columns to prevent N+1
        $query = orders::select('id', 'customer_name', 'table_number', 'room', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->where('status', 'processing')
            ->orderBy('created_at', 'desc');

        // Apply shift filter
        $orders = $this->applyShiftFilter($query, $user)->get();

        return response()->json([
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'table_number' => $order->table_number,
                    'room' => $order->room,
                    'total_price' => $order->total_price,
                    'payment_method' => $order->payment_method,
                    'created_at' => $order->created_at->toIso8601String(),
                    'updated_at' => $order->updated_at->toIso8601String(),
                    'order_items' => $order->orderItems->map(function ($item) {
                        return [
                            'menu_name' => $item->menu_name,
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                            'image' => $item->image,
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Server-Sent Events endpoint untuk real-time orders
     */
    public function ordersStream()
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validation: User must be assigned to a shift or be super_admin
        if (! $user->hasRole('super_admin') && ! $user->shift_id) {
            return response()->json(['error' => 'User tidak memiliki shift yang aktif'], 403);
        }

        // Set headers for SSE
        return response()->stream(function () use ($user) {
            try {
                $lastOrderId = 0;
                $isLocal = app()->environment('local');
                $checkInterval = $isLocal ? 2.0 : 1.5;
                $startedAt = microtime(true);
                $maxConnectionSeconds = $isLocal ? 5 : 30;

                while (true) {
                    // Check if client is still connected
                    if (connection_aborted()) {
                        break;
                    }
                    if ((microtime(true) - $startedAt) >= $maxConnectionSeconds) {
                        echo ": close\n\n";
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                        break;
                    }

                    // Get latest orders (pending atau processing)
                    $query = orders::select('id', 'customer_name', 'table_number', 'room', 'total_price', 'payment_method', 'status', 'created_at', 'updated_at', 'shift_id')
                        ->with('orderItems:id,order_id,menu_name,price,quantity,image')
                        ->whereIn('status', ['pending', 'processing'])
                        ->where('id', '>', $lastOrderId)
                        ->orderBy('created_at', 'asc');

                    $orders = $this->applyShiftFilter($query, $user)->get();

                    // If there are new orders, send them
                    if ($orders->count() > 0) {
                        $ordersData = $orders->map(function ($order) {
                            return [
                                'id' => $order->id,
                                'customer_name' => $order->customer_name,
                                'table_number' => $order->table_number,
                                'room' => $order->room,
                                'total_price' => $order->total_price,
                                'payment_method' => $order->payment_method,
                                'status' => $order->status,
                                'created_at' => $order->created_at->toIso8601String(),
                                'updated_at' => $order->updated_at->toIso8601String(),
                                'order_items' => $order->orderItems->map(function ($item) {
                                    return [
                                        'menu_name' => $item->menu_name,
                                        'price' => $item->price,
                                        'quantity' => $item->quantity,
                                        'image' => $item->image,
                                    ];
                                }),
                            ];
                        });

                        // Update last order ID
                        $lastOrderId = $orders->max('id');

                        // Send SSE event
                        echo 'data: '.json_encode([
                            'type' => 'new_orders',
                            'orders' => $ordersData,
                        ])."\n\n";

                        // Flush output immediately
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    } else {
                        // Send heartbeat to keep connection alive
                        echo ": heartbeat\n\n";
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }

                    // Sleep before next check
                    usleep((int) ($checkInterval * 1000000));
                }
            } catch (\Exception $e) {
                // Send error message via SSE
                echo 'data: '.json_encode([
                    'type' => 'error',
                    'message' => 'Error: '.$e->getMessage(),
                ])."\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // Disable buffering in nginx
        ]);
    }

    public function reports(Request $request)
    {
        // Return view untuk laporan
        return view('dapur.laporan');
    }

    public function reportsOld(Request $request)
    {
        $type = $request->get('type', 'daily'); // daily, monthly, yearly
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $year = $request->get('year', Carbon::now()->format('Y'));

        // Get current user
        $user = Auth::user();

        // Super admin or user with shift_id
        if (! $user->hasRole('super_admin') && ! $user->shift_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum di-assign ke shift. Silakan hubungi administrator.',
                'orders' => [],
                'total_revenue' => 0,
                'total_orders' => 0,
            ], 400);
        }

        // Ambil dari kitchen_reports (data yang sudah tersimpan permanen)
        $kitchenReportsQuery = KitchenReport::query();

        // Filter by shift - apply based on user role
        if (! $user->hasRole('super_admin') && $user->shift_id) {
            $kitchenReportsQuery->whereHas('order', function ($q) use ($user) {
                $q->where('shift_id', $user->shift_id);
            });
        }

        if ($type === 'daily') {
            $kitchenReportsQuery->whereDate('order_date', $date);
        } elseif ($type === 'monthly') {
            $kitchenReportsQuery->whereYear('order_date', Carbon::parse($month)->year)
                ->whereMonth('order_date', Carbon::parse($month)->month);
        } elseif ($type === 'yearly') {
            $kitchenReportsQuery->whereYear('order_date', $year);
        }

        $kitchenReports = $kitchenReportsQuery->orderBy('completed_at', 'desc')->get();

        // Also filter kitchen reports by shift_id if specified
        if (! $user->hasRole('super_admin') && $user->shift_id) {
            $kitchenReportOrderIds = orders::where('shift_id', $user->shift_id)->pluck('id')->toArray();
            $kitchenReports = $kitchenReports->filter(function ($report) use ($kitchenReportOrderIds) {
                return in_array($report->order_id, $kitchenReportOrderIds);
            });
        }

        // Ambil juga dari orders untuk data yang mungkin belum tersimpan di kitchen_reports (optimized)
        $ordersQuery = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->where('status', 'completed');

        // Filter by shift if not super_admin
        if (! $user->hasRole('super_admin') && $user->shift_id) {
            $ordersQuery->where('shift_id', $user->shift_id);
        }

        if ($type === 'daily') {
            $ordersQuery->whereDate('updated_at', $date);
        } elseif ($type === 'monthly') {
            $ordersQuery->whereYear('updated_at', Carbon::parse($month)->year)
                ->whereMonth('updated_at', Carbon::parse($month)->month);
        } elseif ($type === 'yearly') {
            $ordersQuery->whereYear('updated_at', $year);
        }

        $orders = $ordersQuery->orderBy('updated_at', 'desc')->get();

        // Gabungkan data dari kitchen_reports dan orders
        // Gunakan kitchen_reports sebagai sumber utama, lalu tambahkan dari orders yang belum ada
        $kitchenReportOrderIds = $kitchenReports->pluck('order_id')->toArray();

        $allOrders = collect();

        // Tambahkan dari kitchen_reports
        foreach ($kitchenReports as $report) {
            $allOrders->push([
                'id' => $report->order_id,
                'customer_name' => $report->customer_name,
                'table_number' => $report->table_number,
                'room' => $report->room,
                'total_price' => $report->total_price,
                'payment_method' => $report->payment_method,
                'created_at' => $report->completed_at->toISOString(),
                'updated_at' => $report->completed_at->toISOString(),
                'order_items' => $report->order_items,
            ]);
        }

        // Tambahkan dari orders yang belum ada di kitchen_reports
        foreach ($orders as $order) {
            if (! in_array($order->id, $kitchenReportOrderIds)) {
                $allOrders->push([
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'table_number' => $order->table_number,
                    'room' => $order->room,
                    'total_price' => $order->total_price,
                    'payment_method' => $order->payment_method,
                    'created_at' => $order->created_at->toISOString(),
                    'updated_at' => $order->updated_at->toISOString(),
                    'order_items' => $order->orderItems->map(function ($item) {
                        return [
                            'menu_name' => $item->menu_name,
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                            'image' => $item->image,
                        ];
                    }),
                ]);
            }
        }

        // Urutkan berdasarkan updated_at/completed_at terbaru
        $allOrders = $allOrders->sortByDesc(function ($order) {
            return $order['updated_at'];
        })->values();

        $totalRevenue = $allOrders->sum('total_price');
        $totalOrders = $allOrders->count();
        $shiftId = $user->hasRole('super_admin') ? null : $user->shift_id;

        return response()->json([
            'orders' => $allOrders->toArray(),
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'type' => $type,
            'shift_id' => $user->shift_id ?? null,
        ]);
    }

    /**
     * Get order statistics by category for kitchen dashboard chart
     */
    public function getCategoryStats(Request $request)
    {
        $user = Auth::user();
        if (! $user->shift_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum di-assign ke shift.',
                'data' => [],
            ], 400);
        }

        // Get filter parameters
        $type = $request->get('type', 'daily'); // daily, monthly, yearly
        $date = $request->get('date', Carbon::today('Asia/Jakarta')->format('Y-m-d'));
        $month = $request->get('month', Carbon::now('Asia/Jakarta')->format('Y-m'));
        $year = $request->get('year', Carbon::now('Asia/Jakarta')->format('Y'));

        // Build query for completed orders (optimized)
        $ordersQuery = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->where('shift_id', $user->shift_id)
            ->where('status', 'completed');

        // Apply date filter based on type
        if ($type === 'daily') {
            $ordersQuery->whereDate('updated_at', $date);
        } elseif ($type === 'monthly') {
            $ordersQuery->whereYear('updated_at', Carbon::parse($month)->year)
                ->whereMonth('updated_at', Carbon::parse($month)->month);
        } elseif ($type === 'yearly') {
            $ordersQuery->whereYear('updated_at', $year);
        }

        $orders = $ordersQuery->get();

        // Get all categories
        $categories = CategoryMenu::all();

        // Initialize stats
        $categoryStats = [];
        foreach ($categories as $category) {
            $categoryStats[$category->slug] = [
                'name' => $category->name,
                'slug' => $category->slug,
                'total_quantity' => 0,
                'total_revenue' => 0,
                'order_count' => 0,
                'items' => [], // Store item details: ['name' => 'nasi goreng', 'quantity' => 3]
            ];
        }

        // Process each order
        foreach ($orders as $order) {
            $orderCategories = []; // Track which categories this order belongs to

            foreach ($order->orderItems as $item) {
                // Find menu by name to get category
                $menu = Menu::where('name', $item->menu_name)->first();

                if ($menu && $menu->categoryMenu) {
                    $categorySlug = $menu->categoryMenu->slug;

                    if (isset($categoryStats[$categorySlug])) {
                        $categoryStats[$categorySlug]['total_quantity'] += $item->quantity;
                        $categoryStats[$categorySlug]['total_revenue'] += ($item->price * $item->quantity);

                        // Add item detail
                        $itemName = $item->menu_name;
                        if (isset($categoryStats[$categorySlug]['items'][$itemName])) {
                            $categoryStats[$categorySlug]['items'][$itemName] += $item->quantity;
                        } else {
                            $categoryStats[$categorySlug]['items'][$itemName] = $item->quantity;
                        }

                        // Track this category for this order
                        if (! in_array($categorySlug, $orderCategories)) {
                            $orderCategories[] = $categorySlug;
                        }
                    }
                }
            }

            // Count order once per category
            foreach ($orderCategories as $categorySlug) {
                if (isset($categoryStats[$categorySlug])) {
                    $categoryStats[$categorySlug]['order_count']++;
                }
            }
        }

        // Format response - convert items array to list format
        $formattedStats = [];
        foreach ($categoryStats as $slug => $stats) {
            // Convert items associative array to list of objects
            $itemsList = [];
            foreach ($stats['items'] as $itemName => $quantity) {
                $itemsList[] = [
                    'name' => $itemName,
                    'quantity' => $quantity,
                ];
            }
            $stats['items'] = $itemsList;
            $formattedStats[] = $stats;
        }

        return response()->json([
            'success' => true,
            'data' => $formattedStats,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'daily'); // daily, monthly, yearly
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $year = $request->get('year', Carbon::now()->format('Y'));

        $filename = 'Laporan_';
        if ($type === 'daily') {
            $filename .= 'Harian_'.Carbon::parse($date)->format('Y-m-d');
        } elseif ($type === 'monthly') {
            $filename .= 'Bulanan_'.Carbon::parse($month)->format('Y-m');
        } elseif ($type === 'yearly') {
            $filename .= 'Tahunan_'.$year;
        }
        $filename .= '.xlsx';

        return Excel::download(new OrdersExport($type, $date, $month, $year), $filename);
    }

    public function sendReportEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:daily,monthly,yearly',
            'date' => 'nullable|date',
            'month' => 'nullable|string',
            'year' => 'nullable|integer',
        ]);

        $type = $request->get('type', 'daily');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $year = $request->get('year', Carbon::now()->format('Y'));

        // Generate filename
        $filename = 'Laporan_';
        $reportType = 'Harian';
        if ($type === 'daily') {
            $filename .= 'Harian_'.Carbon::parse($date)->format('Y-m-d');
            $reportType = 'Harian '.Carbon::parse($date)->format('d M Y');
        } elseif ($type === 'monthly') {
            $filename .= 'Bulanan_'.Carbon::parse($month)->format('Y-m');
            $reportType = 'Bulanan '.Carbon::parse($month)->format('F Y');
        } elseif ($type === 'yearly') {
            $filename .= 'Tahunan_'.$year;
            $reportType = 'Tahunan '.$year;
        }
        $filename .= '.xlsx';

        try {
            // Check email configuration
            if (config('mail.default') === 'log') {
                Log::info('Email akan dikirim ke: '.$request->email);
            }

            // Ensure temp directory exists
            $tempDir = storage_path('app/temp');
            if (! file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Store Excel file temporarily
            $filePath = 'temp/'.$filename;
            Excel::store(new OrdersExport($type, $date, $month, $year), $filePath, 'local');

            // Get full path for attachment
            $fullPath = Storage::path($filePath);

            // Check if file exists
            if (! file_exists($fullPath)) {
                throw new \Exception('File Excel tidak berhasil dibuat');
            }

            // Log before sending
            Log::info('Attempting to send email', [
                'to' => $request->email,
                'file' => $filename,
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
            ]);

            // Send email with attachment (sync)
            Mail::to($request->email)->send(new SendReportEmail($fullPath, $filename, $reportType));

            // Log after sending
            Log::info('Email sent successfully', [
                'to' => $request->email,
                'file' => $filename,
            ]);

            // Delete temporary file after sending
            Storage::delete($filePath);

            return response()->json([
                'success' => true,
                'message' => 'Laporan Excel berhasil dikirim ke '.$request->email.'. Silakan cek kotak masuk dan folder Spam Anda.',
            ]);
        } catch (\Exception $e) {
            // Delete temporary file if exists
            if (isset($filePath)) {
                Storage::delete($filePath);
            }

            Log::error('Email Error: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: '.$e->getMessage(),
            ], 500);
        }
    }

    public function testEmail(Request $request)
    {
        $email = $request->get('email', 'test@example.com');

        // Get email config
        $mailConfig = [
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        Log::info('Test email configuration', $mailConfig);

        try {
            // Check if using log driver
            if (config('mail.default') === 'log') {
                return response()->json([
                    'success' => false,
                    'message' => 'Email menggunakan log driver. Ubah MAIL_MAILER=smtp di .env',
                    'config' => $mailConfig,
                    'help' => 'Tambahkan di .env: MAIL_MAILER=smtp, MAIL_HOST=smtp.gmail.com, MAIL_PORT=587, MAIL_USERNAME=your-email@gmail.com, MAIL_PASSWORD=your-app-password, MAIL_ENCRYPTION=tls',
                ], 400);
            }

            // Check if config is empty
            if (empty(config('mail.mailers.smtp.host'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konfigurasi email belum di-setup. Pastikan .env sudah dikonfigurasi dengan benar.',
                    'config' => $mailConfig,
                    'help' => 'Tambahkan di .env: MAIL_MAILER=smtp, MAIL_HOST=smtp.gmail.com, MAIL_PORT=587, MAIL_USERNAME=your-email@gmail.com, MAIL_PASSWORD=your-app-password, MAIL_ENCRYPTION=tls',
                ], 400);
            }

            Mail::raw('Ini adalah test email dari Billiard Class. Jika Anda menerima email ini, konfigurasi email sudah benar.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email - Billiard Class');
            });

            Log::info('Test email sent successfully', ['to' => $email]);

            return response()->json([
                'success' => true,
                'message' => 'Test email berhasil dikirim ke '.$email.'. Silakan cek kotak masuk dan folder Spam.',
                'config' => $mailConfig,
                'tips' => [
                    '1. Cek folder Spam/Promosi di Gmail',
                    '2. Email mungkin memerlukan beberapa menit untuk sampai',
                    '3. Pastikan menggunakan App Password, bukan password Gmail biasa',
                    '4. Pastikan 2-Step Verification sudah aktif di Gmail',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Test email failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'config' => $mailConfig,
            ]);

            $errorMessage = $e->getMessage();
            $helpMessage = '';

            if (strpos($errorMessage, 'Connection') !== false || strpos($errorMessage, 'could not be established') !== false) {
                $helpMessage = 'Pastikan MAIL_HOST dan MAIL_PORT benar. Untuk Gmail: smtp.gmail.com:587';
            } elseif (strpos($errorMessage, 'Authentication') !== false || strpos($errorMessage, 'login') !== false) {
                $helpMessage = 'Pastikan menggunakan App Password dari Gmail, bukan password biasa. Buat di: https://myaccount.google.com/apppasswords';
            } elseif (strpos($errorMessage, 'password') !== false) {
                $helpMessage = 'Pastikan MAIL_PASSWORD adalah App Password (16 karakter tanpa spasi) dari Gmail';
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim test email: '.$errorMessage,
                'config' => $mailConfig,
                'help' => $helpMessage ?: 'Cek konfigurasi email di .env dan pastikan sudah benar',
            ], 500);
        }
    }

    /**
     * Rekap order individual ke tutup hari
     */
    public function rekapOrder($id)
    {
        $user = Auth::user();

        // Super admin bisa rekap tanpa harus punya shift_id, tapi regular admin harus punya shift_id
        if (! $user->hasRole('super_admin') && ! $user->shift_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum di-assign ke shift. Silakan hubungi administrator.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $order = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
                ->with('orderItems:id,order_id,menu_name,price,quantity,image')
                ->findOrFail($id);

            // Pastikan order sudah completed
            if ($order->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order harus berstatus completed untuk bisa di-rekap.',
                ], 400);
            }

            // Pastikan order dari shift yang sama (atau super admin dapat akses semua)
            if (! $user->hasRole('super_admin') && $order->shift_id != $user->shift_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke order ini.',
                ], 403);
            }

            // Cek apakah sudah ada tutup hari untuk hari ini
            $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

            // Super admin: cek tanpa filter shift_id
            // Regular admin: cek dengan filter shift_id mereka
            if ($user->hasRole('super_admin')) {
                $report = Report::where('start_date', $today)
                    ->where('end_date', $today)
                    ->first();
            } else {
                $report = Report::where('shift_id', $user->shift_id)
                    ->where('start_date', $today)
                    ->where('end_date', $today)
                    ->first();
            }

            // Buat order summary
            $orderSummary = [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'table_number' => $order->table_number,
                'room' => $order->room,
                'total_price' => $order->total_price,
                'payment_method' => $order->payment_method,
                'created_at' => Carbon::parse($order->created_at)->utc()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'menu_name' => $item->menu_name,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                    ];
                }),
            ];

            if ($report) {
                // Update tutup hari yang sudah ada
                $existingOrderIds = collect($report->order_summary)->pluck('id')->toArray();

                // Cek apakah order sudah ada di tutup hari
                if (in_array($order->id, $existingOrderIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order ini sudah di-rekap sebelumnya.',
                    ], 400);
                }

                // Tambahkan order ke tutup hari
                $orderSummaryArray = collect($report->order_summary)->toArray();
                $orderSummaryArray[] = $orderSummary;

                // Hitung ulang statistik
                $totalOrders = count($orderSummaryArray);
                $totalRevenue = collect($orderSummaryArray)->sum('total_price');
                $cashRevenue = collect($orderSummaryArray)->where('payment_method', 'cash')->sum('total_price');
                $qrisRevenue = collect($orderSummaryArray)->where('payment_method', 'qris')->sum('total_price');
                $transferRevenue = collect($orderSummaryArray)->where('payment_method', 'transfer')->sum('total_price');

                $report->update([
                    'total_orders' => $totalOrders,
                    'total_revenue' => $totalRevenue,
                    'cash_revenue' => $cashRevenue,
                    'qris_revenue' => $qrisRevenue,
                    'transfer_revenue' => $transferRevenue,
                    'order_summary' => $orderSummaryArray,
                ]);
            } else {
                // Buat tutup hari baru untuk hari ini
                // Super admin: shift_id = null (all shifts)
                // Regular admin: shift_id = user->shift_id
                $report = Report::create([
                    'report_date' => Carbon::now('Asia/Jakarta'),
                    'start_date' => $today,
                    'end_date' => $today,
                    'total_orders' => 1,
                    'total_revenue' => $order->total_price,
                    'cash_revenue' => $order->payment_method === 'cash' ? $order->total_price : 0,
                    'qris_revenue' => $order->payment_method === 'qris' ? $order->total_price : 0,
                    'transfer_revenue' => $order->payment_method === 'transfer' ? $order->total_price : 0,
                    'order_summary' => [$orderSummary],
                    'created_by' => $user->name ?? 'System',
                    'shift_id' => $user->hasRole('super_admin') ? null : $user->shift_id,
                ]);
            }

            // Hapus order dan order items
            order_items::where('order_id', $order->id)->delete();
            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil di-rekap ke tutup hari.',
                'report_id' => $report->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rekap order: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal rekap order: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tutup hari - menggabungkan order yang sudah completed menjadi satu laporan
     * Setelah tutup hari, order akan dihapus dari histori
     */
    public function recap(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();

        // Super admin bisa recap tanpa harus punya shift_id, tapi regular admin harus punya shift_id
        if (! $user->hasRole('super_admin') && ! $user->shift_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum di-assign ke shift. Silakan hubungi administrator.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Ambil semua order yang sudah completed dalam periode tertentu
            // Super admin: lihat semua orders dari semua shifts
            // Regular admin: hanya orders dari shift mereka sendiri
            $startDate = Carbon::parse($request->start_date)->setTimezone('Asia/Jakarta')->startOfDay();
            $endDate = Carbon::parse($request->end_date)->setTimezone('Asia/Jakarta')->endOfDay();

            $query = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
                ->with('orderItems:id,order_id,menu_name,price,quantity,image')
                ->where('status', 'completed')
                ->whereBetween('created_at', [
                    $startDate->utc(),
                    $endDate->utc(),
                ]);

            // Filter berdasarkan shift jika bukan super_admin
            if (! $user->hasRole('super_admin')) {
                $query->where('shift_id', $user->shift_id);
            }

            $orders = $query->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada order yang bisa di-rekap untuk periode ini. Pastikan ada order dengan status completed untuk periode yang dipilih.',
                ], 400);
            }

            // Hitung total revenue berdasarkan payment method
            $totalRevenue = $orders->sum('total_price');
            $cashRevenue = $orders->where('payment_method', 'cash')->sum('total_price');
            $qrisRevenue = $orders->where('payment_method', 'qris')->sum('total_price');
            $transferRevenue = $orders->where('payment_method', 'transfer')->sum('total_price');

            // Cek apakah sudah ada tutup hari dengan periode yang sama
            // Super admin: cek tanpa filter shift_id
            // Regular admin: cek dengan filter shift_id mereka
            if ($user->hasRole('super_admin')) {
                $existingReport = Report::where('start_date', Carbon::parse($request->start_date)->setTimezone('Asia/Jakarta')->format('Y-m-d'))
                    ->where('end_date', Carbon::parse($request->end_date)->setTimezone('Asia/Jakarta')->format('Y-m-d'))
                    ->first();
            } else {
                $existingReport = Report::where('shift_id', $user->shift_id)
                    ->where('start_date', Carbon::parse($request->start_date)->setTimezone('Asia/Jakarta')->format('Y-m-d'))
                    ->where('end_date', Carbon::parse($request->end_date)->setTimezone('Asia/Jakarta')->format('Y-m-d'))
                    ->first();
            }

            // Buat ringkasan order (simpan detail order sebagai JSON)
            $orderSummary = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'table_number' => $order->table_number,
                    'room' => $order->room,
                    'total_price' => $order->total_price,
                    'payment_method' => $order->payment_method,
                    'created_at' => Carbon::parse($order->created_at)->utc()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'items' => $order->orderItems->map(function ($item) {
                        return [
                            'menu_name' => $item->menu_name,
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                        ];
                    }),
                ];
            })->toArray();

            if ($existingReport) {
                // Jika sudah ada tutup hari dengan periode yang sama, gabungkan
                $existingOrderIds = collect($existingReport->order_summary)->pluck('id')->toArray();
                $existingOrders = collect($existingReport->order_summary);

                // Filter order baru yang belum ada di tutup hari
                $newOrders = collect($orderSummary)->reject(function ($order) use ($existingOrderIds) {
                    return in_array($order['id'], $existingOrderIds);
                })->toArray();

                // Gabungkan order yang sudah ada dengan order baru
                $mergedOrderSummary = array_merge($existingOrders->toArray(), $newOrders);

                // Hitung ulang statistik
                $mergedTotalOrders = count($mergedOrderSummary);
                $mergedTotalRevenue = collect($mergedOrderSummary)->sum('total_price');
                $mergedCashRevenue = collect($mergedOrderSummary)->where('payment_method', 'cash')->sum('total_price');
                $mergedQrisRevenue = collect($mergedOrderSummary)->where('payment_method', 'qris')->sum('total_price');
                $mergedTransferRevenue = collect($mergedOrderSummary)->where('payment_method', 'transfer')->sum('total_price');

                // Update tutup hari yang sudah ada
                $existingReport->update([
                    'total_orders' => $mergedTotalOrders,
                    'total_revenue' => $mergedTotalRevenue,
                    'cash_revenue' => $mergedCashRevenue,
                    'qris_revenue' => $mergedQrisRevenue,
                    'transfer_revenue' => $mergedTransferRevenue,
                    'order_summary' => $mergedOrderSummary,
                ]);

                $report = $existingReport;
                $message = 'Tutup hari berhasil digabung dengan tutup hari yang sudah ada. '.count($newOrders).' order baru ditambahkan.';
            } else {
                // Buat tutup hari baru
                // Super admin: shift_id = null (all shifts)
                // Regular admin: shift_id = user->shift_id
                $report = Report::create([
                    'report_date' => Carbon::now('Asia/Jakarta'),
                    'start_date' => Carbon::parse($request->start_date)->setTimezone('Asia/Jakarta'),
                    'end_date' => Carbon::parse($request->end_date)->setTimezone('Asia/Jakarta'),
                    'total_orders' => $orders->count(),
                    'total_revenue' => $totalRevenue,
                    'cash_revenue' => $cashRevenue,
                    'qris_revenue' => $qrisRevenue,
                    'transfer_revenue' => $transferRevenue,
                    'order_summary' => $orderSummary,
                    'created_by' => Auth::user()->name ?? 'System',
                    'shift_id' => $user->hasRole('super_admin') ? null : $user->shift_id,
                ]);
                $message = 'Tutup hari berhasil dibuat. '.$orders->count().' order telah di-tutup hari.';
            }

            // Hapus order yang sudah di-rekap beserta order items-nya
            $orderIds = $orders->pluck('id');
            order_items::whereIn('order_id', $orderIds)->delete();
            orders::whereIn('id', $orderIds)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'report' => $report,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating recap: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tutup hari: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan halaman tutup hari
     */
    public function recapIndex(Request $request)
    {
        $user = Auth::user();

        // Super admin bisa lihat semua recap tanpa harus punya shift_id
        // Regular admin harus punya shift_id
        if (! $user->hasRole('super_admin') && ! $user->shift_id) {
            return view('admin.orders.recap', [
                'reports' => new \Illuminate\Pagination\Paginator([], 20),
            ])->with('error', 'Anda belum di-assign ke shift. Silakan hubungi administrator.');
        }

        // Query dasar untuk tutup hari
        // Super admin lihat semua, regular admin filter by shift
        if ($user->hasRole('super_admin')) {
            $query = Report::query();
        } else {
            $query = Report::where(function ($q) use ($user) {
                $q->where('shift_id', $user->shift_id)
                    ->orWhereNull('shift_id'); // Tampilkan juga rekap lama yang belum punya shift_id
            });
        }

        // Filter berdasarkan tanggal - default ke tanggal hari ini jika tidak ada filter
        $filterDate = $request->has('filter_date') && $request->filter_date
            ? Carbon::parse($request->filter_date)->setTimezone('Asia/Jakarta')->format('Y-m-d')
            : Carbon::now('Asia/Jakarta')->format('Y-m-d');

        $query->whereDate('report_date', $filterDate);

        // Order dan paginate
        $reports = $query->orderBy('report_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.orders.recap', compact('reports'));
    }

    /**
     * Get detail tutup hari untuk modal
     */
    public function recapDetail($id)
    {
        $user = Auth::user();
        $report = Report::findOrFail($id);

        // Super admin bisa akses semua recap
        // Regular admin hanya bisa akses recap shift mereka atau rekap lama yang shift_id-nya null
        if (! $user->hasRole('super_admin')) {
            if ($report->shift_id !== null && $report->shift_id != $user->shift_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke tutup hari ini.',
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'order_summary' => $report->order_summary,
        ]);
    }

    /**
     * Update tutup hari yang sudah ada
     */
    public function updateRecap(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $report = Report::findOrFail($id);

            // Super admin bisa update semua recap
            // Regular admin hanya bisa update recap shift mereka
            if (! $user->hasRole('super_admin')) {
                if ($report->shift_id !== null && $report->shift_id != $user->shift_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk mengubah tutup hari ini.',
                    ], 403);
                }
            }

            // Cek apakah periode baru sama dengan tutup hari lain (selain yang sedang diupdate)
            $existingReportWithSamePeriod = Report::where('id', '!=', $id)
                ->where('start_date', Carbon::parse($request->start_date)->format('Y-m-d'))
                ->where('end_date', Carbon::parse($request->end_date)->format('Y-m-d'))
                ->first();

            // Ambil semua order yang sudah completed dalam periode baru
            // Catatan: Order yang sudah di-rekap sebelumnya sudah dihapus,
            // jadi kita hanya bisa mengambil order baru yang completed untuk periode tersebut
            $orders = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'payment_method', 'created_at', 'updated_at', 'shift_id')
                ->with('orderItems:id,order_id,menu_name,price,quantity,image')
                ->where('status', 'completed')
                ->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay(),
                ])
                ->get();

            // Gabungkan dengan order yang sudah ada di tutup hari sebelumnya
            $existingOrderIds = collect($report->order_summary)->pluck('id')->toArray();
            $existingOrders = collect($report->order_summary);

            // Jika ada tutup hari lain dengan periode yang sama, gabungkan juga
            if ($existingReportWithSamePeriod) {
                $otherReportOrderIds = collect($existingReportWithSamePeriod->order_summary)->pluck('id')->toArray();
                $otherReportOrders = collect($existingReportWithSamePeriod->order_summary);

                // Gabungkan order dari tutup hari lain (yang belum ada di tutup hari ini)
                $otherOrdersToMerge = $otherReportOrders->reject(function ($order) use ($existingOrderIds) {
                    return in_array($order['id'], $existingOrderIds);
                });

                // Tambahkan ke existing orders
                $existingOrders = $existingOrders->merge($otherOrdersToMerge);
                $existingOrderIds = $existingOrders->pluck('id')->toArray();
            }

            // Ambil order baru yang belum ada di tutup hari
            $newOrders = $orders->reject(function ($order) use ($existingOrderIds) {
                return in_array($order->id, $existingOrderIds);
            });

            // Buat order summary baru (gabungkan yang lama dengan yang baru)
            $newOrderSummary = $existingOrders->map(function ($order) {
                return [
                    'id' => $order['id'],
                    'customer_name' => $order['customer_name'],
                    'table_number' => $order['table_number'],
                    'room' => $order['room'],
                    'total_price' => $order['total_price'],
                    'payment_method' => $order['payment_method'],
                    'created_at' => $order['created_at'],
                    'items' => $order['items'],
                ];
            })->toArray();

            // Jika ada tutup hari lain dengan periode yang sama, hapus tutup hari tersebut setelah digabung
            if ($existingReportWithSamePeriod) {
                $existingReportWithSamePeriod->delete();
            }

            // Tambahkan order baru jika ada
            foreach ($newOrders as $order) {
                $newOrderSummary[] = [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'table_number' => $order->table_number,
                    'room' => $order->room,
                    'total_price' => $order->total_price,
                    'payment_method' => $order->payment_method,
                    'created_at' => Carbon::parse($order->created_at)->utc()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'items' => $order->orderItems->map(function ($item) {
                        return [
                            'menu_name' => $item->menu_name,
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                        ];
                    }),
                ];
            }

            // Jika tidak ada order sama sekali (baik yang lama maupun yang baru)
            if (empty($newOrderSummary)) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada order yang bisa di-rekap untuk periode ini. Pastikan ada order dengan status completed untuk periode yang dipilih.',
                ], 400);
            }

            // Hitung ulang statistik berdasarkan order_summary yang baru
            $totalOrders = count($newOrderSummary);
            $totalRevenue = collect($newOrderSummary)->sum('total_price');
            $cashRevenue = collect($newOrderSummary)->where('payment_method', 'cash')->sum('total_price');
            $qrisRevenue = collect($newOrderSummary)->where('payment_method', 'qris')->sum('total_price');
            $transferRevenue = collect($newOrderSummary)->where('payment_method', 'transfer')->sum('total_price');

            // Update tutup hari
            $report->update([
                'start_date' => Carbon::parse($request->start_date),
                'end_date' => Carbon::parse($request->end_date),
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'cash_revenue' => $cashRevenue,
                'qris_revenue' => $qrisRevenue,
                'transfer_revenue' => $transferRevenue,
                'order_summary' => $newOrderSummary,
            ]);

            // Hapus order baru yang sudah ditambahkan ke tutup hari
            if ($newOrders->isNotEmpty()) {
                $newOrderIds = $newOrders->pluck('id');
                order_items::whereIn('order_id', $newOrderIds)->delete();
                orders::whereIn('id', $newOrderIds)->delete();
            }

            DB::commit();

            $message = 'Tutup hari berhasil diperbarui.';
            if ($newOrders->count() > 0) {
                $message .= ' '.$newOrders->count().' order baru ditambahkan.';
            } else {
                $message .= ' Periode tutup hari telah diupdate.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'report' => $report->fresh(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating recap: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tutup hari: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download tutup hari sebagai Excel
     */
    public function exportRecap($id)
    {
        $user = Auth::user();
        $report = Report::findOrFail($id);

        // Pastikan user hanya bisa export tutup hari shift mereka sendiri
        if ($report->shift_id != $user->shift_id) {
            return back()->with('error', 'Anda tidak memiliki akses ke tutup hari ini.');
        }

        $startDate = Carbon::parse($report->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($report->end_date)->format('Y-m-d');
        $filename = 'Tutup_Hari_'.$startDate.'_s_d_'.$endDate.'.xlsx';

        return Excel::download(new RecapExport($report), $filename);
    }

    /**
     * Kirim tutup hari via email
     */
    public function sendRecapEmail(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = Auth::user();
        $report = Report::findOrFail($id);

        // Pastikan user hanya bisa kirim tutup hari shift mereka sendiri
        if ($report->shift_id != $user->shift_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke tutup hari ini.',
            ], 403);
        }

        $startDate = Carbon::parse($report->start_date)->format('d M Y');
        $endDate = Carbon::parse($report->end_date)->format('d M Y');
        $reportPeriod = $startDate.' - '.$endDate;

        $filename = 'Tutup_Hari_'.Carbon::parse($report->start_date)->format('Y-m-d').'_s_d_'.Carbon::parse($report->end_date)->format('Y-m-d').'.xlsx';

        try {
            // Check email configuration
            if (config('mail.default') === 'log') {
                Log::info('Email tutup hari akan dikirim ke: '.$request->email);
            }

            // Ensure temp directory exists
            $tempDir = storage_path('app/temp');
            if (! file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Store Excel file temporarily
            $filePath = 'temp/'.$filename;
            Excel::store(new RecapExport($report), $filePath, 'local');

            // Get full path for attachment
            $fullPath = Storage::path($filePath);

            // Check if file exists
            if (! file_exists($fullPath)) {
                throw new \Exception('File Excel tidak berhasil dibuat');
            }

            // Log before sending
            Log::info('Attempting to send recap email', [
                'to' => $request->email,
                'file' => $filename,
                'mailer' => config('mail.default'),
            ]);

            // Send email with attachment
            Mail::to($request->email)->send(new SendRecapEmail($fullPath, $filename, $reportPeriod, $report));

            // Log after sending
            Log::info('Recap email sent successfully', [
                'to' => $request->email,
                'file' => $filename,
            ]);

            // Delete temporary file after sending
            Storage::delete($filePath);

            return response()->json([
                'success' => true,
                'message' => 'Tutup hari berhasil dikirim ke '.$request->email.'. Silakan cek kotak masuk dan folder Spam Anda.',
            ]);
        } catch (\Exception $e) {
            // Delete temporary file if exists
            if (isset($filePath)) {
                Storage::delete($filePath);
            }

            Log::error('Recap Email Error: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: '.$e->getMessage(),
            ], 500);
        }
    }
}
