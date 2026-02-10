<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Enquiry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Request;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected static bool $canCreateAnother = false;

    public ?int $enquiryId = null;

    public function mount(): void
    {
        parent::mount();

        if ($id = Request::query('enquiry_id')) {
            $this->enquiryId = (int) $id;

            $enquiry = Enquiry::find($this->enquiryId);
            if ($enquiry) {
                $this->form->fill([
                    ...$this->data,
                    'name'    => $enquiry->name,
                    'email'   => $enquiry->email,
                    'contact' => $enquiry->contact,
                    'gender'  => $enquiry->gender,
                    'dob'     => $enquiry->dob,
                    'address' => $enquiry->address,
                    'country' => $enquiry->country,
                    'city'    => $enquiry->city,
                    'state'   => $enquiry->state,
                    'pincode' => $enquiry->pincode,
                    'source'  => $enquiry->source,
                    'goal'    => $enquiry->goal,
                ]);
            }
        }
    }

    protected function afterCreate(): void
    {
        if (! $this->enquiryId) {
            return;
        }

        // reload & update status
        Enquiry::where('id', $this->enquiryId)
            ->update(['status' => 'member']);

        Notification::make()
            ->title('Member Created')
            ->body('Enquiry has been successfully converted to a member.')
            ->success()
            ->send();
    }

    public function getTitle(): string
    {
        return 'New Member';
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Memberships',
            MemberResource::getUrl('index')   => 'Members',
        ];
    }
}
