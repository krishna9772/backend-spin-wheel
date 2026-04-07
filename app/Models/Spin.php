<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spin extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'reward_id', 'ip_address', 'spin_hash'];

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
}
