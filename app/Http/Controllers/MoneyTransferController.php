<?php

namespace App\Http\Controllers;

use App\Models\MoneyTransfer;
use App\Models\Account;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;

class MoneyTransferController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if (Auth::user()->role_id <= 2 || ($role && $role->hasPermissionTo('money-transfer'))) {
            $lims_money_transfer_all = MoneyTransfer::with(['fromAccount', 'toAccount'])
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->get();
            $lims_account_list = Account::where('is_active', true)->get();
            return view('backend.money_transfer.index', compact('lims_money_transfer_all', 'lims_account_list'));
        }
        else
            return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['reference_no'] = 'mtr-' . date("Ymd") . '-'. date("his");
        if (isset($data['created_at']) && !empty($data['created_at'])) {
            $data['created_at'] = function_exists('normalize_to_sql_datetime')
                ? normalize_to_sql_datetime($data['created_at'])
                : date('Y-m-d H:i:s', strtotime($data['created_at']));
        }
        MoneyTransfer::create($data);
        return redirect()->back()->with('message', __('db.Money transfered successfully'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        if (isset($data['created_at']) && !empty($data['created_at'])) {
            $data['created_at'] = function_exists('normalize_to_sql_datetime')
                ? normalize_to_sql_datetime($data['created_at'])
                : date('Y-m-d H:i:s', strtotime($data['created_at']));
        }
        MoneyTransfer::find($data['id'])->update($data);
        return redirect()->back()->with('message', __('db.Money transfer updated successfully'));
    }

    public function destroy($id)
    {
        MoneyTransfer::find($id)->delete();
        return redirect()->back()->with('not_permitted', __('db.Data deleted successfully'));
    }
}
