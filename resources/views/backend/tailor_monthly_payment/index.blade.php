@extends('backend.layout.main')

@section('content')
<x-success-message key="message" />
<x-error-message key="not_permitted" />

@if(isset($errors) && $errors->any())
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<section>
    <div class="container-fluid">
        <!-- Top Summary Cards -->
        <div class="row mb-3">
            <div class="col-md-4 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-calendar-check-o" style="color: #00c689; font-size: 28px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number">{{ number_format($total_amount, 2) }}</div>
                        <div class="name"><strong style="color: #00c689">{{ __('db.Total Monthly Collection') ?? 'Total Monthly Collection (মোট মাসিক জমা)' }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-users" style="color: #733686; font-size: 28px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number">{{ count($monthly_tailors) }}</div>
                        <div class="name"><strong style="color: #733686">{{ __('db.Total Monthly Tailors') ?? 'Total Monthly Tailors' }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-list-alt" style="color: #297ff9; font-size: 28px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number">{{ count($payments) }}</div>
                        <div class="name"><strong style="color: #297ff9">{{ __('db.Total Payment Records') ?? 'Total Payment Records' }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h4 class="mb-0 font-weight-bold"><i class="fa fa-filter"></i> {{ __('db.Filter Monthly Payments') ?? 'Filter Monthly Payments' }}</h4>
            </div>
            <div class="card-body">
                {!! Form::open(['route' => 'tailor_monthly_payments.index', 'method' => 'get', 'id' => 'monthly-filter-form']) !!}
                <div class="row align-items-end">
                    <!-- Tailor Filter (Only Monthly Tailors) -->
                    <div class="col-md-4 col-sm-6 form-group">
                        <label><strong>{{ __('db.Select Monthly Tailor') ?? 'Select Monthly Tailor' }}</strong></label>
                        <select name="tailor_id" class="form-control selectpicker" data-live-search="true">
                            <option value="0">{{ __('db.All Monthly Tailors') ?? 'All Monthly Tailors' }}</option>
                            @foreach($monthly_tailors as $t)
                                <option value="{{ $t->id }}" {{ $tailor_id == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ number_format($t->monthly_rate, 2) }}/month)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range Picker -->
                    <div class="col-md-5 col-sm-6 form-group">
                        <label><strong>{{ __('db.Date Range') ?? 'Date Range' }}</strong></label>
                        <div class="input-group">
                            <input type="text" class="daterangepicker-field form-control" value="{{ $starting_date }} To {{ $ending_date }}" required />
                            <input type="hidden" name="starting_date" value="{{ $starting_date }}" />
                            <input type="hidden" name="ending_date" value="{{ $ending_date }}" />
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="dripicons-calendar"></i></span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-3 col-sm-6 form-group">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> {{ __('db.Filter') ?? 'Filter' }}</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="font-weight-bold mb-2"><i class="fa fa-calendar"></i> {{ __('db.Monthly Payment List') ?? 'Monthly Tailor Payment List' }}</h3>
                <button class="btn btn-info btn-sm mb-2" data-toggle="modal" data-target="#add-monthly-payment-modal">
                    <i class="dripicons-plus"></i> {{ __('db.Add Monthly Payment') ?? 'Add Monthly Payment' }}
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="monthly-payment-table" class="table table-hover table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="not-exported"></th>
                                <th>{{ __('db.date') ?? 'Date' }}</th>
                                <th>{{ __('db.reference') ?? 'Reference No' }}</th>
                                <th>{{ __('db.Tailor') ?? 'Tailor Name' }}</th>
                                <th>{{ __('db.For Month / Period') ?? 'For Month / Period' }}</th>
                                <th>{{ __('db.Amount') ?? 'Amount (TK)' }}</th>
                                <th>{{ __('db.Note') ?? 'Note' }}</th>
                                <th class="not-exported">{{ __('db.action') ?? 'Action' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                            <tr data-id="{{ $payment->id }}">
                                <td></td>
                                <td>{{ date(config('date_format') ?? 'Y-m-d', strtotime($payment->payment_date)) }}</td>
                                <td><span class="badge badge-light font-weight-bold">{{ $payment->reference_no }}</span></td>
                                <td><strong>{{ $payment->tailor->name ?? 'N/A' }}</strong></td>
                                <td><span class="badge badge-info p-1">{{ $payment->month ?? '-' }}</span></td>
                                <td class="text-success font-weight-bold">{{ number_format($payment->amount, 2) }}</td>
                                <td><small class="text-muted">{{ $payment->note ?? '-' }}</small></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ __('db.action') ?? 'Action' }}
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                            <li>
                                                <button type="button" 
                                                    class="edit-monthly-btn btn btn-link"
                                                    data-id="{{ $payment->id }}"
                                                    data-tailor-id="{{ $payment->tailor_id }}"
                                                    data-payment-date="{{ $payment->payment_date->format('Y-m-d') }}"
                                                    data-month="{{ $payment->month }}"
                                                    data-amount="{{ $payment->amount }}"
                                                    data-note="{{ $payment->note }}"
                                                    data-toggle="modal" 
                                                    data-target="#edit-monthly-payment-modal">
                                                    <i class="dripicons-document-edit"></i> {{ __('db.edit') ?? 'Edit' }}
                                                </button>
                                            </li>
                                            <li>
                                                {!! Form::open(['route' => ['tailor_monthly_payments.destroy', $payment->id], 'method' => 'DELETE', 'onsubmit' => 'return confirmDelete()']) !!}
                                                <button type="submit" class="btn btn-link text-danger">
                                                    <i class="dripicons-trash"></i> {{ __('db.delete') ?? 'Delete' }}
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
                                <th colspan="5" class="text-right">{{ __('db.Total') ?? 'Total' }}:</th>
                                <th class="text-success">{{ number_format($total_amount, 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Monthly Payment Modal -->
<div id="add-monthly-payment-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'tailor_monthly_payments.store', 'method' => 'post', 'id' => 'add-monthly-form']) !!}
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fa fa-calendar"></i> {{ __('db.Add Monthly Payment') ?? 'Add Monthly Tailor Payment' }}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>{{ __('db.The field labels marked with * are required input fields') ?? 'The field labels marked with * are required input fields' }}.</small></p>
                <div class="row">
                    <!-- Tailor Select (Only Monthly Tailors) -->
                    <div class="col-md-12 form-group">
                        <label><strong>{{ __('db.Monthly Tailor') ?? 'Monthly Tailor' }} *</strong></label>
                        <select name="tailor_id" id="add-mon-tailor" class="form-control selectpicker" required data-live-search="true" title="Select Monthly Tailor...">
                            @foreach($monthly_tailors as $t)
                                <option value="{{ $t->id }}" data-rate="{{ $t->monthly_rate }}">
                                    {{ $t->name }} (Fixed: {{ number_format($t->monthly_rate, 2) }}/month)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Date -->
                    <div class="col-md-6 form-group">
                        <label><strong>{{ __('db.Payment Date') ?? 'Payment Date' }} *</strong></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Month / Period Name -->
                    <div class="col-md-6 form-group">
                        <label><strong>{{ __('db.Month / Period') ?? 'Month / Period' }}</strong></label>
                        <input type="text" name="month" class="form-control" placeholder="e.g. October 2026" value="{{ date('F Y') }}">
                    </div>

                    <!-- Amount -->
                    <div class="col-md-12 form-group">
                        <label><strong>{{ __('db.Amount') ?? 'Amount (TK)' }} *</strong></label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="add-mon-amount" required class="form-control font-weight-bold text-success" placeholder="0.00">
                    </div>

                    <!-- Note -->
                    <div class="col-md-12 form-group">
                        <label><strong>{{ __('db.Note') ?? 'Note' }}</strong></label>
                        <textarea name="note" rows="2" class="form-control" placeholder="Monthly rent payment remarks..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('db.close') ?? 'Close' }}</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ __('db.submit') ?? 'Save Payment' }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Edit Monthly Payment Modal -->
<div id="edit-monthly-payment-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <form id="edit-monthly-form" method="post">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold"><i class="fa fa-calendar"></i> {{ __('db.Edit Monthly Payment') ?? 'Edit Monthly Tailor Payment' }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.Monthly Tailor') ?? 'Monthly Tailor' }} *</strong></label>
                            <select name="tailor_id" id="edit-mon-tailor" class="form-control selectpicker" required data-live-search="true">
                                @foreach($monthly_tailors as $t)
                                    <option value="{{ $t->id }}">
                                        {{ $t->name }} (Fixed: {{ number_format($t->monthly_rate, 2) }}/month)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label><strong>{{ __('db.Payment Date') ?? 'Payment Date' }} *</strong></label>
                            <input type="date" name="payment_date" id="edit-mon-date" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label><strong>{{ __('db.Month / Period') ?? 'Month / Period' }}</strong></label>
                            <input type="text" name="month" id="edit-mon-month" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.Amount') ?? 'Amount (TK)' }} *</strong></label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="edit-mon-amount" required class="form-control font-weight-bold text-success">
                        </div>
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.Note') ?? 'Note' }}</strong></label>
                            <textarea name="note" id="edit-mon-note" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('db.close') ?? 'Close' }}</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ __('db.Update') ?? 'Update Payment' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    function confirmDelete() {
        return confirm("{{ __('db.Are you sure want to delete?') ?? 'Are you sure want to delete this payment record?' }}");
    }

    $(document).ready(function() {
        // DateRangePicker
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

        // When Monthly Tailor is chosen -> auto fill their fixed monthly rate
        $('#add-mon-tailor').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var rate = parseFloat(selectedOption.data('rate')) || 0;
            if (rate > 0) {
                $('#add-mon-amount').val(rate.toFixed(2));
            }
        });

        // Edit Monthly Payment
        $('.edit-monthly-btn').on('click', function() {
            var id = $(this).data('id');
            var tailorId = $(this).data('tailor-id');
            var date = $(this).data('payment-date');
            var month = $(this).data('month');
            var amount = $(this).data('amount');
            var note = $(this).data('note');

            var actionUrl = '{{ url("tailor_monthly_payments") }}/' + id;
            $('#edit-monthly-form').attr('action', actionUrl);

            $('#edit-mon-tailor').val(tailorId).selectpicker('refresh');
            $('#edit-mon-date').val(date);
            $('#edit-mon-month').val(month);
            $('#edit-mon-amount').val(amount);
            $('#edit-mon-note').val(note);
        });

        // DataTable Initialization
        $('#monthly-payment-table').DataTable({
            'language': {
                'lengthMenu': '_MENU_ {{ __('db.records per page') ?? 'records per page' }}',
                "info": '<small>{{ __('db.Showing') ?? 'Showing' }} _START_ - _END_ (_TOTAL_)</small>',
                "search": '{{ __('db.Search') ?? 'Search' }}',
                'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
            order: [['1', 'desc']],
            'columnDefs': [
                {
                    "orderable": false,
                    'targets': [0, 7]
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
                        var payment_id = [];
                        $(':checkbox:checked').each(function(i) {
                            if (i) {
                                var id = $(this).closest('tr').data('id');
                                if (id) payment_id.push(id);
                            }
                        });
                        if (payment_id.length && confirm("{{ __('db.Are you sure want to delete?') ?? 'Are you sure want to delete?' }}")) {
                            $.ajax({
                                type: 'POST',
                                url: '{{ route("tailor_monthly_payments.deletebyselection") }}',
                                data: {
                                    paymentIdArray: payment_id,
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
                        } else if (!payment_id.length) {
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
