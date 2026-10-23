<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tailor;
use App\Models\TailorDelivery;
use App\Models\TailorMonthlyPayment;
use Auth;

class TailorController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $starting_date = $request->get('starting_date', date('Y-m-01'));
        $ending_date = $request->get('ending_date', date('Y-m-d'));

        $query = Tailor::query();

        if ($type === 'percentage') {
            $query->where('type', 'percentage');
        } elseif ($type === 'monthly') {
            $query->where('type', 'monthly');
        }

        $query->withCount([
            'deliveries as period_deliveries_count' => function ($q) use ($starting_date, $ending_date) {
                $q->whereDate('delivery_date', '>=', $starting_date)
                  ->whereDate('delivery_date', '<=', $ending_date);
            },
            'monthlyPayments as period_monthly_payments_count' => function ($q) use ($starting_date, $ending_date) {
                $q->whereDate('payment_date', '>=', $starting_date)
                  ->whereDate('payment_date', '<=', $ending_date);
            },
            'deliveries as all_deliveries_count',
            'monthlyPayments as all_monthly_payments_count',
        ])
        ->withSum([
            'deliveries as period_delivery_paid' => function ($q) use ($starting_date, $ending_date) {
                $q->whereDate('delivery_date', '>=', $starting_date)
                  ->whereDate('delivery_date', '<=', $ending_date);
            }
        ], 'paid_amount')
        ->withSum([
            'deliveries as period_delivery_due' => function ($q) use ($starting_date, $ending_date) {
                $q->whereDate('delivery_date', '>=', $starting_date)
                  ->whereDate('delivery_date', '<=', $ending_date);
            }
        ], 'due_amount')
        ->withSum([
            'deliveries as period_delivery_amount' => function ($q) use ($starting_date, $ending_date) {
                $q->whereDate('delivery_date', '>=', $starting_date)
                  ->whereDate('delivery_date', '<=', $ending_date);
            }
        ], 'delivery_amount')
        ->withSum([
            'deliveries as period_owner_get' => function ($q) use ($starting_date, $ending_date) {
                $q->whereDate('delivery_date', '>=', $starting_date)
                  ->whereDate('delivery_date', '<=', $ending_date);
            }
        ], 'owner_get')
        ->withSum([
            'monthlyPayments as period_monthly_paid' => function ($q) use ($starting_date, $ending_date) {
                $q->whereDate('payment_date', '>=', $starting_date)
                  ->whereDate('payment_date', '<=', $ending_date);
            }
        ], 'amount')
        ->withSum('deliveries as all_delivery_paid', 'paid_amount')
        ->withSum('deliveries as all_delivery_due', 'due_amount')
        ->withSum('monthlyPayments as all_monthly_paid', 'amount')
        ->orderBy('id', 'desc');

        $tailors = $query->get();

        $total_count = Tailor::count();
        $percentage_count = Tailor::where('type', 'percentage')->count();
        $monthly_count = Tailor::where('type', 'monthly')->count();

        // Summary calculations for the filtered period
        $total_period_deliveries = (int)$tailors->where('type', 'percentage')->sum('period_deliveries_count');
        $total_period_payments = (int)$tailors->where('type', 'monthly')->sum('period_monthly_payments_count');

        $total_period_delivery_paid = (float)$tailors->where('type', 'percentage')->sum('period_delivery_paid');
        $total_period_monthly_paid = (float)$tailors->where('type', 'monthly')->sum('period_monthly_paid');
        $total_period_collection = $total_period_delivery_paid + $total_period_monthly_paid;

        $total_period_due = (float)$tailors->where('type', 'percentage')->sum('period_delivery_due');

        return view('backend.tailor.index', compact(
            'tailors',
            'type',
            'starting_date',
            'ending_date',
            'total_count',
            'percentage_count',
            'monthly_count',
            'total_period_deliveries',
            'total_period_payments',
            'total_period_collection',
            'total_period_due'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'phone_number' => 'nullable|string|max:50',
            'type' => 'required|in:monthly,percentage',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'monthly_rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
        ]);

        $data = $request->all();
        if ($data['type'] === 'percentage') {
            $data['percentage'] = $request->percentage ?? 0;
            $data['monthly_rate'] = 0;
        } else {
            $data['percentage'] = 0;
            $data['monthly_rate'] = $request->monthly_rate ?? 0;
        }
        $data['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;

        Tailor::create($data);

        return redirect()->route('tailors.index')->with('message', __('db.Tailor added successfully') ?? 'Tailor added successfully');
    }

    public function update(Request $request, string $id)
    {
        $tailor = Tailor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:191',
            'phone_number' => 'nullable|string|max:50',
            'type' => 'required|in:monthly,percentage',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'monthly_rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
        ]);

        $data = $request->all();
        if ($data['type'] === 'percentage') {
            $data['percentage'] = $request->percentage ?? 0;
            $data['monthly_rate'] = 0;
        } else {
            $data['percentage'] = 0;
            $data['monthly_rate'] = $request->monthly_rate ?? 0;
        }
        $data['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;

        $tailor->update($data);

        return redirect()->route('tailors.index')->with('message', __('db.Tailor updated successfully') ?? 'Tailor updated successfully');
    }

    public function destroy(string $id)
    {
        $tailor = Tailor::findOrFail($id);
        $tailor->delete();

        return redirect()->route('tailors.index')->with('message', __('db.Tailor deleted successfully') ?? 'Tailor deleted successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $tailor_id = $request['tailorIdArray'] ?? [];
        foreach ($tailor_id as $id) {
            $tailor = Tailor::find($id);
            if ($tailor) {
                $tailor->delete();
            }
        }
        return 'Tailor(s) deleted successfully!';
    }

    public function getPercentageTailors()
    {
        $tailors = Tailor::where('type', 'percentage')->where('is_active', true)->select('id', 'name', 'phone_number', 'percentage')->get();
        return response()->json($tailors);
    }

    public function getMonthlyTailors()
    {
        $tailors = Tailor::where('type', 'monthly')->where('is_active', true)->select('id', 'name', 'phone_number', 'monthly_rate')->get();
        return response()->json($tailors);
    }
}
