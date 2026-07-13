<?php

namespace App\Helpers;

class FileUploader
{
    private string $uploadDir;
    private array $allowedTypes;
    private int $maxSize;

    public function __construct(string $uploadDir, array $allowedTypes = [], int $maxSize = 2097152)
    {
        $this->uploadDir = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR;
        $this->allowedTypes = $allowedTypes;
        $this->maxSize = $maxSize;
    }

    public function upload(array $file, string $prefix = 'file'): ?string
    {
        if (empty($file['name'])) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if (!empty($this->allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, $this->allowedTypes)) {
                return null;
            }
        }

        if ($file['size'] > $this->maxSize) {
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . time() . '.' . $ext;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        move_uploaded_file($file['tmp_name'], $this->uploadDir . $filename);
        return $filename;
    }

    public function delete(?string $filename): bool
    {
        if (empty($filename)) {
            return false;
        }
        $path = $this->uploadDir . $filename;
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    public function replace(array $file, ?string $oldFile, string $prefix = 'file'): ?string
    {
        $newFile = $this->upload($file, $prefix);
        if ($newFile !== null) {
            $this->delete($oldFile);
        }
        return $newFile;
    }
}
