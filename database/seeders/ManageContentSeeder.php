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
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ManageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hero Section
        HeroSection::create([
            'title' => 'The Art of',
            'subtitle' => 'Precision',
            'tagline' => 'Premium Billiard Lounge & Bar',
            'cta_text_1' => 'BOOK A TABLE',
            'cta_text_2' => 'EXPLORE',
            'is_active' => true,
        ]);

        // Tentang Kami
        TentangKami::create([
            'title' => 'ABOUT',
            'subtitle' => "Welcome to Class Billiard, where precision meets luxury. We redefine the pool experience with world-class tables, an exclusive atmosphere, and a community of passionate players. Whether you're a seasoned pro or a casual enthusiast, our lounge offers the perfect setting to refine your skills.\n\nEstablished with a vision to elevate the sport, we host regular tournaments and provide top-tier equipment. Join us and experience the gold standard of billiards.",
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_description' => 'Experience the excellence of Class Billiard through our video showcase.',
            'is_active' => true,
        ]);

        // About Founder
        AboutFounder::create([
            'title' => 'Meet Our Visionary',
            'subtitle' => "Crafting the\nPerfect Game",
            'name' => 'Lorem Ipsum',
            'position' => 'Founder & CEO',
            'quote' => 'Billiards is not just a game; it is an art of precision, patience, and strategy. My vision was to create a sanctuary where every shot resonates with luxury and every player feels like a champion.',
            'signature' => 'L.Ipsum',
            'facebook_url' => 'https://facebook.com/classbilliard',
            'instagram_url' => 'https://instagram.com/class_billiard',
            'linkedin_url' => 'https://linkedin.com/in/classbilliard',
            'is_active' => true,
        ]);

        // Portfolio Achievements (Gallery Items)
        PortfolioAchievement::create([
            'title' => 'WINNER',
            'description' => 'Regional Championship',
            'type' => 'gallery',
            'order' => 1,
            'is_active' => true,
        ]);

        PortfolioAchievement::create([
            'title' => 'SEMIFINALIST',
            'description' => 'National Open',
            'type' => 'gallery',
            'order' => 2,
            'is_active' => true,
        ]);

        PortfolioAchievement::create([
            'title' => 'CHAMPION',
            'description' => 'Grand Tournament 2024',
            'type' => 'gallery',
            'order' => 3,
            'is_active' => true,
        ]);

        PortfolioAchievement::create([
            'title' => 'RUNNER UP',
            'description' => 'City League',
            'type' => 'gallery',
            'order' => 4,
            'is_active' => true,
        ]);

        PortfolioAchievement::create([
            'title' => '2ND PLACE',
            'description' => "Mayor's Cup",
            'type' => 'gallery',
            'order' => 5,
            'is_active' => true,
        ]);

        // Tim Kami
        TimKami::create([
            'name' => 'Sarah Jenkins',
            'position' => 'Senior Instructor',
            'bio' => 'Former National Champion with over 10 years of coaching experience. Specializes in tactical gameplay.',
            'facebook_url' => 'https://facebook.com/sarahjenkins',
            'instagram_url' => 'https://instagram.com/sarahjenkins',
            'linkedin_url' => 'https://linkedin.com/in/sarahjenkins',
            'order' => 1,
            'is_active' => true,
        ]);

        TimKami::create([
            'name' => 'David Chen',
            'position' => 'Head Pro',
            'bio' => '3-time World Pool Masters winner. The technical genius behind our training curriculum.',
            'facebook_url' => 'https://facebook.com/davidchen',
            'instagram_url' => 'https://instagram.com/davidchen',
            'linkedin_url' => 'https://linkedin.com/in/davidchen',
            'order' => 2,
            'is_active' => true,
        ]);

        TimKami::create([
            'name' => 'Michael Ross',
            'position' => 'Trick Shot Artist',
            'bio' => 'Famous for his viral trick shots and exhibition matches. Brings creativity to the table.',
            'facebook_url' => 'https://facebook.com/michaelross',
            'instagram_url' => 'https://instagram.com/michaelross',
            'linkedin_url' => 'https://linkedin.com/in/michaelross',
            'order' => 3,
            'is_active' => true,
        ]);

        // Testimoni Pelanggan
        TestimoniPelanggan::create([
            'customer_name' => 'James Peterson',
            'name' => 'James Peterson',
            'customer_role' => 'Pro Player',
            'role' => 'Pro Player',
            'testimonial' => 'The tables here are absolutely world-class. The cloth speed is consistent, and the pockets are cut to professional standards. Best place to practice for upcoming tournaments.',
            'rating' => 5,
            'order' => 1,
            'is_active' => true,
        ]);

        TestimoniPelanggan::create([
            'customer_name' => 'Sarah Mitchell',
            'name' => 'Sarah Mitchell',
            'customer_role' => 'League Captain',
            'role' => 'League Captain',
            'testimonial' => 'Amazing atmosphere and top-tier service. The VIP room is stunning and perfect for private events. I\'ve never seen a billiard club with this level of luxury and attention to detail.',
            'rating' => 5,
            'order' => 2,
            'is_active' => true,
        ]);

        TestimoniPelanggan::create([
            'customer_name' => 'David Chen',
            'name' => 'David Chen',
            'customer_role' => 'Regular Member',
            'role' => 'Regular Member',
            'testimonial' => 'I improved my game significantly thanks to the coaching clinic. The instructors are patient and extremely knowledgeable. Highly recommended for beginners and pros alike.',
            'rating' => 5,
            'order' => 3,
            'is_active' => true,
        ]);

        TestimoniPelanggan::create([
            'customer_name' => 'Robert Fox',
            'name' => 'Robert Fox',
            'customer_role' => 'Tournament Director',
            'role' => 'Tournament Director',
            'testimonial' => 'The gold standard of billiard clubs in the city. The organization of their leagues and tournaments is flawless. It\'s always a pleasure to compete here.',
            'rating' => 5,
            'order' => 4,
            'is_active' => true,
        ]);

        // Events
        Event::create([
            'event_title' => 'Golden Cup 2024',
            'title' => 'Golden Cup 2024',
            'description' => 'The most prestigious 9-ball tournament of the year. Grand prize $10,000.',
            'event_description' => 'The most prestigious 9-ball tournament of the year. Grand prize $10,000.',
            'category' => 'Tournament',
            'event_date' => Carbon::now()->addDays(30),
            'order' => 1,
            'is_active' => true,
        ]);

        Event::create([
            'event_title' => "New Year's Eve",
            'title' => "New Year's Eve",
            'description' => 'Celebrate the new year with free-flow drinks and midnight countdown.',
            'event_description' => 'Celebrate the new year with free-flow drinks and midnight countdown.',
            'category' => 'Social',
            'event_date' => Carbon::now()->addDays(60),
            'order' => 2,
            'is_active' => true,
        ]);

        Event::create([
            'event_title' => 'Pro Coaching Clinic',
            'title' => 'Pro Coaching Clinic',
            'description' => 'Learn from the best. Exclusive session with our founder and pro players.',
            'event_description' => 'Learn from the best. Exclusive session with our founder and pro players.',
            'category' => 'Masterclass',
            'event_date' => Carbon::now()->addDays(75),
            'order' => 3,
            'is_active' => true,
        ]);

        Event::create([
            'event_title' => 'City League Opening',
            'title' => 'City League Opening',
            'description' => 'The start of the new city league season. Come and support your local team.',
            'event_description' => 'The start of the new city league season. Come and support your local team.',
            'category' => 'League',
            'event_date' => Carbon::now()->addDays(90),
            'order' => 4,
            'is_active' => true,
        ]);

        // Footer
        Footer::create([
            'about_text' => 'Class Billiard - Premium Billiard Lounge & Bar',
            'address' => '123 Premium Street, Jakarta, ID',
            'location_name' => 'Class Billiard Main Hall',
            'phone' => '+62 812 3456 7890',
            'email' => 'info@classbilliard.com',
            'opening_hours' => 'Mon - Sun: 10AM - 02AM',
            'google_maps_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.9537353153169!3d-37.8172099797517!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d4c2b349649%3A0xb6899234e561db11!2sEnvato!5e0!3m2!1sen!2sus!4v1641364582314!5m2!1sen!2sus',
            'map_url' => 'https://maps.app.goo.gl/example',
            'facebook_url' => 'https://facebook.com/classbilliard',
            'instagram_url' => 'https://instagram.com/class_billiard',
            'whatsapp' => 'https://web.whatsapp.com/send?phone=6281234567890',
            'copyright' => '© 2024 CLASS BILLIARD. ALL RIGHTS RESERVED.',
            'is_active' => true,
        ]);

        $this->command->info('✅ Manage Content seeder berhasil dijalankan!');
        $this->command->info('   - Hero Section: 1 data');
        $this->command->info('   - Tentang Kami: 1 data');
        $this->command->info('   - About Founder: 1 data');
        $this->command->info('   - Portfolio Achievements: 5 data');
        $this->command->info('   - Tim Kami: 3 data');
        $this->command->info('   - Testimoni Pelanggan: 4 data');
        $this->command->info('   - Events: 4 data');
        $this->command->info('   - Footer: 1 data');
    }
}
