<?php

namespace App\Filament\Clusters\Pet\Resources;

use App\Filament\Clusters\Pet\PetCluster;
use App\Filament\Clusters\Pet\Resources\PetRecordResource\Pages\CreatePetRecord;
use App\Filament\Clusters\Pet\Resources\PetRecordResource\Pages\EditPetRecord;
use App\Filament\Clusters\Pet\Resources\PetRecordResource\Pages\ListPetRecords;
use App\Models\PetRecord;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maggomann\FilamentModelTranslator\Contracts\Translateable;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableResources;

class PetRecordResource extends Resource implements Translateable
{
    use HasTranslateableResources; // 翻译

    protected static ?string $translateablePackageKey = '';

    protected static ?string $model = PetRecord::class;

    protected static ?string $cluster = PetCluster::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pet_id')
                    ->label(trans('filament-model.label.pet.label'))
                    ->relationship('pet', 'name')
                    ->required(),
                Select::make('type')
                    ->label(trans('filament-model.general.type'))
                    ->options([
                        'vaccine' => trans('filament-model.attributes.pet_record.type_options.vaccine'),
                        'checkup' => trans('filament-model.attributes.pet_record.type_options.checkup'),
                        'illness' => trans('filament-model.attributes.pet_record.type_options.illness_detail'),
                        'medication' => trans('filament-model.attributes.pet_record.type_options.medication'),
                        'surgery' => trans('filament-model.attributes.pet_record.type_options.surgery'),
                        'grooming' => trans('filament-model.attributes.pet_record.type_options.grooming_detail'),
                        'other' => trans('filament-model.attributes.pet_record.type_options.other'),
                    ])
                    ->required(),
                TextInput::make('title')
                    ->label(trans('filament-model.general.name'))
                    ->required()
                    ->maxLength(200),
                DatePicker::make('visit_date')
                    ->label(trans('filament-model.attributes.pet_record.visit_date'))
                    ->required(),
                DatePicker::make('next_visit_date')
                    ->label(trans('filament-model.attributes.pet_record.next_visit_date')),
                TextInput::make('hospital_name')
                    ->label(trans('filament-model.attributes.pet_record.hospital_name'))
                    ->maxLength(200),

                TextInput::make('vet_name')
                    ->label(trans('filament-model.attributes.pet_record.vet_name'))
                    ->maxLength(100),
                TextInput::make('hospital_phone')
                    ->label(trans('filament-model.attributes.pet_record.hospital_phone'))
                    ->maxLength(20),
                TextInput::make('weight')
                    ->label(trans('filament-model.attributes.pet_record.weight'))
                    ->numeric()
                    ->step(0.01)
                    ->suffix('kg'),
                TextInput::make('temperature')
                    ->label(trans('filament-model.attributes.pet_record.temperature'))
                    ->numeric()
                    ->step(0.1)
                    ->suffix('℃'),
                Textarea::make('symptoms')
                    ->label(trans('filament-model.attributes.pet_record.symptoms'))
                    ->rows(3),
                Textarea::make('diagnosis')
                    ->label(trans('filament-model.attributes.pet_record.diagnosis'))
                    ->rows(3),
                Textarea::make('treatment')
                    ->label(trans('filament-model.attributes.pet_record.treatment'))
                    ->rows(3),
                Textarea::make('prescription')
                    ->label(trans('filament-model.attributes.pet_record.prescription'))
                    ->rows(3),
                Textarea::make('notes')
                    ->label(trans('filament-model.general.remark'))
                    ->rows(3),
                TextInput::make('cost')
                    ->label(trans('filament-model.attributes.pet_record.cost'))
                    ->numeric()
                    ->prefix('¥'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pet.name')
                    ->label(trans('filament-model.label.pet.label')),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'vaccine' => trans('filament-model.attributes.pet_record.type_options.vaccine'),
                        'checkup' => trans('filament-model.attributes.pet_record.type_options.checkup'),
                        'illness' => trans('filament-model.attributes.pet_record.type_options.illness'),
                        'medication' => trans('filament-model.attributes.pet_record.type_options.medication'),
                        'surgery' => trans('filament-model.attributes.pet_record.type_options.surgery'),
                        'grooming' => trans('filament-model.attributes.pet_record.type_options.grooming'),
                        default => trans('filament-model.attributes.pet_record.type_options.other'),
                    }),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('visit_date')
                    ->date(),
                TextColumn::make('weight')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('kg'),
                TextColumn::make('hospital_name'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(trans('filament-model.general.type'))
                    ->options([
                        'vaccine' => trans('filament-model.attributes.pet_record.type_options.vaccine'),
                        'checkup' => trans('filament-model.attributes.pet_record.type_options.checkup'),
                        'illness' => trans('filament-model.attributes.pet_record.type_options.illness'),
                        'medication' => trans('filament-model.attributes.pet_record.type_options.medication'),
                        'surgery' => trans('filament-model.attributes.pet_record.type_options.surgery'),
                        'grooming' => trans('filament-model.attributes.pet_record.type_options.grooming'),
                        'other' => trans('filament-model.attributes.pet_record.type_options.other'),
                    ]),
                SelectFilter::make('pet_id')
                    ->label(trans('filament-model.label.pet.label'))
                    ->relationship('pet', 'name'),
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
        return trans('filament-model.label.pet_record.label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('filament-model.label.pet_record.plural_label');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPetRecords::route('/'),
            'create' => CreatePetRecord::route('/create'),
            'edit' => EditPetRecord::route('/{record}/edit'),
        ];
    }
}
