<?php

namespace App\Helpers;

class Validator
{
    private array $errors = [];

    public function required(string $field, mixed $value, string $label = ''): self
    {
        if (empty($value) && $value !== '0') {
            $this->errors[$field][] = ($label ?: $field) . ' wajib diisi.';
        }
        return $this;
    }

    public function email(string $field, string $value, string $label = ''): self
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = ($label ?: $field) . ' harus berupa email valid.';
        }
        return $this;
    }

    public function minLength(string $field, string $value, int $min, string $label = ''): self
    {
        if (!empty($value) && strlen($value) < $min) {
            $this->errors[$field][] = ($label ?: $field) . ' minimal ' . $min . ' karakter.';
        }
        return $this;
    }

    public function maxLength(string $field, mixed $value, int $max, string $label = ''): self
    {
        if (!empty($value) && strlen((string)$value) > $max) {
            $this->errors[$field][] = ($label ?: $field) . ' maksimal ' . $max . ' karakter.';
        }
        return $this;
    }

    public function numeric(string $field, mixed $value, string $label = ''): self
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field][] = ($label ?: $field) . ' harus berupa angka.';
        }
        return $this;
    }

    public function inArray(string $field, mixed $value, array $allowed, string $label = ''): self
    {
        if (!empty($value) && !in_array($value, $allowed)) {
            $this->errors[$field][] = ($label ?: $field) . ' tidak valid.';
        }
        return $this;
    }

    public function file(string $field, array $file, array $allowedTypes = [], int $maxSize = 2097152, string $label = ''): self
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[$field][] = ($label ?: $field) . ' gagal diupload.';
            return $this;
        }

        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, $allowedTypes)) {
                $this->errors[$field][] = 'Tipe file ' . ($label ?: $field) . ' tidak diizinkan.';
            }
        }

        if ($file['size'] > $maxSize) {
            $this->errors[$field][] = 'Ukuran ' . ($label ?: $field) . ' maksimal ' . ($maxSize / 1048576) . ' MB.';
        }

        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $field => $messages) {
            return $messages[0] ?? null;
        }
        return null;
    }
}
