<?php

namespace App\Filament\Clusters\Pet\Resources;

use App\Filament\Clusters\CommentCluster;
use App\Filament\Clusters\Pet\Resources\CommentResource\Pages\EditComment;
use App\Filament\Clusters\Pet\Resources\CommentResource\Pages\ListComments;
use App\Models\Comment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;

class CommentResource extends Resource implements Translateable
{
    use HasTranslateableResources;

    protected static ?string $translateablePackageKey = '';

    protected static ?string $model = Comment::class;

    protected static ?string $cluster = CommentCluster::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(trans('filament-model.general.user'))
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('post_id')
                    ->label(trans('filament-model.label.post.label'))
                    ->relationship('post', 'id')
                    ->required(),
                Select::make('parent_id')
                    ->label(trans('filament-model.attributes.comment.parent'))
                    ->relationship('parent', 'id'),
                Textarea::make('content')
                    ->label(trans('filament-model.attributes.comment.content'))
                    ->required()
                    ->rows(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(trans('filament-model.attributes.comment.author'))
                    ->searchable(),
                TextColumn::make('post_id')
                    ->label(trans('filament-model.attributes.comment.post'))
                    ->sortable(),
                TextColumn::make('parent_id')
                    ->label(trans('filament-model.attributes.comment.parent')),
                TextColumn::make('content')
                    ->label(trans('filament-model.attributes.comment.content'))
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('like_count')
                    ->label(trans('filament-model.attributes.comment.like_count'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(trans('filament-model.general.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label(trans('filament-model.general.user'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('post_id')
                    ->label(trans('filament-model.label.post.label'))
                    ->relationship('post', 'id')
                    ->searchable()
                    ->preload(),
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

    public static function getModelLabel(): string
    {
        return trans('filament-model.label.comment.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('filament-model.label.comment.plural_label');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComments::route('/'),
            'edit' => EditComment::route('/{record}/edit'),
        ];
    }
}
