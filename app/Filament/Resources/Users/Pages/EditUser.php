<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use STS\FilamentImpersonate\Actions\Impersonate;

/**
 * 用户编辑页面。
 */
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Impersonate::make()
                ->guard('web')
                ->redirectTo(route('home'))
                ->withoutSpa()
                ->record($this->getRecord()),
            DeleteAction::make(),
        ];
    }
}
