{{-- Meta tags untuk shift end timestamp (digunakan untuk auto-logout real-time) --}}
@push('head')
@if(isset($shiftEndTimestamp) && $shiftEndTimestamp)
<meta name="shift-end" content="{{ $shiftEndDatetime }}">
<meta name="shift-end-timestamp" content="{{ $shiftEndTimestamp }}">
@endif
@endpush

