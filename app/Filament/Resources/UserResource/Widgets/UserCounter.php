<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class UserCounter extends Widget
{
    protected static string $view = 'filament.resources.user-resource.widgets.user-counter';

    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 1,
        'xl' => 1,
    ];

    protected $count=0;

    public function mount()
    {
        $this->count=User::get()->count();
    }
}
