<?php

namespace Database\Seeders;

use App\Models\AboutFounder;
use App\Models\Event;
use App\Models\Footer;
use App\Models\HeroSection;
use App\Models\PortfolioAchievement;
use App\Models\TentangKami;
use App\Models\TestimoniPelanggan;
use App\Models\TimKami;
use Illuminate\Database\Seeder;

class PopulateCMSContentSeeder extends Seeder
{
    public function run(): void
    {
        // Hero Section
        HeroSection::create([
            'title' => 'The Art of',
            'subtitle' => 'Precision',
            'tagline' => 'Premium Billiard Lounge & Bar',
            'cta_text_1' => 'BOOK A TABLE',
            'cta_text_2' => 'EXPLORE',
            'logo_image' => null,
            'background_image' => null,
            'is_active' => true,
        ]);

        // Tentang Kami
        TentangKami::create([
            'title' => 'Tentang CLASS BILLIARD',
            'subtitle' => 'Pengalaman Bermain Billiard Kelas Dunia',
            'visi' => 'Menjadi tempat hiburan billiard terdepan dengan standar internasional',
            'misi' => 'Memberikan pengalaman bermain billiard berkualitas tinggi untuk semua kalangan',
            'arah_gerak' => 'Terus berinovasi dalam layanan dan fasilitas',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_description' => 'Presentasi singkat tentang CLASS BILLIARD',
            'image' => null,
            'is_active' => true,
        ]);

        // About Founder
        AboutFounder::create([
            'title' => 'Pendiri CLASS BILLIARD',
            'subtitle' => 'Visioner Industri Billiard',
            'name' => 'Founder Name',
            'position' => 'CEO & Founder',
            'quote' => 'Billiard bukan hanya permainan, tetapi seni dan passion.',
            'signature' => 'Founder Signature',
            'photo' => null,
            'image' => null,
            'facebook_url' => '#',
            'instagram_url' => '#',
            'linkedin_url' => '#',
            'is_active' => true,
        ]);

        // Portfolio Achievement
        PortfolioAchievement::create([
            'title' => 'Prestasi Kami',
            'type' => 'gallery',
            'description' => 'Pencapaian dan galeri kami',
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        // Tim Kami
        TimKami::create([
            'title' => 'Tim Profesional',
            'subtitle' => 'Instruktur Bersertifikat',
            'name' => 'Tim Member',
            'position' => 'Instruktur Billiard',
            'bio' => 'Instruktur berpengalaman dengan sertifikat internasional',
            'photo' => null,
            'image' => null,
            'facebook_url' => '#',
            'instagram_url' => '#',
            'linkedin_url' => '#',
            'order' => 1,
            'is_active' => true,
        ]);

        // Testimoni
        TestimoniPelanggan::create([
            'title' => 'Testimoni Pelanggan',
            'subtitle' => 'Kepuasan Pelanggan adalah Prioritas',
            'customer_name' => 'Pelanggan Kami',
            'customer_role' => 'Regular Customer',
            'name' => 'Nama Pelanggan',
            'role' => 'Customer',
            'testimonial' => 'Tempat yang bagus dengan fasilitas lengkap dan layanan memuaskan!',
            'rating' => 5,
            'photo' => null,
            'image' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        // Event
        Event::create([
            'title' => 'Event & Promo',
            'subtitle' => 'Acara Menarik Setiap Bulan',
            'event_title' => 'Grand Opening Tournament',
            'event_description' => 'Turnamen billiard terbesar dengan hadiah menarik',
            'description' => 'Acara grand opening dengan turnamen dan hiburan',
            'category' => 'tournament',
            'event_date' => now()->addMonth(),
            'image' => null,
            'link_url' => '#',
            'order' => 1,
            'is_active' => true,
        ]);

        // Footer
        Footer::create([
            'about_text' => 'CLASS BILLIARD adalah tempat billiard premium dengan fasilitas lengkap.',
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'twitter_url' => 'https://twitter.com',
            'youtube_url' => 'https://youtube.com',
            'whatsapp' => '+62812345678',
            'address' => 'Jl. Example No. 123',
            'location_name' => 'CLASS BILLIARD',
            'phone' => '+62212345678',
            'email' => 'info@classbilliard.com',
            'google_maps_url' => 'https://maps.google.com',
            'map_url' => 'https://maps.google.com',
            'monday_friday_hours' => '10:00 - 22:00',
            'saturday_sunday_hours' => '10:00 - 23:00',
            'opening_hours' => 'Senin - Minggu: 10:00 - 23:00',
            'copyright' => '© 2024 CLASS BILLIARD. All rights reserved.',
            'is_active' => true,
        ]);

        $this->command->info('✅ CMS content berhasil dipopulasi!');
    }
}
