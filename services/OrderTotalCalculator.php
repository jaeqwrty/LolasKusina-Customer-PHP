<?php
/**
 * Order Total Calculator — SRP service
 * 
 * Encapsulates all order pricing math in one place.
 * Used by OrderController instead of inline calculations.
 */

class OrderTotalCalculator {
    
    /**
     * Calculate order totals from cart items and optional modifiers.
     *
     * @param array  $cartItems   Items from the session cart
     * @param float  $deliveryFee Delivery fee to apply
     * @param float  $discount    Discount to subtract
     * @return array ['subtotal' => float, 'delivery_fee' => float, 'discount' => float, 'total' => float]
     */
    public function calculate(array $cartItems, float $deliveryFee = 0, float $discount = 0): array {
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $total = $subtotal + $deliveryFee - $discount;
        
        return [
            'subtotal'     => $subtotal,
            'delivery_fee' => $deliveryFee,
            'discount'     => $discount,
            'total'        => max(0, $total),
        ];
    }
}
?>
