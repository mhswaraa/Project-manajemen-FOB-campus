<?php
// Path: app/Models/Investment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\Investor;
use App\Models\ProductionProgress; // tambahkan import model ProductionProgress
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    // --- AWAL PERUBAHAN ---
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['profit'];
    // --- AKHIR PERUBAHAN ---

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

     /**
     * Relasi ke Payout.
     */
    public function payout()
    {
        return $this->hasOne(Payout::class);
    }

    /**
     * Accessor untuk menghitung total profit investasi secara otomatis.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function profit(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->project && is_numeric($this->project->profit)) {
                    return $this->qty * $this->project->profit;
                }
                return 0;
            }
        );
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
