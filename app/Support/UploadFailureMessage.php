<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

final class UploadFailureMessage
{
    public static function forInvalidUpload(UploadedFile $file): string
    {
        return match ($file->getError()) {
            \UPLOAD_ERR_INI_SIZE => 'The server rejected the file (PHP upload_max_filesize). Ask your host to raise upload_max_filesize/post_max_size, or use a smaller image.',
            \UPLOAD_ERR_FORM_SIZE => 'That photo is too large for the form limit. Use an image under 5 MB.',
            \UPLOAD_ERR_PARTIAL => 'The upload was interrupted (only part of the file arrived). Try again or use a smaller file.',
            \UPLOAD_ERR_NO_FILE => 'No file was received. Pick a photo again, then save.',
            \UPLOAD_ERR_NO_TMP_DIR => 'Server is missing a temporary upload folder. Contact your host or developer.',
            \UPLOAD_ERR_CANT_WRITE => 'The server could not write the upload to disk. Check disk space and permissions.',
            \UPLOAD_ERR_EXTENSION => 'A PHP extension blocked this upload. Try a different format (JPG or PNG).',
            default => 'The photo could not be uploaded (upload error code '.$file->getError().'). Try JPG or PNG under 5 MB.',
        };
    }
}
