@extends('backend.layout.main') @section('content')

<x-success-message key="message" />
<x-error-message key="not_permitted" />

<section>
    <div class="container-fluid">
        <button class="btn btn-info" data-toggle="modal" data-target="#create-capital-deposit-modal"><i class="dripicons-plus"></i> {{__('db.Add Capital Deposit')}}</button>
    </div>
    <div class="table-responsive">
        <table id="capital-deposit-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{__('db.date')}}</th>
                    <th>{{__('db.Reference No')}}</th>
                    <th>{{__('db.Account')}}</th>
                    <th>{{__('db.Amount')}}</th>
                    <th>{{__('db.Note')}}</th>
                    <th class="not-exported">{{__('db.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_capital_deposit_all as $key=>$capital_deposit)
                <tr data-id="{{$capital_deposit->id}}">
                    <td>{{$key}}</td>
                    <td>{{date($general_setting->date_format, strtotime($capital_deposit->created_at->toDateString())) . ' '. $capital_deposit->created_at->toTimeString() }}</td>
                    <td>{{ $capital_deposit->reference_no }}</td>
                    <td>{{ $capital_deposit->account ? $capital_deposit->account->name . ' [' . $capital_deposit->account->account_no . ']' : 'N/A' }}</td>
                    <td>{{ number_format((float)$capital_deposit->amount, $general_setting->decimal, '.', '')}}</td>
                    <td>{{ $capital_deposit->note }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{__('db.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" id="edit-btn" 
                                        data-id="{{$capital_deposit->id}}" 
                                        data-created_at="{{date(config('date_format'), strtotime($capital_deposit->created_at->toDateString()))}}" 
                                        data-account_id="{{$capital_deposit->account_id}}" 
                                        data-amount="{{$capital_deposit->amount}}" 
                                        data-note="{{$capital_deposit->note}}" 
                                        class="btn btn-link" data-toggle="modal" data-target="#edit-capital-deposit-modal">
                                        <i class="dripicons-document-edit"></i> {{__('db.edit')}}
                                    </button>
                                </li>
                                <li class="divider"></li>
                                {{ Form::open(['route' => ['capital-deposits.destroy', $capital_deposit->id], 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{__('db.delete')}}</button>
                                </li>
                                {{ Form::close() }}
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="tfoot active">
                <th></th>
                <th>{{__('db.Total')}}</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
        </table>
    </div>
</section>

<!-- Create Capital Deposit modal -->
<div id="create-capital-deposit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{__('db.Add Capital Deposit')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
              <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                {!! Form::open(['route' => 'capital-deposits.store', 'method' => 'post']) !!}
                  <div class="row">
                      <div class="col-md-6 form-group">
                          <label>{{__('db.date')}} *</label>
                          <input type="text" name="created_at" class="form-control date" placeholder="{{__('db.Choose date')}}" value="{{date($general_setting->date_format, strtotime('now'))}}" required />
                      </div>

                      <div class="col-md-6 form-group">
                          <label> {{__('db.Account')}} *</label>
                          <select class="form-control selectpicker" name="account_id" data-live-search="true" data-live-search-style="begins" title="Select account..." required>
                          @foreach($lims_account_list as $account)
                              @if($account->is_default)
                              <option selected value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                              @else
                              <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                              @endif
                          @endforeach
                          </select>
                      </div>

                      <div class="col-md-12 form-group">
                          <label>{{__('db.Amount')}} *</label>
                          <input type="number" name="amount" class="form-control" step="any" min="0.01" placeholder="0.00" required>
                      </div>

                      <div class="col-md-12 form-group">
                          <label>{{__('db.Note')}}</label>
                          <textarea name="note" rows="3" class="form-control" placeholder="মালিকের ব্যক্তিগত তহবিল জমা"></textarea>
                      </div>
                  </div>
                  <div class="form-group">
                      <button type="submit" class="btn btn-primary">{{__('db.submit')}}</button>
                  </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

<!-- Edit Capital Deposit modal -->
<div id="edit-capital-deposit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{__('db.Update Capital Deposit')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                {!! Form::open(['route' => ['capital-deposits.update', 1], 'method' => 'put']) !!}
                  <div class="row">
                        <input type="hidden" name="id">
                        <div class="col-md-6 form-group">
                            <label>{{__('db.date')}} *</label>
                            <input type="text" name="created_at" class="form-control date" placeholder="{{__('db.Choose date')}}" required />
                        </div>

                      <div class="col-md-6 form-group">
                          <label> {{__('db.Account')}} *</label>
                          <select class="form-control selectpicker" name="account_id" data-live-search="true" data-live-search-style="begins" title="Select account..." required>
                          @foreach($lims_account_list as $account)
                              <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                          @endforeach
                          </select>
                      </div>

                      <div class="col-md-12 form-group">
                          <label>{{__('db.Amount')}} *</label>
                          <input type="number" name="amount" class="form-control" step="any" min="0.01" required>
                      </div>

                      <div class="col-md-12 form-group">
                          <label>{{__('db.Note')}}</label>
                          <textarea name="note" rows="3" class="form-control"></textarea>
                      </div>
                  </div>
                  <div class="form-group">
                      <button type="submit" class="btn btn-primary">{{__('db.submit')}}</button>
                  </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">

    $("ul#account").siblings('a').attr('aria-expanded','true');
    $("ul#account").addClass("show");
    $("ul#account #capital-deposit-menu").addClass("active");

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click', '#edit-btn', function() {
        $("#edit-capital-deposit-modal input[name='created_at']").val($(this).data('created_at'));
        $("#edit-capital-deposit-modal select[name='account_id']").val($(this).data('account_id'));
        $("#edit-capital-deposit-modal input[name='id']").val($(this).data('id'));
        $("#edit-capital-deposit-modal input[name='amount']").val($(this).data('amount'));
        $("#edit-capital-deposit-modal textarea[name='note']").val($(this).data('note'));
        $('.selectpicker').selectpicker('refresh');
    });

    function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }

    $('#capital-deposit-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{__("db.records per page")}}',
             "info":      '<small>{{__("db.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{__("db.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 6]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
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
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }
    } );

    function datatable_sum(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();
            $( dt_selector.column( 4 ).footer() ).html(dt_selector.cells( rows, 4, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
        else {
            $( dt_selector.column( 4 ).footer() ).html(dt_selector.column( 4, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
    }

</script>
@endpush
