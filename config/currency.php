<?php

declare(strict_types=1);

return [
    /**
     * Tipo de cambio (PEN por 1 USD).
     * Se usa para conversiones referenciales en pantallas (cotizador, reportes, etc).
     */
    'usd_pen' => env('CURRENCY_USD_PEN', '3.75'),

    /**
     * Escala decimal para conversiones.
     */
    'scale' => (int) env('CURRENCY_SCALE', 4),
];

