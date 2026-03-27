<?php

declare(strict_types=1);

namespace App\Enums;

enum ShipmentStatus: string
{
    case PREPARANDO = 'preparando';
    case EN_TRANSITO = 'en_transito';
    case ENTREGADO = 'entregado';
    case CANCELADO = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::PREPARANDO => 'Preparando',
            self::EN_TRANSITO => 'En Transito',
            self::ENTREGADO => 'Entregado',
            self::CANCELADO => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PREPARANDO => 'yellow',
            self::EN_TRANSITO => 'blue',
            self::ENTREGADO => 'green',
            self::CANCELADO => 'red',
        };
    }
}
