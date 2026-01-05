<?php

namespace App\Http\Controllers;

use App\Models\AboutFounder;
use App\Models\CategoryMenu;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Footer;
use App\Models\HeroSection;
use App\Models\KeunggulanFasilitas;
use App\Models\KitchenReport;
use App\Models\Menu;
use App\Models\order_items;
use App\Models\orders;
use App\Models\PortfolioAchievement;
use App\Models\TentangKami;
use App\Models\TestimoniPelanggan;
use App\Models\TimKami;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
    // ===================== PRO TIM (CRUD dalam satu file, manage-content) =====================
    public function proTimIndex()
    {
        $this->authorizeAdminOnly();
        $proTeams = \App\Models\ProTeam::orderBy('order')->get();
        return view('admin.manage-content.pro-tim', compact('proTeams'));
    }

    public function proTimStore(Request $request)
    {
        $this->authorizeAdminOnly();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0|max:100',
            'origin' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['order'] = $validated['order'] ?? (\App\Models\ProTeam::max('order') + 1);
        \App\Models\ProTeam::create($validated);
        return redirect()->route('admin.cms.pro-tim')->with('success', 'Pro Tim berhasil ditambahkan!');
    }

    public function proTimEdit($id)
    {
        $this->authorizeAdminOnly();
        $proTeams = \App\Models\ProTeam::orderBy('order')->get();
        $editData = \App\Models\ProTeam::findOrFail($id);
        return view('admin.manage-content.pro-tim', compact('proTeams', 'editData'));
    }

    public function proTimUpdate(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0|max:100',
            'origin' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
        ]);
        $proTeam = \App\Models\ProTeam::findOrFail($id);
        $proTeam->update($validated);
        return redirect()->route('admin.cms.pro-tim')->with('success', 'Pro Tim berhasil diperbarui!');
    }

    public function proTimDestroy($id)
    {
        $this->authorizeAdminOnly();
        $proTeam = \App\Models\ProTeam::findOrFail($id);
        $proTeam->delete();
        return redirect()->route('admin.cms.pro-tim')->with('success', 'Pro Tim berhasil dihapus!');
    }
{
    /**
     * Image validation rules for production
     */
    private const IMAGE_RULES = 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048';
    private const IMAGE_REQUIRED_RULES = 'required|image|mimes:jpeg,jpg,png,webp|max:2048';

    /**
     * Clear CMS component caches after update
     * This ensures homepage reflects changes immediately
     */
    private function clearCmsCache(array $keys = []): void
    {
        // Default cache keys used by blade components
        $defaultKeys = [
            'component_hero',
            'component_about',
            'component_founder', 
            'component_keunggulan',
            'component_achievements',  // Portfolio/Achievement section
            'component_team',
            'component_testimonials',
            'component_events',
            'component_contact',
            'component_footer',
            // Navbar caches
            'navbar_contact',
            'navbar_hero',
        ];
        
        $keysToForget = empty($keys) ? $defaultKeys : $keys;
        
        foreach ($keysToForget as $key) {
            Cache::forget($key);
        }
        
        // Also clear application cache, views, and OPcache for immediate effect
        try {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            
            // Clear OPcache if available
            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear system caches', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get the correct storage path for both local and Hostinger production
     * Hostinger: public_html/storage/  (document root is public_html, NOT public_html/public)
     * Local: public/storage/ (standard Laravel)
     */
    private function getStoragePath(string $relativePath = ''): string
    {
        // On Hostinger, base_path() = public_html, so storage is at base_path('storage/')
        // On local, public_path('storage/') is correct
        if (str_contains(base_path(), 'public_html')) {
            return base_path('storage/' . $relativePath);
        }
        
        return public_path('storage/' . $relativePath);
    }

    /**
     * Safe delete file from storage (supports both local and Hostinger)
     */
    private function safeDeleteFile(?string $path): void
    {
        if (!$path) return;
        
        // Try multiple possible locations
        $possiblePaths = [
            base_path('storage/' . $path),              // Hostinger: public_html/storage/
            public_path('storage/' . $path),            // Standard Laravel: public/storage/
            storage_path('app/public/' . $path),        // Laravel storage disk
        ];
        
        foreach ($possiblePaths as $fullPath) {
            if (file_exists($fullPath)) {
                try {
                    @unlink($fullPath);
                    Log::info('Deleted file: ' . $fullPath);
                    return;
                } catch (\Exception $e) {
                    Log::warning('Failed to delete file: ' . $fullPath, ['error' => $e->getMessage()]);
                }
            }
        }
        
        // Last resort: try Laravel Storage facade
        if (Storage::disk('public')->exists($path)) {
            try {
                Storage::disk('public')->delete($path);
            } catch (\Exception $e) {
                Log::warning('Failed to delete via Storage: ' . $path, ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Safe store file (works on both local and Hostinger/LiteSpeed)
     */
    private function safeStoreFile($file, string $folder): ?string
    {
        try {
            // Generate unique filename
            $filename = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
            $relativePath = $folder . '/' . $filename;
            
            // Determine correct storage directory
            // Hostinger: base_path('storage/') = public_html/storage/
            // Local: public_path('storage/') = public/storage/
            if (str_contains(base_path(), 'public_html')) {
                $directoryPath = base_path('storage/' . $folder);
            } else {
                $directoryPath = public_path('storage/' . $folder);
            }
            
            // Ensure directory exists
            if (!is_dir($directoryPath)) {
                @mkdir($directoryPath, 0755, true);
            }
            
            // Move file to storage
            $file->move($directoryPath, $filename);
            
            Log::info('Stored file: ' . $directoryPath . '/' . $filename);
            
            return $relativePath;
        } catch (\Exception $e) {
            Log::error('Failed to store file', ['folder' => $folder, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Authorize only admin and super_admin for CMS content management
     * CMS routes are for managing website content (not core business operations)
     */
    private function authorizeAdminOnly(): void
    {
        if (! auth()->user()->hasRole(['admin', 'super_admin'])) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Unauthorized action.');
        }
    }

    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Sales Analytics page (Check report.view permission)
     */
    public function salesAnalytics()
    {
        // Permission check sudah dilakukan di middleware, tidak perlu hardcode role check
        return view('admin.sales-analytics');
    }

    /**
     * Get menu sales data by category for chart (Check report.view permission)
     */
    public function getMenuSalesData(Request $request)
    {
        // Permission check sudah dilakukan di middleware, tidak perlu hardcode role check

        // Get date range from request (default: all time)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get order items from both sources:
        // 1. From orders table (status = completed) - primary source
        // 2. From kitchen_reports table - backup source for data persistence

        $orderItems = collect();

        // Source 1: Get from orders table (completed orders)
        $orderItemsQuery = order_items::select(
            'order_items.menu_name',
            'order_items.quantity',
            'order_items.price',
            'orders.created_at',
            'orders.id as order_id'
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed');

        // Apply date filter if provided
        if ($startDate) {
            $orderItemsQuery->whereDate('orders.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $orderItemsQuery->whereDate('orders.created_at', '<=', $endDate);
        }

        $orderItemsFromOrders = $orderItemsQuery->get();
        $orderItems = $orderItems->merge($orderItemsFromOrders);

        // Get unique order IDs from orders table
        $existingOrderIds = $orderItemsFromOrders->pluck('order_id')->unique()->toArray();

        // Source 2: Get from kitchen_reports table (backup data for deleted orders)
        $kitchenReportsQuery = KitchenReport::query();

        if ($startDate) {
            $kitchenReportsQuery->whereDate('order_date', '>=', $startDate);
        }
        if ($endDate) {
            $kitchenReportsQuery->whereDate('order_date', '<=', $endDate);
        }

        $kitchenReports = $kitchenReportsQuery->get();

        // Extract order items from kitchen_reports (only for orders not in orders table)
        foreach ($kitchenReports as $report) {
            // Skip if this order already exists in orders table (to avoid duplicates)
            if (in_array($report->order_id, $existingOrderIds)) {
                continue;
            }

            $items = json_decode($report->order_items, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    // Add as object-like structure
                    $orderItems->push((object) [
                        'menu_name' => $item['menu_name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'created_at' => \Carbon\Carbon::parse($report->order_date),
                    ]);
                }
            }
        }

        // Initialize category stats DYNAMIS dari database
        $allCategories = CategoryMenu::pluck('name')->toArray();
        $categoryStats = [];
        foreach ($allCategories as $category) {
            $categoryStats[$category] = ['quantity' => 0, 'revenue' => 0];
        }

        // Optimasi: Load semua menu sekaligus untuk menghindari N+1 query
        $menuNames = $orderItems->pluck('menu_name')->unique()->toArray();
        $menus = Menu::select('id', 'name', 'category_menu_id')
            ->with('categoryMenu:id,name')
            ->whereIn('name', $menuNames)
            ->get()
            ->keyBy('name'); // Key by name untuk fast lookup

        // Process each order item
        foreach ($orderItems as $item) {
            // Find menu by name from pre-loaded collection
            $menu = $menus->get($item->menu_name);

            if ($menu && $menu->categoryMenu) {
                $categoryName = $menu->categoryMenu->name;

                // Process semua category yang ada di database
                if (isset($categoryStats[$categoryName])) {
                    $categoryStats[$categoryName]['quantity'] += $item->quantity;
                    $categoryStats[$categoryName]['revenue'] += ($item->price * $item->quantity);
                }
            }
        }

        // Get menu sales detail (per menu item)
        $menuSalesDetail = [];
        foreach ($orderItems as $item) {
            // Find menu by name from pre-loaded collection
            $menu = $menus->get($item->menu_name);

            if ($menu && $menu->categoryMenu) {
                $categoryName = $menu->categoryMenu->name;

                if (isset($categoryStats[$categoryName])) {
                    if (! isset($menuSalesDetail[$item->menu_name])) {
                        $menuSalesDetail[$item->menu_name] = [
                            'name' => $item->menu_name,
                            'quantity' => 0,
                            'category' => $categoryName,
                        ];
                    }
                    $menuSalesDetail[$item->menu_name]['quantity'] += $item->quantity;
                }
            }
        }

        // Sort by quantity descending and convert to indexed array
        usort($menuSalesDetail, function ($a, $b) {
            return $b['quantity'] - $a['quantity'];
        });

        // Convert to indexed array
        $menuSalesDetail = array_values($menuSalesDetail);

        // Format data untuk chart (DYNAMIS)
        $chartLabels = array_keys($categoryStats);
        $chartQuantities = array_values(array_map(fn ($stat) => (int) $stat['quantity'], $categoryStats));
        $chartRevenues = array_values(array_map(fn ($stat) => (float) $stat['revenue'], $categoryStats));

        $chartData = [
            'labels' => $chartLabels, // Dynamis dari database
            'quantities' => $chartQuantities,
            'revenues' => $chartRevenues,
            'total_items' => (int) array_sum(array_column($categoryStats, 'quantity')),
            'total_revenue' => (float) array_sum(array_column($categoryStats, 'revenue')),
            'menu_count' => count($menuSalesDetail), // Jumlah menu yang terjual
            'menu_details' => $menuSalesDetail, // Detail per menu
        ];

        return response()->json($chartData);
    }

    // Hero Section
    public function heroIndex()
    {
        $this->authorizeAdminOnly();
        $hero = HeroSection::first();

        return view('admin.manage-content.hero', compact('hero'));
    }

    public function heroUpdate(Request $request)
    {
        $this->authorizeAdminOnly();

        $validated = $request->validate([
            'logo_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'title' => 'required|string|max:100',
            'subtitle' => 'required|string|max:100',
            'tagline' => 'nullable|string|max:255',
            'cta_text_1' => 'nullable|string|max:50',
            'cta_link_1' => 'nullable|string|max:255',
            'cta_text_2' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();
            
            $hero = HeroSection::firstOrNew();

            // Handle logo_image upload
            if ($request->hasFile('logo_image')) {
                $this->safeDeleteFile($hero->logo_image);
                $hero->logo_image = $this->safeStoreFile($request->file('logo_image'), 'hero');
            }

            // Handle background_image upload
            if ($request->hasFile('background_image')) {
                $this->safeDeleteFile($hero->background_image);
                $hero->background_image = $this->safeStoreFile($request->file('background_image'), 'hero');
            }

            // Update text fields
            $hero->title = $validated['title'];
            $hero->subtitle = $validated['subtitle'];
            $hero->tagline = $validated['tagline'] ?? '';
            $hero->cta_text_1 = $validated['cta_text_1'] ?? 'BOOK A TABLE';
            $hero->cta_text_2 = $validated['cta_text_2'] ?? 'EXPLORE';
            
            // Normalize CTA link 1 if provided
            $hero->cta_link_1 = $this->normalizeWhatsAppLink($validated['cta_link_1'] ?? null);
            
            $hero->is_active = $request->boolean('is_active');
            $hero->save();

            DB::commit();
            
            // Clear hero cache so homepage reflects changes immediately
            // Also clear navbar_hero since navbar uses hero logo
            $this->clearCmsCache(['component_hero', 'navbar_hero']);
            
            return redirect()->route('admin.cms.hero')->with('success', 'Hero section berhasil diperbarui!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hero update failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Normalize phone/WhatsApp input to canonical URL
     */
    private function normalizeWhatsAppLink(?string $input): ?string
    {
        if (!$input || trim($input) === '') {
            return null;
        }
        
        $link = trim($input);
        
        // If already a URL, normalize scheme
        if (preg_match('#^https?://#i', $link)) {
            return preg_replace('#^http://#i', 'https://', $link);
        }
        
        // Extract digits and convert to wa.me URL
        $digits = preg_replace('/\D+/', '', $link);
        if (preg_match('/^0/', $digits)) {
            $digits = preg_replace('/^0+/', '', $digits);
            $digits = '62' . $digits;
        }
        
        return $digits !== '' ? 'https://wa.me/' . $digits : null;
    }

    // Tentang Kami
    public function tentangKamiIndex()
    {
        $this->authorizeAdminOnly();
        $tentangKami = TentangKami::first();

        return view('admin.manage-content.tentang-kami', compact('tentangKami'));
    }

    public function tentangKamiUpdate(Request $request)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'visi' => 'nullable|string|max:2000',
            'misi' => 'nullable|string|max:2000',
            'arah_gerak' => 'nullable|string|max:2000',
            'video_url' => 'nullable|string|max:2048',
            'video_description' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();
            
            $tentangKami = TentangKami::firstOrNew();

            if ($request->hasFile('image')) {
                $this->safeDeleteFile($tentangKami->image);
                $tentangKami->image = $this->safeStoreFile($request->file('image'), 'tentang-kami');
            }

            // Remove image from validated to prevent overwriting with temp path
            unset($validated['image']);
            $tentangKami->fill($validated);

            if (!empty($validated['video_url'])) {
                $tentangKami->video_url = $this->convertToEmbedUrl($validated['video_url']);
            }

            $tentangKami->is_active = $request->boolean('is_active');
            $tentangKami->save();

            DB::commit();
            $this->clearCmsCache(['component_about']);
            return redirect()->route('admin.cms.tentang-kami')->with('success', 'Tentang Kami berhasil diperbarui!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tentang Kami update failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    private function convertToEmbedUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }
        $url = trim($url);
        $parsed = parse_url($url);
        if (! $parsed || empty($parsed['host'])) {
            return $url;
        }
        $host = $parsed['host'];
        $path = $parsed['path'] ?? '';
        $query = $parsed['query'] ?? '';

        if (strpos($host, 'youtu.be') !== false) {
            $id = ltrim($path, '/');
            $id = preg_replace('/[^A-Za-z0-9_-]/', '', $id);

            return $id ? "https://www.youtube.com/embed/{$id}" : $url;
        }

        if (strpos($host, 'youtube.com') !== false) {
            if (strpos($path, '/watch') === 0 || $path === '/watch') {
                parse_str($query, $params);
                $id = $params['v'] ?? null;
                $id = $id ? preg_replace('/[^A-Za-z0-9_-]/', '', $id) : null;

                return $id ? "https://www.youtube.com/embed/{$id}" : $url;
            }
            if (strpos($path, '/shorts/') === 0) {
                $id = trim(substr($path, strlen('/shorts/')));
                $id = preg_replace('/[^A-Za-z0-9_-]/', '', $id);

                return $id ? "https://www.youtube.com/embed/{$id}" : $url;
            }
            if (strpos($path, '/embed/') === 0) {
                return $url;
            }
        }

        // Instagram: support /p/{code}, /reel/{code}, /tv/{code}
        if (strpos($host, 'instagram.com') !== false) {
            $segments = array_values(array_filter(explode('/', $path)));
            if (count($segments) >= 2) {
                $type = $segments[0];
                $code = $segments[1];
                if (in_array($type, ['p', 'reel', 'tv'])) {
                    $code = preg_replace('/[^A-Za-z0-9_-]/', '', $code);
                    if (! empty($code)) {
                        return "https://www.instagram.com/{$type}/{$code}/embed";
                    }
                }
            }
            // If already embed
            if (strpos($path, '/embed') !== false) {
                return $url;
            }
        }

        return $url;
    }

    // About Founder
    public function aboutFounderIndex()
    {
        $this->authorizeAdminOnly();
        $aboutFounder = AboutFounder::first();

        return view('admin.manage-content.about-founder', compact('aboutFounder'));
    }

    public function aboutFounderUpdate(Request $request)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'quote' => 'nullable|string|max:500',
            'signature' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            $aboutFounder = AboutFounder::firstOrNew();

            if ($request->hasFile('photo')) {
                $this->safeDeleteFile($aboutFounder->photo);
                $aboutFounder->photo = $this->safeStoreFile($request->file('photo'), 'founder');
            }

            if ($request->hasFile('image')) {
                $this->safeDeleteFile($aboutFounder->image);
                $aboutFounder->image = $this->safeStoreFile($request->file('image'), 'founder');
            }

            // Remove file fields from validated to prevent overwriting with temp path
            unset($validated['photo'], $validated['image']);
            $aboutFounder->fill($validated);
            $aboutFounder->is_active = $request->boolean('is_active');
            $aboutFounder->save();

            DB::commit();
            $this->clearCmsCache(['component_founder']);
            return redirect()->route('admin.cms.about-founder')->with('success', 'About Founder berhasil diperbarui!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('About Founder update failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    // Keunggulan Fasilitas
    public function keunggulanFasilitasIndex()
    {
        $this->authorizeAdminOnly();
        $keunggulanFasilitas = KeunggulanFasilitas::orderBy('order')->get();

        return view('admin.manage-content.keunggulan-fasilitas', compact('keunggulanFasilitas'));
    }

    public function keunggulanFasilitasStore(Request $request)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        try {
            $validated['is_active'] = $request->boolean('is_active');
            $validated['order'] = $validated['order'] ?? KeunggulanFasilitas::max('order') + 1;
            
            KeunggulanFasilitas::create($validated);

            $this->clearCmsCache(['component_keunggulan']);
            return redirect()->route('admin.cms.keunggulan-fasilitas')->with('success', 'Fasilitas berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            Log::error('Keunggulan Fasilitas store failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function keunggulanFasilitasUpdate(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        try {
            $fasilitas = KeunggulanFasilitas::findOrFail($id);
            $fasilitas->fill($validated);
            $fasilitas->is_active = $request->boolean('is_active');
            $fasilitas->save();

            $this->clearCmsCache(['component_keunggulan']);
            return redirect()->route('admin.cms.keunggulan-fasilitas')->with('success', 'Fasilitas berhasil diperbarui!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.cms.keunggulan-fasilitas')->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Keunggulan Fasilitas update failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function keunggulanFasilitasDestroy($id)
    {
        $this->authorizeAdminOnly();
        
        try {
            $fasilitas = KeunggulanFasilitas::findOrFail($id);
            $fasilitas->delete();

            $this->clearCmsCache(['component_keunggulan']);
            return redirect()->route('admin.cms.keunggulan-fasilitas')->with('success', 'Fasilitas berhasil dihapus!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.cms.keunggulan-fasilitas')->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Keunggulan Fasilitas delete failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    // Portfolio Achievement
    public function portfolioAchievementIndex()
    {
        $this->authorizeAdminOnly();
        $allAchievements = PortfolioAchievement::orderBy('order')->get();

        return view('admin.manage-content.portfolio-achievement', compact('allAchievements'));
    }

    public function portfolioAchievementStore(Request $request)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:15360',
            'order' => 'nullable|integer|min:0|max:999',
        ]);

        try {
            $validated['type'] = 'gallery';
            $validated['is_active'] = $request->boolean('is_active');
            $validated['order'] = $validated['order'] ?? PortfolioAchievement::max('order') + 1;

            if ($request->hasFile('image')) {
                $validated['image'] = $this->safeStoreFile($request->file('image'), 'portfolio');
            }

            PortfolioAchievement::create($validated);

            $this->clearCmsCache(['component_achievements']);
            return redirect()->route('admin.cms.portfolio-achievement')->with('success', 'Achievement berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            Log::error('Portfolio store failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function portfolioAchievementUpdate(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:15360',
            'order' => 'nullable|integer|min:0|max:999',
        ]);

        try {
            $item = PortfolioAchievement::findOrFail($id);

            if ($request->hasFile('image')) {
                $this->safeDeleteFile($item->image);
                $item->image = $this->safeStoreFile($request->file('image'), 'portfolio');
            }

            // Remove image from validated to prevent overwriting with temp path
            unset($validated['image']);
            $item->fill($validated);
            $item->type = 'gallery';
            $item->is_active = $request->boolean('is_active');
            $item->save();

            $this->clearCmsCache(['component_achievements']);
            return redirect()->route('admin.cms.portfolio-achievement')->with('success', 'Achievement berhasil diperbarui!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.cms.portfolio-achievement')->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Portfolio update failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function portfolioAchievementDestroy($id)
    {
        $this->authorizeAdminOnly();
        
        try {
            $item = PortfolioAchievement::findOrFail($id);
            $this->safeDeleteFile($item->image);
            $item->delete();

            $this->clearCmsCache(['component_achievements']);
            return redirect()->route('admin.cms.portfolio-achievement')->with('success', 'Achievement berhasil dihapus!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.cms.portfolio-achievement')->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Portfolio delete failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    // Tim Kami
    public function timKamiIndex()
    {
        $this->authorizeAdminOnly();
        $timKami = TimKami::orderBy('order')->get();

        return view('admin.manage-content.tim-kami', compact('timKami'));
    }

    public function timKamiStore(Request $request)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'bio' => 'nullable|string|max:500',
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0|max:999',
        ]);

        try {
            $validated['is_active'] = $request->boolean('is_active');
            $validated['order'] = $validated['order'] ?? TimKami::max('order') + 1;

            if ($request->hasFile('photo')) {
                $validated['photo'] = $this->safeStoreFile($request->file('photo'), 'team');
            }

            TimKami::create($validated);

            $this->clearCmsCache(['component_team']);
            return redirect()->route('admin.cms.tim-kami')->with('success', 'Anggota tim berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            Log::error('Tim Kami store failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function timKamiUpdate(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'bio' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0|max:999',
        ]);

        try {
            $member = TimKami::findOrFail($id);

            if ($request->hasFile('photo')) {
                $this->safeDeleteFile($member->photo);
                $member->photo = $this->safeStoreFile($request->file('photo'), 'team');
            }

            // Remove photo from validated to prevent overwriting with temp path
            unset($validated['photo']);
            $member->fill($validated);
            $member->is_active = $request->boolean('is_active');
            $member->save();

            $this->clearCmsCache(['component_team']);
            return redirect()->route('admin.cms.tim-kami')->with('success', 'Anggota tim berhasil diperbarui!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.cms.tim-kami')->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Tim Kami update failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function timKamiDestroy($id)
    {
        $this->authorizeAdminOnly();
        
        try {
            $member = TimKami::findOrFail($id);
            $this->safeDeleteFile($member->photo);
            $member->delete();

            $this->clearCmsCache(['component_team']);
            return redirect()->route('admin.cms.tim-kami')->with('success', 'Anggota tim berhasil dihapus!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.cms.tim-kami')->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Tim Kami delete failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    // Testimoni Pelanggan
    public function testimoniPelangganIndex()
    {
        $this->authorizeAdminOnly();
        $testimonis = TestimoniPelanggan::orderBy('order')->get();

        return view('admin.manage-content.testimoni-pelanggan', compact('testimonis'));
    }

    public function testimoniPelangganStore(Request $request)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'customer_role' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:100',
            'testimonial' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:15360',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:15360',
            'order' => 'nullable|integer|min:0|max:999',
        ]);

        try {
            $validated['is_active'] = $request->boolean('is_active');
            $validated['order'] = $validated['order'] ?? TestimoniPelanggan::max('order') + 1;

            // Handle photo upload (check both 'photo' and 'image' fields)
            if ($request->hasFile('photo')) {
                $validated['photo'] = $this->safeStoreFile($request->file('photo'), 'testimoni');
            } elseif ($request->hasFile('image')) {
                $validated['photo'] = $this->safeStoreFile($request->file('image'), 'testimoni');
            }

            // Use name as fallback for customer_name
            if (empty($validated['customer_name']) && !empty($validated['name'])) {
                $validated['customer_name'] = $validated['name'];
            }
            
            // Use role as fallback for customer_role
            if (empty($validated['customer_role']) && !empty($validated['role'])) {
                $validated['customer_role'] = $validated['role'];
            }

            TestimoniPelanggan::create($validated);

            $this->clearCmsCache(['component_testimonials']);
            return redirect()->route('admin.cms.testimoni-pelanggan')->with('success', 'Testimoni pelanggan berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            Log::error('Testimoni store failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function testimoniPelangganUpdate(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'customer_role' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:100',
            'testimonial' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:15360',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:15360',
            'order' => 'nullable|integer|min:0|max:999',
        ]);

        try {
            $testimoni = TestimoniPelanggan::findOrFail($id);

            // Handle photo upload (check both 'photo' and 'image' fields)
            if ($request->hasFile('photo')) {
                $this->safeDeleteFile($testimoni->photo);
                $testimoni->photo = $this->safeStoreFile($request->file('photo'), 'testimoni');
            } elseif ($request->hasFile('image')) {
                $this->safeDeleteFile($testimoni->photo);
                $testimoni->photo = $this->safeStoreFile($request->file('image'), 'testimoni');
            }

            // Use name as fallback for customer_name
            if (empty($validated['customer_name']) && !empty($validated['name'])) {
                $validated['customer_name'] = $validated['name'];
            }
            
            // Use role as fallback for customer_role
            if (empty($validated['customer_role']) && !empty($validated['role'])) {
                $validated['customer_role'] = $validated['role'];
            }

            // Remove photo from validated to prevent overwriting with temp path
            unset($validated['photo']);
            $testimoni->fill($validated);
            $testimoni->is_active = $request->boolean('is_active');
            $testimoni->save();

            $this->clearCmsCache(['component_testimonials']);
            return redirect()->route('admin.cms.testimoni-pelanggan')->with('success', 'Testimoni pelanggan berhasil diperbarui!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.cms.testimoni-pelanggan')->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Testimoni update failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function testimoniPelangganDestroy($id)
    {
        $this->authorizeAdminOnly();
        
        try {
            $testimoni = TestimoniPelanggan::findOrFail($id);
            $this->safeDeleteFile($testimoni->photo);
            $testimoni->delete();

            $this->clearCmsCache(['component_testimonials']);
            return redirect()->route('admin.cms.testimoni-pelanggan')->with('success', 'Testimoni pelanggan berhasil dihapus!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.cms.testimoni-pelanggan')->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Testimoni delete failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    // Event
    public function eventIndex()
    {
        $this->authorizeAdminOnly();
        $events = Event::orderBy('order')->get();

        return view('admin.manage-content.event', compact('events'));
    }

    public function eventStore(Request $request)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'event_title' => 'required|string|max:150',
            'event_description' => 'nullable|string|max:2000',
            'category' => 'nullable|string|max:50',
            'event_date' => 'nullable|date',
            'link_url' => 'nullable|url|max:255',
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'order' => 'nullable|integer|min:0|max:999',
        ]);

        try {
            $validated['is_active'] = $request->boolean('is_active');
            $validated['order'] = $validated['order'] ?? Event::max('order') + 1;

            if ($request->hasFile('image')) {
                $validated['image'] = $this->safeStoreFile($request->file('image'), 'events');
            }

            Event::create($validated);

            $this->clearCmsCache(['component_events']);
            return redirect()->route('admin.cms.event')->with('success', 'Event berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            Log::error('Event store failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function eventUpdate(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'event_title' => 'required|string|max:150',
            'event_description' => 'nullable|string|max:2000',
            'category' => 'nullable|string|max:50',
            'event_date' => 'nullable|date',
            'link_url' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'order' => 'nullable|integer|min:0|max:999',
        ]);

        try {
            $event = Event::findOrFail($id);

            if ($request->hasFile('image')) {
                $this->safeDeleteFile($event->image);
                $event->image = $this->safeStoreFile($request->file('image'), 'events');
            }

            // Remove image from validated to prevent overwriting with temp path
            unset($validated['image']);
            $event->fill($validated);
            $event->is_active = $request->boolean('is_active');
            $event->save();

            $this->clearCmsCache(['component_events']);
            return redirect()->route('admin.cms.event')->with('success', 'Event berhasil diperbarui!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.cms.event')->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Event update failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function eventDestroy($id)
    {
        $this->authorizeAdminOnly();
        
        try {
            $event = Event::findOrFail($id);
            $this->safeDeleteFile($event->image);
            $event->delete();

            $this->clearCmsCache(['component_events']);
            return redirect()->route('admin.cms.event')->with('success', 'Event berhasil dihapus!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.cms.event')->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Event delete failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    // Footer
    public function footerIndex()
    {
        $this->authorizeAdminOnly();
        $footer = Footer::first();

        return view('admin.manage-content.footer', compact('footer'));
    }

    public function footerUpdate(Request $request)
    {
        $this->authorizeAdminOnly();
        
        $validated = $request->validate([
            'about_text' => ['nullable', 'string', 'max:1000'],
            'facebook_url' => ['nullable', 'url:http,https', 'max:500'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:500'],
            'twitter_url' => ['nullable', 'url:http,https', 'max:500'],
            'youtube_url' => ['nullable', 'url:http,https', 'max:500'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'location_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'google_maps_url' => ['nullable', 'string', 'max:5000'],
            'map_url' => ['nullable', 'url:http,https', 'max:2000'],
            'monday_friday_hours' => ['nullable', 'string', 'max:100'],
            'saturday_sunday_hours' => ['nullable', 'string', 'max:100'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'copyright' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $footer = Footer::firstOrNew();
            
            // Normalize WhatsApp link
            $validated['whatsapp'] = $this->normalizeWhatsAppLink($request->input('whatsapp'));
            
            $footer->fill($validated);
            $footer->is_active = $request->boolean('is_active');
            $footer->save();

            $this->clearCmsCache(['component_footer']);
            return redirect()->route('admin.cms.footer')->with('success', 'Footer berhasil diperbarui!');
            
        } catch (\Exception $e) {
            Log::error('Footer update failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    // Contact
    public function contactIndex()
    {
        $this->authorizeAdminOnly();
        $contact = Contact::first();

        return view('admin.manage-content.contact', compact('contact'));
    }

    public function contactUpdate(Request $request)
    {
        $this->authorizeAdminOnly();

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'google_maps_url' => ['nullable', 'url', 'max:500'],
            'map_url' => ['nullable', 'url', 'max:500'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'navbar_label' => ['nullable', 'string', 'max:100'],
            'navbar_link' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $contact = Contact::firstOrNew();

            // Normalize WhatsApp link
            $validated['whatsapp'] = $this->normalizeWhatsAppLink($request->input('whatsapp'));
            
            // Normalize navbar_link (could be WhatsApp or URL)
            $navbarInput = $request->input('navbar_link');
            if ($navbarInput && trim($navbarInput) !== '') {
                $nav = trim($navbarInput);
                if (preg_match('#^https?://#i', $nav)) {
                    $validated['navbar_link'] = preg_replace('#^http://#i', 'https://', $nav);
                } else {
                    // Treat as phone number
                    $validated['navbar_link'] = $this->normalizeWhatsAppLink($nav);
                }
            }

            $contact->fill($validated);
            $contact->is_active = $request->boolean('is_active');
            $contact->save();

            // Also clear navbar_contact since navbar uses contact info
            $this->clearCmsCache(['component_contact', 'navbar_contact']);
            return redirect()->route('admin.cms.contact')->with('success', 'Halaman kontak berhasil diperbarui!');
            
        } catch (\Exception $e) {
            Log::error('Contact update failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    // Profile Management
    public function profileEdit()
    {
        $user = auth()->user();

        return view('admin.profile', compact('user'));
    }

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $user->update($validated);

        return redirect()->route('admin.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function profileColorUpdate(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'primary_color' => ['required', 'string', 'in:#fbbf24,#fa9a08,#2f7d7a'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.profile.edit')->with('success', 'Warna preferensi berhasil diperbarui.');
    }

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

        return redirect()->route('admin.profile.edit')->with('success', 'Password berhasil diperbarui.');
    }
}
