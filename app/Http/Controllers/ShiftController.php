<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /**
     * Display a listing of shifts
     */
    public function index()
    {
        // Optimized: Select specific columns and eager load users
        $shifts = Shift::select('id', 'name', 'start_time', 'end_time', 'is_active')
            ->with('users:id,name,email,shift_id')
            ->orderBy('start_time')
            ->get();

        return view('admin.shifts.index', compact('shifts'));
    }

    /**
     * Store a newly created shift
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        Shift::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil dibuat.');
    }

    /**
     * Update the specified shift
     */
    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name,'.$id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        $shift->update([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil diperbarui.');
    }

    /**
     * Assign user to shift
     */
    public function assignUser(Request $request, $shiftId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Only allow admin, kitchen, and super_admin roles (check spatie roles)
        $allowedRoles = ['admin', 'kitchen', 'super_admin'];
        $userRole = $user->getRoleNames()->first();
        if (! in_array($userRole, $allowedRoles)) {
            return back()->with('error', 'Hanya user dengan role admin, kitchen, atau super admin yang bisa di-assign ke shift.');
        }

        $user->update([
            'shift_id' => $shiftId,
        ]);

        return back()->with('success', 'User berhasil di-assign ke shift.');
    }

    /**
     * Remove user from shift
     */
    public function removeUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['shift_id' => null]);

        return back()->with('success', 'User berhasil dihapus dari shift.');
    }

    /**
     * Check shift status for current user (AJAX endpoint)
     */
    public function checkStatus()
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json([
                    'shift_active' => false,
                    'message' => 'User tidak terautentikasi',
                ], 401);
            }

            // Super admin always has access
            if ($user->hasRole('super_admin')) {
                return response()->json([
                    'shift_active' => true,
                    'shift_name' => 'Super Admin',
                    'start_time' => '00:00',
                    'end_time' => '23:59',
                    'current_time' => Carbon::now()->format('H:i:s'),
                ]);
            }

            if (! $user->shift_id) {
                return response()->json([
                    'shift_active' => false,
                    'message' => 'User tidak memiliki shift yang aktif',
                ]);
            }

            // Load shift with relationship (optimized)
            $shift = Shift::select('id', 'name', 'start_time', 'end_time', 'is_active')
                ->find($user->shift_id);

            if (! $shift) {
                return response()->json([
                    'shift_active' => false,
                    'message' => 'Shift tidak ditemukan',
                ]);
            }

            if (! $shift->is_active) {
                return response()->json([
                    'shift_active' => false,
                    'message' => 'Shift tidak aktif',
                ]);
            }

            $now = Carbon::now('Asia/Jakarta');

            // Parse shift times - handle both string and Carbon formats
            $startTimeStr = $shift->start_time instanceof Carbon ? $shift->start_time->format('H:i') : $shift->start_time;
            $endTimeStr = $shift->end_time instanceof Carbon ? $shift->end_time->format('H:i') : $shift->end_time;

            $shiftStart = Carbon::createFromFormat('H:i', $startTimeStr, 'Asia/Jakarta');
            $shiftEnd = Carbon::createFromFormat('H:i', $endTimeStr, 'Asia/Jakarta');

            // Set to today
            $shiftStart->setDate($now->year, $now->month, $now->day);
            $shiftEnd->setDate($now->year, $now->month, $now->day);

            // Adjust for shifts that cross midnight
            if ($shiftEnd->lt($shiftStart)) {
                if ($now->lt($shiftStart)) {
                    $shiftStart->subDay();
                } else {
                    $shiftEnd->addDay();
                }
            }

            $isWithinShiftTime = $now->between($shiftStart, $shiftEnd);

            return response()->json([
                'shift_active' => $isWithinShiftTime,
                'shift_name' => $shift->name,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'current_time' => $now->format('H:i:s'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'shift_active' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }
}
