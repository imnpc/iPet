<?php

namespace App\Filament\Clusters\Pet\Resources\PostResource\Pages;

use App\Filament\Clusters\Pet\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
