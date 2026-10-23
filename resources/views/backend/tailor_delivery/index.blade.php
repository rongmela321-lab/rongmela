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
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-shopping-bag" style="color: #733686; font-size: 26px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number">{{ number_format($total_delivery_amount, 2) }}</div>
                        <div class="name"><strong style="color: #733686">{{ __('db.Total Delivery Amount') }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-hand-holding-usd" style="color: #297ff9; font-size: 26px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number">{{ number_format($total_owner_get, 2) }}</div>
                        <div class="name"><strong style="color: #297ff9">{{ __('db.Total Owner Share') }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-check-circle" style="color: #00c689; font-size: 26px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number">{{ number_format($total_paid_amount, 2) }}</div>
                        <div class="name"><strong style="color: #00c689">{{ __('db.Total Collected') }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="wrapper count-title d-flex align-items-center">
                    <div class="icon"><i class="fa fa-exclamation-triangle" style="color: #e03b3b; font-size: 26px;"></i></div>
                    <div class="ml-3">
                        <div class="count-number">{{ number_format($total_due_amount, 2) }}</div>
                        <div class="name"><strong style="color: #e03b3b">{{ __('db.Total Due') }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h4 class="mb-0 font-weight-bold"><i class="fa fa-filter"></i> {{ __('db.Filter Deliveries') }}</h4>
            </div>
            <div class="card-body">
                {!! Form::open(['route' => 'tailor_deliveries.index', 'method' => 'get', 'id' => 'delivery-filter-form']) !!}
                <div class="row align-items-end">
                    <!-- Tailor Filter -->
                    <div class="col-md-3 col-sm-6 form-group">
                        <label><strong>{{ __('db.Select Tailor') }}</strong></label>
                        <select name="tailor_id" class="form-control selectpicker" data-live-search="true">
                            <option value="0">{{ __('db.All Percentage Tailors') }}</option>
                            @foreach($percentage_tailors as $t)
                                <option value="{{ $t->id }}" {{ $tailor_id == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ number_format($t->percentage, 2) }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range Picker (like Dashboard) -->
                    <div class="col-md-4 col-sm-6 form-group">
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

                    <!-- Payment Status Filter -->
                    <div class="col-md-3 col-sm-6 form-group">
                        <label><strong>{{ __('db.Payment Status') }}</strong></label>
                        <select name="payment_status" class="form-control selectpicker">
                            <option value="all" {{ $payment_status == 'all' ? 'selected' : '' }}>{{ __('db.All Status') }}</option>
                            <option value="paid" {{ $payment_status == 'paid' ? 'selected' : '' }}>{{ __('db.Paid') }}</option>
                            <option value="partial" {{ $payment_status == 'partial' ? 'selected' : '' }}>{{ __('db.Partial') }}</option>
                            <option value="due" {{ $payment_status == 'due' ? 'selected' : '' }}>{{ __('db.Due') }}</option>
                        </select>
                    </div>

                    <!-- Submit & Reset -->
                    <div class="col-md-2 col-sm-6 form-group">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> {{ __('db.Filter') }}</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        <!-- Deliveries Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="font-weight-bold mb-2"><i class="fa fa-truck"></i> {{ __('db.Delivery List') }}</h3>
                <button class="btn btn-info btn-sm mb-2" data-toggle="modal" data-target="#add-delivery-modal">
                    <i class="dripicons-plus"></i> {{ __('db.Add Delivery') }}
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="delivery-table" class="table table-hover table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="not-exported"></th>
                                <th>{{ __('db.date') }}</th>
                                <th>{{ __('db.reference') }}</th>
                                <th>{{ __('db.Tailor') }}</th>
                                <th>{{ __('db.Delivery Amount') }}</th>
                                <th>{{ __('db.Percentage') }}</th>
                                <th>{{ __('db.Owner Get') }}</th>
                                <th>{{ __('db.Paid Amount') }}</th>
                                <th>{{ __('db.Due Amount') }}</th>
                                <th>{{ __('db.Status') }}</th>
                                <th>{{ __('db.Note') }}</th>
                                <th class="not-exported">{{ __('db.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deliveries as $delivery)
                            <tr data-id="{{ $delivery->id }}">
                                <td></td>
                                <td>{{ date(config('date_format') ?? 'Y-m-d', strtotime($delivery->delivery_date)) }}</td>
                                <td><span class="badge badge-light font-weight-bold">{{ $delivery->reference_no }}</span></td>
                                <td><strong>{{ $delivery->tailor->name ?? 'N/A' }}</strong></td>
                                <td class="font-weight-bold">{{ number_format($delivery->delivery_amount, 2) }}</td>
                                <td><span class="badge badge-primary">{{ number_format($delivery->percentage, 2) }}%</span></td>
                                <td class="text-primary font-weight-bold">{{ number_format($delivery->owner_get, 2) }}</td>
                                <td class="text-success font-weight-bold">{{ number_format($delivery->paid_amount, 2) }}</td>
                                <td class="text-danger font-weight-bold">
                                    @if($delivery->due_amount > 0)
                                        {{ number_format($delivery->due_amount, 2) }}
                                    @else
                                        <span class="text-muted">0.00</span>
                                    @endif
                                </td>
                                <td>
                                    @if($delivery->payment_status === 'paid')
                                        <span class="badge badge-success p-1"><i class="fa fa-check"></i> {{ __('db.Paid') }}</span>
                                    @elseif($delivery->payment_status === 'partial')
                                        <span class="badge badge-warning p-1 text-dark"><i class="fa fa-clock-o"></i> {{ __('db.Partial') }}</span>
                                    @else
                                        <span class="badge badge-danger p-1"><i class="fa fa-times-circle"></i> {{ __('db.Due') }}</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $delivery->note ?? '-' }}</small></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ __('db.action') }}
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                            @if($delivery->due_amount > 0)
                                             <li>
                                                <button type="button" 
                                                    class="add-payment-btn btn btn-link text-success"
                                                    data-id="{{ $delivery->id }}"
                                                    data-ref="{{ $delivery->reference_no }}"
                                                    data-tailor="{{ $delivery->tailor->name ?? '' }}"
                                                    data-owner-get="{{ $delivery->owner_get }}"
                                                    data-paid="{{ $delivery->paid_amount }}"
                                                    data-due="{{ $delivery->due_amount }}"
                                                    data-toggle="modal" 
                                                    data-target="#add-payment-modal">
                                                    <i class="fa fa-plus-circle text-success"></i> {{ __('db.Add Payment') }}
                                                </button>
                                            </li>
                                            @endif
                                            <li>
                                                <button type="button" 
                                                    class="view-payments-btn btn btn-link"
                                                    data-id="{{ $delivery->id }}"
                                                    data-ref="{{ $delivery->reference_no }}"
                                                    data-toggle="modal" 
                                                    data-target="#view-payments-modal">
                                                    <i class="dripicons-document"></i> {{ __('db.Payment History') }}
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" 
                                                    class="edit-delivery-btn btn btn-link"
                                                    data-id="{{ $delivery->id }}"
                                                    data-tailor-id="{{ $delivery->tailor_id }}"
                                                    data-delivery-date="{{ $delivery->delivery_date->format('Y-m-d') }}"
                                                    data-delivery-amount="{{ $delivery->delivery_amount }}"
                                                    data-percentage="{{ $delivery->percentage }}"
                                                    data-owner-get="{{ $delivery->owner_get }}"
                                                    data-paid="{{ $delivery->paid_amount }}"
                                                    data-due="{{ $delivery->due_amount }}"
                                                    data-note="{{ $delivery->note }}"
                                                    data-toggle="modal" 
                                                    data-target="#edit-delivery-modal">
                                                    <i class="dripicons-document-edit"></i> {{ __('db.edit') }}
                                                </button>
                                            </li>
                                            <li>
                                                {!! Form::open(['route' => ['tailor_deliveries.destroy', $delivery->id], 'method' => 'DELETE', 'onsubmit' => 'return confirmDelete()']) !!}
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
                                <th colspan="4" class="text-right">{{ __('db.Total') }}:</th>
                                <th class="text-dark">{{ number_format($total_delivery_amount, 2) }}</th>
                                <th></th>
                                <th class="text-primary">{{ number_format($total_owner_get, 2) }}</th>
                                <th class="text-success">{{ number_format($total_paid_amount, 2) }}</th>
                                <th class="text-danger">{{ number_format($total_due_amount, 2) }}</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Delivery Modal -->
<div id="add-delivery-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-lg">
        <div class="modal-content">
            {!! Form::open(['route' => 'tailor_deliveries.store', 'method' => 'post', 'id' => 'add-delivery-form']) !!}
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fa fa-truck"></i> {{ __('db.Add Delivery') }}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>{{ __('db.The field labels marked with * are required input fields') }}.</small></p>
                <div class="row">
                    <!-- Tailor Select (Only Percentage Tailors) -->
                    <div class="col-md-6 form-group">
                        <label><strong>{{ __('db.Tailor') }} *</strong></label>
                        <select name="tailor_id" id="add-del-tailor" class="form-control selectpicker" required data-live-search="true" title="Select Percentage Tailor...">
                            @foreach($percentage_tailors as $t)
                                <option value="{{ $t->id }}" data-percentage="{{ $t->percentage }}">
                                    {{ $t->name }} ({{ number_format($t->percentage, 2) }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Delivery Date -->
                    <div class="col-md-6 form-group">
                        <label><strong>{{ __('db.Delivery Date') }} *</strong></label>
                        <input type="date" name="delivery_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Delivery Amount -->
                    <div class="col-md-4 form-group">
                        <label><strong>{{ __('db.Delivery Amount') }} *</strong></label>
                        <input type="number" step="0.01" min="0" name="delivery_amount" id="add-del-amount" required class="form-control" placeholder="e.g. 1000">
                        <small class="form-text text-muted">{{ __('db.Total delivery bill amount') }}</small>
                    </div>

                    <!-- Percentage -->
                    <div class="col-md-4 form-group">
                        <label><strong>{{ __('db.Percentage') }} *</strong></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" name="percentage" id="add-del-percentage" required class="form-control" placeholder="25">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ __('db.Tailor agreed percentage') }}</small>
                    </div>

                    <!-- Owner Get (Auto Calculated) -->
                    <div class="col-md-4 form-group">
                        <label><strong>{{ __('db.Owner Get') }} *</strong></label>
                        <input type="number" step="0.01" min="0" name="owner_get" id="add-del-owner-get" required class="form-control font-weight-bold text-primary bg-light" placeholder="0.00">
                        <small class="form-text text-muted">{{ __('db.Total amount due to owner based on percentage') }}</small>
                    </div>

                    <!-- Paid Amount -->
                    <div class="col-md-6 form-group">
                        <label><strong>{{ __('db.Paid Amount') }} *</strong></label>
                        <input type="number" step="0.01" min="0" name="paid_amount" id="add-del-paid" class="form-control font-weight-bold text-success" placeholder="0.00">
                        <small class="form-text text-muted">{{ __('db.Amount paid immediately by tailor') }}</small>
                    </div>

                    <!-- Due Amount (Readonly) -->
                    <div class="col-md-6 form-group">
                        <label><strong>{{ __('db.Due Amount') }}</strong></label>
                        <input type="number" step="0.01" readonly id="add-del-due" class="form-control font-weight-bold text-danger bg-light" value="0.00">
                        <small class="form-text text-muted">{{ __('db.Remaining due from owner share') }}</small>
                    </div>

                    <!-- Note -->
                    <div class="col-md-12 form-group">
                        <label><strong>{{ __('db.Note') }}</strong></label>
                        <textarea name="note" rows="2" class="form-control" placeholder="Delivery details, order reference, customer info..."></textarea>
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

<!-- Edit Delivery Modal -->
<div id="edit-delivery-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="edit-delivery-form" method="post">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold"><i class="fa fa-truck"></i> {{ __('db.Edit Delivery') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label><strong>{{ __('db.Tailor') }} *</strong></label>
                            <select name="tailor_id" id="edit-del-tailor" class="form-control selectpicker" required data-live-search="true">
                                @foreach($percentage_tailors as $t)
                                    <option value="{{ $t->id }}" data-percentage="{{ $t->percentage }}">
                                        {{ $t->name }} ({{ number_format($t->percentage, 2) }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label><strong>{{ __('db.Delivery Date') }} *</strong></label>
                            <input type="date" name="delivery_date" id="edit-del-date" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label><strong>{{ __('db.Delivery Amount') }} *</strong></label>
                            <input type="number" step="0.01" min="0" name="delivery_amount" id="edit-del-amount" required class="form-control">
                        </div>
                        <div class="col-md-4 form-group">
                            <label><strong>{{ __('db.Percentage') }} *</strong></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="percentage" id="edit-del-percentage" required class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label><strong>{{ __('db.Owner Get') }} *</strong></label>
                            <input type="number" step="0.01" min="0" name="owner_get" id="edit-del-owner-get" required class="form-control font-weight-bold text-primary">
                        </div>
                        <div class="col-md-6 form-group">
                            <label><strong>{{ __('db.Paid Amount') }} *</strong></label>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="edit-del-paid" class="form-control font-weight-bold text-success" placeholder="0.00">
                            <small class="form-text text-muted">{{ __('db.Amount paid immediately by tailor') }}</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label><strong>{{ __('db.Due Amount') }}</strong></label>
                            <input type="number" step="0.01" readonly id="edit-del-due" class="form-control font-weight-bold text-danger bg-light" value="0.00">
                            <small class="form-text text-muted">{{ __('db.Remaining due from owner share') }}</small>
                        </div>
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.Note') }}</strong></label>
                            <textarea name="note" id="edit-del-note" rows="2" class="form-control"></textarea>
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

<!-- Add Payment Modal (Due Collection) -->
<div id="add-payment-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <form id="add-payment-form" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-success"><i class="fa fa-money"></i> {{ __('db.Add Payment for Due') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <!-- Delivery Info Box -->
                    <div class="alert alert-info py-2">
                        <div><strong>Reference:</strong> <span id="pay-ref"></span></div>
                        <div><strong>Tailor:</strong> <span id="pay-tailor"></span></div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>Owner Share: <strong id="pay-owner-get"></strong></span>
                            <span>Paid: <strong class="text-success" id="pay-already-paid"></strong></span>
                            <span>Due: <strong class="text-danger" id="pay-current-due"></strong></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.Paying Amount') }} *</strong></label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="paying-amount-input" required class="form-control font-weight-bold text-success" placeholder="0.00">
                            <small class="form-text text-muted">{{ __('db.Enter amount up to total due') }}</small>
                        </div>
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.Payment Date') }} *</strong></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-12 form-group">
                            <label><strong>{{ __('db.Note') }}</strong></label>
                            <textarea name="note" rows="2" class="form-control" placeholder="e.g. Due cleared / Remaining payment..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('db.close') }}</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> {{ __('db.Save Payment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Payment History Modal -->
<div id="view-payments-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fa fa-history"></i> {{ __('db.Payment History') }} - <span id="history-ref"></span></h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="payment-history-table">
                        <thead>
                            <tr class="bg-light">
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount (TK)</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody id="payment-history-body">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('db.close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#tailor").siblings('a').attr('aria-expanded','true');
    $("ul#tailor").addClass("show");
    $("ul#tailor #tailor-delivery-menu").addClass("active");

    function confirmDelete() {
        return confirm("{{ __('db.Are you sure want to delete?') }}");
    }

    $(document).ready(function() {
        // Initialize DateRangePicker (identical to Dashboard)
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

        // ============================================
        // ADD DELIVERY: Auto calculation logic
        // ============================================
        // 1. When Tailor is selected -> auto fill percentage
        $('#add-del-tailor').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var percentage = parseFloat(selectedOption.data('percentage')) || 0;
            $('#add-del-percentage').val(percentage);
            calculateAddDelivery();
        });

        // 2. When Delivery Amount or Percentage is typed -> auto calculate Owner Get & Due
        $('#add-del-amount, #add-del-percentage').on('input', function() {
            calculateAddDelivery();
        });

        // 3. When Owner Get is manually adjusted
        $('#add-del-owner-get').on('input', function() {
            var ownerGet = parseFloat($('#add-del-owner-get').val()) || 0;
            var paid = parseFloat($('#add-del-paid').val()) || 0;
            var due = Math.max(0, ownerGet - paid);
            $('#add-del-due').val(due.toFixed(2));
        });

        // 4. When Paid Amount is entered -> recalculate Due
        $('#add-del-paid').on('input', function() {
            var ownerGet = parseFloat($('#add-del-owner-get').val()) || 0;
            var paid = parseFloat($(this).val()) || 0;
            var due = Math.max(0, ownerGet - paid);
            $('#add-del-due').val(due.toFixed(2));
        });

        function calculateAddDelivery() {
            var amount = parseFloat($('#add-del-amount').val()) || 0;
            var percentage = parseFloat($('#add-del-percentage').val()) || 0;
            var ownerGet = (amount * percentage) / 100;
            $('#add-del-owner-get').val(ownerGet.toFixed(2));

            // Default paid amount to ownerGet if user hasn't explicitly set it or wants full payment
            if (!$('#add-del-paid').data('user-changed')) {
                $('#add-del-paid').val(ownerGet.toFixed(2));
                $('#add-del-due').val('0.00');
            } else {
                var paid = parseFloat($('#add-del-paid').val()) || 0;
                var due = Math.max(0, ownerGet - paid);
                $('#add-del-due').val(due.toFixed(2));
            }
        }

        $('#add-del-paid').on('focus', function() {
            $(this).data('user-changed', true);
        });

        // ============================================
        // EDIT DELIVERY
        // ============================================
        $('.edit-delivery-btn').on('click', function() {
            var id = $(this).data('id');
            var tailorId = $(this).data('tailor-id');
            var date = $(this).data('delivery-date');
            var amount = $(this).data('delivery-amount');
            var percentage = $(this).data('percentage');
            var ownerGet = $(this).data('owner-get');
            var paid = $(this).data('paid');
            var due = $(this).data('due');
            var note = $(this).data('note');

            var actionUrl = '{{ url("tailor_deliveries") }}/' + id;
            $('#edit-delivery-form').attr('action', actionUrl);

            $('#edit-del-tailor').val(tailorId).selectpicker('refresh');
            $('#edit-del-date').val(date);
            $('#edit-del-amount').val(amount);
            $('#edit-del-percentage').val(percentage);
            $('#edit-del-owner-get').val(ownerGet);
            $('#edit-del-paid').val(paid);
            $('#edit-del-due').val(due);
            $('#edit-del-note').val(note);
        });

        $('#edit-del-tailor').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var percentage = parseFloat(selectedOption.data('percentage')) || 0;
            $('#edit-del-percentage').val(percentage);
            calculateEditDelivery();
        });

        $('#edit-del-amount, #edit-del-percentage').on('input', function() {
            calculateEditDelivery();
        });

        $('#edit-del-owner-get').on('input', function() {
            var ownerGet = parseFloat($('#edit-del-owner-get').val()) || 0;
            var paid = parseFloat($('#edit-del-paid').val()) || 0;
            var due = Math.max(0, ownerGet - paid);
            $('#edit-del-due').val(due.toFixed(2));
        });

        $('#edit-del-paid').on('input', function() {
            var ownerGet = parseFloat($('#edit-del-owner-get').val()) || 0;
            var paid = parseFloat($(this).val()) || 0;
            var due = Math.max(0, ownerGet - paid);
            $('#edit-del-due').val(due.toFixed(2));
        });

        function calculateEditDelivery() {
            var amount = parseFloat($('#edit-del-amount').val()) || 0;
            var percentage = parseFloat($('#edit-del-percentage').val()) || 0;
            var ownerGet = (amount * percentage) / 100;
            $('#edit-del-owner-get').val(ownerGet.toFixed(2));

            var paid = parseFloat($('#edit-del-paid').val()) || 0;
            var due = Math.max(0, ownerGet - paid);
            $('#edit-del-due').val(due.toFixed(2));
        }

        // ============================================
        // ADD PAYMENT (DUE COLLECTION)
        // ============================================
        $('.add-payment-btn').on('click', function() {
            var id = $(this).data('id');
            var ref = $(this).data('ref');
            var tailor = $(this).data('tailor');
            var ownerGet = parseFloat($(this).data('owner-get')).toFixed(2);
            var paid = parseFloat($(this).data('paid')).toFixed(2);
            var due = parseFloat($(this).data('due')).toFixed(2);

            var actionUrl = '{{ url("tailor_deliveries") }}/' + id + '/add-payment';
            $('#add-payment-form').attr('action', actionUrl);

            $('#pay-ref').text(ref);
            $('#pay-tailor').text(tailor);
            $('#pay-owner-get').text(ownerGet);
            $('#pay-already-paid').text(paid);
            $('#pay-current-due').text(due);

            $('#paying-amount-input').val(due).attr('max', due);
        });

        // ============================================
        // VIEW PAYMENT HISTORY
        // ============================================
        $('.view-payments-btn').on('click', function() {
            var id = $(this).data('id');
            var ref = $(this).data('ref');
            $('#history-ref').text(ref);
            $('#payment-history-body').html('<tr><td colspan="4" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');

            $.get('{{ url("tailor_deliveries") }}/' + id + '/payments', function(res) {
                var html = '';
                if (res.payments && res.payments.length > 0) {
                    $.each(res.payments, function(i, p) {
                        html += '<tr>' +
                            '<td>' + (i + 1) + '</td>' +
                            '<td>' + p.payment_date + '</td>' +
                            '<td class="font-weight-bold text-success">' + parseFloat(p.amount).toFixed(2) + '</td>' +
                            '<td>' + (p.note ? p.note : '-') + '</td>' +
                            '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center text-muted">No payment records found</td></tr>';
                }
                $('#payment-history-body').html(html);
            });
        });

        // ============================================
        // DataTable Initialization
        // ============================================
        $('#delivery-table').DataTable({
            'language': {
                'lengthMenu': '_MENU_ {{ __('db.records per page') }}',
                "info": '<small>{{ __('db.Showing') }} _START_ - _END_ (_TOTAL_)</small>',
                "search": '{{ __('db.Search') }}',
                'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
            order: [['1', 'desc']],
            'columnDefs': [
                {
                    "orderable": false,
                    'targets': [0, 11]
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
                        var delivery_id = [];
                        $(':checkbox:checked').each(function(i) {
                            if (i) {
                                var id = $(this).closest('tr').data('id');
                                if (id) delivery_id.push(id);
                            }
                        });
                        if (delivery_id.length && confirm("{{ __('db.Are you sure want to delete?') }}")) {
                            $.ajax({
                                type: 'POST',
                                url: '{{ route("tailor_deliveries.deletebyselection") }}',
                                data: {
                                    deliveryIdArray: delivery_id,
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
                        } else if (!delivery_id.length) {
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
