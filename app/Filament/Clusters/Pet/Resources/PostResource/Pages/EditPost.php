<?php

namespace App\Filament\Clusters\Pet\Resources\PostResource\Pages;

use App\Filament\Clusters\Pet\Resources\PostResource;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;
}
