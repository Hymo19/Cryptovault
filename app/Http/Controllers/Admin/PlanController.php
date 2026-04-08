<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('subscriptions')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'max_apps'          => 'required|integer|min:1',
            'max_ops_per_month' => 'required|integer|min:1',
            'price'             => 'required|numeric|min:0',
        ]);

        $plan->update($request->only('max_apps', 'max_ops_per_month', 'price'));

        return back()->with('success', 'Plan "' . $plan->name . '" mis à jour.');
    }
}