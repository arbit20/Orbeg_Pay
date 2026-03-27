<?php

declare(strict_types=1);

namespace App\Enums;

enum MovementType: string
{
    case ENTRADA = 'entrada';
    case SALIDA = 'salida';
    case AJUSTE = 'ajuste';

    public function label(): string
    {
        return match ($this) {
            self::ENTRADA => 'Entrada',
            self::SALIDA => 'Salida',
            self::AJUSTE => 'Ajuste',
        };
    }

    public function sign(): int
    {
        return match ($this) {
            self::ENTRADA => 1,
            self::SALIDA => -1,
            self::AJUSTE => 0,
        };
    }
}
