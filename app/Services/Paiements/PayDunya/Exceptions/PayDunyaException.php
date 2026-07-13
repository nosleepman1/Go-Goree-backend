<?php

declare(strict_types=1);

namespace App\Services\Paiements\PayDunya\Exceptions;

use RuntimeException;

/**
 * Exception levée lorsqu'une opération PayDunya échoue (configuration manquante,
 * réponse d'erreur de l'API, réseau indisponible, etc.).
 */
class PayDunyaException extends RuntimeException {}
