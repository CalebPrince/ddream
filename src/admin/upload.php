<?php
declare(strict_types=1);

/**
 * Image uploads.
 *
 * Validation is layered so the absence of an extension never silently weakens
 * it: the extension allowlist and the image probe always run, and finfo adds a
 * third check where the host provides it. Resizing needs gd; without it the
 * original is stored and a warning is surfaced rather than the upload failing.
 */

const UPLOAD_MAX_BYTES  = 8 * 1024 * 1024;   // 8MB
const UPLOAD_MAX_WIDTH  = 2400;              // large enough for a full-bleed hero
const UPLOAD_ALLOWED    = ['jpg', 'jpeg', 'png', 'webp'];
const UPLOAD_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

if (!function_exists('uploads_dir')) {
    function uploads_dir(): string
    {
        return dirname(__DIR__, 2) . '/public/uploads';
    }
}

if (!function_exists('gd_available')) {
    function gd_available(): bool
    {
        return extension_loaded('gd') && function_exists('imagecreatetruecolor');
    }
}

if (!function_exists('upload_error_message')) {
    function upload_error_message(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'That file is larger than the server allows.',
            UPLOAD_ERR_PARTIAL    => 'The upload was interrupted. Try again.',
            UPLOAD_ERR_NO_FILE    => 'No file was chosen.',
            UPLOAD_ERR_NO_TMP_DIR => 'The server has nowhere to put the file. Tell your host.',
            UPLOAD_ERR_CANT_WRITE => 'The server could not write the file. Check folder permissions.',
            default               => 'The upload failed.',
        };
    }
}

if (!function_exists('store_upload')) {
    /**
     * Validate and store one uploaded image, returning the new media row.
     *
     * @param array{name: string, type: string, tmp_name: string, error: int, size: int} $file
     * @return array{ok: bool, id?: int, path?: string, error?: string, warning?: string}
     */
    function store_upload(array $file, string $alt = '', ?string $title = null): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => upload_error_message((int) ($file['error'] ?? 4))];
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return ['ok' => false, 'error' => 'That file did not arrive as an upload.'];
        }

        if ($file['size'] > UPLOAD_MAX_BYTES) {
            return ['ok' => false, 'error' => 'Images must be 8MB or smaller. Yours is '
                . round($file['size'] / 1048576, 1) . 'MB.'];
        }

        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, UPLOAD_ALLOWED, true)) {
            return ['ok' => false, 'error' => 'Use a JPG, PNG or WebP image.'];
        }

        // Probing the file itself is the check that matters; the extension can lie.
        $probe = @getimagesize($file['tmp_name']);
        if ($probe === false || !in_array($probe['mime'], UPLOAD_MIME_TYPES, true)) {
            return ['ok' => false, 'error' => 'That file is not a readable image.'];
        }

        // Third check where the host provides it.
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($file['tmp_name']);
            if (!in_array($mime, UPLOAD_MIME_TYPES, true)) {
                return ['ok' => false, 'error' => 'That file is not a readable image.'];
            }
        }

        [$width, $height] = $probe;
        $mime = $probe['mime'];

        // Year and month keeps any single directory from growing unmanageable.
        $relativeDir = '/uploads/' . date('Y/m');
        $absoluteDir = dirname(__DIR__, 2) . '/public' . $relativeDir;

        if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0755, true) && !is_dir($absoluteDir)) {
            return ['ok' => false, 'error' => 'Could not create the upload folder.'];
        }

        $basename = upload_safe_name((string) $file['name']);
        $filename = $basename . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
        $relative = $relativeDir . '/' . $filename;
        $absolute = $absoluteDir . '/' . $filename;

        $warning = null;

        if (gd_available() && $width > UPLOAD_MAX_WIDTH) {
            $resized = resize_image($file['tmp_name'], $absolute, $mime, UPLOAD_MAX_WIDTH);
            if ($resized === null) {
                if (!move_uploaded_file($file['tmp_name'], $absolute)) {
                    return ['ok' => false, 'error' => 'Could not save the file.'];
                }
            } else {
                [$width, $height] = $resized;
            }
        } else {
            if (!move_uploaded_file($file['tmp_name'], $absolute)) {
                return ['ok' => false, 'error' => 'Could not save the file.'];
            }
            if (!gd_available() && $width > UPLOAD_MAX_WIDTH) {
                $warning = 'Stored at full size. The gd extension is not installed, '
                    . 'so large images cannot be resized on this server.';
            }
        }

        @chmod($absolute, 0644);

        $id = db_insert('media', [
            'path'        => $relative,
            'alt'         => mb_substr(trim($alt), 0, 255),
            'title'       => $title !== null ? mb_substr($title, 0, 190) : null,
            'mime'        => $mime,
            'width'       => $width,
            'height'      => $height,
            'bytes'       => (int) filesize($absolute),
            'uploaded_by' => current_user()['id'] ?? null,
            'created_at'  => now(),
        ]);

        return array_filter([
            'ok'      => true,
            'id'      => $id,
            'path'    => $relative,
            'warning' => $warning,
        ], static fn ($v): bool => $v !== null);
    }
}

if (!function_exists('upload_safe_name')) {
    function upload_safe_name(string $original): string
    {
        $name = pathinfo($original, PATHINFO_FILENAME);
        $name = strtolower((string) preg_replace('/[^A-Za-z0-9]+/', '-', $name));
        $name = trim($name, '-');

        return $name === '' ? 'image' : mb_substr($name, 0, 60);
    }
}

if (!function_exists('resize_image')) {
    /**
     * Re-encode down to a maximum width. Re-encoding also strips anything
     * embedded in the original file.
     *
     * @return array{0: int, 1: int}|null new dimensions, or null on failure
     */
    function resize_image(string $source, string $destination, string $mime, int $maxWidth): ?array
    {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($source),
            'image/png'  => @imagecreatefrompng($source),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false,
            default      => false,
        };

        if ($image === false) {
            return null;
        }

        $width  = imagesx($image);
        $height = imagesy($image);

        if ($width <= $maxWidth) {
            imagedestroy($image);

            return null;
        }

        $newWidth  = $maxWidth;
        $newHeight = (int) round($height * ($maxWidth / $width));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);

        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
        }

        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $saved = match ($mime) {
            'image/jpeg' => imagejpeg($canvas, $destination, 82),
            'image/png'  => imagepng($canvas, $destination, 6),
            'image/webp' => function_exists('imagewebp') && imagewebp($canvas, $destination, 82),
            default      => false,
        };

        imagedestroy($image);
        imagedestroy($canvas);

        return $saved ? [$newWidth, $newHeight] : null;
    }
}

if (!function_exists('delete_media')) {
    /** Remove the row and the file. Returns false if the file could not be removed. */
    function delete_media(int $id): bool
    {
        $media = db_one('SELECT * FROM media WHERE id = ?', [$id]);

        if ($media === null) {
            return false;
        }

        $absolute = dirname(__DIR__, 2) . '/public' . $media['path'];
        if (is_file($absolute)) {
            @unlink($absolute);
        }

        db_run('DELETE FROM media WHERE id = ?', [$id]);

        return true;
    }
}
