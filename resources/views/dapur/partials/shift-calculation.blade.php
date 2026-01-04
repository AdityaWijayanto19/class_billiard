{{-- PHP Block untuk menghitung shift end timestamp --}}
@php
    // Get shift_end from session untuk auto-logout real-time
    $shiftEndTimestamp = session('shift_end');
    $shiftEndDatetime = session('shift_end_datetime');
    
    // Jika belum ada di session, hitung dari active shift
    $activeShift = \App\Models\Shift::getActiveShift();
    if (!$shiftEndTimestamp && $activeShift) {
        $now = \Carbon\Carbon::now('Asia/Jakarta');
        $shiftStart = \Carbon\Carbon::today('Asia/Jakarta')->setTimeFromTimeString($activeShift->start_time);
        $shiftEnd = \Carbon\Carbon::today('Asia/Jakarta')->setTimeFromTimeString($activeShift->end_time);
        
        if ($shiftEnd->lt($shiftStart)) {
            $shiftEnd->addDay();
        }
        
        $shiftEndTimestamp = $shiftEnd->timestamp;
        $shiftEndDatetime = $shiftEnd->toIso8601String();
    }
@endphp

