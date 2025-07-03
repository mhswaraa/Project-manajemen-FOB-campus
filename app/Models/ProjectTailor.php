<?php

// Path: app/Models/ProjectTailor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTailor extends Model
{
    protected $table = 'project_tailor';
    protected $fillable = [
        'project_id',
        'tailor_id',
        'assigned_qty',
        'started_at',
        'status',
        'completed_at', // <-- Tambahkan baris ini
    ];

    // Relasi ke Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Relasi ke Tailor (Penjahit)
    public function tailor()
    {
        return $this->belongsTo(Tailor::class, 'tailor_id', 'tailor_id');
    }

    // Relasi ke TailorProgress
    public function progress()
    {
        return $this->hasMany(TailorProgress::class, 'assignment_id');
    }
}
