<?php

namespace App\Filament\Clusters\Pet\Resources;

use App\Filament\Clusters\Pet\PetCluster;
use App\Filament\Clusters\Pet\Resources\PetSpeciesResource\Pages\CreatePetSpecies;
use App\Filament\Clusters\Pet\Resources\PetSpeciesResource\Pages\EditPetSpecies;
use App\Filament\Clusters\Pet\Resources\PetSpeciesResource\Pages\ListPetSpecies;
use App\Models\PetSpecies;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;

class PetSpeciesResource extends Resource implements Translateable
{
    use HasTranslateableResources;

    protected static ?string $translateablePackageKey = '';

    protected static ?string $model = PetSpecies::class;

    protected static ?string $cluster = PetCluster::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 20;

    public static function getNavigationParentItem(): ?string
    {
        return null;
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(trans('filament-model.attributes.pet_species.name'))
                    ->required()
                    ->maxLength(50),
                TextInput::make('icon')
                    ->label(trans('filament-model.attributes.pet_species.icon'))
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->label(trans('filament-model.attributes.pet_species.sort_order'))
                    ->numeric()
                    ->default(0),
                Toggle::make('is_enabled')
                    ->label(trans('filament-model.attributes.pet_species.is_enabled'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(trans('filament-model.attributes.pet_species.name'))
                    ->searchable(),
                TextColumn::make('icon')
                    ->label(trans('filament-model.attributes.pet_species.icon')),

                TextColumn::make('sort_order')
                    ->label(trans('filament-model.attributes.pet_species.sort_order'))
                    ->sortable(),
                IconColumn::make('is_enabled')
                    ->label(trans('filament-model.attributes.pet_species.is_enabled'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(trans('filament-model.general.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
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

    public static function getPages(): array
    {
        return [
            'index' => ListPetSpecies::route('/'),
            'create' => CreatePetSpecies::route('/create'),
            'edit' => EditPetSpecies::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return trans('filament-model.label.pet_species.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('filament-model.label.pet_species.plural_label');
    }
}
