<?php

namespace App\Filament\Resources\FollowUpResource\Pages;

use App\Filament\Resources\FollowUpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFollowUp extends EditRecord
{
    protected static string $resource = FollowUpResource::class;

    public function getTitle(): string
    {
        return 'Update Follow Up: '. $this->record->id ;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
