<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliverySetting;
use App\Models\District;
use App\Models\Division;
use Illuminate\Http\Request;

class DeliverySettingController extends Controller
{
    public function index(Request $request)
    {
        $deliverySetting = DeliverySetting::getSettings();
        $divisions = Division::with(['districts' => fn ($q) => $q->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.delivery.index', compact('deliverySetting', 'divisions'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'free_delivery_enabled' => 'nullable|boolean',
            'free_delivery_threshold' => 'required|numeric|min:0',
        ]);

        $setting = DeliverySetting::getSettings();
        $setting->update([
            'free_delivery_enabled' => $request->has('free_delivery_enabled'),
            'free_delivery_threshold' => (float) $data['free_delivery_threshold'],
        ]);

        return back()->with('success', 'Delivery & free shipping settings updated successfully.');
    }

    public function updateDistrictCharge(Request $request, District $district)
    {
        $data = $request->validate([
            'delivery_charge' => 'required|numeric|min:0',
            'status' => 'nullable|boolean',
        ]);

        $district->update([
            'delivery_charge' => (float) $data['delivery_charge'],
            'status' => $request->has('status'),
        ]);

        return back()->with('success', "Delivery charge for '{$district->name}' updated to ৳" . number_format($district->delivery_charge, 2) . '.');
    }

    public function bulkUpdateDistricts(Request $request)
    {
        $data = $request->validate([
            'charges' => 'required|array',
            'charges.*' => 'required|numeric|min:0',
        ]);

        foreach ($data['charges'] as $districtId => $charge) {
            District::where('id', $districtId)->update([
                'delivery_charge' => (float) $charge,
            ]);
        }

        return back()->with('success', 'District delivery charges updated successfully.');
    }
}
