<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentType: string
{
    case RUC = 'RUC';
    case DNI = 'DNI';
    case CE = 'CE';
    case PASAPORTE = 'PASAPORTE';

    public function label(): string
    {
        return match ($this) {
            self::RUC => 'RUC',
            self::DNI => 'DNI',
            self::CE => 'Carnet de Extranjeria',
            self::PASAPORTE => 'Pasaporte',
        };
    }
}
