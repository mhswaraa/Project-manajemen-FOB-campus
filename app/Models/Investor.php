<?php
// Path: app/Models/Investor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini

class Investor extends Model
{
    use HasFactory; // Tambahkan ini jika belum ada

    protected $primaryKey = 'investor_id';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'amount',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'date',
    ];

    /**
     * Relasi ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke semua investasi yang dimiliki oleh investor ini.
     * INI ADALAH PERBAIKAN UNTUK ERROR SAAT INI.
     */
    public function investments()
    {
        return $this->hasMany(Investment::class, 'investor_id', 'investor_id');
    }
}

