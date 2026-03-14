<?php
/**
 * Input Validator — SRP service
 * 
 * Reusable validation and sanitization for user input.
 * Used by controllers before processing form data.
 */

class InputValidator {
    
    /**
     * Check that all required fields are present and non-empty in the data array.
     *
     * @param array $requiredFields List of field names that must be present
     * @param array $data           The input data to validate
     * @return array                Error messages keyed by field name (empty if valid)
     */
    public function validateRequired(array $requiredFields, array $data): array {
        $errors = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }
        return $errors;
    }
    
    /**
     * Sanitize a string for safe HTML output.
     *
     * @param string $input Raw input string
     * @return string       Trimmed and escaped string
     */
    public function sanitizeString(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate a Philippine phone number format.
     * Accepts: 09XXXXXXXXX, +639XXXXXXXXX, 639XXXXXXXXX
     *
     * @param string $phone Phone number to validate
     * @return bool         True if valid PH phone format
     */
    public function validatePhone(string $phone): bool {
        $cleaned = preg_replace('/[\s\-\(\)]/', '', $phone);
        return (bool) preg_match('/^(\+?63|0)9\d{9}$/', $cleaned);
    }
    
    /**
     * Validate an email address.
     *
     * @param string $email Email to validate
     * @return bool         True if valid email format
     */
    public function validateEmail(string $email): bool {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate that a value is a positive number.
     *
     * @param mixed $value Value to check
     * @return bool        True if positive numeric value
     */
    public function validatePositiveNumber($value): bool {
        return is_numeric($value) && $value > 0;
    }
}
?>
