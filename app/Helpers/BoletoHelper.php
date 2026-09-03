<?php

namespace App\Helpers;

class BoletoHelper
{
    /**
     * Remove pontuação da linha digitável.
     */
    public static function sanitizeDigitableLine(?string $line): string
    {
        return preg_replace('/\D/', '', (string) $line) ?? '';
    }

    /**
     * Converte linha digitável (47 ou 48 dígitos) para código de barras (44 dígitos).
     */
    public static function digitableToBarcode(?string $digitableLine): ?string
    {
        $line = self::sanitizeDigitableLine($digitableLine);

        if (strlen($line) === 47) {
            return self::bankSlipToBarcode($line);
        }

        if (strlen($line) === 48) {
            return self::concessionaireToBarcode($line);
        }

        // Já é código de barras de 44 posições
        if (strlen($line) === 44) {
            return $line;
        }

        return null;
    }

    /**
     * Boleto bancário FEBRABAN — 47 → 44.
     */
    public static function bankSlipToBarcode(string $line): string
    {
        // Banco(3) + Moeda(1) + DV geral(1) + Fator+Valor(14) + Campo livre(25)
        return substr($line, 0, 4)
            . substr($line, 32, 1)
            . substr($line, 33, 14)
            . substr($line, 4, 5)
            . substr($line, 10, 10)
            . substr($line, 21, 10);
    }

    /**
     * Arrecadação/concessionárias — 48 → 44 (remove DVs dos 4 blocos).
     */
    public static function concessionaireToBarcode(string $line): string
    {
        return substr($line, 0, 11)
            . substr($line, 12, 11)
            . substr($line, 24, 11)
            . substr($line, 36, 11);
    }

    /**
     * Formata linha digitável para exibição.
     */
    public static function formatDigitableLine(?string $digitableLine): string
    {
        $line = self::sanitizeDigitableLine($digitableLine);

        if (strlen($line) === 47) {
            return substr($line, 0, 5) . '.' . substr($line, 5, 5) . ' '
                . substr($line, 10, 5) . '.' . substr($line, 15, 6) . ' '
                . substr($line, 21, 5) . '.' . substr($line, 26, 6) . ' '
                . substr($line, 32, 1) . ' '
                . substr($line, 33, 14);
        }

        if (strlen($line) === 48) {
            return substr($line, 0, 12) . ' '
                . substr($line, 12, 12) . ' '
                . substr($line, 24, 12) . ' '
                . substr($line, 36, 12);
        }

        return $digitableLine ?? '';
    }

    /**
     * Converte código de barras bancário (44) para linha digitável (47).
     */
    public static function barcodeToDigitableLine(?string $barcode): ?string
    {
        $barcode = self::sanitizeDigitableLine($barcode);

        if (strlen($barcode) !== 44 || in_array($barcode[0], ['8', '9'], true)) {
            return null;
        }

        $field1 = substr($barcode, 0, 4) . substr($barcode, 19, 5);
        $field2 = substr($barcode, 24, 10);
        $field3 = substr($barcode, 34, 10);

        return $field1 . self::modulo10($field1)
            . $field2 . self::modulo10($field2)
            . $field3 . self::modulo10($field3)
            . substr($barcode, 4, 1)
            . substr($barcode, 5, 14);
    }

    public static function modulo10(string $number): int
    {
        $sum = 0;
        $factor = 2;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $product = (int) $number[$i] * $factor;
            $sum += intdiv($product, 10) + ($product % 10);
            $factor = $factor === 2 ? 1 : 2;
        }

        $remainder = $sum % 10;

        return $remainder === 0 ? 0 : 10 - $remainder;
    }

    /**
     * Valida DV geral do boleto bancário (módulo 11) quando possível.
     */
    public static function isValidBankBarcode(?string $barcode): bool
    {
        if (!$barcode || strlen($barcode) !== 44) {
            return false;
        }

        // Concessionária (produto 8 ou 9) usa outro DV — não valida aqui
        if (in_array($barcode[0], ['8', '9'], true)) {
            return true;
        }

        $dv = (int) $barcode[4];
        $withoutDv = substr($barcode, 0, 4) . substr($barcode, 5);
        $calculated = self::modulo11($withoutDv);

        return $dv === $calculated;
    }

    public static function modulo11(string $number): int
    {
        $factor = 2;
        $sum = 0;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $sum += ((int) $number[$i]) * $factor;
            $factor = $factor === 9 ? 2 : $factor + 1;
        }

        $remainder = $sum % 11;
        $dv = 11 - $remainder;

        if ($dv === 0 || $dv === 10 || $dv === 11) {
            return 1;
        }

        return $dv;
    }
}
