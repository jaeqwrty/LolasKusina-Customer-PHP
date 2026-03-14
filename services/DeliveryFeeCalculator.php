<?php
/**
 * Delivery Fee Calculator — SRP service (FR-C06 prep)
 * 
 * Calculates delivery fees based on distance.
 * SRS Rule: ₱100 for ≤10km, +₱12 per km beyond 10km.
 * 
 * Currently returns a flat fee. When Google Maps Distance Matrix API
 * is integrated, the distance parameter will come from the API response.
 */

class DeliveryFeeCalculator {
    
    /** Base fee for deliveries within the base distance (in PHP). */
    private const BASE_FEE = 100.00;
    
    /** Maximum distance (km) covered by the base fee. */
    private const BASE_DISTANCE_KM = 10.0;
    
    /** Additional fee per km beyond the base distance. */
    private const PER_KM_FEE = 12.00;
    
    /** Default flat fee when distance is unknown. */
    private const DEFAULT_FLAT_FEE = 50.00;
    
    /**
     * Calculate delivery fee based on distance.
     *
     * @param float|null $distanceKm Distance in kilometers (null if unknown)
     * @return float                  Calculated delivery fee in PHP
     */
    public function calculate(?float $distanceKm = null): float {
        // Until Google Maps API is integrated, return flat fee for unknown distance
        if ($distanceKm === null) {
            return self::DEFAULT_FLAT_FEE;
        }
        
        if ($distanceKm <= 0) {
            return 0.00;
        }
        
        if ($distanceKm <= self::BASE_DISTANCE_KM) {
            return self::BASE_FEE;
        }
        
        $extraKm = $distanceKm - self::BASE_DISTANCE_KM;
        return self::BASE_FEE + ($extraKm * self::PER_KM_FEE);
    }
    
    /**
     * Get the fee for pickup orders (always free).
     *
     * @return float Always 0.00
     */
    public function getPickupFee(): float {
        return 0.00;
    }
}
?>
