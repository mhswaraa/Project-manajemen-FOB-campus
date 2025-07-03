<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\Investment;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "updated" event.
     */
     /**
     * Handle the Project "updated" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function updated(Project $project)
    {
        // Cek apakah kolom 'status' baru saja diubah menjadi 'completed'
        if ($project->wasChanged('status') && $project->status === 'completed') {
            
            // Cari semua investasi yang terkait dengan proyek ini
            // dan ubah status pembayaran profitnya menjadi 'unpaid'
            Investment::where('project_id', $project->id)
                      ->where('approved', true) // Hanya untuk investasi yang disetujui
                      ->update(['profit_payout_status' => 'unpaid']);
        }
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        //
    }
}
