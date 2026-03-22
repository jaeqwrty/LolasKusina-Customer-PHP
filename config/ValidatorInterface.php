<?php
/**
 * Validator Interface — ISP / DIP abstraction
 * 
 * Small, client-specific interface for input validation and sanitization.
 * Controllers depend on this interface, not the concrete InputValidator.
 */
interface ValidatorInterface {
    /**
     * Check that all required fields are present and non-empty.
     *
     * @param array $requiredFields List of field names that must be present
     * @param array $data           The input data to validate
     * @return array                Error messages keyed by field name (empty if valid)
     */
    public function validateRequired(array $requiredFields, array $data): array;

    /**
     * Sanitize a string for safe HTML output.
     *
     * @param string $input Raw input string
     * @return string       Trimmed and escaped string
     */
    public function sanitizeString(string $input): string;

    /**
     * Validate a Philippine phone number format.
     *
     * @param string $phone Phone number to validate
     * @return bool         True if valid PH phone format
     */
    public function validatePhone(string $phone): bool;

    /**
     * Validate an email address.
     *
     * @param string $email Email to validate
     * @return bool         True if valid email format
     */
    public function validateEmail(string $email): bool;

    /**
     * Validate that a value is a positive number.
     *
     * @param mixed $value Value to check
     * @return bool        True if positive numeric value
     */
    public function validatePositiveNumber($value): bool;
}
?>
