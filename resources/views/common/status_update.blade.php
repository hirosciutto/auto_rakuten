<?php $checked = false; ?>
@php
    if (empty($data)) {
        $data = $dataTypeContent;
    }
@endphp

@if(isset($content) || old($row->field))
    <?php $checked = old($row->field, $content); ?>
@else
    <?php $checked = isset($options->checked) &&
        filter_var($options->checked, FILTER_VALIDATE_BOOLEAN) ? true: false; ?>
@endif

<?php $class = $options->class ?? "toggleswitch"; ?>

@if(isset($options->on) && isset($options->off))
    <input type="checkbox" name="{{ $row->field }}[{{ $data->id }}]" class="{{ $class }} status-update"
        data-toggle="toggle"
        data-slug="{{$dataType->slug}}"
        data-id="{{ $data->id }}"
        data-on="{{ $options->on }}" {!! $checked ? 'checked="checked"' : '' !!}
        data-off="{{ $options->off }}"
        data-name="{{ $row->field }}"
    >
@else
    <input type="checkbox" name="{{ $row->field }}[{{ $data->id }}]" class="{{ $class }} status-update"
        data-toggle="toggle"
        data-id="{{ $data->id }}"
        data-name="{{ $row->field }}"
        @if($checked) checked @endif
    >
@endif
