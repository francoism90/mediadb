WEBVTT

@foreach ($items as $item)

{{ $loop->iteration }}
{{ $item['start_time'] }} --> {{ $item['end_time'] }}
@json($item['contents'], JSON_PRETTY_PRINT)

@endforeach
