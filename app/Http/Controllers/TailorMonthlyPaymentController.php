<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tailor;
use App\Models\TailorMonthlyPayment;

class TailorMonthlyPaymentController extends Controller
{
    public function index(Request $request)
    {
        $starting_date = $request->get('starting_date', date('Y-m-01'));
        $ending_date = $request->get('ending_date', date('Y-m-d'));
        $tailor_id = $request->get('tailor_id', 0);

        $query = TailorMonthlyPayment::with('tailor')
            ->whereDate('payment_date', '>=', $starting_date)
            ->whereDate('payment_date', '<=', $ending_date)
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc');

        if ($tailor_id && $tailor_id != 0) {
            $query->where('tailor_id', $tailor_id);
        }

        $payments = $query->get();

        $total_amount = $payments->sum('amount');

        // Only monthly tailors for dropdown
        $monthly_tailors = Tailor::where('type', 'monthly')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('backend.tailor_monthly_payment.index', compact(
            'payments',
            'monthly_tailors',
            'starting_date',
            'ending_date',
            'tailor_id',
            'total_amount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tailor_id' => 'required|exists:tailors,id',
            'payment_date' => 'required|date',
            'month' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $tailor = Tailor::findOrFail($request->tailor_id);
        if ($tailor->type !== 'monthly') {
            return redirect()->back()->withErrors('Selected tailor is not a monthly basis tailor.');
        }

        $reference_no = 'tmp-' . date('Ymd') . '-' . date('His') . '-' . rand(10, 99);

        TailorMonthlyPayment::create([
            'reference_no' => $reference_no,
            'tailor_id' => $tailor->id,
            'payment_date' => $request->payment_date,
            'month' => $request->month,
            'amount' => (float)$request->amount,
            'note' => $request->note,
        ]);

        return redirect()->route('tailor_monthly_payments.index')->with('message', __('db.Monthly payment added successfully') ?? 'Monthly payment added successfully');
    }

    public function update(Request $request, string $id)
    {
        $payment = TailorMonthlyPayment::findOrFail($id);

        $request->validate([
            'tailor_id' => 'required|exists:tailors,id',
            'payment_date' => 'required|date',
            'month' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $payment->update([
            'tailor_id' => $request->tailor_id,
            'payment_date' => $request->payment_date,
            'month' => $request->month,
            'amount' => (float)$request->amount,
            'note' => $request->note,
        ]);

        return redirect()->route('tailor_monthly_payments.index')->with('message', __('db.Monthly payment updated successfully') ?? 'Monthly payment updated successfully');
    }

    public function destroy(string $id)
    {
        $payment = TailorMonthlyPayment::findOrFail($id);
        $payment->delete();

        return redirect()->route('tailor_monthly_payments.index')->with('message', __('db.Monthly payment deleted successfully') ?? 'Monthly payment deleted successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $payment_id = $request['paymentIdArray'] ?? [];
        foreach ($payment_id as $id) {
            $payment = TailorMonthlyPayment::find($id);
            if ($payment) {
                $payment->delete();
            }
        }
        return 'Monthly payment record(s) deleted successfully!';
    }
}
