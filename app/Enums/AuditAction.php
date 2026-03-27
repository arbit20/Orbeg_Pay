<?php

declare(strict_types=1);

namespace App\Enums;

enum AuditAction: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RESTORED = 'restored';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Creado',
            self::UPDATED => 'Actualizado',
            self::DELETED => 'Eliminado',
            self::RESTORED => 'Restaurado',
        };
    }
}
