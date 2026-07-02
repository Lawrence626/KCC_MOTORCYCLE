<?php

namespace App\Services;

class VatCalculationService
{
    const VAT_RATE = 0.12; // 12% VAT rate

    /**
     * Calculate VAT-Inclusive pricing
     * 
     * @param float $subtotal Sum of product prices (already VAT-inclusive)
     * @param float $servicesTotal Sum of services (already VAT-inclusive)
     * @param float $extraCharges Extra charges (already VAT-inclusive)
     * @param float $discount Discount amount
     * @return array
     */
    public function calculateVatInclusive(float $subtotal, float $servicesTotal = 0, float $extraCharges = 0, float $discount = 0): array
    {
        // Total = Subtotal + Services + Extra - Discount
        // VAT is already included in all prices
        $total = max(0, $subtotal + $servicesTotal + $extraCharges - $discount);
        
        // Included VAT = Total × (12 / 112)
        $includedVat = $total * (self::VAT_RATE / (1 + self::VAT_RATE));
        
        // VATable Sales = Total - Included VAT
        $vatableSales = $total - $includedVat;
        
        return [
            'subtotal' => $subtotal,
            'services_total' => $servicesTotal,
            'extra_charges' => $extraCharges,
            'discount' => $discount,
            'total' => $total,
            'included_vat' => $includedVat,
            'vatable_sales' => $vatableSales,
            'vat_rate' => self::VAT_RATE,
        ];
    }

    /**
     * Calculate included VAT from a VAT-inclusive amount
     * 
     * @param float $amount VAT-inclusive amount
     * @return float
     */
    public function calculateIncludedVat(float $amount): float
    {
        return $amount * (self::VAT_RATE / (1 + self::VAT_RATE));
    }

    /**
     * Calculate VATable sales from a VAT-inclusive amount
     * 
     * @param float $amount VAT-inclusive amount
     * @return float
     */
    public function calculateVatableSales(float $amount): float
    {
        $includedVat = $this->calculateIncludedVat($amount);
        return $amount - $includedVat;
    }

    /**
     * Format currency for display
     * 
     * @param float $amount
     * @return string
     */
    public function formatCurrency(float $amount): string
    {
        return '₱' . number_format($amount, 2);
    }
}
