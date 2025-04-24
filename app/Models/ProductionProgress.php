<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ProductionProgress extends Model
{
    protected $table = 'production_progresses';

    protected $fillable = [
        'project_id',
        'completed_units',
        'total_units',
        'note',
    ];

    // Relasi ke Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
