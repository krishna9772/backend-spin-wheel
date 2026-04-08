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
            // 1. Get ALL active rewards, regardless of stock
            $allRewards = Reward::where('is_active', 1)->lockForUpdate()->get();

            if ($allRewards->isEmpty()) {
                return response()->json(['error' => 'No rewards configured'], 400);
            }

            // 2. Roll the dice against the absolute totals
            $totalChance = $allRewards->sum('chance');
            $rand = mt_rand() / mt_getrandmax() * $totalChance;

            $cumulative = 0;
            $selected = null;

            foreach ($allRewards as $reward) {
                $cumulative += $reward->chance;
                if ($rand <= $cumulative) {
                    $selected = $reward;
                    break;
                }
            }
            $selected ??= $allRewards->first();

           if ($selected->stock <= 0) {
                // The item they landed on is empty. 
                // Randomly pick one of the default consolations
                $consolations = ['Happy Thingyan', 'Have a good day !'];
                $randomConsolation = $consolations[array_rand($consolations)];
                
                $selected = Reward::where('label', $randomConsolation)->first(); 
                
                if (!$selected) {
                    // Failsafe if you forgot to create the consolation prizes in the DB
                    throw new \Exception("System out of fallback rewards.");
                }
            } else {
                // Only deduct stock if it wasn't the fallback
                $selected->decrement('stock');
            }

            // 4. Log the win
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