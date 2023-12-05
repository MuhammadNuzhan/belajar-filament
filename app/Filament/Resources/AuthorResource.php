<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use App\Models\Author;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AuthorResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AuthorResource\RelationManagers\PostsRelationManager;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup= 'People';

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
            Section::make('Author')
                ->icon('heroicon-m-user-group')
                ->description('Author Information')
                ->aside()
                ->schema([
               TextInput::make('nickname')
               ->label('Nickname')->required()->columnSpan(2),
               TextInput::make('firstname')
               ->label('First Name')->required()->columnSpan(1),
               TextInput::make('lastname')
               ->label('Last Name')->required()->columnSpan(1),
            ])->columns(2),
            ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('nickname')->label('Nickname')
            ->toggleable()->searchable(),
            TextColumn::make('firstname')->label('First Name')
            ->toggleable()->searchable(),
            TextColumn::make('lastname')->label('Last Name')
            ->toggleable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                   Tables\Actions\Action::make('create')
    ->form([
        Section::make('')
        ->schema([
            Section::make('Author')
                ->icon('heroicon-m-user-group')
                ->description('Author Information')
                ->aside()
                ->schema([
               TextInput::make('nickname')
               ->label('Nickname')->required()->columnSpan(2),
               TextInput::make('firstname')
               ->label('First Name')->required()->columnSpan(1),
               TextInput::make('lastname')
               ->label('Last Name')->required()->columnSpan(1),
                ])
        ])
    ])
    ->action(function (array $data, Post $record): void {
        $record->author()->associate($data['id']);
        $record->save();
    })
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
