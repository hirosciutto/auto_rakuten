@php
    if (empty($data)) {
        $data = $dataTypeContent;
    }
    if (is_array($data->{$row->field})) {
        $imageList = $data->{$row->field};
    } else {
        $imageList = json_decode($data->{$row->field}, true);
    }
@endphp

@if ($view === 'browse')
    @if (count($imageList) > 0)
        <img src="{{ $imageList[0] }}" alt="{{ $imageList[0] }}" width="100" height="100">
    @endif
@else
    @foreach($imageList as $image)
        <img src="{{ $image }}" alt="{{ $image }}" width="300">
    @endforeach
@endif
