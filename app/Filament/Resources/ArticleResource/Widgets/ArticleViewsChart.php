<?php

namespace App\Filament\Resources\ArticleResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ArticleViewsChart extends ChartWidget
{
    protected static ?string $heading = 'Andamento Visualizzazioni Articoli';

    public ?string $filter = 'daily';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        return $this->loadData();
    }

    protected function loadData()
    {
        switch ($this->filter) {
            case 'monthly':
                $viewsData = $this->getMonthlyViews();
                break;
            case 'yearly':
                $viewsData = $this->getYearlyViews();
                break;
            default:
                $viewsData = $this->getDailyViews();
                break;
        }

        // Convertiamo le chiavi delle date in stringhe
        $labels = array_map('strval', array_keys($viewsData));

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Visualizzazioni',
                    'data' => array_values($viewsData),
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'fill' => true,
                ],
            ],
        ];
    }

    protected function getDailyViews()
    {
        $dates = collect();

        // Genera gli ultimi 30 giorni
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $dates->put($date, 0);
        }

        // Interroga direttamente la tabella views
        $views = DB::table('views')
            ->select(DB::raw('DATE(viewed_at) as date'), DB::raw('count(*) as views'))
            ->where('viewable_type', Article::class)
            ->where('viewed_at', '>=', Carbon::today()->subDays(29))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('views', 'date');

        // Combina le date con le visualizzazioni
        $viewsData = $dates->merge($views);

        return $viewsData->toArray();
    }

    protected function getMonthlyViews()
    {
        $dates = collect();

        // Genera gli ultimi 12 mesi
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $dates->put($month, 0);
        }

        // Interroga direttamente la tabella views
        $views = DB::table('views')
            ->select(DB::raw('DATE_FORMAT(viewed_at, "%Y-%m") as date'), DB::raw('count(*) as views'))
            ->where('viewable_type', Article::class)
            ->where('viewed_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('views', 'date');

        // Combina le date con le visualizzazioni
        $viewsData = $dates->merge($views);

        return $viewsData->toArray();
    }

    protected function getYearlyViews()
    {
        $dates = collect();

        // Genera gli ultimi 5 anni
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->format('Y');
            $dates->put($year, 0);
        }

        // Interroga direttamente la tabella views
        $views = DB::table('views')
            ->select(DB::raw('YEAR(viewed_at) as year'), DB::raw('count(*) as views'))
            ->where('viewable_type', Article::class)
            ->where('viewed_at', '>=', Carbon::now()->subYears(4)->startOfYear())
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('views', 'year');


        return $views->toArray();
    }

    protected function getFilters(): ?array
    {
        return [
            'daily' => 'Ultimi 30 giorni',
            'monthly' => 'Ultimi 12 mesi',
            'yearly' => 'Ultimi 5 anni',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
