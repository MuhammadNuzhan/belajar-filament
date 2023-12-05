<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\BlogPostResource\Widgets\StatsOverview;

// use Filament\Infolists\Components\Section\Section;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;


class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    // protected static ?string $navigationLabel = 'Posts';
    // protected static ?string $pluralModelLabel = 'Siswa';
    protected static string $view = 'filament.pages.upload-scoresheet';

    protected static ?string $navigationGroup= 'Submission';

    protected static ?string $pluralModelLabel= 'Article';

    protected static ?string $activeNavigationIcon = 'heroicon-o-square-2-stack';

    protected static ?string $navigationIcon = 'heroicon-o-stop';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
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
            ->required()
            ->columnSpan(1),
            TextInput::make('slug')
            ->rules('regex:/^[a-z0-9-]+$/')
            ->disabledOn('edit')
            ->unique(ignoreRecord:true)
            ->required()
            ->columnSpan(1),

            Select::make('category_id')
            ->relationship('category', 'name')
            ->label('Category')
            // ->options(Category::all()->pluck('name' , 'id'))

            ->preload()
            ->required()
            ->createOptionForm([
                Section::make('Create New Category')
                ->schema([
                TextInput::make('name')
                ->required(),
                TextInput::make('slug')
                ->required(),
                ])
            ])->columnSpan(1),
            ColorPicker::make('color'),
            MarkdownEditor::make('content')
            ->rules('max:10000')
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
                    ->label('Last updated at')
                    ->content(function (Post $post) : ?string {
                        return $post->updated_at
                            ? $post->updated_at->setTimezone('Asia/Jakarta')->isoFormat('LLL')
                            : null;
                    })

            ])->columnSpan(2)->columns(2)
            // ->hiddenOn('create')
            ->hidden(fn (string $operation): bool => $operation === 'create'),

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
            ->hiddenOn('view')
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
                // ->multiple()
                //   ->preload()
                ->relationship('author', 'nickname')
                ->required()
                ->createOptionForm([
                    Section::make('Add new author')
                    ->schema([
                        TextInput::make('nickname')
                        ->label('Nickname')->required()->columnSpan(2),
                        TextInput::make('firstname')
                        ->label('First Name')->required()->columnSpan(1),
                        TextInput::make('lastname')
                        ->label('Last Name')->required()->columnSpan(1),
                    ])->Columns(2)
                ]),

                ]),
        ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')->toggleable(),
                TextColumn::make('title')->sortable()->searchable()->toggleable(),
                TextColumn::make('author.nickname')->sortable()->toggleable(),
                // TextColumn::make('slug')->searchable()->toggleable(),
                TextColumn::make('category.name')->sortable()->toggleable(),
                TextColumn::make('issue.title')->sortable()->toggleable()->label('Issue'),
                // ColorColumn::make('color')->toggleable(),
                // TextColumn::make('tags')->searchable()->toggleable(),
                TextColumn::make('content')->searchable()->toggleable()->words(2),
                // TextColumn::make('published')->toggleable()->label('Status'),
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

                TextColumn::make('created_at')->label('Created at')->date()->toggleable(),


            ])
            ->filters([
                // Filter::make('Published Post')
                // ->query(function ($query){
                //     return $query->where('published', true);
                // })
                // ->toggle(),
                // TernaryFilter::make('published')
                // ->label('Published'),
                SelectFilter::make('published')
                ->label('Status')
               ->options([
                'Draft' => 'Draft',
                'Reviewing' => 'Reviewing',
                'Published' => 'Published',
                'Declined' => 'Declined',
            ]),
                SelectFilter::make('author_id')
                ->relationship('author', 'nickname')
                ->searchable()
                ->preload()
                ->label('by Authors'),
                SelectFilter::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->label('by Category')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                // Tables\Actions\Action::make('View')

                // ->infolist([
                //      Section::make('Test 1')
                //         ->schema([
                //             TextEntry::make('title'),
                //             TextEntry::make('author.nickname')->listWithLineBreaks(),

                //             TextEntry::make('category.name')->label('Category'),
                //             TextEntry::make('Issue.title')->label('Issue'),
                //             TextEntry::make('tags')->listWithLineBreaks(),
                //             TextEntry::make('published')->label('Status')
                //             ->badge()
                //             ->color(fn (string $state): string => match ($state) {
                //             'Draft' => 'info',
                //             'Reviewing' => 'warning',
                //             'Published' => 'success',
                //             'Declined' => 'danger',
                //         }),
                //         TextEntry::make('created_at')->label('Created at'),
                //         TextEntry::make('updated_at')->label('Last updated at'),
                //         ])
                //         ->columns(),
                //     Section::make('Additional Details')
                //         ->schema([
                //             TextEntry::make('title'),
                //         ]),
                //     Section::make('Lead and Stage Information')
                //         ->schema([
                //             TextEntry::make('title'),

                //         ])
                //         ->columns(),
                // ]),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('Change Status')
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->form([
                        Select::make('published')
                        ->label('Status')
                        ->options([
                            'Draft' => 'Draft',
                            'Reviewing' => 'Reviewing',
                            'Published' => 'Published',
                            'Declined' => 'Declined',
                        ])
                        ->required(),
                    ])
                        ->action(function (Collection $records, array $data){
                            $records->each(function($record) use ($data){
                                post::where('id', $record->id)->update(['published' => $data['published']]);
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
            AuthorsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),

        ];
    }
    public static function getWidgets(): array
{
    return [
        StatsOverview::class,
    ];
}

}
