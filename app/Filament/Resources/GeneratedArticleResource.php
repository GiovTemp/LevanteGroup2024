<?php

namespace App\Filament\Resources;

use App\Console\Commands\GenerateArticle;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\GeneratedArticle;
use Filament\Resources\Resource;
use App\Models\Article as Article;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\GeneratedArticleResource\Pages;


class GeneratedArticleResource extends Resource
{
    protected static ?string $model = GeneratedArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'AI';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Titolo
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255)
                    ->reactive()
                    ->debounce(3000) 
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (!empty($state)) {
                            $set('slug', Str::slug($state));
                            $set('meta_title', $state);
                        }
                    }),

                // Slug
                TextInput::make('slug')
                    ->label('Slug (URL - viene generato automaticamente dopo 5 seocondi)')
                    ->required()
                    ->unique(GeneratedArticle::class, 'slug', ignoreRecord: true),

                // Subtitle
                TextInput::make('subtitle')
                    ->label('Subtitle')
                    ->maxLength(255)
                    ->columnSpan('full'),

                // Immagine
                FileUpload::make('image')
                    ->label('Image')
                    ->directory('articles/temp')
                    ->image()
                    ->required()
                    ->columnSpan('full'),                

                // Content
                RichEditor::make('content')
                    ->label('Content')
                    ->required()
                    ->columnSpan('full')
                    ->disableToolbarButtons([
                        'codeBlock',
                        'attachFiles',
                    ])
                    ->extraAttributes(['style' => 'width: 100%; min-height:400px;']),

                // Category
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->required(),

                // Tags
                Select::make('tags')
                    ->multiple()
                    ->label('Tags')
                    ->relationship('tags', 'name')
                    ->columnSpan('full'),

                // AI Generated Toggle
                Toggle::make('ai_generated')
                    ->label('AI Generated')
                    ->default(false)
                    ->inline(false)
                    ->columnSpan('full'),

                // Published Toggle
                Toggle::make('is_published')
                    ->label('Published')
                    ->default(false)
                    ->inline(false)
                    ->columnSpan('full'),

                // Published Date
                DatePicker::make('published_at')
                    ->label('Published Date')
                    ->default(now())
                    ->required()
                    ->columnSpan('full'),

                // Meta Keywords
                TextInput::make('meta_keywords')
                    ->label('Meta Keywords')
                    ->placeholder('comma, separated, keywords')
                    ->maxLength(255),
                
                // Meta Description
                Textarea::make('meta_description')
                    ->label('Meta Description')
                    ->maxLength(160)
                    ->rows(2),
            ])
            ->columns(2);
    }

    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('subtitle')
                    ->label('Subtitle')
                    ->sortable()
                    ->searchable()
                    ->limit(30),
    
    
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('move_to_article')
                ->label('Move to Article')
                ->icon('heroicon-o-arrow-top-right-on-square') // Icona opzionale
                ->requiresConfirmation() // Richiede conferma prima di eseguire l'azione
                ->action(function (GeneratedArticle $record) {
                    // Verifica che tutti i campi siano pieni
                    if (
                        empty($record->title) ||
                        empty($record->slug) ||
                        empty($record->subtitle) ||
                        empty($record->image) ||
                        empty($record->content) ||
                        empty($record->category_id) ||
                        empty($record->published_at)
                    ) {
                        // Mostra una notifica di errore se qualche campo è vuoto
                        \Filament\Notifications\Notification::make()
                            ->title('Impossibile spostare l\'articolo')
                            ->body('Assicurati che tutti i campi obbligatori siano compilati.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Verifica se lo slug è unico
                    if (Article::where('slug', $record->slug)->exists()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Impossibile spostare l\'articolo')
                            ->body('Lo slug esiste già in un altro articolo. Modifica lo slug e riprova.')
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        // Crea un nuovo record in Article
                        Article::create([
                            'title' => $record->title,
                            'slug' => $record->slug,
                            'subtitle' => $record->subtitle,
                            'image' => $record->image,
                            'content' => $record->content,
                            'category_id' => $record->category_id,
                            'ai_generated' => $record->ai_generated,
                            'is_published' => $record->is_published,
                            'published_at' => $record->published_at,
                            'meta_keywords' => $record->meta_keywords,
                            'meta_description' => $record->meta_description,
                            'user_id' => Auth::user()->id,
                            // Aggiungi altri campi se necessario
                        ]);

                        // Elimina il record da GeneratedArticle
                        $record->delete();

                        // Mostra una notifica di successo
                        \Filament\Notifications\Notification::make()
                            ->title('Articolo spostato con successo')
                            ->body('L\'articolo è stato spostato in "Article".')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        // Gestisci eventuali errori e mostra una notifica di errore
                        \Filament\Notifications\Notification::make()
                            ->title('Errore durante lo spostamento')
                            ->body('Si è verificato un errore: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        // Log dell'errore per debug
                        Log::error('Errore nello spostamento dell\'articolo: ' . $e->getMessage());
                    }
                }),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            // Aggiungi eventuali relation managers
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneratedArticles::route('/'),
            'create' => Pages\CreateGeneratedArticle::route('/create'),
            'edit' => Pages\EditGeneratedArticle::route('/{record}/edit'),
        ];
    }
}
