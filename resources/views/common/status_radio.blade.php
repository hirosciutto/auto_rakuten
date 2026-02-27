@php
    if (empty($data)) {
        $data = $dataTypeContent;
    }
    $selected_value = $data->{$row->field};
    $rowId = $data->getKey();
@endphp
@if(isset($options->options))
    @foreach($options->options as $key => $option)
        <label class="radio-inline">
            <input type="radio"
                class="status-radio"
                name="{{ $row->field }}[{{ $rowId }}]"
                value="{{ $key }}"
                data-slug="{{ $dataType->slug }}"
                data-id="{{ $rowId }}"
                data-name="{{ $row->field }}"
                @if((string)$selected_value === (string)$key) checked @endif
            >
            {{ $option }}
        </label>
    @endforeach
@endif
