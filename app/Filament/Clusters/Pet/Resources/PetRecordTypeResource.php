<?php

namespace App\Filament\Clusters\Pet\Resources;

use App\Filament\Clusters\MedicalCluster;
use App\Filament\Clusters\Pet\Resources\PetRecordTypeResource\Pages\CreatePetRecordType;
use App\Filament\Clusters\Pet\Resources\PetRecordTypeResource\Pages\EditPetRecordType;
use App\Filament\Clusters\Pet\Resources\PetRecordTypeResource\Pages\ListPetRecordTypes;
use App\Models\PetRecordType;
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

class PetRecordTypeResource extends Resource implements Translateable
{
    use HasTranslateableResources;

    protected static ?string $translateablePackageKey = '';

    protected static ?string $model = PetRecordType::class;

    protected static ?string $cluster = MedicalCluster::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(trans('filament-model.attributes.pet_record_type.name'))
                    ->required()
                    ->maxLength(50),
                TextInput::make('slug')
                    ->label(trans('filament-model.attributes.pet_record_type.slug'))
                    ->required()
                    ->maxLength(30),
                TextInput::make('color')
                    ->label(trans('filament-model.attributes.pet_record_type.color'))
                    ->maxLength(20),
                TextInput::make('sort_order')
                    ->label(trans('filament-model.attributes.pet_record_type.sort_order'))
                    ->numeric()
                    ->default(0),
                Toggle::make('is_enabled')
                    ->label(trans('filament-model.attributes.pet_record_type.is_enabled'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(trans('filament-model.attributes.pet_record_type.name'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(trans('filament-model.attributes.pet_record_type.slug')),

                TextColumn::make('color')
                    ->label(trans('filament-model.attributes.pet_record_type.color')),

                TextColumn::make('sort_order')
                    ->label(trans('filament-model.attributes.pet_record_type.sort_order'))
                    ->sortable(),
                IconColumn::make('is_enabled')
                    ->label(trans('filament-model.attributes.pet_record_type.is_enabled'))
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
            'index' => ListPetRecordTypes::route('/'),
            'create' => CreatePetRecordType::route('/create'),
            'edit' => EditPetRecordType::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return trans('filament-model.label.pet_record_type.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('filament-model.label.pet_record_type.plural_label');
    }
}
