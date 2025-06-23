<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'tailor_id',
        'amount',
        'payment_date',
        'period_start',
        'period_end',
        'receipt_path', // <-- TAMBAHKAN INI
        'notes',
        'processed_by_user_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function tailor()
    {
        return $this->belongsTo(Tailor::class, 'tailor_id', 'tailor_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
}
