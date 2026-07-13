<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'Admin';
    case AGENT = 'Agent';
    case CLIENT = 'Client';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrateur',
            self::AGENT => 'Agent',
            self::CLIENT => 'Client',
        };
    }
}
