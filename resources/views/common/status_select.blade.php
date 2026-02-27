@php
    $selected_value = null;
    if (empty($data)) {
        $data = $dataTypeContent;
    }
    $selected_value = $data->{$row->field};
@endphp
<select
    class="form-control status-select"
    name="{{ $row->field }}"
    data-slug="{{$dataType->slug}}"
    data-id="{{ $data->id }}"
    data-name="{{ $row->field }}"
>
    <?php $default = (isset($options->default) && !isset($dataTypeContent->{$row->field})) ? $options->default : null; ?>
    @if(isset($options->options))
        @foreach($options->options as $key => $option)
            <option value="{{ $key }}" @if($default == $key && $selected_value === NULL) selected="selected" @endif @if($selected_value == $key) selected="selected" @endif>{{ $option }}</option>
        @endforeach
    @endif
</select>

