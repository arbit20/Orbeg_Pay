<?php

declare(strict_types=1);

namespace App\Enums;

enum PurchaseRequestStatus: string
{
    case PENDIENTE = 'pendiente';
    case EN_REVISION = 'en_revision';
    case APROBADA = 'aprobada';
    case RECHAZADA = 'rechazada';
    case CONVERTIDA = 'convertida';

    public function label(): string
    {
        return match ($this) {
            self::PENDIENTE => 'Pendiente',
            self::EN_REVISION => 'En Revisión',
            self::APROBADA => 'Aprobada',
            self::RECHAZADA => 'Rechazada',
            self::CONVERTIDA => 'Convertida a Compra',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDIENTE => 'amber',
            self::EN_REVISION => 'blue',
            self::APROBADA => 'green',
            self::RECHAZADA => 'red',
            self::CONVERTIDA => 'indigo',
        };
    }

    /**
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDIENTE => [self::EN_REVISION, self::RECHAZADA],
            self::EN_REVISION => [self::APROBADA, self::RECHAZADA],
            self::APROBADA => [self::CONVERTIDA],
            self::RECHAZADA => [],
            self::CONVERTIDA => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }
}
