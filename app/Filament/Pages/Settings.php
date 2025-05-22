<?php

namespace App\Filament\Pages;

use App\Helpers\Helpers;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    /** @var string|null Page title */
    protected static ?string $title = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    /** @var string|null Stores the uploaded settings file */
    public ?string $settings_file = null;

    /**
     * Mount the page and load settings from the storage.
     */
    public function mount(): void
    {
        $settings = Helpers::getSettings();
        $this->data = $settings;

        // Ensure logo is always set correctly
        foreach (['gym_logo'] as $logoType) {
            if (!empty($this->data['general'][$logoType]) && is_array($this->data['general'][$logoType])) {
                $this->data['general'][$logoType] = $this->data['general'][$logoType];
            }
        }

        $this->form->fill($settings);
    }

    /**
     * Defines the form schema with multiple tabs.
     *
     * @return array
     */
    protected function getFormSchema(): array
    {
        return [
            Tabs::make('Settings Tabs')
                ->tabs([
                    $this->generalTab(),
                    $this->invoiceTab(),
                    $this->memberTab(),
                ])
        ];
    }
    /**
     * Business Info Tab Schema.
     *
     * @return Forms\Components\Tabs\Tab
     */
    private function generalTab()
    {
        return Tab::make('Gym Info')
            ->icon('heroicon-m-briefcase')
            ->schema([
                Section::make('General Information')
                    ->aside()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('general.gym_name')
                                    ->label('Gym Name'),
                                FileUpload::make('general.gym_logo')
                                    ->label('Gym Logo')
                                    ->disk('public')
                                    ->directory('images')
                                    ->preserveFilenames()
                                    ->imageEditor()
                                    ->deletable()
                                    ->visibility('public')
                                    ->image()
                                    ->afterStateUpdated(fn($state, callable $set) => $this->handleFileUpload($state, 'logo', $set)),
                            ]),
                    ])
                    ->columnSpan(3),

                Section::make('Address')
                    ->aside()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Textarea::make('general.address')
                                    ->label('Address'),
                            ]),
                        Grid::make(4)
                            ->schema([
                                Select::make('general.country')
                                    ->label('Country')
                                    ->options(Helpers::getCountries())
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set) => [
                                        $set('general.state', null),
                                        $set('general.city', null),
                                    ]),
                                Select::make('general.state')
                                    ->label('State')
                                    ->options(fn($get) => Helpers::getStates($get('general.country')))
                                    ->searchable()
                                    ->reactive(),
                                Select::make('general.city')
                                    ->label('City')
                                    ->options(fn($get) => Helpers::getCities($get('general.state')))
                                    ->searchable()
                                    ->reactive(),
                                TextInput::make('general.zip')
                                    ->label('Zip Code')
                                    ->numeric()
                                    ->maxLength(10),
                            ]),
                    ])
                    ->columnSpan(3),

                Section::make('Contact Information')
                    ->aside()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('general.gym_email')
                                    ->label('Email Address')
                                    ->email()
                                    ->prefixIcon('heroicon-o-envelope'),
                                TextInput::make('general.gym_contact')
                                    ->numeric()
                                    ->prefixIcon('heroicon-o-phone')
                                    ->label('Contact No.'),
                            ]),
                    ])
                    ->columnSpan(3),

                
            ]);
    }

    /**
     * Invoice Tab Schema.
     *
     * @return Forms\Components\Tabs\Tab
     */
    private function invoiceTab()
    {
        return (
            Tab::make('Invoice')->icon('heroicon-m-document-text')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('invoice.invoice_prefix')
                                ->label('Prefix')
                                ->placeholder('GY'),
                            TextInput::make('invoice.invoice_number')
                                ->label('Number')
                                ->numeric()
                                ->maxLength(10),
                            Select::make('invoice.name_type')
                                ->label('Name Type')
                                ->native(false)
                                ->options([
                                    'gym_name' => 'Gym Name',
                                    'gym_logo' => 'Gym Logo'
                                ]),
                        ]),
                ])
        );
    }

     /**
     * Invoice Tab Schema.
     *
     * @return Forms\Components\Tabs\Tab
     */
    private function memberTab()
    {
        return (
            Tab::make('Member')->icon('heroicon-m-user-group')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('member.invoice_prefix')
                                ->label('Prefix')
                                ->placeholder('GY'),
                            TextInput::make('member.invoice_number')
                                ->label('Number')
                                ->numeric()
                                ->maxLength(10),
                        ]),
                ])
        );
    }

    /**
     * Configures a form instance by setting its schema and state path.
     *
     * @param Form $form The form instance to configure.
     * @return Form The configured form instance.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data');
    }

    /**
     * Saves settings to JSON file.
     */
    public function save()
    {
        $path = storage_path('data/settingsData.json');

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, json_encode($this->data, JSON_PRETTY_PRINT));

        Notification::make()
            ->title('Success')
            ->body('Settings saved successfully!')
            ->success()
            ->send();
    }

    /**
     * Handles the file upload process and updates the settings data.
     *
     * @param TemporaryUploadedFile|string|null $state The uploaded file state.
     * @param string $key The key to store the uploaded file path in the settings.
     * @param callable $set The callback to update the form state.
     */
    private function handleFileUpload($state, string $key, callable $set)
    {
        if (!$state instanceof TemporaryUploadedFile) {
            return;
        }

        $path = $state->storeAs('images', $state->getClientOriginalName(), 'public');
        $jsonPath = storage_path('data/settingsData.json');
        $jsonData = Helpers::getSettings();

        // Ensure 'business_info' and its key exist as an array
        $jsonData['general'] = $jsonData['general'] ?? [];
        $jsonData['general'][$key] = $path;

        // Save updated data
        if (file_put_contents($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT)) === false) {
            throw new \RuntimeException("Failed to write to settings file.");
        }

        // Update the form state
        $set("general.$key", [$path]);
    }
}
