<?php

namespace App\Filament\Clusters\Pet\Resources;

use App\Filament\Clusters\Pet\Resources\PostResource\Pages\CreatePost;
use App\Filament\Clusters\Pet\Resources\PostResource\Pages\EditPost;
use App\Filament\Clusters\Pet\Resources\PostResource\Pages\ListPosts;
use App\Filament\Clusters\PostCluster;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;

class PostResource extends Resource implements Translateable
{
    use HasTranslateableResources; // 翻译

    protected static ?string $translateablePackageKey = '';

    protected static ?string $model = Post::class;

    protected static ?string $cluster = PostCluster::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(trans('filament-model.general.user'))
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('pet_id')
                    ->label(trans('filament-model.label.pet.label'))
                    ->relationship('pet', 'name'),
                Textarea::make('content')
                    ->label(trans('filament-model.attributes.post.content'))
                    ->required()
                    ->rows(5),
                TextInput::make('location')
                    ->label(trans('filament-model.attributes.post.location'))
                    ->maxLength(200),
                Select::make('visibility')
                    ->label(trans('filament-model.attributes.post.visibility'))
                    ->options([
                        'public' => trans('filament-model.attributes.post.visibility_options.public'),
                        'followers' => trans('filament-model.attributes.post.visibility_options.followers'),
                        'private' => trans('filament-model.attributes.post.visibility_options.private'),
                    ])
                    ->default('public'),
                Toggle::make('is_pinned')
                    ->label(trans('filament-model.attributes.post.is_pinned')),
                Toggle::make('allow_comment')
                    ->label(trans('filament-model.attributes.post.allow_comment'))
                    ->default(true),
                DateTimePicker::make('published_at')
                    ->label(trans('filament-model.attributes.post.published_at'))
                    ->default(now()),
                SpatieTagsInput::make('tags')
                    ->label(trans('filament-model.general.tags')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(trans('filament-model.attributes.post.author')),
                TextColumn::make('content')
                    ->limit(50)
                    ->label(trans('filament-model.attributes.post.content')),

                TextColumn::make('pet.name')
                    ->label(trans('filament-model.attributes.post.related_pet')),
                TextColumn::make('visibility')
                    ->label(trans('filament-model.attributes.post.visibility'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'public' => trans('filament-model.attributes.post.visibility_options.public'),
                        'followers' => trans('filament-model.attributes.post.visibility_options.followers'),
                        'private' => trans('filament-model.attributes.post.visibility_options.private'),
                        default => $state,
                    }),
                IconColumn::make('is_pinned')
                    ->boolean()
                    ->label(trans('filament-model.attributes.post.is_pinned')),
                TextColumn::make('like_count')
                    ->label(trans('filament-model.attributes.post.like_count')),
                SpatieTagsColumn::make('tags')
                    ->label(trans('filament-model.general.tags')),
                TextColumn::make('created_at')
                    ->label(trans('filament-model.general.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tags')
                    ->label(trans('filament-model.general.tags'))
                    ->relationship('tags', 'name')
                    ->multiple(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getModelLabel(): string
    {
        return trans('filament-model.label.post.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('filament-model.label.post.plural_label');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
