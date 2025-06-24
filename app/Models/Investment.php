<?php
// Path: app/Models/Investment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\Investor;
use App\Models\ProductionProgress; // tambahkan import model ProductionProgress

class Investment extends Model
{
    // Jika primary key bukan 'id', bisa di‐uncomment berikut:
    // protected $primaryKey = 'id';

    protected $fillable = [
        'project_id',
        'investor_id',
        'qty',
        'amount',
        'receipt',
        'message',
        'approved',
        'profit_payout_status', // <-- TAMBAHKAN INI
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

     public function payout()
    {
        return $this->hasOne(Payout::class);
    }

    /**
     * Relasi ke ProductionProgress lewat Project.
     * Mengembalikan semua record ProductionProgress
     * untuk proyek yang di‐investasikan.
     */
    public function productionProgress()
{
    return $this->hasManyThrough(
        ProductionProgress::class,  // model progress
        Project::class,             // intermediate model
        'id',                       // local key on projects
        'project_id',               // foreign key on production_progresses
        'project_id',               // local key on investments
        'id'                        // local key on projects
    );
}
}
