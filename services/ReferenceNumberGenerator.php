<?php
/**
 * Reference Number Generator — SRP service (FR-C08 prep)
 * 
 * Generates unique alphanumeric reference numbers for order tracking.
 * Format: LK-YYYYMMDD-XXXX (e.g., LK-20260314-A7B2)
 */

class ReferenceNumberGenerator {
    
    private const PREFIX = 'LK';
    private const RANDOM_LENGTH = 4;
    
    /**
     * Generate a unique reference number.
     *
     * @return string Reference number in LK-YYYYMMDD-XXXX format
     */
    public function generate(): string {
        $datePart = date('Ymd');
        $randomPart = $this->generateRandomAlphanumeric(self::RANDOM_LENGTH);
        
        return self::PREFIX . '-' . $datePart . '-' . $randomPart;
    }
    
    /**
     * Generate a random alphanumeric string (uppercase letters and digits).
     *
     * @param int $length Desired length
     * @return string     Random alphanumeric string
     */
    private function generateRandomAlphanumeric(int $length): string {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Excludes ambiguous: 0/O, 1/I
        $result = '';
        $maxIndex = strlen($characters) - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, $maxIndex)];
        }
        
        return $result;
    }
}
?>
