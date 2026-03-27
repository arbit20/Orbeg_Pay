<?php

declare(strict_types=1);

namespace App\Services;

final class CurrencyConverter
{
    public function __construct(
        private readonly string $usdPenRate,
        private readonly int $scale = 4,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            (string) config('currency.usd_pen', '3.75'),
            (int) config('currency.scale', 4),
        );
    }

    /**
     * @param  string  $amount  decimal string
     * @return string decimal string
     */
    public function convert(string $amount, string $from, string $to): string
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return $this->round($amount);
        }

        if ($from === 'USD' && $to === 'PEN') {
            return $this->round(bcmul($amount, $this->usdPenRate, $this->scale + 2));
        }

        if ($from === 'PEN' && $to === 'USD') {
            return $this->round(bcdiv($amount, $this->usdPenRate, $this->scale + 2));
        }

        // Si aparecen nuevas monedas, se decide explícitamente.
        throw new \InvalidArgumentException("Unsupported currency conversion: {$from} -> {$to}");
    }

    private function round(string $amount): string
    {
        // bc math no tiene round directo; formateamos truncando a escala final.
        // Para UI referencial es suficiente; si necesitas redondeo bancario, lo ajustamos.
        return bcadd($amount, '0', $this->scale);
    }
}

