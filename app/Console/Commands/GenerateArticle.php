<?php

namespace App\Console\Commands;

use PhpParser\Node\Arg;
use App\Models\Argument;
use Illuminate\Console\Command;
use App\Jobs\GenerateArticleJob;
use Illuminate\Support\Facades\Log;


class GenerateArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-article';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $arguments = Argument::where('to_generate','>',0)->get();

        foreach ($arguments as $argument) {
            $limit=$argument->to_generate;
            for ($i = 0; $i < $limit; $i++) {
                dispatch(new GenerateArticleJob($argument));
                $argument->to_generate--;
                $argument->save();
                Log::info('Articolo generato: ' . $argument->content);
            }
        }
    }
}
