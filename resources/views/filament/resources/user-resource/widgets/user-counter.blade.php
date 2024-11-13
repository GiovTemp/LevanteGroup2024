<x-filament-widgets::widget>
    <x-filament::section>
        <div style="max-width: 300px; margin: 0 auto; text-align: center;">
            <div style="border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h1 style="margin-bottom: 20px;"><b>Utenti iscritti</b></h1>
                <div style="display: flex; justify-content: center; align-items: center;">
                    <div style="width: 120px; height: 120px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 3rem;">
                        {{ $this->count }}
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
