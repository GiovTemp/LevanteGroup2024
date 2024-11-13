<x-filament-widgets::widget>
    <x-filament::section>
        <div style="max-width: 300px; margin: 0 auto; text-align: center;">
            <div style="border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h1 style="margin-bottom: 20px;"><b>Commenti da Revisionare</b></h1>
                <div style="display: flex; justify-content: center; align-items: center;">
                    <div
                        style="
                            width: 120px;
                            height: 120px;
                            border-radius: 50%;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            font-size: 3rem;
                            @if ($this->count > 10)
                                background-color: rgba(255, 0, 0, 0.7);
                            @elseif ($this->count == 0)
                                background-color: rgba(0, 128, 0, 0.7);
                            @else
                                background-color: rgba(255, 255, 0, 0.7);
                            @endif
                        "
                        onclick="window.open('{{ route('filament.admin.resources.comments.index') }}', '_blank')"
                    >
                        {{ $this->count }}
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
