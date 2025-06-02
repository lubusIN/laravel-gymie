<?php

namespace App\Filament\Resources\EnquiryResource\RelationManagers;

use App\Models\Enquiry;
use App\Models\FollowUp;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FollowUpsRelationManager extends RelationManager
{
    protected static string $relationship = 'follow_up';

    protected static ?string $title = 'Follow Up Timeline';

    /**
     * Determine if the relation manager is read-only.
     *
     * @return bool Returns false, indicating the relation manager is not read-only.
     */
    public function isReadOnly(): bool
    {
        return false;
    }

    /**
     * Define the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('enquiry_id')
                    ->label('Enquiry')
                    ->options(fn() => Enquiry::pluck('name', 'id'))
                    ->disabled()
                    ->visibleOn('view'),
                DatePicker::make('date')
                    ->label('Date')
                    ->default(now())
                    ->disabledOn('edit')
                    ->hiddenOn('create'),
                Select::make('follow_up_method')
                    ->options([
                        'call' => 'Call',
                        'email' => 'Email',
                        'in_person' => 'In person',
                        'whatsapp' => 'WhatsApp',
                        'other' => 'Others'
                    ])->default('call')
                    ->required()
                    ->label('Follow-up method')
                    ->searchable(),
                DatePicker::make('due_date')
                    ->native(false)
                    ->label('Due Date')
                    ->displayFormat('d-m-Y')
                    ->closeOnDateSelection()
                    ->suffixIcon('heroicon-m-calendar-days')
                    ->minDate(now())
                    ->disabledOn('edit'),
                Textarea::make('outcome')
                    ->placeholder('Not interested, etc.')
                    ->label('Outcome')
                    ->required()
                    ->hiddenOn('create'),
            ]);
    }

    /**
     * Define the table for listing records in the resource.
     *
     * @param \Filament\Tables\Table $table
     * @return \Filament\Tables\Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('id', 'desc')
            ->columns(FollowUp::getTableColumns())
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-m-plus')
                    ->visible(fn() => $this->getOwnerRecord()->follow_up()->exists()),
            ])
            ->emptyStateIcon('heroicon-o-arrow-path-rounded-square')
            ->emptyStateHeading('No Follow-Ups')
            ->emptyStateDescription('Create follow-ups to begin tracking.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make('create-followUps')
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => !$this->getOwnerRecord()->follow_up()->exists()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Status')
                            ->visible(fn($record) => in_array($record->status, ['pending']))
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\Action::make('mark_as_done')
                            ->color('success')
                            ->label('Mark as Done')
                            ->requiresConfirmation()
                            ->action(fn(FollowUp $record) => tap($record, function ($record) {
                                $record->update(['status' => 'done']);
                                Notification::make()
                                    ->title('Marked as done')
                                    ->success()
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status === 'pending'),
                    ])->dropdown(false),
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\EditAction::make()->hiddenLabel(),
                        Tables\Actions\ViewAction::make()->hiddenLabel(),
                        Tables\Actions\DeleteAction::make()->hiddenLabel(),
                    ])->dropdown(false),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
