<?php
/**
 * File Upload Validator Interface — ISP / DIP abstraction
 * 
 * Defines the contract for validating uploaded files.
 */
interface FileUploadValidatorInterface {
    /**
     * Validate an uploaded file from $_FILES.
     *
     * @param array $file A single entry from $_FILES
     * @return array       ['valid' => bool, 'error' => string|null, 'extension' => string|null]
     */
    public function validate(array $file): array;
}
?>
