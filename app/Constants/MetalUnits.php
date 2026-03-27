<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Constantes de conversion para unidades de peso de metales preciosos.
 * Fuente unica de verdad para todo calculo de unidades en el sistema.
 */
final class MetalUnits
{
    /** 1 onza troy = 31.1035 gramos */
    public const TROY_OUNCE_IN_GRAMS = '31.1035';

    /** Precision decimal para calculos financieros */
    public const FINANCIAL_SCALE = 4;

    /** Precision decimal para calculos intermedios */
    public const INTERMEDIATE_SCALE = 10;

    /**
     * Convierte gramos a onzas troy.
     */
    public static function gramsToTroyOunces(string $grams): string
    {
        return bcdiv($grams, self::TROY_OUNCE_IN_GRAMS, self::FINANCIAL_SCALE);
    }

    /**
     * Convierte onzas troy a gramos.
     */
    public static function troyOuncesToGrams(string $ounces): string
    {
        return bcmul($ounces, self::TROY_OUNCE_IN_GRAMS, self::FINANCIAL_SCALE);
    }
}
