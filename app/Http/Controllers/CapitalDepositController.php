<?php

namespace App\Http\Controllers;

use App\Models\CapitalDeposit;
use App\Models\Account;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Auth;

class CapitalDepositController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if ($role && ($role->hasPermissionTo('account-index') || $role->hasPermissionTo('money-transfer') || Auth::user()->role_id <= 2)) {
            $lims_capital_deposit_all = CapitalDeposit::with('account')->orderBy('id', 'desc')->get();
            $lims_account_list = Account::where('is_active', true)->get();
            return view('backend.capital_deposit.index', compact('lims_capital_deposit_all', 'lims_account_list'));
        }

        return redirect()->back()->with('not_permitted', __('db.Sorry! You are not allowed to access this module'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $data = $request->all();
        $data['reference_no'] = 'cd-' . date("Ymd") . '-' . date("his");
        $data['user_id'] = Auth::id();
        $data['type'] = $request->input('type', 'deposit');

        if (isset($data['created_at']) && !empty($data['created_at'])) {
            $data['created_at'] = function_exists('normalize_to_sql_datetime')
                ? normalize_to_sql_datetime($data['created_at'])
                : date('Y-m-d H:i:s', strtotime($data['created_at']));
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        CapitalDeposit::create($data);
        return redirect()->back()->with('message', __('db.Capital deposited successfully'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $data = $request->all();
        $depositId = $data['id'] ?? $id;

        if (isset($data['created_at']) && !empty($data['created_at'])) {
            $data['created_at'] = function_exists('normalize_to_sql_datetime')
                ? normalize_to_sql_datetime($data['created_at'])
                : date('Y-m-d H:i:s', strtotime($data['created_at']));
        }

        $deposit = CapitalDeposit::findOrFail($depositId);
        $deposit->update($data);

        return redirect()->back()->with('message', __('db.Capital deposit updated successfully'));
    }

    public function destroy($id)
    {
        $deposit = CapitalDeposit::findOrFail($id);
        $deposit->delete();
        return redirect()->back()->with('not_permitted', __('db.Data deleted successfully'));
    }
}

