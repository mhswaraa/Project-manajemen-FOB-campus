<?php

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
        'deadline',
    ];

    public function user()
    {
        // relasi ke User melalui user_id
        return $this->belongsTo(User::class, 'user_id');
    }
}
