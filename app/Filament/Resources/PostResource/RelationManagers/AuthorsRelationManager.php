<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class AuthorsRelationManager extends RelationManager
{
    protected static string $relationship = 'author';
    // protected static string $tableLabel = 'Test';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('')
            ->schema([
                Section::make('Author')
                    ->icon('heroicon-m-user-group')
                    ->description('Create author description')
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


    public function table(Table $table): Table
    {

        return $table
            // ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('nickname'),
                // Tables\Columns\TextColumn::make('firstname'),
                // Tables\Columns\TextColumn::make('lastname'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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
}
