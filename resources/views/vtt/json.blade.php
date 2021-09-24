WEBVTT

@foreach ($sections as $section)

{{ $loop->iteration }}
{{ $section['start_time'] }} --> {{ $section['end_time'] }}
@json($section['contents'])

@endforeach
