<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;
use CyrildeWit\EloquentViewable\Support\Period;

class CategoryViewsChart extends ChartWidget
{
    protected static ?string $heading = 'Visualizzazioni Ricerche per Categoria';
    public ?string $filter = 'today';
    protected int | string | array $columnSpan = 'full';


    protected function getData(): array
    {
        return $this->loadData();
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    protected function loadData(){

        // Ottieni tutte le categorie con il conteggio delle visualizzazioni
        $categories = Category::all();
        
        // Definisci una serie di colori per i dataset
        $colors = [
            'rgba(75, 192, 192, 0.6)', // Verde
            'rgba(255, 99, 132, 0.6)', // Rosso
            'rgba(54, 162, 235, 0.6)', // Blu
            'rgba(255, 206, 86, 0.6)', // Giallo
            'rgba(153, 102, 255, 0.6)', // Viola
            'rgba(255, 159, 64, 0.6)', // Arancione
        ];
        
        $datasets = [];
        foreach ($categories as $index => $category) {
            
            $data=[$this->getViews($category)];
            $datasets[] = [
                'label' => $category->name,
                'data' =>  $data, // Conteggio delle visualizzazioni
                'backgroundColor' => $colors[$index % count($colors)], // Colore ciclico
                'borderColor' => $colors[$index % count($colors)],
                'borderWidth' => 1,
            ];
        }

        return [
            'labels' => ['Views'],
            'datasets' => $datasets,
        ];


    }

    protected function getViews($category){

        switch ($this->filter) {
            case 'week':
                return views($category)->period(Period::create(now()->startOfWeek(), now()->endOfWeek()))->remember()->count();
            case 'month':
                return views($category)->period(Period::create(now()->startOfMonth(), now()->endOfMonth()))->remember()->count();
            case 'year':
                return views($category)->period(Period::create(now()->startOfYear(), now()->endOfYear()))->remember()->count();
            default:
                return views($category)->period(Period::create(now()->startOfDay(), now()->endOfDay()))->remember()->count();
        }
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
