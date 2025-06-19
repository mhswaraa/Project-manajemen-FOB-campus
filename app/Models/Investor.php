<?php
// Path: app/Models/Investor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;  // jangan lupa import User model

class Investor extends Model
{
    protected $primaryKey = 'investor_id';

    // Tambahkan 'user_id' di sini:
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'amount',
        'registered_at',
    ];

    public function user()
    {
        // relasi ke User melalui user_id
        return $this->belongsTo(User::class, 'user_id');
    }

    // Cast tanggal
protected $casts = [
    'registered_at' => 'date',
];
}
