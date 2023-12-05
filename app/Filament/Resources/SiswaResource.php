<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Pages\ListPosts;
use App\Models\Siswa;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Section;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\SiswaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SiswaResource\RelationManagers;
use App\Filament\Resources\BlogPostResource\Widgets\StatsOverview;
use App\Filament\Resources\CategoryResource\RelationManagers\PostsRelationManager;

class SiswaResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationGroup= 'Submission';

    protected static ?string $pluralModelLabel = 'Published Article';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square';

    protected static ?string $activeNavigationIcon = 'heroicon-o-arrow-up-on-square-stack';

    public static function getNavigationBadge(): ?string
    {
        return Post::query()->where('published', 'Published')->count();
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
                ->label('Last updated at')
                ->content(function (Post $post) : ?string {
                    return $post->updated_at
                        ? $post->updated_at->setTimezone('Asia/Jakarta')->isoFormat('LLL')
                        : null;
                })

                ->hidden(fn (string $operation): bool => $operation === 'create'),
                // ->hiddeonOn('create')
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
            ->relationship('author', 'nickname')
            ->required(),

            ]),
    ]),
        ])->columns(3);
}

    public static function canCreate(): bool
    {
       return false;
    }
    public static function table(Table $table): Table
    {
        return $table
            // ->columns([
            //     TextColumn::make('nama')->searchable(),
            //     TextColumn::make('kelas')->searchable(),
            // ])
            // ->filters([
            //     //
            // ])
            ->columns([
                ImageColumn::make('thumbnail')->circular()->toggleable(),
                TextColumn::make('title')->sortable()->searchable()->toggleable(),
                TextColumn::make('author.nickname')->sortable()->toggleable(),
                TextColumn::make('slug')->searchable()->toggleable(),
                TextColumn::make('category.name')->sortable()->toggleable(),
                // ColorColumn::make('color')->toggleable(),
                // TextColumn::make('tags')->searchable()->toggleable(),
                // TextColumn::make('published')->toggleable()->label('Status'),
                // // TextColumn::make('published')
                // // ->label('Status')
                // // ->toggleable()
                // // ->badge()
                // // ->icons([
                // //     'heroicon-o-check-circle' => 'Published',
                // //     'heroicon-o-ellipsis-horizontal-circle' => 'Reviewing',
                // //     'heroicon-o-pencil-square' => 'Draft',
                // //     'heroicon-o-exclamation-circle' => 'Declined',
                // // ])
                // ->colors([
                //     'info' => 'Draft',
                //     'warning' => 'Reviewing',
                //     'success' => 'Published',
                //     'danger' => 'Declined',
                //     //everything else will become 'primary'
                // ]),
                TextColumn::make('created_at')->label('Created at')->date()->toggleable(),

            ])
            ->filters([
                // Filter::make('Published Post')
                // ->query(function ($query){
                //     return $query->where('published', true);
                // })
                // ->toggle(),
                SelectFilter::make('published')
                ->label('Status')
               ->options([
                'draft' => 'Draft',
                'reviewing' => 'Reviewing',
                'published' => 'Published',
                'declined' => 'Declined',
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
                // Tables\Actions\ActionGroup::make([
                // Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('View')

                ->infolist([
                     Section::make('About the Article')
                     ->Icon('heroicon-o-information-circle')
                        ->schema([
                            TextEntry::make('title')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
                            TextEntry::make('author.nickname')
                            ->label('Author')
                            ->listWithLineBreaks(),
                            TextEntry::make('category.name')->label('Category'),
                            TextEntry::make('Issue.title')->label('Issue'),
                            TextEntry::make('tags')->listWithLineBreaks()->badge()->color('info'),
                            TextEntry::make('published')->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                            'Draft' => 'info',
                            'Reviewing' => 'warning',
                            'Published' => 'success',
                            'Declined' => 'danger',
                        }),

                        ])
                        ->columns(),
                    Section::make('')
                        ->schema([
                            TextEntry::make('content')->label('Abstract'),
                        ]),
                    Section::make('')
                        ->schema([
                            TextEntry::make('created_at')->label('Created at'),
                        TextEntry::make('updated_at')->label('Last updated at'),
                        ])
                        ->columns(),
                ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
    // public static function getWidgets(): array
    // {
    //     return [
    //         StatsOverview::class,
    //     ];
    // }
}
