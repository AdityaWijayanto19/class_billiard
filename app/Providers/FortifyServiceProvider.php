<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // 1. Tentukan View Login - Redirect jika sudah login
        Fortify::loginView(function (Request $request) {
            // Jika user sudah login, redirect ke dashboard sesuai role
            if (Auth::check()) {
                $role = Auth::user()->getRoleNames()->first() ?? Auth::user()->role;
                if ($role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($role === 'kitchen') {
                    return redirect()->route('dapur');
                }

                return redirect()->route('home');
            }

            return view('auth.login');
        });

        // 2. Custom Login Response (Redirect berdasarkan Role)
        $this->app->instance(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            new class implements \Laravel\Fortify\Contracts\LoginResponse
            {
                public function toResponse($request)
                {
                    $user = auth()->user();
                    $role = $user->getRoleNames()->first() ?? $user->role;

                    // Simpan waktu akhir shift di session untuk auto-logout
                    if ($user->shift_id) {
                        $shift = $user->shift;
                        if ($shift && $shift->is_active) {
                            $now = \Carbon\Carbon::now('Asia/Jakarta');
                            $today = $now->copy()->startOfDay();

                            // Parse shift end time
                            $shiftEnd = $this->parseShiftEndTime($shift->end_time, $today, $shift->start_time);

                            // Simpan di session untuk cek di middleware dan JavaScript
                            session(['shift_end' => $shiftEnd->timestamp]);
                            session(['shift_end_datetime' => $shiftEnd->toIso8601String()]);
                        }
                    }

                    // Redirect berdasarkan role
                    if ($role === 'admin' || $role === 'super_admin') {
                        // Admin dan Super Admin ke dashboard admin
                        return redirect()->route('admin.dashboard');
                    } elseif ($role === 'kitchen') {
                        // Kitchen ke halaman dapur
                        return redirect()->route('dapur');
                    }

                    // Default ke home jika tidak ada role yang cocok
                    return redirect('/');
                }

                /**
                 * Parse shift end time dengan handling midnight crossing
                 */
                private function parseShiftEndTime($endTime, $today, $startTime)
                {
                    $now = \Carbon\Carbon::now('Asia/Jakarta');

                    if ($endTime instanceof \Carbon\Carbon) {
                        $end = $today->copy()->setHour($endTime->hour)->setMinute($endTime->minute)->setSecond($endTime->second);
                    } else {
                        $parts = explode(':', $endTime);
                        $hour = (int) ($parts[0] ?? 0);
                        $minute = (int) ($parts[1] ?? 0);
                        $second = (int) ($parts[2] ?? 0);
                        $end = $today->copy()->setHour($hour)->setMinute($minute)->setSecond($second);
                    }

                    // Parse start time untuk cek midnight crossing
                    if ($startTime instanceof \Carbon\Carbon) {
                        $start = $today->copy()->setHour($startTime->hour)->setMinute($startTime->minute)->setSecond($startTime->second);
                    } else {
                        $parts = explode(':', $startTime);
                        $startHour = (int) ($parts[0] ?? 0);
                        $startMinute = (int) ($parts[1] ?? 0);
                        $startSecond = (int) ($parts[2] ?? 0);
                        $start = $today->copy()->setHour($startHour)->setMinute($startMinute)->setSecond($startSecond);
                    }

                    // Handle midnight crossing (e.g., 22:00 - 06:00)
                    if ($end < $start) {
                        // Shift crosses midnight
                        if ($now < $start) {
                            // Before start time, shift end is yesterday
                            $end = $end->copy()->subDay();
                        } else {
                            // After start time, shift end is tomorrow
                            $end = $end->copy()->addDay();
                        }
                    }

                    return $end;
                }
            }
        );

        // 3. Custom Logout Response (Redirect ke halaman login)
        $this->app->instance(
            \Laravel\Fortify\Contracts\LogoutResponse::class,
            new class implements \Laravel\Fortify\Contracts\LogoutResponse
            {
                public function toResponse($request)
                {
                    return redirect('/login')->with('success', 'Anda telah berhasil logout.');
                }
            }
        );

        // Rate Limiter bawaan tetap dipertahankan
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
