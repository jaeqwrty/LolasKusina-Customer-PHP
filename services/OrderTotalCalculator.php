<?php
/**
 * Order Total Calculator — SRP service
 * 
 * Encapsulates all order pricing math in one place.
 * Used by OrderController instead of inline calculations.
 */
require_once __DIR__ . '/../config/CalculatorInterface.php';

class OrderTotalCalculator implements CalculatorInterface {
    
    /**
     * Calculate order totals from cart items and optional modifiers.
     *
     * @param array  $cartItems       Items from the session cart
     * @param float  $deliveryFee     Delivery fee to apply
     * @param float  $discount        Discount to subtract
     * @param string $fulfillmentType 'delivery' or 'pickup' (FR-C05 prep)
     * @return array ['subtotal', 'delivery_fee', 'discount', 'total']
     */
    public function calculate(
        array $cartItems,
        float $deliveryFee = 0,
        float $discount = 0,
        string $fulfillmentType = 'delivery'
    ): array {
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // FR-C05: Pickup orders have no delivery fee
        $actualDeliveryFee = ($fulfillmentType === 'pickup') ? 0 : $deliveryFee;
        
        $total = $subtotal + $actualDeliveryFee - $discount;
        
        return [
            'subtotal'     => $subtotal,
            'delivery_fee' => $actualDeliveryFee,
            'discount'     => $discount,
            'total'        => max(0, $total),
        ];
    }
    
    /**
     * Calculate the required downpayment for an order (FR-C07 prep).
     * Default: 50% of the grand total.
     *
     * @param float $grandTotal The order's grand total
     * @param float $percentage Downpayment percentage (0.0–1.0)
     * @return float            Required downpayment amount
     */
    public function calculateDownpayment(float $grandTotal, float $percentage = 0.50): float {
        return round($grandTotal * $percentage, 2);
    }
}
?>
