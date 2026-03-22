<?php
/**
 * Calculator Interface — ISP / DIP abstraction
 * 
 * Defines the contract for order total calculation.
 * Controllers depend on this, not the concrete OrderTotalCalculator.
 */
interface CalculatorInterface {
    /**
     * Calculate order totals from cart items and optional modifiers.
     *
     * @param array  $cartItems       Items from the session cart
     * @param float  $deliveryFee     Delivery fee to apply
     * @param float  $discount        Discount to subtract
     * @param string $fulfillmentType 'delivery' or 'pickup'
     * @return array ['subtotal', 'delivery_fee', 'discount', 'total']
     */
    public function calculate(
        array $cartItems,
        float $deliveryFee = 0,
        float $discount = 0,
        string $fulfillmentType = 'delivery'
    ): array;

    /**
     * Calculate the required downpayment for an order.
     *
     * @param float $grandTotal The order's grand total
     * @param float $percentage Downpayment percentage (0.0–1.0)
     * @return float            Required downpayment amount
     */
    public function calculateDownpayment(float $grandTotal, float $percentage = 0.50): float;
}
?>
