<?php

namespace App\Filament\Clusters\Pet\Resources\PetResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'records';

    public static function getTitle(
        Model $ownerRecord,
        string $pageClass
    ): string {
        return trans('filament-model.label.pet_record.plural_label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pet_record_type_id')
                    ->label(trans('filament-model.general.type'))
                    ->relationship('type', 'name')
                    ->preload()
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
                TextInput::make('weight')
                    ->label(trans('filament-model.attributes.pet_record.weight'))
                    ->numeric()
                    ->suffix('kg'),
                TextInput::make('temperature')
                    ->label(trans('filament-model.attributes.pet_record.temperature'))
                    ->numeric()
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('type.name')
                    ->label(trans('filament-model.general.type')),
                TextColumn::make('title'),
                TextColumn::make('visit_date')
                    ->date(),
                TextColumn::make('hospital_name'),
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
