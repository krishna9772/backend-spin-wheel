<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\Spin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpinController extends Controller
{
    public function getAdminSpins()
    {
        // with('reward') attaches the reward data to every spin
        // latest() sorts them by newest first
        // take(100) prevents the page from crashing if you have thousands of spins
        $spins = Spin::with('reward')
                     ->latest()
                     ->take(100) 
                     ->get();

        return response()->json($spins);
    }

    public function spin(Request $request)
    {

        DB::beginTransaction();

        try {
            $rewards = Reward::where('is_active', 1)
                ->where('stock', '>', 0)
                ->lockForUpdate()
                ->get();

            if ($rewards->isEmpty()) {
                return response()->json(['error' => 'No rewards available'], 400);
            }

            $totalChance = $rewards->sum('chance');
            $rand = mt_rand() / mt_getrandmax() * $totalChance;

            $cumulative = 0;
            $selected = null;

            foreach ($rewards as $reward) {
                $cumulative += $reward->chance;
                if ($rand <= $cumulative) {
                    $selected = $reward;
                    break;
                }
            }

            $selected ??= $rewards->first();

            $selected->decrement('stock');
            $selected->increment('times_won');

            $spin = Spin::create([
                'user_id' => $request->user()?->id,
                'reward_id' => $selected->id,
                'ip_address' => $request->ip(),
                'spin_hash' => hash('sha256', uniqid('', true))
            ]);

            DB::commit();

            return response()->json([
                'reward' => $selected->label,
                'reward_id' => $selected->id,
                'spin_id' => $spin->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Spin failed', 'message' => $e->getMessage()], 500);
        }
    }
}