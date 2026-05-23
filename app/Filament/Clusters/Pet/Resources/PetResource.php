<?php

namespace App\Filament\Clusters\Pet\Resources;

use App\Filament\Clusters\Pet\PetCluster;
use App\Filament\Clusters\Pet\Resources\PetResource\Pages\CreatePet;
use App\Filament\Clusters\Pet\Resources\PetResource\Pages\EditPet;
use App\Filament\Clusters\Pet\Resources\PetResource\Pages\ListPets;
use App\Filament\Clusters\Pet\Resources\PetResource\Pages\ViewPet;
use App\Filament\Clusters\Pet\Resources\PetResource\RelationManagers\PostsRelationManager;
use App\Filament\Clusters\Pet\Resources\PetResource\RelationManagers\RecordsRelationManager;
use App\Models\Pet;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;

class PetResource extends Resource implements Translateable
{
    use HasTranslateableResources; // 翻译

    protected static ?string $translateablePackageKey = '';

    protected static ?string $model = Pet::class;

    protected static ?string $cluster = PetCluster::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-heart';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(50),
                TextInput::make('species')
                    ->required()
                    ->label(trans('filament-model.attributes.pet.species'))
                    ->placeholder(trans('filament-model.attributes.pet.species_placeholder')),
                TextInput::make('breed')
                    ->label(trans('filament-model.attributes.pet.breed'))
                    ->placeholder(trans('filament-model.attributes.pet.breed_placeholder')),
                Select::make('gender')
                    ->label(trans('filament-model.attributes.pet.gender'))
                    ->options([
                        'male' => trans('filament-model.attributes.pet.gender_options.male'),
                        'female' => trans('filament-model.attributes.pet.gender_options.female'),
                        'unknown' => trans('filament-model.attributes.pet.gender_options.unknown'),
                    ]),
                DatePicker::make('birthday')
                    ->label(trans('filament-model.attributes.pet.birthday')),
                DatePicker::make('adoption_date')
                    ->label(trans('filament-model.attributes.pet.adoption_date')),
                TextInput::make('avatar')
                    ->label(trans('filament-model.general.avatar'))
                    ->placeholder(trans('filament-model.attributes.pet.avatar_placeholder')),
                KeyValue::make('metadata')
                    ->label(trans('filament-model.attributes.pet.metadata'))
                    ->keyLabel(trans('filament-model.attributes.pet.metadata_key'))
                    ->valueLabel(trans('filament-model.attributes.pet.metadata_value')),
                Toggle::make('is_default')
                    ->label(trans('filament-model.attributes.pet.is_default')),
                Select::make('status')
                    ->label(trans('filament-model.general.status'))
                    ->options([
                        'active' => trans('filament-model.attributes.pet.status_options.active'),
                        'archived' => trans('filament-model.attributes.pet.status_options.archived'),
                        'deceased' => trans('filament-model.attributes.pet.status_options.deceased'),
                    ])
                    ->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('species')
                    ->label(trans('filament-model.attributes.pet.species')),
                TextColumn::make('breed')
                    ->label(trans('filament-model.attributes.pet.breed')),

                TextColumn::make('gender')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'male' => trans('filament-model.attributes.pet.gender_options.male'),
                        'female' => trans('filament-model.attributes.pet.gender_options.female'),
                        default => trans('filament-model.attributes.pet.gender_options.unknown'),
                    }),
                TextColumn::make('user.name')
                    ->label(trans('filament-model.attributes.pet.owner')),
                IconColumn::make('is_default')
                    ->boolean()
                    ->label(trans('filament-model.attributes.pet.is_default_table')),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => trans('filament-model.attributes.pet.status_options.active'),
                        'archived' => trans('filament-model.attributes.pet.status_options.archived'),
                        'deceased' => trans('filament-model.attributes.pet.status_options.deceased'),
                        default => $state,
                    }),
                TextColumn::make('records_count')
                    ->label(trans('filament-model.attributes.pet.records_count'))
                    ->counts('records')
                    ->sortable(),
                TextColumn::make('posts_count')
                    ->label(trans('filament-model.attributes.pet.posts_count'))
                    ->counts('posts')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('species')
                    ->label(trans('filament-model.attributes.pet.species'))
                    ->options(fn (): array => Pet::distinct()->pluck('species', 'species')->toArray()),
                SelectFilter::make('status')
                    ->label(trans('filament-model.general.status'))
                    ->options([
                        'active' => trans('filament-model.attributes.pet.status_options.active'),
                        'archived' => trans('filament-model.attributes.pet.status_options.archived'),
                        'deceased' => trans('filament-model.attributes.pet.status_options.deceased'),
                    ]),
                SelectFilter::make('user_id')
                    ->label(trans('filament-model.general.user'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
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
        return [
            RecordsRelationManager::class,
            PostsRelationManager::class,
        ];
    }

    public static function getModelLabel(): string
    {
        return trans('filament-model.label.pet.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('filament-model.label.pet.plural_label');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPets::route('/'),
            'create' => CreatePet::route('/create'),
            'view' => ViewPet::route('/{record}'),
            'edit' => EditPet::route('/{record}/edit'),
        ];
    }
}
