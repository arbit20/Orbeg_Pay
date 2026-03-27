<?php

declare(strict_types=1);

namespace App\Enums;

enum SaleStatus: string
{
    case BORRADOR = 'borrador';
    case CONFIRMADA = 'confirmada';
    case LIQUIDADA = 'liquidada';
    case CANCELADA = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::BORRADOR => 'Borrador',
            self::CONFIRMADA => 'Confirmada',
            self::LIQUIDADA => 'Liquidada',
            self::CANCELADA => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BORRADOR => 'gray',
            self::CONFIRMADA => 'blue',
            self::LIQUIDADA => 'green',
            self::CANCELADA => 'red',
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::BORRADOR => [self::CONFIRMADA, self::CANCELADA],
            self::CONFIRMADA => [self::LIQUIDADA, self::CANCELADA],
            self::LIQUIDADA => [],
            self::CANCELADA => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }
}
