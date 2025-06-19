<?php
// Path: app/Models/Tailor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tailor extends Model
{
    // Tambahkan baris ini:
    protected $table = 'penjahits';

    protected $primaryKey = 'tailor_id';
    protected $fillable   = [
        'user_id',
        'address',
        'phone',
        'email',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignments()
    {
        return $this->hasMany(ProjectTailor::class, 'tailor_id', 'tailor_id');
    }
}
