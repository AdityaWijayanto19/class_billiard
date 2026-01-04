<?php

namespace Database\Seeders;

use App\Models\AboutFounder;
use App\Models\Event;
use App\Models\Footer;
use App\Models\HeroSection;
use App\Models\KeunggulanFasilitas;
use App\Models\PortfolioAchievement;
use App\Models\TentangKami;
use App\Models\TestimoniPelanggan;
use App\Models\TimKami;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ClearManageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua data dari tabel Hero Sections
        $heroSections = HeroSection::all();
        foreach ($heroSections as $hero) {
            if ($hero->logo_image) {
                Storage::disk('public')->delete($hero->logo_image);
            }
            if ($hero->background_image) {
                Storage::disk('public')->delete($hero->background_image);
            }
        }
        HeroSection::truncate();
        $this->command->info('✓ Hero Sections cleared');

        // Hapus semua data dari tabel Tentang Kami
        $tentangKamis = TentangKami::all();
        foreach ($tentangKamis as $tentang) {
            if ($tentang->image) {
                Storage::disk('public')->delete($tentang->image);
            }
        }
        TentangKami::truncate();
        $this->command->info('✓ Tentang Kami cleared');

        // Hapus semua data dari tabel About Founders
        $aboutFounders = AboutFounder::all();
        foreach ($aboutFounders as $founder) {
            if ($founder->photo) {
                Storage::disk('public')->delete($founder->photo);
            }
            if ($founder->image) {
                Storage::disk('public')->delete($founder->image);
            }
        }
        AboutFounder::truncate();
        $this->command->info('✓ About Founders cleared');

        // Hapus semua data dari tabel Keunggulan Fasilitas
        $keunggulanFasilitas = KeunggulanFasilitas::all();
        foreach ($keunggulanFasilitas as $keunggulan) {
            if ($keunggulan->image) {
                Storage::disk('public')->delete($keunggulan->image);
            }
        }
        KeunggulanFasilitas::truncate();
        $this->command->info('✓ Keunggulan Fasilitas cleared');

        // Hapus semua data dari tabel Portfolio Achievements
        $portfolioAchievements = PortfolioAchievement::all();
        foreach ($portfolioAchievements as $portfolio) {
            if ($portfolio->image) {
                Storage::disk('public')->delete($portfolio->image);
            }
        }
        PortfolioAchievement::truncate();
        $this->command->info('✓ Portfolio Achievements cleared');

        // Hapus semua data dari tabel Tim Kami
        $timKamis = TimKami::all();
        foreach ($timKamis as $tim) {
            if ($tim->photo) {
                Storage::disk('public')->delete($tim->photo);
            }
            if ($tim->image) {
                Storage::disk('public')->delete($tim->image);
            }
        }
        TimKami::truncate();
        $this->command->info('✓ Tim Kami cleared');

        // Hapus semua data dari tabel Testimoni Pelanggan
        $testimoniPelanggans = TestimoniPelanggan::all();
        foreach ($testimoniPelanggans as $testimoni) {
            if ($testimoni->photo) {
                Storage::disk('public')->delete($testimoni->photo);
            }
            if ($testimoni->image) {
                Storage::disk('public')->delete($testimoni->image);
            }
        }
        TestimoniPelanggan::truncate();
        $this->command->info('✓ Testimoni Pelanggan cleared');

        // Hapus semua data dari tabel Events
        $events = Event::all();
        foreach ($events as $event) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
        }
        Event::truncate();
        $this->command->info('✓ Events cleared');

        // Hapus semua data dari tabel Footer
        Footer::truncate();
        $this->command->info('✓ Footer cleared');

        $this->command->info('');
        $this->command->info('✅ Semua data manage-content berhasil dihapus!');
        $this->command->info('Sekarang Anda bisa mengisi ulang data melalui admin panel.');
    }
}
