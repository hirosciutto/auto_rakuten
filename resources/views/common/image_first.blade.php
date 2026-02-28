@php
    if (empty($data)) {
        $data = $dataTypeContent;
    }
    $imageList = json_decode($data->{$row->field}, true);
@endphp
<img src="{{ $imageList[0] }}" alt="{{ $imageList[0] }}" width="100" height="100">
