<?php

namespace App\Http\Controllers;

use App\Models\PriceEquation;
use Illuminate\Http\Request;

class PriceEquationController extends Controller
{
    public function index()
    {
        $equation = PriceEquation::first();
        return response()->json([
            'status' => true,
            'data' => $equation,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'base_price' => 'required|numeric|min:0',
            'multiplier' => 'required|numeric|min:0',
        ]);

        $equation = PriceEquation::first();

        if (!$equation) {
            $equation = PriceEquation::create($request->only(['base_price', 'multiplier']));
        } else {
            $equation->update($request->only(['base_price', 'multiplier']));
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث المعادلة بنجاح',
            'data' => $equation,
        ]);
    }
}
