<?php

namespace App\Filament\Clusters\Pet\Resources\CommentResource\Pages;

use App\Filament\Clusters\Pet\Resources\CommentResource;
use Filament\Resources\Pages\ListRecords;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;
}
