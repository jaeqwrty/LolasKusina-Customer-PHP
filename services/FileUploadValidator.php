<?php
/**
 * File Upload Validator — SRP service (NFR-S04 / NFR-P05 prep)
 * 
 * Validates uploaded files (specifically receipt images) for:
 * - Allowed MIME types (JPG, PNG) via signature check
 * - Maximum file size (2MB per NFR-P05)
 * - Safe file extension
 */

class FileUploadValidator {
    
    /** Maximum upload size in bytes (2MB per NFR-P05). */
    private const MAX_SIZE_BYTES = 2 * 1024 * 1024;
    
    /** Allowed MIME types mapped to their expected file extensions. */
    private const ALLOWED_TYPES = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png'  => ['png'],
    ];
    
    /**
     * Validate an uploaded file from $_FILES.
     *
     * @param array $file A single entry from $_FILES (e.g., $_FILES['receipt'])
     * @return array       ['valid' => bool, 'error' => string|null, 'extension' => string|null]
     */
    public function validate(array $file): array {
        // Check for upload errors
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return $this->fail($this->getUploadErrorMessage($file['error'] ?? -1));
        }
        
        // Check file size (NFR-P05: 2MB limit)
        if ($file['size'] > self::MAX_SIZE_BYTES) {
            $maxMB = self::MAX_SIZE_BYTES / (1024 * 1024);
            return $this->fail("File size exceeds the {$maxMB}MB limit.");
        }
        
        // Check MIME type via file signature (NFR-S04: signature check)
        $detectedMime = $this->detectMimeType($file['tmp_name']);
        if (!array_key_exists($detectedMime, self::ALLOWED_TYPES)) {
            return $this->fail('Only JPG and PNG images are allowed.');
        }
        
        // Check file extension matches MIME type (NFR-S04: extension check)
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = self::ALLOWED_TYPES[$detectedMime];
        if (!in_array($extension, $allowedExtensions, true)) {
            return $this->fail('File extension does not match its content type.');
        }
        
        return [
            'valid'     => true,
            'error'     => null,
            'extension' => $extension,
        ];
    }
    
    /**
     * Detect MIME type using file signature (finfo), not the user-provided type.
     *
     * @param string $filePath Path to the file on disk
     * @return string          Detected MIME type
     */
    private function detectMimeType(string $filePath): string {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($filePath) ?: 'application/octet-stream';
    }
    
    /**
     * Return a failure result array.
     *
     * @param string $errorMessage Description of the validation failure
     * @return array
     */
    private function fail(string $errorMessage): array {
        return [
            'valid'     => false,
            'error'     => $errorMessage,
            'extension' => null,
        ];
    }
    
    /**
     * Map PHP upload error codes to human-readable messages.
     *
     * @param int $errorCode PHP upload error code constant
     * @return string
     */
    private function getUploadErrorMessage(int $errorCode): string {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds the server upload limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds the form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server missing temporary upload directory.',
            UPLOAD_ERR_CANT_WRITE => 'Server failed to write uploaded file.',
            UPLOAD_ERR_EXTENSION  => 'Upload blocked by a server extension.',
        ];
        
        return $messages[$errorCode] ?? 'Unknown upload error.';
    }
}
?>
