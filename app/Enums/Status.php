<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasLabel
{
    case Pending   = 'pending';
    case Done      = 'done';
    case Active    = 'active';
    case Inactive  = 'inactive';
    case Issued    = 'issued';
    case Cancelled = 'cancelled';
    case Refund    = 'refund';
    case Overdue   = 'overdue';
    case Paid      = 'paid';
    case Partial   = 'partial';
    case Ongoing   = 'ongoing';
    case Expiring  = 'expiring';
    case Expired   = 'expired';
    case Lead      = 'lead';
    case Lost      = 'lost';
    case Member    = 'member';

    private const COLORS = [
        self::Pending->value    => 'warning',
        self::Done->value       => 'success',
        self::Active->value     => 'success',
        self::Inactive->value   => 'danger',
        self::Issued->value     => 'gray',
        self::Cancelled->value  => 'danger',
        self::Refund->value     => 'danger',
        self::Overdue->value    => 'warning',
        self::Paid->value       => 'success',
        self::Partial->value    => 'info',
        self::Ongoing->value    => 'info',
        self::Expiring->value   => 'warning',
        self::Expired->value    => 'danger',
        self::Lead->value       => 'info',
        self::Lost->value       => 'danger',
        self::Member->value     => 'success',
    ];

    public function getLabel(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return self::COLORS[$this->value] ?? 'secondary';
    }
}
