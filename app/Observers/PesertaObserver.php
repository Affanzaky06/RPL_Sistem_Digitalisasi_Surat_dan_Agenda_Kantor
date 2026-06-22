<?php

namespace App\Observers;

use App\Models\Peserta;

class PesertaObserver
{
    /**
     * Handle the Peserta "created" event.
     */
    public function created(Peserta $peserta): void
    {
        //
    }

    /**
     * Handle the Peserta "updated" event.
     */
    public function updated(Peserta $peserta): void
    {
        //
    }

    /**
     * Handle the Peserta "deleted" event.
     */
    public function deleted(Peserta $peserta): void
    {
        //
    }

    /**
     * Handle the Peserta "restored" event.
     */
    public function restored(Peserta $peserta): void
    {
        //
    }

    /**
     * Handle the Peserta "force deleted" event.
     */
    public function forceDeleted(Peserta $peserta): void
    {
        //
    }
}
