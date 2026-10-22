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

        $query = Tailor::withCount(['deliveries', 'monthlyPayments'])
            ->withSum('deliveries as total_delivery_amount', 'delivery_amount')
            ->withSum('deliveries as total_owner_get', 'owner_get')
            ->withSum('deliveries as total_delivery_paid', 'paid_amount')
            ->withSum('deliveries as total_delivery_due', 'due_amount')
            ->withSum('monthlyPayments as total_monthly_paid', 'amount')
            ->orderBy('id', 'desc');

        if ($type === 'percentage') {
            $query->where('type', 'percentage');
        } elseif ($type === 'monthly') {
            $query->where('type', 'monthly');
        }

        $tailors = $query->get();

        $total_count = Tailor::count();
        $percentage_count = Tailor::where('type', 'percentage')->count();
        $monthly_count = Tailor::where('type', 'monthly')->count();

        return view('backend.tailor.index', compact(
            'tailors',
            'type',
            'total_count',
            'percentage_count',
            'monthly_count'
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
