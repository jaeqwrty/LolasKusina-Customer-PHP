<?php
/**
 * Reference Generator Interface — ISP / DIP abstraction
 * 
 * Defines the contract for generating unique reference numbers.
 */
interface ReferenceGeneratorInterface {
    /**
     * Generate a unique reference number.
     *
     * @return string Unique reference number
     */
    public function generate(): string;
}
?>
