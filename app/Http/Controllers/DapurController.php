<?php

namespace App\Http\Controllers;

use App\Models\KitchenReport;
use App\Models\order_items;
use App\Models\orders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DapurController extends Controller
{
    /**
     * Display kitchen dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Get active orders for current shift (optimized query)
        $query = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'created_at', 'updated_at', 'shift_id')
            ->with(['orderItems' => function ($query) {
                $query->select('id', 'order_id', 'menu_name', 'price', 'quantity', 'image');
            }])
            ->where('status', 'processing')
            ->orderBy('created_at', 'desc');

        // Apply shift filter
        if (! $user->hasRole('super_admin') && $user->shift_id) {
            $query->where('shift_id', $user->shift_id);
        }

        $orders = $query->get();

        $todayJakarta = Carbon::now('Asia/Jakarta')->toDateString();

        // Optimized query untuk total menu sold
        $totalMenuSold = order_items::select('quantity')
            ->whereHas('order', function ($q) use ($todayJakarta, $user) {
                $q->select('id', 'status', 'created_at', 'shift_id')
                    ->where('status', 'completed')
                    ->whereDate('created_at', $todayJakarta);

                if (! $user->hasRole('super_admin') && $user->shift_id) {
                    $q->where('shift_id', $user->shift_id);
                }
            })
            ->sum('quantity');

        return view('dapur.dashboard', compact('orders', 'totalMenuSold'));
    }

    /**
     * Get active orders (API endpoint)
     */
    public function activeOrders()
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['orders' => []], 401);
        }

        // Get active orders (optimized query)
        $query = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->where('status', 'processing')
            ->orderBy('created_at', 'desc');

        // Apply shift filter
        if (! $user->hasRole('super_admin') && $user->shift_id) {
            $query->where('shift_id', $user->shift_id);
        }

        $orders = $query->get();

        return response()->json([
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'table_number' => $order->table_number,
                    'room' => $order->room,
                    'status' => $order->status,
                    'total_price' => $order->total_price,
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

        return response()->stream(function () use ($user) {
            // keep script alive while client is connected
            if (function_exists('ignore_user_abort')) {
                ignore_user_abort(true);
            }
            if (function_exists('set_time_limit')) {
                @set_time_limit(0);
            }

            // Initialize lastOrderId to the latest existing order id for the relevant scope
            $initialQuery = orders::whereIn('status', ['pending', 'processing']);
            if (! $user->hasRole('super_admin') && $user->shift_id) {
                $initialQuery->where('shift_id', $user->shift_id);
            }
            $lastOrderId = (int) $initialQuery->max('id');
            $isLocal = app()->environment('local');
            $checkInterval = $isLocal ? 2.0 : 1.5;
            $startedAt = microtime(true);
            $maxConnectionSeconds = $isLocal ? 5 : 30;

            while (true) {
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

                // Get latest orders (optimized query)
                $query = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'created_at', 'updated_at', 'shift_id')
                    ->with('orderItems:id,order_id,menu_name,price,quantity,image')
                    ->whereIn('status', ['pending', 'processing'])
                    ->where('id', '>', $lastOrderId)
                    ->orderBy('created_at', 'asc');

                // Apply shift filter
                if (! $user->hasRole('super_admin') && $user->shift_id) {
                    $query->where('shift_id', $user->shift_id);
                }

                $orders = $query->get();

                if ($orders->count() > 0) {
                    $ordersData = $orders->map(function ($order) {
                        return [
                            'id' => $order->id,
                            'customer_name' => $order->customer_name,
                            'table_number' => $order->table_number,
                            'room' => $order->room,
                            'status' => $order->status,
                            'total_price' => $order->total_price,
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

                    $lastOrderId = $orders->max('id');

                    // send SSE id so client can track last event
                    echo "id: {$lastOrderId}\n";
                    echo 'data: '.json_encode([
                        'type' => 'new_orders',
                        'orders' => $ordersData,
                    ])."\n\n";

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                } else {
                    // Send heartbeat
                    echo ": heartbeat\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }

                usleep((int) ($checkInterval * 1000000));
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Start cooking order
     */
    public function startCooking($id)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $order = orders::findOrFail($id);

        // Authorization
        if (! $user->hasRole('super_admin') && $order->shift_id !== $user->shift_id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke order ini.'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Order ini tidak dapat dimulai karena statusnya bukan pending.'], 400);
        }

        $order->update(['status' => 'processing']);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dimulai.',
            'order' => $order->fresh(),
        ]);
    }

    /**
     * Complete order
     */
    public function complete($id)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $order = orders::select('id', 'customer_name', 'table_number', 'room', 'status', 'total_price', 'created_at', 'updated_at', 'shift_id')
            ->with('orderItems:id,order_id,menu_name,price,quantity,image')
            ->findOrFail($id);

        // Authorization
        if (! $user->hasRole('super_admin') && $order->shift_id !== $user->shift_id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke order ini.'], 403);
        }

        // Update status
        $order->update(['status' => 'completed']);

        // Save to kitchen_reports
        try {
            $existingReport = KitchenReport::where('order_id', $order->id)->first();

            if (! $existingReport) {
                $orderItems = $order->orderItems->map(function ($item) {
                    return [
                        'menu_name' => $item->menu_name,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'image' => $item->image,
                    ];
                })->toArray();

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
            Log::error('Error saving to kitchen_reports', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

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
    }

    /**
     * Show profile edit form for kitchen staff
     */
    public function profileEdit()
    {
        $user = auth()->user();

        return view('dapur.profile', compact('user'));
    }

    /**
     * Update profile information for kitchen staff
     */
    public function profileUpdate(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $user->update($validated);

        return redirect()->route('dapur.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password for kitchen staff
     */
    public function profilePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('dapur.profile.edit')->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Update color preference for kitchen staff
     */
    public function profileColorUpdate(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'primary_color' => ['required', 'string', 'in:#fbbf24,#fa9a08,#2f7d7a'],
        ]);

        $user->update($validated);

        return redirect()->route('dapur.profile.edit')->with('success', 'Warna preferensi berhasil diperbarui.');
    }
}
