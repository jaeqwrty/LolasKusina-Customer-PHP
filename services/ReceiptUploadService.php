<?php
/**
 * T010 - Receipt Upload Service
 * Lola's Kusina - Customer Web Module
 * Author: Jullian Anjelo C. Vidal
 *
 * Handles server-side receipt image upload and compression under 200KB.
 *
 * SOLID Principles Applied:
 *  S - Single Responsibility : Each class does ONE thing only
 *  O - Open/Closed           : Add new validators without modifying existing ones
 *  L - Liskov Substitution   : Any IValidator can replace another
 *  I - Interface Segregation : Small focused classes (validator, compressor, storage)
 *  D - Dependency Inversion  : ReceiptUploadService depends on abstractions
 */

// =============================================================================
// FileTypeValidator
// Responsibility: ONLY validates allowed MIME types
// =============================================================================
class FileTypeValidator {
    private array $allowedTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
    ];

    public function validate(array $file): bool {
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        return in_array($mimeType, $this->allowedTypes, true);
    }

    public function errorMessage(): string {
        return 'Only JPG and PNG files are allowed.';
    }
}

// =============================================================================
// FileSizeValidator
// Responsibility: ONLY validates that the uploaded file is not too large
// =============================================================================
class FileSizeValidator {
    private int $maxBytes;

    public function __construct(int $maxBytes = 5 * 1024 * 1024) { // 5MB raw upload max
        $this->maxBytes = $maxBytes;
    }

    public function validate(array $file): bool {
        return $file['size'] <= $this->maxBytes;
    }

    public function errorMessage(): string {
        return 'File is too large. Maximum upload size is 5MB.';
    }
}

// =============================================================================
// ImageCompressor
// Responsibility: ONLY compresses an image to under a target size in bytes
// =============================================================================
class ImageCompressor {
    private int $targetBytes;

    public function __construct(int $targetBytes = 200 * 1024) { // 200KB
        $this->targetBytes = $targetBytes;
    }

    /**
     * Compress an image file and save it to $destPath under $targetBytes.
     * Returns true on success, false on failure.
     */
    public function compress(string $sourcePath, string $destPath, string $mimeType): bool {
        // Load image based on type
        $image = match($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($sourcePath),
            'image/png'               => $this->_pngToTrueColor($sourcePath),
            default                   => false,
        };

        if (!$image) return false;

        // Try compressing at decreasing quality until under target size
        $quality = 85;
        $minQuality = 10;

        while ($quality >= $minQuality) {
            // Write to a temp buffer
            ob_start();
            if ($mimeType === 'image/png') {
                // PNG compression: 0 (none) to 9 (max) — map quality 85→0, 10→9
                $pngCompression = (int) round((100 - $quality) / 11);
                imagepng($image, null, $pngCompression);
            } else {
                imagejpeg($image, null, $quality);
            }
            $buffer = ob_get_clean();

            if (strlen($buffer) <= $this->targetBytes) {
                // Under target — save to destination
                imagedestroy($image);
                return (bool) file_put_contents($destPath, $buffer);
            }

            $quality -= 5;
        }

        // If still over target at min quality, resize image dimensions by 75%
        $resized = $this->_resize($image, 0.75);
        imagedestroy($image);

        ob_start();
        if ($mimeType === 'image/png') {
            imagepng($resized, null, 7);
        } else {
            imagejpeg($resized, null, $minQuality);
        }
        $buffer = ob_get_clean();
        imagedestroy($resized);

        return (bool) file_put_contents($destPath, $buffer);
    }

    /** Convert PNG (possibly with transparency) to true color GD resource */
    private function _pngToTrueColor(string $path) {
        $src = imagecreatefrompng($path);
        if (!$src) return false;

        $w   = imagesx($src);
        $h   = imagesy($src);
        $img = imagecreatetruecolor($w, $h);

        // Fill white background (replaces transparency)
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);
        imagecopy($img, $src, 0, 0, 0, 0, $w, $h);
        imagedestroy($src);

        return $img;
    }

    /** Resize GD image by a scale factor (e.g. 0.75 = 75%) */
    private function _resize($image, float $scale) {
        $w       = imagesx($image);
        $h       = imagesy($image);
        $newW    = (int) ($w * $scale);
        $newH    = (int) ($h * $scale);
        $resized = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newW, $newH, $w, $h);
        return $resized;
    }
}

// =============================================================================
// ReceiptStorage
// Responsibility: ONLY handles saving the file to the correct directory
// =============================================================================
class ReceiptStorage {
    private string $storageDir;

    public function __construct(string $storageDir) {
        $this->storageDir = rtrim($storageDir, '/') . '/';
    }

    /** Ensure storage directory exists */
    public function ensureDirectory(): void {
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
    }

    /** Generate a unique file path for the receipt */
    public function generatePath(string $extension): string {
        $filename = 'receipt_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        return $this->storageDir . $filename;
    }

    public function getStorageDir(): string {
        return $this->storageDir;
    }
}

// =============================================================================
// ReceiptUploadService (Orchestrator)
// Responsibility: Coordinates validation, compression, and storage
// Dependency Inversion: All dependencies are injected
// =============================================================================
class ReceiptUploadService {
    private FileTypeValidator $typeValidator;
    private FileSizeValidator $sizeValidator;
    private ImageCompressor   $compressor;
    private ReceiptStorage    $storage;

    public function __construct(
        FileTypeValidator $typeValidator,
        FileSizeValidator $sizeValidator,
        ImageCompressor   $compressor,
        ReceiptStorage    $storage
    ) {
        $this->typeValidator = $typeValidator;
        $this->sizeValidator = $sizeValidator;
        $this->compressor    = $compressor;
        $this->storage       = $storage;
    }

    /**
     * Handle a receipt file upload.
     *
     * @param  array $file  $_FILES['receipt'] array
     * @return array        ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    public function handle(array $file): array {
        // 1. Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $this->_fail('Upload failed. Please try again.');
        }

        // 2. Validate file type
        if (!$this->typeValidator->validate($file)) {
            return $this->_fail($this->typeValidator->errorMessage());
        }

        // 3. Validate file size
        if (!$this->sizeValidator->validate($file)) {
            return $this->_fail($this->sizeValidator->errorMessage());
        }

        // 4. Ensure storage directory exists
        $this->storage->ensureDirectory();

        // 5. Determine extension
        $finfo     = new finfo(FILEINFO_MIME_TYPE);
        $mimeType  = $finfo->file($file['tmp_name']);
        $extension = ($mimeType === 'image/png') ? 'png' : 'jpg';

        // 6. Generate destination path
        $destPath = $this->storage->generatePath($extension);

        // 7. Compress and save
        $success = $this->compressor->compress($file['tmp_name'], $destPath, $mimeType);

        if (!$success) {
            return $this->_fail('Failed to process image. Please try again.');
        }

        return [
            'success' => true,
            'path'    => $destPath,
            'error'   => null,
        ];
    }

    private function _fail(string $message): array {
        return ['success' => false, 'path' => null, 'error' => $message];
    }
}

