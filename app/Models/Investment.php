<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project; // import model Project

class Investment extends Model
{
    protected $primaryKey = 'id';      // atau investor_id jika PK khusus
    protected $fillable = [
        'investor_id', 'project_id',
        'amount', 'deadline',
        'message','receipt',
    ];

    /**
     * Relasi ke Project (belongsTo)
     */
    public function project()
    {
        // project_id adalah foreign key di tabel investments
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * (Opsional) Relasi ke Investor
     */
    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }
}
