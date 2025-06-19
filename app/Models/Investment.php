<?php
// Path: app/Models/Investment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Project;
use App\Models\Investor;

class Investment extends Model
{
    // Jika PK bukan 'id', maka uncomment baris ini:
    // protected $primaryKey = 'id';

    protected $fillable = [
        'investor_id',
        'project_id',
        'qty',
        'amount',
        'message',
        'receipt',
        'approved',
    ];

    protected $casts = [
        'qty'      => 'integer',
        'amount'   => 'decimal:2',
        'approved' => 'boolean',
    ];

    /**
     * Relasi ke Project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Relasi ke Investor.
     */
    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }
}
