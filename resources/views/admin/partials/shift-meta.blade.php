{{-- Meta tags untuk shift end timestamp (Admin) --}}
@if(isset($shiftEndTimestamp) && $shiftEndTimestamp)
<meta name="shift-end" content="{{ $shiftEndDatetime }}">
<meta name="shift-end-timestamp" content="{{ $shiftEndTimestamp }}">
@endif

