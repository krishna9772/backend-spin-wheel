<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = ['label', 'chance', 'stock', 'is_active'];

    public function spins()
    {
        return $this->hasMany(Spin::class);
    }
}
