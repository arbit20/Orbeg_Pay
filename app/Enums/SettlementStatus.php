<?php

declare(strict_types=1);

namespace App\Enums;

enum SettlementStatus: string
{
    case PENDIENTE = 'pendiente';
    case PARCIAL = 'parcial';
    case COMPLETADA = 'completada';
    case ANULADA = 'anulada';

    public function label(): string
    {
        return match ($this) {
            self::PENDIENTE => 'Pendiente',
            self::PARCIAL => 'Parcial',
            self::COMPLETADA => 'Completada',
            self::ANULADA => 'Anulada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDIENTE => 'yellow',
            self::PARCIAL => 'orange',
            self::COMPLETADA => 'green',
            self::ANULADA => 'red',
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDIENTE => [self::PARCIAL, self::COMPLETADA, self::ANULADA],
            self::PARCIAL => [self::COMPLETADA, self::ANULADA],
            self::COMPLETADA => [],
            self::ANULADA => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }
}
