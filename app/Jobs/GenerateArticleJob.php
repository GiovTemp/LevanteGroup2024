<?php

namespace App\Jobs;

use App\Models\Argument;
use Illuminate\Support\Str;
use App\Models\GeneratedArticle;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateArticleJob implements ShouldQueue
{
    use Queueable;

    public $argument;
    /**
     * Create a new job instance.
     */
    public function __construct(Argument $argument)
    {
        $this->argument = $argument;
    }

    /**
     * Execute the job.
     * gpt-4o-mini-2024-07-18
     */
    public function handle(): void
    {
        $prompt = "
        Sei uno scrittore esperto per una wiki/blog e ti è stato assegnato un nuovo argomento da scrivere.
        Sulla base della seguente descrizione dell'argomento, genera un titolo, 
        un sottotitolo e il testo principale per un articolo di una wiki/blog. 
        Cerca di mantenere il testo il più neutrale possibile.
        Cerca di essere il più accurato possibile e di fornire informazioni utili.
        Cerca di essere empatico e di scrivere in modo chiaro e comprensibile.
        Rispondi con un oggetto JSON contenente i campi 'title', 'subtitle' e 'text'. 
        Assicurati che il JSON sia formattato correttamente.

        Nel campo text, cerca di scrivere almeno 400 parole e puoi usare l'html per gestirlo meglio graficamente .
        \n
        Esempio di risposta corretta:

        {
            \"title\": \"Titolo dell'articolo\",
            \"subtitle\": \"Sottotitolo dell'articolo\",
            \"text\": \"Testo principale dell'articolo...\"
        }
        \n
        \n\n Descrizione dell'argomento e istruzioni: {$this->argument->content}";
    
        try {
            $result = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini-2024-07-18', 
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7, // Opzionale: Regola la creatività della risposta
            ]);

            $responseContent = $result->choices[0]->message->content;

            // Rimuovi eventuali backticks o specificatori di linguaggio
            $cleanResponse = preg_replace('/```json\s*|\s*```/', '', $responseContent);

            // Decodifica la risposta JSON pulita
            $data = json_decode($cleanResponse, true);

            // Verifica se la decodifica JSON è andata a buon fine
            if (json_last_error() === JSON_ERROR_NONE) {
                $title = $data['title'] ?? 'Senza Titolo';
                $subtitle = $data['subtitle'] ?? '';
                $text = $data['text'] ?? '';

                Log::info("Articolo generato: Titolo - {$title}");

                GeneratedArticle::create([
                    'argument_id' => $this->argument->id,
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'subtitle' => $subtitle,
                    'content' => $text,
                ]);
            } else {
                Log::error('Risposta JSON non valida da OpenAI: ' . $responseContent);
            }
        } catch (\Exception $e) {
            Log::error('Errore durante la generazione dell\'articolo: ' . $e->getMessage());
        }
        
    }
    
}
