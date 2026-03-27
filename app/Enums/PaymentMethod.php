<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case EFECTIVO = 'efectivo';
    case TRANSFERENCIA = 'transferencia';
    case CHEQUE = 'cheque';
    case OTRO = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::EFECTIVO => 'Efectivo',
            self::TRANSFERENCIA => 'Transferencia Bancaria',
            self::CHEQUE => 'Cheque',
            self::OTRO => 'Otro',
        };
    }
}
