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
        'nik',           // Tambahkan ini
        'alamat',
        'amount',
        'gdrive_link', // <-- Tambahkan ini
        'mou_path', // Tambahkan kolom baru di sini
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

    public function investments()
    {
        return $this->hasMany(Investment::class, 'investor_id', 'investor_id');
    }

    public function payouts()
    {
        return $this->hasManyThrough(
            Payout::class,       // Model akhir yang ingin diakses
            Investment::class,   // Model perantara
            'investor_id',       // Foreign key di tabel perantara (investments)
            'investment_id',     // Foreign key di tabel akhir (payouts)
            'investor_id',       // Local key di tabel awal (investors)
            'id'                 // Local key di tabel perantara (investments)
        );
    }
}

