<?php

namespace App\Http\Controllers;

use App\Models\DSSSettings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DSSSettingsController extends Controller
{
    /**
     * Display DSS settings page.
     */
    public function index(): View
    {
        $settings = DSSSettings::all();

        return view('dss.settings.index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update DSS settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'dead_stock_threshold_days' => 'required|integer|min:30|max:365',
            'slow_moving_threshold_days' => 'required|integer|min:30|max:365',
            'fast_moving_threshold_units' => 'required|integer|min:10|max:1000',
            'bundle_recommendation_enabled' => 'boolean',
            'promotion_recommendation_enabled' => 'boolean',
            'discount_recommendation_enabled' => 'boolean',
            'dss_analysis_enabled' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            DSSSettings::setSetting($key, $value);
        }

        return redirect()->back()->with('success', 'DSS settings updated successfully.');
    }

    /**
     * Get settings as JSON (for API).
     */
    public function getSettings()
    {
        return response()->json(DSSSettings::allSettings());
    }
}
