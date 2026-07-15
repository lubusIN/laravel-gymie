<?php

use App\Filament\Resources\Plans\Schemas\PlanForm;
use App\Models\Plan;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

it('denies inline service creation without service permission', function (): void {
    $this->actingAs(User::factory()->create());

    $livewire = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        public function render(): string
        {
            return '<div></div>';
        }
    };
    $schema = PlanForm::configure(Schema::make($livewire)->model(Plan::class));
    $serviceField = $schema->getComponentByStatePath('service_id');

    expect($serviceField)->toBeInstanceOf(Select::class)
        ->and($serviceField->getCreateOptionAction()?->isAuthorized())->toBeFalse();
});

it('allows inline service creation with service permission', function (): void {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $permission = Permission::findOrCreate('Create:Service', 'web');
    $user = User::factory()->create();
    $user->givePermissionTo($permission);
    $this->actingAs($user);

    $livewire = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        public function render(): string
        {
            return '<div></div>';
        }
    };
    $schema = PlanForm::configure(Schema::make($livewire)->model(Plan::class));
    $serviceField = $schema->getComponentByStatePath('service_id');

    expect($serviceField)->toBeInstanceOf(Select::class)
        ->and($serviceField->getCreateOptionAction()?->isAuthorized())->toBeTrue();
});
