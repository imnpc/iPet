<?php

namespace App\Filament\Clusters\Pet\Resources\CommentResource\Pages;

use App\Filament\Clusters\Pet\Resources\CommentResource;
use Filament\Resources\Pages\EditRecord;

class EditComment extends EditRecord
{
    protected static string $resource = CommentResource::class;
}
