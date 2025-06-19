<?php
// Path: app/Models/TailorProgress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TailorProgress extends Model
{
    protected $table = 'tailor_progress';
    protected $fillable = [
        'assignment_id',
        'date',
        'quantity_done',
        'notes',
    ];

    // Relasi ke ProjectTailor
    public function assignment()
    {
        return $this->belongsTo(ProjectTailor::class, 'assignment_id');
    }
}
