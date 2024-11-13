<?php

namespace App\Filament\Resources;


use Filament\Forms;
use Filament\Tables;
use App\Models\Article;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;

use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use Filament\Tables\Actions\Action;


class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    
    protected static ?string $recordTitleAttribute = 'title';

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
                    ->unique(Article::class, 'slug', ignoreRecord: true),

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

                TextColumn::make('published_at')
                    ->label('Published Date')
                    ->date()
                    ->sortable(), // Rende la colonna Published Date ordinabile

                IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published'),

                // TextColumn::make('id')
                //     ->label('ID')
                //     ->sortable(), // Rende la colonna ID ordinabile

                TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('subtitle')
                    ->label('Subtitle')
                    ->sortable()
                    ->searchable()
                    ->limit(30),
    
                IconColumn::make('ai_generated')
                    ->boolean()
                    ->label('AI Generated'),
    
            ])
            ->filters([
                Tables\Filters\Filter::make('published')
                    ->query(fn (Builder $query) => $query->where('is_published', true))
                    ->label('Published Articles'),
                
                Tables\Filters\Filter::make('ai_generated')
                    ->query(fn (Builder $query) => $query->where('ai_generated', true))
                    ->label('AI Generated Articles'),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->relationship('category', 'name') // Relazione con la tabella delle categorie
                    ->multiple(true),

                Tables\Filters\SelectFilter::make('tags')
                    ->label('Tag')
                    ->relationship('tags', 'name') // Relazione con la tabella delle categorie
                    ->multiple(true),


            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Action::make('analytics')
                ->label('Analytics')
                ->icon('heroicon-o-chart-bar')
                ->action(fn (Article $record, array $data) => $record)
                ->modalHeading('Analytics')
                ->modalContent(function (Article $record) {
                    return view('filament.resources.article-resource.pages.analytics', [
                        'article' => $record,
                    ]);
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'title' => $record->title,
            'Author' => $record->user->name,
            'Category' => $record->category->name,
            // 'Published At' => $record->published_at->format('d/m/Y'),
            'Published At' => $record->published_at,
        ];
    }


}