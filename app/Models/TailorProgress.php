<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TailorProgress extends Model
{
    use HasFactory;

    protected $table = 'tailor_progress';

    protected $fillable = [
        'assignment_id',
        'date',
        'quantity_done',
        'notes',
        'invoice_id',
    ];

    /**
     * INI ADALAH PERBAIKANNYA.
     * Secara otomatis mengubah kolom 'date' menjadi objek Carbon.
     */
    protected $casts = [
        'date' => 'date',
    ];

    public function assignment()
    {
        return $this->belongsTo(ProjectTailor::class, 'assignment_id');
    }
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
