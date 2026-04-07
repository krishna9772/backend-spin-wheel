<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reward;

class RewardController extends Controller
{
    public function index()
    {
        return Reward::latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string',
            'chance' => 'required|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        return Reward::create($data);
    }

    public function update(Request $request, $id)
    {
        $reward = Reward::findOrFail($id);

        $reward->update($request->only([
            'label', 'chance', 'stock', 'is_active'
        ]));

        return $reward;
    }

    public function destroy($id)
    {
        Reward::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function refill(Request $request, $id)
    {
        $reward = Reward::findOrFail($id);
        $reward->increment('stock', $request->stock);

        return $reward;
    }
}
