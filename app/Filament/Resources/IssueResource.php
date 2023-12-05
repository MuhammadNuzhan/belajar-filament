<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use App\Models\User;
use Filament\Tables;
use App\Models\Issue;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\IssueResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\IssueResource\RelationManagers;
use App\Filament\Resources\IssueResource\RelationManagers\PostsRelationManager;

class IssueResource extends Resource
{
    protected static ?string $model = Issue::class;

    protected static ?string $pluralModelLabel = 'Issue';

    protected static ?string $navigationGroup = 'Submission';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $activeNavigationIcon = 'heroicon-o-clipboard-document';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->schema([
                Section::make('Issue')
                ->description('Issue for Article')
                ->aside()
                ->icon('heroicon-o-clipboard-document')
                ->schema([
                    TextInput::make('volume')
                    ->label('Volume'),
                    TextInput::make('number')
                    ->label('Number'),
                    TextInput::make('year')
                    ->label('Year'),
                    TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->columnSpan(3),
                    MarkdownEditor::make('description')
                    ->label('Description')
                    ->required()
                    ->columnSpan(3),
                    FileUpload::make('cover')
                    ->label('Cover Image')
                    ->imageEditorEmptyFillColor('#fffff')
                    ->columnSpan(3),
                    Radio::make('status')
                    ->label('Status')
                    ->options([
                        'Future Issue' => 'Publish Issue',
                        'Back Issue' => 'Unpublish Issue',
                    ])
                    ])
                ])
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->searchable()->toggleable()
                ->weight(FontWeight::Bold)
                ->description(fn (Issue $record): string => $record->description),
                TextColumn::make('volume')
            ])
            ->filters([
                SelectFilter::make('year')
                ->label('Year')
               ->options([
                '2020' => '2020',
                '2021' => '2021',
                '2022'=> '2022',
                '2023' => '2023',
               ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
                // Tables\Actions\Action::make('updateAuthor')
    // ->form([
    //     Select::make('authorId')
    //         ->label('Author')
    //         ->options(User::query()->pluck('name', 'id'))
    //         ->required(),
    // ])
    // ->action(function (array $data, Post $record): void {
    //     $record->author()->associate($data['authorId']);
    //     $record->save();
    // })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('Change Status')
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->form([
                        Select::make('status')
                        ->label('Status')
                        ->options([
                            'Future Issue' => 'Unpublish Issue',
                            'Back Issue' => 'Publish Issue',
                        ])
                        ->required(),
                    ])
                        ->action(function (Collection $records, array $data){
                            $records->each(function($record) use ($data){
                                Issue::where('id', $record->id)->update(['status' => $data['status']]);
                            });
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
            Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PostsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssues::route('/'),
            'create' => Pages\CreateIssue::route('/create'),
            'edit' => Pages\EditIssue::route('/{record}/edit'),
        ];
    }
}
