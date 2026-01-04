<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckShiftTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (! $user) {
            return $next($request);
        }

        // Allow API routes to pass through FIRST (they handle their own validation)
        // This must be checked before any shift time validation
        $path = $request->path();
        if (str_starts_with($path, 'dapur/orders/') ||
            str_starts_with($path, 'shift/check')) {
            return $next($request);
        }

        // If user is super_admin, skip shift time check entirely
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // If user doesn't have a shift assigned, allow access
        // (for users without shift restrictions)
        if (! $user->shift_id) {
            return $next($request);
        }

        $shift = $user->shift;

        // If shift doesn't exist or not active, allow access
        if (! $shift || ! $shift->is_active) {
            return $next($request);
        }

        // Get current time (with Asia/Jakarta timezone)
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->copy()->startOfDay();

        // Parse shift times
        $startTime = $this->parseTime($shift->start_time, $today);
        $endTime = $this->parseTime($shift->end_time, $today);

        // Handle midnight crossing (e.g., 22:00 - 06:00)
        if ($endTime < $startTime) {
            // Shift crosses midnight
            if ($now < $startTime) {
                $startTime = $startTime->copy()->subDay();
            } else {
                $endTime = $endTime->copy()->addDay();
            }
        }

        // Add 30 minutes tolerance before and after
        $toleranceStart = $startTime->copy()->subMinutes(30);
        $toleranceEnd = $endTime->copy()->addMinutes(30);

        // Update shift_end di session jika belum ada atau perlu update
        if (! session('shift_end') || ! session('shift_end_datetime')) {
            session(['shift_end' => $endTime->timestamp]);
            session(['shift_end_datetime' => $endTime->toIso8601String()]);
        }

        // Transfer order dari shift sebelumnya yang belum selesai ke shift aktif saat ini
        $shouldAttemptTransfer = $request->routeIs('dapur') || $request->routeIs('reports') || $request->routeIs('pengaturan-audio') || $request->is('admin*');
        $activeShift = cache()->remember('active_shift', 60, function () {
            return \App\Models\Shift::getActiveShift();
        });

        if ($shouldAttemptTransfer && $activeShift) {
            $cacheKey = 'shift_transfer_done_'.$today->format('Y-m-d').'_'.$activeShift->id;
            if (! cache()->has($cacheKey)) {
                $previousShiftIds = \App\Models\Shift::query()
                    ->where('is_active', true)
                    ->where('id', '!=', $activeShift->id)
                    ->pluck('id')
                    ->all();

                if (! empty($previousShiftIds)) {
                    \App\Models\orders::query()
                        ->whereIn('shift_id', $previousShiftIds)
                        ->whereIn('status', ['pending', 'processing'])
                        ->update(['shift_id' => $activeShift->id]);
                }

                cache()->put($cacheKey, true, 86400);
            }
        }

        // Check if current time is within tolerance range
        if ($now < $toleranceStart || $now > $toleranceEnd) {
            // Outside working hours - FORCE LOGOUT
            $shiftName = $shift->name;
            $startTimeFormatted = $shift->start_time;
            $endTimeFormatted = $shift->end_time;

            // Transfer pending orders to next shift before logout (optimized)
            $nextShift = cache()->remember('next_shift', 60, function () {
                return \App\Models\Shift::getNextShift();
            });
            if ($nextShift) {
                // Optimized: Bulk update instead of individual saves
                \App\Models\orders::where('shift_id', $user->shift_id)
                    ->whereIn('status', ['pending', 'processing'])
                    ->update(['shift_id' => $nextShift->id]);
            }

            // For API routes, return JSON response instead of redirect
            if ($request->expectsJson() || $request->is('orders/*') || $request->is('shift/*')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'error' => true,
                    'message' => "Anda hanya bisa login saat jam shift aktif. Shift: {$shiftName} ({$startTimeFormatted} - {$endTimeFormatted} WIB).",
                    'redirect' => '/',
                ], 403);
            }

            // Force logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Flash error message
            session()->flash('error', "Anda hanya bisa login saat jam shift aktif. Shift: {$shiftName} ({$startTimeFormatted} - {$endTimeFormatted} WIB).");

            return redirect('/');
        }

        // Check if within 30 min before start or 30 min after end for warning
        // Hanya tampilkan warning di halaman dapur/admin, bukan di halaman pelanggan
        if ($request->routeIs('dapur') || $request->routeIs('reports') || $request->routeIs('pengaturan-audio') || $request->is('admin*')) {
            if ($now < $startTime && $now >= $toleranceStart) {
                $minutesUntil = $startTime->diffInMinutes($now);
                session()->flash('warning', "Shift Anda belum dimulai. Mulai dalam {$minutesUntil} menit.");
            } elseif ($now > $endTime && $now <= $toleranceEnd) {
                $minutesAfter = $now->diffInMinutes($endTime);
                session()->flash('warning', "Shift Anda sudah berakhir {$minutesAfter} menit lalu. Segera selesaikan pekerjaan.");
            }
        }

        return $next($request);
    }

    /**
     * Parse time string to Carbon instance
     */
    private function parseTime($time, $date)
    {
        if ($time instanceof \Carbon\Carbon) {
            return $date->copy()->setHour($time->hour)->setMinute($time->minute)->setSecond($time->second);
        }

        if (is_string($time)) {
            $parts = explode(':', $time);
            $hour = (int) $parts[0] ?? 0;
            $minute = (int) $parts[1] ?? 0;
            $second = (int) $parts[2] ?? 0;

            return $date->copy()->setHour($hour)->setMinute($minute)->setSecond($second);
        }

        return $date->copy();
    }
}
