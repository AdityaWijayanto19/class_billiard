<?php

namespace App\Http\Controllers;

use App\Models\AboutFounder;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Footer;
use App\Models\HeroSection;
use App\Models\PortfolioAchievement;
use App\Models\TentangKami;
use App\Models\TestimoniPelanggan;
use App\Models\TimKami;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        // Cache data yang tidak sering berubah (1 jam) untuk performa lebih baik
        $hero = cache()->remember('home_hero', 3600, function () {
            return HeroSection::select('id', 'background_image', 'logo_image', 'title', 'subtitle', 'tagline', 'cta_text_1', 'cta_link_1', 'cta_text_2')
                ->where('is_active', true)
                ->first();
        });

        $about = cache()->remember('home_about', 3600, function () {
            return TentangKami::select('id', 'title', 'subtitle', 'image', 'visi', 'misi', 'arah_gerak', 'video_url', 'video_description')
                ->where('is_active', true)
                ->first();
        });

        $founder = cache()->remember('home_founder', 3600, function () {
            return AboutFounder::select('id', 'title', 'subtitle', 'name', 'position', 'description', 'quote', 'signature', 'photo', 'image', 'video_url', 'facebook_url', 'instagram_url', 'linkedin_url')
                ->where('is_active', true)
                ->first();
        });

        // Data yang mungkin lebih sering berubah - cache 30 menit
        $achievements = cache()->remember('home_achievements', 1800, function () {
            return PortfolioAchievement::select('id', 'title', 'subtitle', 'type', 'icon', 'number', 'label', 'description', 'image', 'order')
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        });

        $teamMembers = cache()->remember('home_team', 1800, function () {
            return TimKami::select('id', 'title', 'subtitle', 'name', 'position', 'bio', 'photo', 'image', 'facebook_url', 'instagram_url', 'linkedin_url', 'order')
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        });

        $testimonials = cache()->remember('home_testimonials', 1800, function () {
            return TestimoniPelanggan::select('id', 'title', 'subtitle', 'customer_name', 'name', 'customer_role', 'role', 'testimonial', 'rating', 'photo', 'image', 'order')
                ->where('is_active', true)
                ->orderBy('order')
                ->limit(4)
                ->get();
        });

        $events = cache()->remember('home_events', 1800, function () {
            return Event::select('id', 'title', 'subtitle', 'event_title', 'event_description', 'description', 'category', 'event_date', 'image', 'link_url', 'order')
                ->where('is_active', true)
                ->orderBy('event_date')
                ->get();
        });

        $footer = cache()->remember('home_footer', 3600, function () {
            return Footer::select('id', 'about_text', 'facebook_url', 'instagram_url', 'twitter_url', 'youtube_url', 'whatsapp', 'address', 'location_name', 'phone', 'email', 'google_maps_url', 'map_url', 'monday_friday_hours', 'saturday_sunday_hours', 'opening_hours', 'copyright')
                ->where('is_active', true)
                ->first();
        });

        $contact = cache()->remember('home_contact', 3600, function () {
            return Contact::select('id', 'title', 'subtitle', 'description', 'location_name', 'address', 'phone', 'email', 'whatsapp', 'navbar_label', 'navbar_link', 'google_maps_url', 'map_url', 'opening_hours', 'facebook_url', 'instagram_url', 'twitter_url', 'youtube_url')
                ->where('is_active', true)
                ->first();
        });

        return view('home', compact(
            'hero',
            'about',
            'founder',
            'achievements',
            'teamMembers',
            'testimonials',
            'events',
            'footer',
            'contact'
        ));
    }

    public function submitTestimonial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'testimonial' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->to(route('home').'#testimonials')
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'customer_name' => $request->name,
            'name' => $request->name,
            'customer_role' => $request->role ?? 'Customer',
            'role' => $request->role ?? 'Customer',
            'rating' => $request->rating,
            'testimonial' => $request->testimonial,
            'is_active' => true, // Langsung aktif, bisa di-nonaktifkan atau dihapus oleh admin
            'order' => 0,
        ];

        TestimoniPelanggan::create($data);

        return redirect()->to(route('home').'#testimonials')
            ->with('testimonial_success', 'Terima kasih! Testimoni Anda telah berhasil dikirim dan akan ditampilkan di website.');
    }
}
