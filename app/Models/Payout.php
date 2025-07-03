<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Disesuaikan agar HANYA berisi kolom yang diisi oleh PayoutController.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'investment_id',
        'amount',
        'paid_at',
        'receipt_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'paid_at' => 'datetime',
    ];

    /**
     * Mendefinisikan relasi "milik" ke model Investment.
     * Ini adalah satu-satunya relasi yang kita butuhkan saat ini.
     */
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}