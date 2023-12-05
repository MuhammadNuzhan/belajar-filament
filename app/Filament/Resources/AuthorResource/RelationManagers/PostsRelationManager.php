<?php

namespace App\Filament\Resources\AuthorResource\RelationManagers;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Group::make()
            ->schema([
            Section::make('')
            ->description('Type your post over here!')
            ->icon('heroicon-o-pencil-square')
            ->schema([
        TextInput::make('title')
        ->rules('min:3|max:50')
        ->required()
        ->columnSpan(2),
        Select::make('issue_id')
        ->relationship('issue','title')
        ->required(),
        TextInput::make('slug')
        ->rules('regex:/^[a-z0-9-]+$/')
        ->disabledOn('edit')
        ->unique(ignoreRecord:true)
        ->required(),

        Select::make('category_id')
        ->relationship('category', 'name')
        ->label('Category')
        // ->options(Category::all()->pluck('name' , 'id'))

        ->preload()
        ->required()
        ->createOptionForm([
            TextInput::make('name')
            ->required(),
            TextInput::make('slug')
            ->required(),
        ]),
        ColorPicker::make('color'),
        MarkdownEditor::make('content')
        ->columnSpan('full'),
        ])->columnSpan(2)->columns(2),
        Section::make('Detail created')
        ->collapsible()
        ->schema([

            Placeholder::make('created_at')
                ->label('Created at')
                ->content(function (Post $post) : ?string {
                    return $post->created_at
                        ? $post->created_at->setTimezone('Asia/Jakarta')->isoFormat('LLL')
                        : null;
                }),

            Placeholder::make('updated_at')
                ->label('Updated at')
                ->content(function (Post $post) : ?string {
                    return $post->updated_at
                        ? $post->updated_at->setTimezone('Asia/Jakarta')->isoFormat('LLL')
                        : null;
                })

                ->hidden(fn (string $operation): bool => $operation === 'create'),

        ])->columnSpan(2)->columns(2),
        ])->columnSpan(2)->columns(2),


        Group::make()
        ->schema([
            Section::make('Thumbnail')
            ->collapsible()
        ->schema([
        FileUpload::make('thumbnail')
        ->imageEditor()
        ->imageEditorEmptyFillColor('#fffff'),
        ])->columnSpan(1),
            Section::make('Meta')
            ->collapsible()
            ->schema([
        TagsInput::make('tags'),
        // Checkbox::make('published'),
        Radio::make('published')
        ->label('Status')
        ->options([
            'Draft' => 'Draft',
            'Reviewing' => 'Reviewing',
            'Published' => 'Published',
            'Declined' => 'Declined',
        ])
         ]),
        Section::make('Authors')
        ->collapsible()
        ->schema([
            Select::make('author_id')
            ->relationship('author', 'nickname')
            ->required(),
            ]),
    ]),
        ])->columns(3);
}
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('author_id')
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')->toggleable()->circular(),
                Tables\Columns\TextColumn::make('title')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('slug')->searchable()->toggleable(),
                // Tables\Columns\TextColumn::make('author.nickname')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('issue.title')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('tags')->searchable()->toggleable(),
                TextColumn::make('published')
                ->label('Status')
                ->toggleable()
                ->badge()
                ->icons([
                    'heroicon-o-check-circle' => 'Published',
                    'heroicon-o-ellipsis-horizontal-circle' => 'Reviewing',
                    'heroicon-o-pencil-square' => 'Draft',
                    'heroicon-o-exclamation-circle' => 'Declined',
                ])
                ->colors([
                    'info' => 'Draft',
                    'warning' => 'Reviewing',
                    'success' => 'Published',
                    'danger' => 'Declined',
                    //everything else will become 'primary'
                ]),
                Tables\Columns\TextColumn::make('created_at')->label('Created at')->date()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
