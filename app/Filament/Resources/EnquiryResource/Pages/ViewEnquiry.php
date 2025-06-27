<?php

namespace App\Filament\Resources\EnquiryResource\Pages;

use App\Filament\Resources\EnquiryResource;
use App\Filament\Resources\MemberResource;
use App\Models\Enquiry;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEnquiry extends ViewRecord
{
    protected static string $resource = EnquiryResource::class;

    public function getTitle(): string
    {
        return 'Enquiry ' . $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
            Action::make('convert_to_member')
                ->label('Convert to Member')
                ->icon('heroicon-s-arrows-right-left')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn(Enquiry $record) => $record->status === 'lead')
                ->url(fn(Enquiry $record) => MemberResource::getUrl(
                    'create',
                    ['enquiry_id' => $record->id],
                )),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Sales',
            EnquiryResource::getUrl('index')   => 'Enquiries',
            $this->record->name,
        ];
    }
}
