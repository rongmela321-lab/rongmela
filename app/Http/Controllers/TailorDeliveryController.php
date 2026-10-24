<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tailor;
use App\Models\TailorDelivery;
use App\Models\TailorDeliveryPayment;
use DB;

class TailorDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $starting_date = $request->get('starting_date', date('Y-m-01'));
        $ending_date = $request->get('ending_date', date('Y-m-d'));
        $tailor_id = $request->get('tailor_id', 0);
        $payment_status = $request->get('payment_status', 'all');

        $query = TailorDelivery::with(['tailor', 'payments'])
            ->whereDate('delivery_date', '>=', $starting_date)
            ->whereDate('delivery_date', '<=', $ending_date)
            ->orderBy('delivery_date', 'desc')
            ->orderBy('id', 'desc');

        if ($tailor_id && $tailor_id != 0) {
            $query->where('tailor_id', $tailor_id);
        }

        if ($payment_status && $payment_status !== 'all') {
            $query->where('payment_status', $payment_status);
        }

        $deliveries = $query->get();

        // Calculate Totals
        $total_delivery_amount = (float)$deliveries->sum('delivery_amount');
        $total_owner_get = (float)$deliveries->sum('owner_get');
        $total_tailor_get = max(0, $total_delivery_amount - $total_owner_get);
        $total_paid_amount = (float)$deliveries->sum('paid_amount');
        $total_due_amount = (float)$deliveries->sum('due_amount');

        // Only percentage tailors for dropdown
        $percentage_tailors = Tailor::where('type', 'percentage')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('backend.tailor_delivery.index', compact(
            'deliveries',
            'percentage_tailors',
            'starting_date',
            'ending_date',
            'tailor_id',
            'payment_status',
            'total_delivery_amount',
            'total_owner_get',
            'total_tailor_get',
            'total_paid_amount',
            'total_due_amount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tailor_id' => 'required|exists:tailors,id',
            'delivery_date' => 'required|date',
            'delivery_amount' => 'required|numeric|min:0',
            'percentage' => 'required|numeric|min:0|max:100',
            'owner_get' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $tailor = Tailor::findOrFail($request->tailor_id);
        if ($tailor->type !== 'percentage') {
            return redirect()->back()->withErrors('Selected tailor is not a percentage basis tailor.');
        }

        $delivery_amount = (float)$request->delivery_amount;
        $percentage = (float)$request->percentage;
        $owner_get = (float)($request->owner_get ?? (($delivery_amount * $percentage) / 100));
        $paid_amount = (float)($request->paid_amount ?? 0);
        $due_amount = max(0, $owner_get - $paid_amount);

        if ($paid_amount >= $owner_get && $owner_get > 0) {
            $payment_status = 'paid';
        } elseif ($paid_amount > 0) {
            $payment_status = 'partial';
        } else {
            $payment_status = 'due';
        }

        $reference_no = 'tdr-' . date('Ymd') . '-' . date('His') . '-' . rand(10, 99);

        DB::beginTransaction();
        try {
            $delivery = TailorDelivery::create([
                'reference_no' => $reference_no,
                'tailor_id' => $tailor->id,
                'delivery_date' => $request->delivery_date,
                'delivery_amount' => $delivery_amount,
                'percentage' => $percentage,
                'owner_get' => $owner_get,
                'paid_amount' => $paid_amount,
                'due_amount' => $due_amount,
                'payment_status' => $payment_status,
                'note' => $request->note,
            ]);

            if ($paid_amount > 0) {
                TailorDeliveryPayment::create([
                    'tailor_delivery_id' => $delivery->id,
                    'payment_date' => $request->delivery_date,
                    'amount' => $paid_amount,
                    'note' => 'Initial payment at delivery',
                ]);
            }

            DB::commit();
            return redirect()->route('tailor_deliveries.index')->with('message', __('db.Delivery recorded successfully') ?? 'Delivery recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        $delivery = TailorDelivery::findOrFail($id);

        $request->validate([
            'tailor_id' => 'required|exists:tailors,id',
            'delivery_date' => 'required|date',
            'delivery_amount' => 'required|numeric|min:0',
            'percentage' => 'required|numeric|min:0|max:100',
            'owner_get' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $delivery_amount = (float)$request->delivery_amount;
        $percentage = (float)$request->percentage;
        $owner_get = (float)$request->owner_get;
        $paid_amount = $request->has('paid_amount') ? (float)$request->paid_amount : (float)$delivery->paid_amount;
        $due_amount = max(0, $owner_get - $paid_amount);

        if ($paid_amount >= $owner_get && $owner_get > 0) {
            $payment_status = 'paid';
        } elseif ($paid_amount > 0) {
            $payment_status = 'partial';
        } else {
            $payment_status = 'due';
        }

        $delivery->update([
            'tailor_id' => $request->tailor_id,
            'delivery_date' => $request->delivery_date,
            'delivery_amount' => $delivery_amount,
            'percentage' => $percentage,
            'owner_get' => $owner_get,
            'paid_amount' => $paid_amount,
            'due_amount' => $due_amount,
            'payment_status' => $payment_status,
            'note' => $request->note,
        ]);

        return redirect()->route('tailor_deliveries.index')->with('message', __('db.Delivery updated successfully') ?? 'Delivery updated successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $delivery_id = $request['deliveryIdArray'] ?? [];
        foreach ($delivery_id as $id) {
            $delivery = TailorDelivery::find($id);
            if ($delivery) {
                $delivery->delete();
            }
        }
        return 'Delivery record(s) deleted successfully!';
    }

    public function addPayment(Request $request, string $id)
    {
        $delivery = TailorDelivery::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($delivery->due_amount + 0.01),
            'payment_date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $paying_amount = (float)$request->amount;

        DB::beginTransaction();
        try {
            TailorDeliveryPayment::create([
                'tailor_delivery_id' => $delivery->id,
                'payment_date' => $request->payment_date,
                'amount' => $paying_amount,
                'note' => $request->note,
            ]);

            $new_paid = $delivery->paid_amount + $paying_amount;
            $new_due = max(0, $delivery->owner_get - $new_paid);

            if ($new_paid >= $delivery->owner_get) {
                $status = 'paid';
            } elseif ($new_paid > 0) {
                $status = 'partial';
            } else {
                $status = 'due';
            }

            $delivery->update([
                'paid_amount' => $new_paid,
                'due_amount' => $new_due,
                'payment_status' => $status,
            ]);

            DB::commit();
            return redirect()->route('tailor_deliveries.index')->with('message', __('db.Payment added successfully') ?? 'Payment added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function getPayments(string $id)
    {
        $delivery = TailorDelivery::with(['tailor', 'payments'])->findOrFail($id);
        return response()->json([
            'delivery' => $delivery,
            'payments' => $delivery->payments,
        ]);
    }

    public function destroy(string $id)
    {
        $delivery = TailorDelivery::findOrFail($id);
        $delivery->delete();

        return redirect()->route('tailor_deliveries.index')->with('message', __('db.Delivery deleted successfully') ?? 'Delivery deleted successfully');
    }
}
