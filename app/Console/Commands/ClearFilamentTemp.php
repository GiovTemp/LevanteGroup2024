<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ClearFilamentTemp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament:clear-temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Svuota la cartella temporanea di Filament per le immagini';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Definisci il percorso assoluto della cartella temporanea
        $directory = storage_path('app/public/articles/temp');

        // Controlla se la directory esiste e se è una directory
        if (File::exists($directory) && File::isDirectory($directory)) {
            try {
                // Elimina l'intera directory e i suoi contenuti
                File::deleteDirectory($directory);

                // Ricrea la directory per futuri utilizzi
                File::makeDirectory($directory, 0755, true);

                $this->info("La cartella temporanea '{$directory}' è stata svuotata con successo.");
                Log::info("La cartella temporanea '{$directory}' è stata svuotata con successo.");
            } catch (\Exception $e) {
                $this->error("Errore durante la svuotatura della cartella temporanea: " . $e->getMessage());
                Log::error("Errore durante la svuotatura della cartella temporanea '{$directory}': " . $e->getMessage());
                return 1; // Indica un errore
            }
        } else {
            $this->warn("La cartella temporanea '{$directory}' non esiste.");
            Log::warning("Tentativo di svuotare una cartella temporanea inesistente: '{$directory}'.");
        }

        return 0; // Indica successo
    }
}
