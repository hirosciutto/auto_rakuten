@if(isset($options->model) && isset($options->type))

    @if(class_exists($options->model))

        @php $relationshipField = $row->field; @endphp

        @if($options->type == 'belongsTo')

            @if(isset($view) && ($view == 'browse' || $view == 'read'))

                @php
                    $relationshipData = (isset($data)) ? $data : $dataTypeContent;
                    $currentValue = $relationshipData->{$options->column} ?? null;
                    $customOptionText = null;
                    if ($currentValue !== null && $currentValue !== '') {
                        $customOptions = array_merge(
                            isset($options->prepend_options) && is_array($options->prepend_options) ? $options->prepend_options : [],
                            isset($options->append_options) && is_array($options->append_options) ? $options->append_options : []
                        );
                        foreach ($customOptions as $opt) {
                            $optId = data_get($opt, 'id');
                            if ((string)$optId === (string)$currentValue) {
                                $customOptionText = data_get($opt, 'text', '');
                                break;
                            }
                        }
                    }
                @endphp

                @if($customOptionText !== null)
                    <p>{{ $customOptionText }}</p>
                @else
                    @php
                        $model = app($options->model);
                        $query = $model::where($options->key, $currentValue)->first();
                    @endphp
                    @if(isset($query))
                        <p>{{ $query->{$options->label} }}</p>
                    @else
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @endif
                @endif

            @else

                @php
                    $isEdit = !is_null($dataTypeContent->getKey());
                    $isReadonly = false;
                    if (isset($options->readonly)) {
                        if ($isEdit && isset($options->readonly->edit) && $options->readonly->edit) {
                            $isReadonly = true;
                        } elseif (!$isEdit && isset($options->readonly->add) && $options->readonly->add) {
                            $isReadonly = true;
                        }
                    }
                @endphp

                @if($isReadonly)
                    {{-- readonly の場合はテキスト表示のみ --}}
                    @php
                        $readonlyValue = old($options->column, $dataTypeContent->{$options->column});
                        $readonlyCustomText = null;
                        if ($readonlyValue !== null && $readonlyValue !== '') {
                            $readonlyCustomOptions = array_merge(
                                isset($options->prepend_options) && is_array($options->prepend_options) ? $options->prepend_options : [],
                                isset($options->append_options) && is_array($options->append_options) ? $options->append_options : []
                            );
                            foreach ($readonlyCustomOptions as $opt) {
                                if ((string)data_get($opt, 'id', '') === (string)$readonlyValue) {
                                    $readonlyCustomText = data_get($opt, 'text', '');
                                    break;
                                }
                            }
                        }
                        $query = null;
                        if ($readonlyCustomText === null) {
                            $model = app($options->model);
                            $query = $model::where($options->key, $readonlyValue)->first();
                        }
                    @endphp
                    @if($readonlyCustomText !== null)
                        <p>{{ $readonlyCustomText }}</p>
                    @elseif(isset($query))
                        <p>{{ $query->{$options->label} }}</p>
                    @else
                        <p>{{ __('voyager::generic.none') }}</p>
                    @endif
                    <input type="hidden" name="{{ $options->column }}" value="{{ $readonlyValue }}">
                @else
                    <select
                        class="form-control select2-ajax-custom" name="{{ $options->column }}"
                        data-get-items-route="{{route('voyager.' . $dataType->slug.'.relation')}}"
                        data-get-items-field="{{$row->field}}"
                        @if($isEdit) data-id="{{$dataTypeContent->getKey()}}" @endif
                        data-method="{{ $isEdit ? 'edit' : 'add' }}"
                        @if(isset($options->scope_params)) data-scope-params="" @endif
                        @if($row->required == 1) required @endif
                    >
                        @php
                            $model = app($options->model);
                            $selectedValue = old($options->column, $dataTypeContent->{$options->column});
                            $query = $model::where($options->key, $selectedValue)->get();
                        @endphp

                        @if(!$row->required)
                            <option value="">{{ __('voyager::generic.none') }}</option>
                        @endif

                        @if(isset($options->prepend_options) && (is_array($options->prepend_options) || is_object($options->prepend_options)))
                            @foreach($options->prepend_options as $opt)
                                <option value="{{ data_get($opt, 'id', '') }}" @if((string)($selectedValue ?? '') === (string)data_get($opt, 'id', '')) selected="selected" @endif>{{ data_get($opt, 'text', '') }}</option>
                            @endforeach
                        @endif

                        @foreach($query as $relationshipData)
                            <option value="{{ $relationshipData->{$options->key} }}" @if((string)($selectedValue ?? '') === (string)$relationshipData->{$options->key}) selected="selected" @endif>{{ $relationshipData->{$options->label} }}</option>
                        @endforeach

                        @if(isset($options->append_options) && (is_array($options->append_options) || is_object($options->append_options)))
                            @foreach($options->append_options as $opt)
                                <option value="{{ data_get($opt, 'id', '') }}" @if((string)($selectedValue ?? '') === (string)data_get($opt, 'id', '')) selected="selected" @endif>{{ data_get($opt, 'text', '') }}</option>
                            @endforeach
                        @endif
                    </select>

                    {{-- scope_paramsで指定されたフィールドの値が変更されたときに、付与パラメータをセットする --}}
                    @if(isset($options->scope_params) && is_array($options->scope_params) && count($options->scope_params) > 0)
                    <script>
                      (function() {
                        $(document).ready(function() {
                          var scopeParams = @json($options->scope_params);
                          scopeParams.forEach(function(paramField) {
                            if (window.relation_data.columns[paramField] == undefined) {
                              window.relation_data.columns[paramField] = [];
                            }
                            window.relation_data.columns[paramField].push('{{ $options->column }}');
                          });
                        });
                      })();
                    </script>
                    @endif

                @endif

            @endif

        @elseif($options->type == 'hasOne')

            @php
                $relationshipData = (isset($data)) ? $data : $dataTypeContent;

                $model = app($options->model);
                $query = $model::where($options->column, '=', $relationshipData->{$options->key})->first();

            @endphp

            @if(isset($query))
                <p>{{ $query->{$options->label} }}</p>
            @else
                <p>{{ __('voyager::generic.no_results') }}</p>
            @endif

        @elseif($options->type == 'hasMany')

            @if(isset($view) && ($view == 'browse' || $view == 'read'))

                @php
                    $relationshipData = (isset($data)) ? $data : $dataTypeContent;
                    $model = app($options->model);

                    $selected_values = $model::where($options->column, '=', $relationshipData->{$options->key})->get()->map(function ($item, $key) use ($options) {
                        return $item->{$options->label};
                    })->all();
                @endphp

                @if($view == 'browse')
                    @php
                        $string_values = implode(", ", $selected_values);
                        if(mb_strlen($string_values) > 25){ $string_values = mb_substr($string_values, 0, 25) . '...'; }
                    @endphp
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        <p>{{ $string_values }}</p>
                    @endif
                @else
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        <ul>
                            @foreach($selected_values as $selected_value)
                                <li>{{ $selected_value }}</li>
                            @endforeach
                        </ul>
                    @endif
                @endif

            @else

                @php
                    $model = app($options->model);
                    $query = $model::where($options->column, '=', $dataTypeContent->{$options->key})->get();
                @endphp

                @if($query->isNotEmpty())
                    <ul>
                        @foreach($query as $query_res)
                            <li>{{ $query_res->{$options->label} }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>{{ __('voyager::generic.no_results') }}</p>
                @endif

            @endif

        @elseif($options->type == 'belongsToMany')

            @if(isset($view) && ($view == 'browse' || $view == 'read'))

                @php
                    $relationshipData = (isset($data)) ? $data : $dataTypeContent;

                    $selected_values = isset($relationshipData) ? $relationshipData->belongsToMany($options->model, $options->pivot_table, $options->foreign_pivot_key ?? null, $options->related_pivot_key ?? null, $options->parent_key ?? null, $options->key)->get()->map(function ($item, $key) use ($options) {
            			return $item->{$options->label};
            		})->all() : array();
                @endphp

                @if($view == 'browse')
                    @php
                        $string_values = implode(", ", $selected_values);
                        if(mb_strlen($string_values) > 25){ $string_values = mb_substr($string_values, 0, 25) . '...'; }
                    @endphp
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        <p>{{ $string_values }}</p>
                    @endif
                @else
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        <ul>
                            @foreach($selected_values as $selected_value)
                                <li>{{ $selected_value }}</li>
                            @endforeach
                        </ul>
                    @endif
                @endif

            @else
                @php
                    $isEdit = !is_null($dataTypeContent->getKey());
                    $isReadonly = false;
                    if (isset($options->readonly)) {
                        if ($isEdit && isset($options->readonly->edit) && $options->readonly->edit) {
                            $isReadonly = true;
                        } elseif (!$isEdit && isset($options->readonly->add) && $options->readonly->add) {
                            $isReadonly = true;
                        }
                    }
                @endphp

                @if($isReadonly)
                    {{-- readonly の場合はテキスト表示のみ --}}
                    @php
                        $relationshipData = $dataTypeContent;
                        $selected_values = isset($relationshipData) ? $relationshipData->belongsToMany($options->model, $options->pivot_table, $options->foreign_pivot_key ?? null, $options->related_pivot_key ?? null, $options->parent_key ?? null, $options->key)->get()->map(function ($item, $key) use ($options) {
                            return $item->{$options->label};
                        })->all() : array();
                    @endphp
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.none') }}</p>
                    @else
                        <ul>
                            @foreach($selected_values as $selected_value)
                                <li>{{ $selected_value }}</li>
                            @endforeach
                        </ul>
                    @endif
                @else
                    <select
                        class="form-control select2-ajax-custom @if(isset($options->taggable) && $options->taggable === 'on') taggable @endif"
                        name="{{ $relationshipField }}[]" multiple
                        data-get-items-route="{{route('voyager.' . $dataType->slug.'.relation')}}"
                        data-get-items-field="{{$row->field}}"
                        @if($isEdit) data-id="{{$dataTypeContent->getKey()}}" @endif
                        data-method="{{ $isEdit ? 'edit' : 'add' }}"
                        @if(isset($options->scope_params)) data-scope-params="{}" @endif
                        @if(isset($options->taggable) && $options->taggable === 'on')
                            data-route="{{ route('voyager.'.\Illuminate\Support\Str::slug($options->table).'.store') }}"
                            data-label="{{$options->label}}"
                            data-error-message="{{__('voyager::bread.error_tagging')}}"
                        @endif
                        @if($row->required == 1) required @endif
                    >

                        @php
                            $selected_keys = [];

                            if (!is_null($dataTypeContent->getKey())) {
                                $selected_keys = $dataTypeContent->belongsToMany(
                                    $options->model,
                                    $options->pivot_table,
                                    $options->foreign_pivot_key ?? null,
                                    $options->related_pivot_key ?? null,
                                    $options->parent_key ?? null,
                                    $options->key
                                )->pluck($options->table.'.'.$options->key);
                            }
                            $selected_keys = old($relationshipField, $selected_keys);
                            $selected_values = app($options->model)->whereIn($options->key, $selected_keys)->pluck($options->label, $options->key);
                        @endphp

                        @if(!$row->required)
                            <option value="">{{ __('voyager::generic.none') }}</option>
                        @endif

                        @foreach ($selected_values as $key => $value)
                            <option value="{{ $key }}" selected="selected">{{ $value }}</option>
                        @endforeach

                </select>
                @endif

            @endif

        @endif

    @else

        cannot make relationship because {{ $options->model }} does not exist.

    @endif

@endif
