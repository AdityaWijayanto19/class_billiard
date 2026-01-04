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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
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
        $hero = HeroSection::firstOrNew();

        // Validate new CTA link input
        $request->validate([
            'cta_link_1' => ['nullable', 'string', 'max:255'],
        ]);

        // Only update logo_image if new file uploaded
        if ($request->hasFile('logo_image')) {
            // Delete old image if exists
            if ($hero->logo_image && Storage::disk('public')->exists($hero->logo_image)) {
                Storage::disk('public')->delete($hero->logo_image);
            }
            $hero->logo_image = $request->file('logo_image')->store('hero', 'public');
        }

        // Only update background_image if new file uploaded
        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($hero->background_image && Storage::disk('public')->exists($hero->background_image)) {
                Storage::disk('public')->delete($hero->background_image);
            }
            $hero->background_image = $request->file('background_image')->store('hero', 'public');
        }

        // Update text fields
        $hero->title = $request->title ?? 'The Art of';
        $hero->subtitle = $request->subtitle ?? 'Precision';
        $hero->tagline = $request->tagline ?? 'Premium Billiard Lounge & Bar';
        $hero->cta_text_1 = $request->cta_text_1 ?? 'BOOK A TABLE';
        
        // Normalize CTA link 1 if provided (allow plain numbers -> wa.me)
        $ctaLinkInput = $request->input('cta_link_1');
        if ($ctaLinkInput && trim($ctaLinkInput) !== '') {
            $link = trim($ctaLinkInput);
            if (preg_match('#^https?://#i', $link)) {
                $link = preg_replace('#^http://#i', 'https://', $link);
            } else {
                $digits = preg_replace('/\D+/', '', $link);
                if (preg_match('/^0/', $digits)) {
                    $digits = preg_replace('/^0+/', '', $digits);
                    $digits = '62'.$digits;
                }
                if ($digits !== '') {
                    $link = 'https://wa.me/'.$digits;
                }
            }
            $hero->cta_link_1 = $link;
        } else {
            if ($request->has('cta_link_1') && ($request->input('cta_link_1') === null || trim($request->input('cta_link_1')) === '')) {
                $hero->cta_link_1 = null;
            }
        }
        
        $hero->cta_text_2 = $request->cta_text_2 ?? 'EXPLORE';
        $hero->is_active = $request->has('is_active') && $request->input('is_active') ? true : false;
        $hero->save();

        return redirect()->route('admin.cms.hero')->with('success', 'Hero section updated successfully');
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
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'arah_gerak' => 'nullable|string',
            'is_active' => 'nullable',
            'video_url' => 'nullable|string|max:2048',
            'video_description' => 'nullable|string',
        ]);

        $tentangKami = TentangKami::firstOrNew();

        if ($request->hasFile('image')) {
            if ($tentangKami->image) {
                Storage::disk('public')->delete($tentangKami->image);
            }
            $tentangKami->image = $request->file('image')->store('tentang-kami', 'public');
        }

        $tentangKami->fill($request->only(['title', 'subtitle']));

        if ($request->filled('video_url')) {
            $embed = $this->convertToEmbedUrl($request->input('video_url'));
            // hapus file lama jika sebelumnya pakai storage lokal
            if ($tentangKami->video_url && ! preg_match('/^https?:\/\//', $tentangKami->video_url)) {
                Storage::disk('public')->delete($tentangKami->video_url);
            }
            $tentangKami->video_url = $embed;
        }

        if ($request->has('video_description')) {
            $tentangKami->video_description = $request->input('video_description');
        }

        $tentangKami->is_active = $request->has('is_active');
        $tentangKami->save();

        return redirect()->route('admin.cms.tentang-kami')->with('success', 'Tentang Kami updated successfully');
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
        $aboutFounder = AboutFounder::firstOrNew();

        if ($request->hasFile('photo')) {
            if ($aboutFounder->photo) {
                Storage::disk('public')->delete($aboutFounder->photo);
            }
            $aboutFounder->photo = $request->file('photo')->store('founder', 'public');
        }

        if ($request->hasFile('image')) {
            if ($aboutFounder->image) {
                Storage::disk('public')->delete($aboutFounder->image);
            }
            $aboutFounder->image = $request->file('image')->store('founder', 'public');
        }

        $aboutFounder->fill($request->only(['title', 'subtitle', 'name', 'position', 'quote', 'signature', 'facebook_url', 'instagram_url', 'linkedin_url']));
        $aboutFounder->is_active = $request->has('is_active');
        $aboutFounder->save();

        return redirect()->route('admin.cms.about-founder')->with('success', 'About Founder updated successfully');
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
        $data = $request->only(['title', 'subtitle', 'icon', 'name', 'description', 'order']);
        $data['is_active'] = $request->has('is_active');
        KeunggulanFasilitas::create($data);

        return redirect()->route('admin.cms.keunggulan-fasilitas')->with('success', 'Fasilitas added successfully');
    }

    public function keunggulanFasilitasUpdate(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        $fasilitas = KeunggulanFasilitas::findOrFail($id);
        $fasilitas->fill($request->only(['title', 'subtitle', 'icon', 'name', 'description', 'order']));
        $fasilitas->is_active = $request->has('is_active');
        $fasilitas->save();

        return redirect()->route('admin.cms.keunggulan-fasilitas')->with('success', 'Fasilitas updated successfully');
    }

    public function keunggulanFasilitasDestroy($id)
    {
        $this->authorizeAdminOnly();
        KeunggulanFasilitas::findOrFail($id)->delete();

        return redirect()->route('admin.cms.keunggulan-fasilitas')->with('success', 'Fasilitas deleted successfully');
    }

    // Portfolio Achievement
    public function portfolioAchievementIndex()
    {
        $this->authorizeAdminOnly();
        // Semua achievements ditampilkan di dashboard tanpa filter type
        $allAchievements = PortfolioAchievement::orderBy('order')->get();

        return view('admin.manage-content.portfolio-achievement', compact('allAchievements'));
    }

    public function portfolioAchievementStore(Request $request)
    {
        $this->authorizeAdminOnly();
        $data = $request->only(['title', 'description', 'order', 'type', 'subtitle', 'icon', 'number', 'label']);
        $data['is_active'] = $request->has('is_active');

        // Set type ke gallery karena dashboard hanya menampilkan items dengan image
        $data['type'] = 'gallery';

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('portfolio', 'public');
        }

        PortfolioAchievement::create($data);

        return redirect()->route('admin.cms.portfolio-achievement')->with('success', 'Item added successfully');
    }

    public function portfolioAchievementUpdate(Request $request, $id)
    {
        $item = PortfolioAchievement::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $item->image = $request->file('image')->store('portfolio', 'public');
        }

        $data = $request->only(['title', 'description', 'order', 'type', 'subtitle', 'icon', 'number', 'label']);

        // Pastikan type tetap gallery
        $data['type'] = 'gallery';

        $item->fill($data);
        $item->is_active = $request->has('is_active');
        $item->save();

        return redirect()->route('admin.cms.portfolio-achievement')->with('success', 'Item updated successfully');
    }

    public function portfolioAchievementDestroy($id)
    {
        $item = PortfolioAchievement::findOrFail($id);
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();

        return redirect()->route('admin.cms.portfolio-achievement')->with('success', 'Item deleted successfully');
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
        $data = $request->only(['title', 'subtitle', 'name', 'position', 'bio', 'facebook_url', 'instagram_url', 'linkedin_url', 'order']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('team', 'public');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('team', 'public');
        }

        TimKami::create($data);

        return redirect()->route('admin.cms.tim-kami')->with('success', 'Team member added successfully');
    }

    public function timKamiUpdate(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        $member = TimKami::findOrFail($id);

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $member->photo = $request->file('photo')->store('team', 'public');
        }

        if ($request->hasFile('image')) {
            if ($member->image) {
                Storage::disk('public')->delete($member->image);
            }
            $member->image = $request->file('image')->store('team', 'public');
        }

        $member->fill($request->only(['title', 'subtitle', 'name', 'position', 'bio', 'facebook_url', 'instagram_url', 'linkedin_url', 'order']));
        $member->is_active = $request->has('is_active');
        $member->save();

        return redirect()->route('admin.cms.tim-kami')->with('success', 'Team member updated successfully');
    }

    public function timKamiDestroy($id)
    {
        $this->authorizeAdminOnly();
        $member = TimKami::findOrFail($id);
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        return redirect()->route('admin.cms.tim-kami')->with('success', 'Team member deleted successfully');
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
        $data = $request->only(['title', 'subtitle', 'customer_name', 'name', 'customer_role', 'role', 'testimonial', 'rating', 'order']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('testimoni', 'public');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('testimoni', 'public');
        }

        TestimoniPelanggan::create($data);

        return redirect()->route('admin.cms.testimoni-pelanggan')->with('success', 'Testimoni added successfully');
    }

    public function testimoniPelangganUpdate(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        $testimoni = TestimoniPelanggan::findOrFail($id);

        if ($request->hasFile('photo')) {
            if ($testimoni->photo) {
                Storage::disk('public')->delete($testimoni->photo);
            }
            $testimoni->photo = $request->file('photo')->store('testimoni', 'public');
        }

        if ($request->hasFile('image')) {
            if ($testimoni->image) {
                Storage::disk('public')->delete($testimoni->image);
            }
            $testimoni->image = $request->file('image')->store('testimoni', 'public');
        }

        $testimoni->fill($request->only(['title', 'subtitle', 'customer_name', 'name', 'customer_role', 'role', 'testimonial', 'rating', 'order']));
        $testimoni->is_active = $request->has('is_active');
        $testimoni->save();

        return redirect()->route('admin.cms.testimoni-pelanggan')->with('success', 'Testimoni updated successfully');
    }

    public function testimoniPelangganDestroy($id)
    {
        $this->authorizeAdminOnly();
        $testimoni = TestimoniPelanggan::findOrFail($id);
        if ($testimoni->photo) {
            Storage::disk('public')->delete($testimoni->photo);
        }
        $testimoni->delete();

        return redirect()->route('admin.cms.testimoni-pelanggan')->with('success', 'Testimoni deleted successfully');
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
        $data = $request->only(['title', 'subtitle', 'event_title', 'event_description', 'description', 'category', 'event_date', 'link_url', 'order']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($data);

        return redirect()->route('admin.cms.event')->with('success', 'Event added successfully');
    }

    public function eventUpdate(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        $event = Event::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $event->image = $request->file('image')->store('events', 'public');
        }

        $event->fill($request->only(['title', 'subtitle', 'event_title', 'event_description', 'description', 'category', 'event_date', 'link_url', 'order']));
        $event->is_active = $request->has('is_active');
        $event->save();

        return redirect()->route('admin.cms.event')->with('success', 'Event updated successfully');
    }

    public function eventDestroy($id)
    {
        $this->authorizeAdminOnly();
        $event = Event::findOrFail($id);
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        $event->delete();

        return redirect()->route('admin.cms.event')->with('success', 'Event deleted successfully');
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
        $footer = Footer::firstOrNew();
        $footer->fill($request->only([
            'about_text',
            'facebook_url',
            'instagram_url',
            'twitter_url',
            'youtube_url',
            'whatsapp',
            'address',
            'location_name',
            'phone',
            'email',
            'google_maps_url',
            'map_url',
            'monday_friday_hours',
            'saturday_sunday_hours',
            'opening_hours',
            'copyright',
        ]));
        $footer->is_active = $request->has('is_active');
        $footer->save();

        return redirect()->route('admin.cms.footer')->with('success', 'Footer updated successfully');
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

        // Basic validation (don't block save for other fields)
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'google_maps_url' => ['nullable', 'string'],
            'map_url' => ['nullable', 'string'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'twitter_url' => ['nullable', 'string', 'max:255'],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'navbar_label' => ['nullable', 'string', 'max:100'],
            'navbar_link' => ['nullable', 'string', 'max:255'],
        ]);

        $contact = Contact::firstOrNew();

        // Normalize WhatsApp input to a canonical URL if provided
        $whatsappInput = $request->input('whatsapp');
        $whatsappUrl = null;
        if ($whatsappInput && trim($whatsappInput) !== '') {
            $wp = trim($whatsappInput);

            // If a URL is provided, normalize scheme to https
            if (preg_match('#^https?://#i', $wp)) {
                $whatsappUrl = preg_replace('#^http://#i', 'https://', $wp);
            } else {
                // Strip non-digit characters
                $digits = preg_replace('/\D+/', '', $wp);
                // If starts with 0, assume Indonesian number and replace leading 0 with 62
                if (preg_match('/^0/', $digits)) {
                    $digits = preg_replace('/^0+/', '', $digits);
                    $digits = '62'.$digits;
                }
                // If starts with country code like +62 or 62 already, ensure no leading +
                if (preg_match('/^\+/', $wp)) {
                    $digits = ltrim($digits, '+');
                }

                if ($digits !== '') {
                    $whatsappUrl = 'https://wa.me/'.$digits;
                }
            }
        }

        // Fill other fields
        $contact->fill($request->only([
            'title',
            'subtitle',
            'description',
            'location_name',
            'address',
            'phone',
            'email',
            'navbar_label',
            'navbar_link',
            'google_maps_url',
            'map_url',
            'opening_hours',
            'facebook_url',
            'instagram_url',
            'twitter_url',
            'youtube_url',
        ]));

        // Apply whatsapp normalized value (if any)
        if ($whatsappUrl !== null) {
            $contact->whatsapp = $whatsappUrl;
        } else {
            // If input blank, clear value
            if ($request->has('whatsapp') && ($request->input('whatsapp') === null || trim($request->input('whatsapp')) === '')) {
                $contact->whatsapp = null;
            }
        }

        // Normalize and apply navbar_link if provided (server-side canonicalization)
        $navbarInput = $request->input('navbar_link');
        if ($navbarInput && trim($navbarInput) !== '') {
            $nav = trim($navbarInput);
            if (preg_match('#^https?://#i', $nav)) {
                $nav = preg_replace('#^http://#i', 'https://', $nav);
            } else {
                // treat as phone/number-like, normalize to wa.me
                $digits = preg_replace('/\D+/', '', $nav);
                if (preg_match('/^0/', $digits)) {
                    $digits = preg_replace('/^0+/', '', $digits);
                    $digits = '62'.$digits;
                }
                if ($digits !== '') {
                    $nav = 'https://wa.me/'.$digits;
                }
            }
            $contact->navbar_link = $nav;
        } else {
            // if explicitly blank, clear navbar_link
            if ($request->has('navbar_link') && ($request->input('navbar_link') === null || trim($request->input('navbar_link')) === '')) {
                $contact->navbar_link = null;
            }
        }

        $contact->is_active = $request->has('is_active');
        $contact->save();

        return redirect()->route('admin.cms.contact')->with('success', 'Contact page updated successfully');
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
