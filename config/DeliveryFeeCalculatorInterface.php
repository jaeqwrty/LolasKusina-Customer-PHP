<?php
/**
 * Delivery Fee Calculator Interface — ISP / DIP abstraction
 * 
 * Defines the contract for delivery fee calculation.
 */
interface DeliveryFeeCalculatorInterface {
    /**
     * Calculate delivery fee based on distance.
     *
     * @param float|null $distanceKm Distance in kilometers (null if unknown)
     * @return float                  Calculated delivery fee
     */
    public function calculate(?float $distanceKm = null): float;

    /**
     * Get the fee for pickup orders.
     *
     * @return float Pickup fee (typically 0.00)
     */
    public function getPickupFee(): float;
}
?>
