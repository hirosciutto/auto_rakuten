@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());

    // show_rule: details.display.id と details.show_rule が設定されている場合、
    // 指定フィールドの値に応じてこの項目の表示/非表示を切り替える。
    // show_rule は "フィールド名:値" 形式（複数値は "a|b" でOR、配列でAND）。
    $ruleScripts = [];
    $role_id = Auth::user()->role_id ?? null;
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp

                            @foreach($dataTypeRows as $row)
                                @php
                                    $display_options = $row->details->display ?? NULL;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }

                                    // show_rule のルール情報を収集
                                    if (isset($display_options->id) && isset($row->details->show_rule)) {
                                        $ruleScripts[$row->field] = [];
                                        $show_rules = is_array($row->details->show_rule) ? $row->details->show_rule : [$row->details->show_rule];
                                        foreach ($show_rules as $rule_part) {
                                            list($ruleField, $ruleValue) = explode(':', $rule_part);
                                            if (empty($ruleScripts[$row->field]['rule'])) $ruleScripts[$row->field]['rule'] = [];
                                            $targetData = $dataTypeRows->filter(function($data) use($ruleField) { return $data->field == $ruleField; });
                                            $targetOption = $targetData->isNotEmpty() ? $targetData->first() : null;
                                            $targetDefault = ($targetOption && isset($targetOption->details->default)) ? $targetOption->details->default : null;
                                            $targetValue = $dataTypeContent->{$ruleField} ?? $targetDefault;
                                            $ruleScripts[$row->field]['rule'][] = [
                                                'id' => $display_options->id,
                                                'field' => $ruleField,
                                                'value' => $ruleValue,
                                                'target' => old($ruleField, $targetValue)
                                            ];
                                        }
                                    }

                                    // ロールによる表示制御（details.role に "1|2" のように有効 role_id を指定）
                                    $role_hide = false;
                                    if ($role_id !== null && isset($row->details->role)) {
                                        $enable_role_ids = explode('|', $row->details->role);
                                        if (!in_array((string)$role_id, $enable_role_ids)) {
                                            $role_hide = true;
                                        }
                                    }
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif
                                @if (!$role_hide)
                                @php
                                    $isRequired = !empty($row->required);
                                    if (!$isRequired && isset($row->details->validation)) {
                                        $v = $row->details->validation;
                                        if (!empty($v->required)) $isRequired = true;
                                        else {
                                            $rule = ($edit && isset($v->edit->rule)) ? $v->edit->rule : ((!$edit && isset($v->add->rule)) ? $v->add->rule : ($v->rule ?? null));
                                            $isRequired = $rule && (str_contains($rule, 'required'));
                                        }
                                    }
                                @endphp
                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)) id="{{ $display_options->id }}" @endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}@if($isRequired)<span class="text-danger">*</span>@endif</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if ($add && isset($row->details->view_add))
                                        @include($row->details->view_add, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'view' => 'add', 'options' => $row->details])
                                    @elseif ($edit && isset($row->details->view_edit))
                                        @include($row->details->view_edit, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'view' => 'edit', 'options' => $row->details])
                                    @elseif (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @else
                                        @php
                                            $isReadonlyFormField = false;
                                            if (isset($row->details->readonly)) {
                                                if ($edit && !empty($row->details->readonly->edit)) {
                                                    $isReadonlyFormField = true;
                                                } elseif ($add && !empty($row->details->readonly->add)) {
                                                    $isReadonlyFormField = true;
                                                }
                                            }
                                        @endphp
                                        @if ($isReadonlyFormField)
                                            @php
                                                $roRaw = old($row->field, $dataTypeContent->{$row->field} ?? null);
                                                $roVal = $roRaw;
                                                if (is_array($roVal)) {
                                                    $roVal = implode(', ', $roVal);
                                                }
                                                if ($roVal === null || $roVal === '') {
                                                    $roVal = '-';
                                                }
                                            @endphp
                                            <p class="form-control-static">{{ $roVal }}</p>
                                            @if (is_array($roRaw))
                                                @foreach($roRaw as $v)
                                                    <input type="hidden" name="{{ $row->field }}[]" value="{{ $v }}">
                                                @endforeach
                                            @else
                                                <input type="hidden" name="{{ $row->field }}" value="{{ $roRaw ?? '' }}">
                                            @endif
                                        @else
                                            {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                        @endif
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                    @if (isset($row->details->caption))
                                        <div class="caption">{!! nl2br(e($row->details->caption)) !!}</div>
                                    @endif
                                </div>
                                @endif
                            @endforeach

                        </div>

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                    </form>

                    <div style="display:none">
                        <input type="hidden" id="upload_url" value="{{ route('voyager.upload') }}">
                        <input type="hidden" id="upload_type_slug" value="{{ $dataType->slug }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>
                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);
            params = {
                slug:   '{{ $dataType->slug }}',
                filename:  $file.data('file-name'),
                id:     $file.data('id'),
                field:  $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            };
            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
          };
        }

        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({ format: 'L', extraFormats: [ 'YYYY-MM-DD' ] }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if (isset($isModelTranslatable) && $isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, elt) { $(elt).slugify(); });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.'.$dataType->slug.'.media.remove') }}', params, function (response) {
                    if (response && response.data && response.data.status == 200) {
                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); });
                    } else {
                        toastr.error("Error removing file.");
                    }
                });
                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();

            @foreach($ruleScripts as $field => $data)
                setShowRule({!! json_encode($data['rule']) !!});
            @endforeach
        });

        function setShowRule(ruleArray) {
            if (!ruleArray || !ruleArray.length) return;
            var rule_id = null, default_show = true, rule_value_arr = {};
            ruleArray.forEach(function(rule) {
                rule_id = rule.id;
                if (!rule_value_arr[rule_id]) rule_value_arr[rule_id] = [];
                rule_value_arr[rule_id][rule.field] = rule.value.split('|');
            });
            ruleArray.forEach(function(rule) {
                rule_id = rule.id;
                var target_val = rule.target;
                if (rule_value_arr[rule_id][rule.field].length > 1) {
                    if (rule_value_arr[rule_id][rule.field].indexOf(target_val) === -1) default_show = false;
                } else {
                    if (target_val != rule.value) default_show = false;
                }
            });
            ruleArray.forEach(function(rule) {
                rule_id = rule.id;
                $(document).on('change', '[name="' + rule.field + '"]', function() {
                    var is_show = true;
                    ruleArray.forEach(function(r) {
                        $('[name="' + r.field + '"]').each(function() {
                            var $elm = $(this), val = null;
                            if ($elm.attr('type') == 'checkbox') val = $elm.prop('checked') ? 1 : 0;
                            else if ($elm.attr('type') == 'radio') val = $('[name="' + r.field + '"]:checked').val();
                            else val = $elm.val();
                            var ok = rule_value_arr[rule_id][r.field].length > 1
                                ? (rule_value_arr[rule_id][r.field].indexOf(val) !== -1)
                                : (val == r.value);
                            if (!ok) is_show = false;
                        });
                    });
                    $('#' + rule_id)[is_show ? 'show' : 'hide']();
                });
            });
            if (default_show) $('#' + rule_id).show(); else $('#' + rule_id).hide();
        }

        function setScopeParams(name, value) {
            if (typeof window.relation_data === 'undefined' || !window.relation_data.columns[name]) return;
            window.relation_data.columns[name].forEach(function(column) {
                if (!window.relation_data.values[column]) window.relation_data.values[column] = {};
                window.relation_data.values[column][name] = value;
                $('[name="' + column + '"]').attr('data-scope-params', JSON.stringify(window.relation_data.values[column]));
            });
        }
        $('select.select2-ajax-custom').each(function() {
            var $sel = $(this);
            $sel.select2({
                width: '100%',
                ajax: {
                    url: $sel.data('get-items-route'),
                    data: function(params) {
                        return {
                            search: params.term,
                            type: $sel.data('get-items-field'),
                            method: $sel.data('method'),
                            scope_params: $sel.attr('data-scope-params'),
                            id: $sel.data('id'),
                            page: params.page || 1
                        };
                    },
                    cache: false
                }
            });
            $sel.on('select2:select', function(e) {
                var data = e.params.data;
                if (data.id === '') $sel.val([]).trigger('change');
                else $sel.find("option[value='" + data.id + "']").attr('selected', 'selected');
                setScopeParams($sel.attr('name'), data.id);
            });
        });
        $(function() {
            if (typeof window.relation_data !== 'undefined' && window.relation_data.columns) {
                for (var name in window.relation_data.columns) {
                    var value = $('[name="' + name + '"]').val();
                    if (value) setScopeParams(name, value);
                }
            }
        });
    </script>
@stop
