<?php

namespace App\Filament\Clusters\Pet\Resources\PetResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public static function getTitle(
        Model $ownerRecord,
        string $pageClass
    ): string {
        return trans('filament-model.label.post.plural_label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                TextColumn::make('content')
                    ->limit(50),
                TextColumn::make('visibility')
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
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make(),
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
}
