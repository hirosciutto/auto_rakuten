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
<img src="{{ $imageList[0] }}" alt="{{ $imageList[0] }}" width="100" height="100">
