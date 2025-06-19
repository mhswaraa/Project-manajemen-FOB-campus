<?php
namespace App\Observers;

use App\Models\Investment;
use App\Models\Investor;

class InvestmentObserver
{
    /**
     * Handle the Investment "created" event.
     */
    public function created(Investment $inv)
    {
        // Tambah amount investor sebesar inv.amount
        Investor::where('investor_id', $inv->investor_id)
                ->increment('amount', $inv->amount);
    }

    /**
     * Handle the Investment "updated" event.
     */
    public function updated(Investment $inv)
    {
        $original = $inv->getOriginal('amount');
        $delta    = $inv->amount - $original;
        if ($delta !== 0) {
            Investor::where('investor_id', $inv->investor_id)
                    ->increment('amount', $delta);
        }
    }

    /**
     * Handle the Investment "deleted" event.
     */
    public function deleted(Investment $inv)
    {
        // Kurangi amount investor
        Investor::where('investor_id', $inv->investor_id)
                ->decrement('amount', $inv->amount);
    }

    /**
     * (Opsional) jika Anda menggunakan softDeletes:
     */
    public function restoring(Investment $inv)
    {
        Investor::where('investor_id', $inv->investor_id)
                ->increment('amount', $inv->amount);
    }
}
