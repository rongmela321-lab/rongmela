@extends('backend.layout.main')

@section('content')
<x-success-message key="message" />
<x-error-message key="not_permitted" />

@if(isset($errors) && $errors->any())
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<section>
    <div class="container-fluid">
        <!-- Top Stats Cards -->
        <div class="row mb-3">
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-users" style="color: #733686; font-size: 28px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number">{{ $total_count }}</div>
                        <div class="name"><strong style="color: #733686">{{ __('db.Total Tailors') }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-percent" style="color: #297ff9; font-size: 28px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number">{{ $percentage_count }}</div>
                        <div class="name"><strong style="color: #297ff9">{{ __('db.Percentage Tailors') }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-calendar-check-o" style="color: #17a2b8; font-size: 28px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number">{{ $monthly_count }}</div>
                        <div class="name"><strong style="color: #17a2b8">{{ __('db.Monthly Tailors') }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-money" style="color: #00c689; font-size: 28px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number text-success font-weight-bold">{{ number_format($total_period_collection, 2) }}</div>
                        <div class="name"><strong style="color: #00c689">{{ __('db.Period Collection') }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h4 class="mb-0 font-weight-bold"><i class="fa fa-filter"></i> {{ __('db.Filter') }}</h4>
            </div>
            <div class="card-body">
                {!! Form::open(['route' => 'tailors.index', 'method' => 'get', 'id' => 'tailor-filter-form']) !!}
                <div class="row align-items-end">
                    <!-- Work Type Filter -->
                    <div class="col-md-3 col-sm-6 form-group">
                        <label><strong>{{ __('db.Work Type') }}</strong></label>
                        <select name="type" class="form-control selectpicker">
                            <option value="all" {{ $type == 'all' ? 'selected' : '' }}>{{ __('db.All') }}</option>
                            <option value="percentage" {{ $type == 'percentage' ? 'selected' : '' }}>{{ __('db.Percentage Basis') }}</option>
                            <option value="monthly" {{ $type == 'monthly' ? 'selected' : '' }}>{{ __('db.Monthly Basis') }}</option>
                        </select>
                    </div>

                    <!-- Date Range Picker -->
                    <div class="col-md-5 col-sm-6 form-group">
                        <label><strong>{{ __('db.Date Range') }}</strong></label>
                        <div class="input-group">
                            <input type="text" class="daterangepicker-field form-control" value="{{ $starting_date }} To {{ $ending_date }}" required />
                            <input type="hidden" name="starting_date" value="{{ $starting_date }}" />
                            <input type="hidden" name="ending_date" value="{{ $ending_date }}" />
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="dripicons-calendar"></i></span>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Button -->
                    <div class="col-md-2 col-sm-6 form-group">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> {{ __('db.Filter') }}</button>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-md-2 col-sm-6 form-group">
                        <a href="{{ route('tailors.index') }}" class="btn btn-outline-secondary btn-block"><i class="fa fa-undo"></i> {{ __('db.Reset') }}</a>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="font-weight-bold mb-2"><i class="fa fa-scissors"></i> {{ __('db.Tailor List') }}</h3>
                <div class="d-flex align-items-center flex-wrap">
                    <!-- Filter by Type -->
                    <div class="btn-group mr-2 mb-2" role="group">
                        <a href="{{ route('tailors.index', ['type' => 'all', 'starting_date' => $starting_date, 'ending_date' => $ending_date]) }}" class="btn btn-sm {{ $type == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('db.All') }}</a>
                        <a href="{{ route('tailors.index', ['type' => 'percentage', 'starting_date' => $starting_date, 'ending_date' => $ending_date]) }}" class="btn btn-sm {{ $type == 'percentage' ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('db.Percentage Basis') }}</a>
                        <a href="{{ route('tailors.index', ['type' => 'monthly', 'starting_date' => $starting_date, 'ending_date' => $ending_date]) }}" class="btn btn-sm {{ $type == 'monthly' ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('db.Monthly Basis') }}</a>
                    </div>
                    <button class="btn btn-info btn-sm mb-2" data-toggle="modal" data-target="#add-tailor-modal">
                        <i class="dripicons-plus"></i> {{ __('db.Add Tailor') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tailor-table" class="table table-hover table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="not-exported"></th>
                                <th>{{ __('db.name') }}</th>
                                <th>{{ __('db.Phone Number') }}</th>
                                <th>{{ __('db.Work Type') }}</th>
                                <th>{{ __('db.Rate / Percentage') }}</th>
                                <th>{{ __('db.Activity Count') }}</th>
                                <th>{{ __('db.Total Collected') }}</th>
                                <th>{{ __('db.Total Due') }}</th>
                                <th>{{ __('db.Status') }}</th>
                                <th class="not-exported">{{ __('db.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tailors as $key => $tailor)
                            <tr data-id="{{ $tailor->id }}">
                                <td></td>
                                <td><strong>{{ $tailor->name }}</strong></td>
                                <td>{{ $tailor->phone_number ?? '-' }}</td>
                                <td>
                                    @if($tailor->type == 'percentage')
                                        <span class="badge badge-primary"><i class="fa fa-percent"></i> {{ __('db.Percentage Basis') }}</span>
                                    @else
                                        <span class="badge badge-success"><i class="fa fa-calendar"></i> {{ __('db.Monthly Basis') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tailor->type == 'percentage')
                                        <span class="text-primary font-weight-bold">{{ number_format($tailor->percentage, 2) }}%</span>
                                    @else
                                        <span class="text-success font-weight-bold">{{ number_format($tailor->monthly_rate, 2) }} / {{ __('db.month') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tailor->type == 'percentage')
                                        <a href="{{ route('tailor_deliveries.index', ['tailor_id' => $tailor->id, 'starting_date' => $starting_date, 'ending_date' => $ending_date]) }}" class="badge badge-info p-2" title="{{ __('db.View Deliveries in this period') }}">
                                            {{ $tailor->period_deliveries_count ?? 0 }} {{ __('db.Deliveries') }}
                                        </a>
                                    @else
                                        <a href="{{ route('tailor_monthly_payments.index', ['tailor_id' => $tailor->id, 'starting_date' => $starting_date, 'ending_date' => $ending_date]) }}" class="badge badge-info p-2" title="{{ __('db.View Payments in this period') }}">
                                            {{ $tailor->period_monthly_payments_count ?? 0 }} {{ __('db.Payments') }}
                                        </a>
                                    @endif
                                </td>
                                <td class="text-success font-weight-bold">
                                    @if($tailor->type == 'percentage')
                                        @php
                                            $paid = (float)($tailor->period_delivery_paid ?? 0);
                                            $allPaid = (float)($tailor->all_delivery_paid ?? 0);
                                        @endphp
                                        <span>{{ number_format($paid, 2) }}</span>
                                        @if($paid != $allPaid)
                                            <br><small class="text-muted font-weight-normal">{{ __('db.All Time') }}: {{ number_format($allPaid, 2) }}</small>
                                        @endif
                                    @else
                                        @php
                                            $paid = (float)($tailor->period_monthly_paid ?? 0);
                                            $allPaid = (float)($tailor->all_monthly_paid ?? 0);
                                        @endphp
                                        <span>{{ number_format($paid, 2) }}</span>
                                        @if($paid != $allPaid)
                                            <br><small class="text-muted font-weight-normal">{{ __('db.All Time') }}: {{ number_format($allPaid, 2) }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-danger font-weight-bold">
                                    @if($tailor->type == 'percentage')
                                        @php
                                            $due = (float)($tailor->period_delivery_due ?? 0);
                                            $allDue = (float)($tailor->all_delivery_due ?? 0);
                                        @endphp
                                        <span>{{ number_format($due, 2) }}</span>
                                        @if($due != $allDue)
                                            <br><small class="text-muted font-weight-normal">{{ __('db.All Time') }}: {{ number_format($allDue, 2) }}</small>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($tailor->is_active)
                                        <span class="badge badge-success">{{ __('db.Active') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('db.Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ __('db.action') }}
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                            <li>
                                                <button type="button" 
                                                    class="edit-btn btn btn-link" 
                                                    data-id="{{ $tailor->id }}"
                                                    data-name="{{ $tailor->name }}"
                                                    data-phone_number="{{ $tailor->phone_number }}"
                                                    data-type="{{ $tailor->type }}"
                                                    data-percentage="{{ $tailor->percentage }}"
                                                    data-monthly_rate="{{ $tailor->monthly_rate }}"
                                                    data-address="{{ $tailor->address }}"
                                                    data-is_active="{{ $tailor->is_active ? 1 : 0 }}"
                                                    data-toggle="modal" 
                                                    data-target="#edit-tailor-modal">
                                                    <i class="dripicons-document-edit"></i> {{ __('db.edit') }}
                                                </button>
                                            </li>
                                            @if($tailor->type == 'percentage')
                                            <li>
                                                <a href="{{ route('tailor_deliveries.index', ['tailor_id' => $tailor->id]) }}" class="btn btn-link">
                                                    <i class="dripicons-list"></i> {{ __('db.Deliveries') }}
                                                </a>
                                            </li>
                                            @else
                                            <li>
                                                <a href="{{ route('tailor_monthly_payments.index', ['tailor_id' => $tailor->id]) }}" class="btn btn-link">
                                                    <i class="dripicons-list"></i> {{ __('db.Payments') }}
                                                </a>
                                            </li>
                                            @endif
                                            <li>
                                                {!! Form::open(['route' => ['tailors.destroy', $tailor->id], 'method' => 'DELETE', 'onsubmit' => 'return confirmDelete()']) !!}
                                                <button type="submit" class="btn btn-link text-danger">
                                                    <i class="dripicons-trash"></i> {{ __('db.delete') }}
                                                </button>
                                                {!! Form::close() !!}
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="tfoot font-weight-bold bg-light">
                            <tr>
                                <th colspan="5" class="text-right">{{ __('db.Total') }}:</th>
                                <th>{{ $total_period_deliveries + $total_period_payments }}</th>
                                <th class="text-success">{{ number_format($total_period_collection, 2) }}</th>
                                <th class="text-danger">{{ number_format($total_period_due, 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Tailor Modal -->
<div id="add-tailor-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'tailors.store', 'method' => 'post']) !!}
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fa fa-scissors"></i> {{ __('db.Add Tailor') }}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>{{ __('db.The field labels marked with * are required input fields') }}.</small></p>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label><strong>{{ __('db.name') }} *</strong></label>
                        <input type="text" name="name" required class="form-control" placeholder="e.g. Master Rafiq Tailor">
                    </div>
                    <div class="col-md-12 form-group">
                        <label><strong>{{ __('db.Phone Number') }}</strong></label>
                        <input type="text" name="phone_number" class="form-control" placeholder="017xxxxxxxx">
                    </div>
                    <div class="col-md-12 form-group">
                        <label><strong>{{ __('db.Work Type') }} *</strong></label>
                        <select name="type" id="add-tailor-type" class="form-control selectpicker" required>
                            <option value="percentage">{{ __('db.Percentage Basis') }}</option>
                            <option value="monthly">{{ __('db.Monthly Basis') }}</option>
                        </select>
                    </div>
                    <!-- Percentage Field (Shown for percentage) -->
                    <div class="col-md-12 form-group" id="add-percentage-wrapper">
                        <label><strong>{{ __('db.Owner Share Percentage') }} *</strong></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" name="percentage" id="add-percentage-input" class="form-control" placeholder="e.g. 25 or 30" value="25">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ __('db.Owner share percentage per delivery (e.g. 25%)') }}</small>
                    </div>
                    <!-- Monthly Rate Field (Shown for monthly) -->
                    <div class="col-md-12 form-group d-none" id="add-monthly-wrapper">
                        <label><strong>{{ __('db.Monthly Fixed Rate') }} *</strong></label>
                        <input type="number" step="0.01" min="0" name="monthly_rate" id="add-monthly-input" class="form-control" placeholder="e.g. 10000">
                        <small class="form-text text-muted">{{ __('db.Monthly fixed amount to pay the shop') }}</small>
                    </div>
                    <div class="col-md-12 form-group">
                        <label><strong>{{ __('db.Address') }}</strong></label>
                        <textarea name="address" rows="2" class="form-control" placeholder="Shop/Address details..."></textarea>
                    </div>
                    <div class="col-md-12 form-group">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="add-active" checked>
                            <label class="form-check-label" for="add-active"><strong>{{ __('db.Active Status') }}</strong></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('db.close') }}</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ __('db.submit') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Edit Tailor Modal -->
<div id="edit-tailor-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <form id="edit-tailor-form" method="post">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold"><i class="fa fa-scissors"></i> {{ __('db.Edit Tailor') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.name') }} *</strong></label>
                            <input type="text" name="name" id="edit-name" required class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.Phone Number') }}</strong></label>
                            <input type="text" name="phone_number" id="edit-phone" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.Work Type') }} *</strong></label>
                            <select name="type" id="edit-tailor-type" class="form-control selectpicker" required>
                                <option value="percentage">{{ __('db.Percentage Basis') }}</option>
                                <option value="monthly">{{ __('db.Monthly Basis') }}</option>
                            </select>
                        </div>
                        <!-- Percentage Field -->
                        <div class="col-md-12 form-group" id="edit-percentage-wrapper">
                            <label><strong>{{ __('db.Owner Share Percentage') }} *</strong></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="percentage" id="edit-percentage-input" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <!-- Monthly Rate Field -->
                        <div class="col-md-12 form-group d-none" id="edit-monthly-wrapper">
                            <label><strong>{{ __('db.Monthly Fixed Rate') }} *</strong></label>
                            <input type="number" step="0.01" min="0" name="monthly_rate" id="edit-monthly-input" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.Address') }}</strong></label>
                            <textarea name="address" id="edit-address" rows="2" class="form-control"></textarea>
                        </div>
                        <div class="col-md-12 form-group">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="edit-active">
                                <label class="form-check-label" for="edit-active"><strong>{{ __('db.Active Status') }}</strong></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('db.close') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ __('db.Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#tailor").siblings('a').attr('aria-expanded','true');
    $("ul#tailor").addClass("show");
    $("ul#tailor #tailor-list-menu").addClass("active");

    function confirmDelete() {
        return confirm("{{ __('db.Are you sure want to delete?') }}");
    }

    $(document).ready(function() {
        // Initialize DateRangePicker (identical to Deliveries and Monthly Payments)
        $(".daterangepicker-field").daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'All Time': [moment('2020-01-01'), moment()]
            }
        }, function(start, end, label) {
            let sDate = start.format('YYYY-MM-DD');
            let eDate = end.format('YYYY-MM-DD');
            $('input[name="starting_date"]').val(sDate);
            $('input[name="ending_date"]').val(eDate);
            $('.daterangepicker-field').val(sDate + ' To ' + eDate);
        });

        // Toggle add modal type fields
        $('#add-tailor-type').on('change', function() {
            var type = $(this).val();
            if (type === 'percentage') {
                $('#add-percentage-wrapper').removeClass('d-none');
                $('#add-monthly-wrapper').addClass('d-none');
            } else {
                $('#add-percentage-wrapper').addClass('d-none');
                $('#add-monthly-wrapper').removeClass('d-none');
            }
        });

        // Toggle edit modal type fields
        $('#edit-tailor-type').on('change', function() {
            var type = $(this).val();
            if (type === 'percentage') {
                $('#edit-percentage-wrapper').removeClass('d-none');
                $('#edit-monthly-wrapper').addClass('d-none');
            } else {
                $('#edit-percentage-wrapper').addClass('d-none');
                $('#edit-monthly-wrapper').removeClass('d-none');
            }
        });

        // Edit button click - populate modal
        $('.edit-btn').on('click', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var phone = $(this).data('phone_number');
            var type = $(this).data('type');
            var percentage = $(this).data('percentage');
            var monthly_rate = $(this).data('monthly_rate');
            var address = $(this).data('address');
            var is_active = $(this).data('is_active');

            var actionUrl = '{{ url("tailors") }}/' + id;
            $('#edit-tailor-form').attr('action', actionUrl);

            $('#edit-name').val(name);
            $('#edit-phone').val(phone);
            $('#edit-tailor-type').val(type).selectpicker('refresh');
            $('#edit-percentage-input').val(percentage);
            $('#edit-monthly-input').val(monthly_rate);
            $('#edit-address').val(address);
            $('#edit-active').prop('checked', is_active == 1);

            if (type === 'percentage') {
                $('#edit-percentage-wrapper').removeClass('d-none');
                $('#edit-monthly-wrapper').addClass('d-none');
            } else {
                $('#edit-percentage-wrapper').addClass('d-none');
                $('#edit-monthly-wrapper').removeClass('d-none');
            }
        });

        // DataTable Initialization
        $('#tailor-table').DataTable({
            'language': {
                'lengthMenu': '_MENU_ {{ __('db.records per page') }}',
                "info": '<small>{{ __('db.Showing') }} _START_ - _END_ (_TOTAL_)</small>',
                "search": '{{ __('db.Search') }}',
                'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
            order: [['1', 'asc']],
            'columnDefs': [
                {
                    "orderable": false,
                    'targets': [0, 9]
                },
                {
                    'render': function(data, type, row, meta) {
                        if (type === 'display') {
                            data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                        }
                        return data;
                    },
                    'checkboxes': {
                        'selectRow': true,
                        'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                    },
                    'targets': [0]
                }
            ],
            'select': {
                style: 'multi',
                selector: 'td:first-child'
            },
            'lengthMenu': [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            dom: '<"row"lfB>rtip',
            rowId: 'ObjectID',
            buttons: [
                {
                    extend: 'pdf',
                    text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    text: '<i title="export to excel" class="dripicons-document-new"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: '<i title="print" class="fa fa-print"></i>',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    }
                },
                {
                    text: '<i title="delete" class="dripicons-cross"></i>',
                    className: 'buttons-delete',
                    action: function(e, dt, node, config) {
                        var tailor_id = [];
                        $(':checkbox:checked').each(function(i) {
                            if (i) {
                                var id = $(this).closest('tr').data('id');
                                if (id) tailor_id.push(id);
                            }
                        });
                        if (tailor_id.length && confirm("{{ __('db.Are you sure want to delete?') }}")) {
                            $.ajax({
                                type: 'POST',
                                url: '{{ route("tailors.deletebyselection") }}',
                                data: {
                                    tailorIdArray: tailor_id,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(data) {
                                    alert(data);
                                    dt.rows({
                                        page: 'current',
                                        selected: true
                                    }).remove().draw(false);
                                }
                            });
                        } else if (!tailor_id.length) {
                            alert('Nothing is selected!');
                        }
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i title="column visibility" class="fa fa-eye"></i>',
                    columns: ':gt(0)'
                }
            ]
        });
    });
</script>
@endpush
