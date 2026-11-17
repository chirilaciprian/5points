<?php

namespace Core;

use finfo;
use RuntimeException;

/**
 * An image sent through a file input: validates it and moves it into the
 * public/uploads directory.
 */
class UploadedImage
{
    public const TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    protected ?string $error = null;
    protected ?string $extension = null;

    protected function __construct(protected array $file)
    {
        $this->check();
    }

    public static function from(mixed $file): ?static
    {
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return new static($file);
    }

    public function error(): ?string
    {
        return $this->error;
    }

    public function store(): string
    {
        if ($this->error !== null) {
            throw new RuntimeException('An invalid upload cannot be stored.');
        }

        $directory = static::directory();

        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException("The uploads directory {$directory} could not be created.");
        }

        $name = bin2hex(random_bytes(16)) . '.' . $this->extension;

        if (!move_uploaded_file($this->file['tmp_name'], $directory . '/' . $name)) {
            throw new RuntimeException('The uploaded image could not be saved.');
        }

        return $name;
    }

    public static function delete(?string $name): void
    {
        if (!static::isStoredName($name)) {
            return;
        }

        $path = static::directory() . '/' . $name;

        if (is_file($path)) {
            unlink($path);
        }
    }

    public static function url(?string $name): ?string
    {
        if (!static::isStoredName($name)) {
            return null;
        }

        return rtrim(static::config()['url'], '/') . '/' . $name;
    }

    public static function maxSize(): int
    {
        return static::config()['max_size'];
    }

    public static function formats(): string
    {
        $formats = array_map('strtoupper', array_values(static::TYPES));
        $last = array_pop($formats);

        return implode(', ', $formats) . ' or ' . $last;
    }

    protected function check(): void
    {
        $megabytes = round(static::maxSize() / 1024 / 1024, 1);
        $tooLarge = "The image may not be larger than {$megabytes} MB.";

        if (!is_int($this->file['error'] ?? null) || !is_string($this->file['tmp_name'] ?? null)) {
            $this->error = 'Please upload a single image.';
            return;
        }

        if (in_array($this->file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            $this->error = $tooLarge;
            return;
        }

        if ($this->file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($this->file['tmp_name'])) {
            $this->error = 'The image could not be uploaded. Please try again.';
            return;
        }

        if (filesize($this->file['tmp_name']) > static::maxSize()) {
            $this->error = $tooLarge;
            return;
        }

        $type = (new finfo(FILEINFO_MIME_TYPE))->file($this->file['tmp_name']);

        if (!isset(static::TYPES[$type]) || @getimagesize($this->file['tmp_name']) === false) {
            $this->error = 'The image must be a ' . static::formats() . ' file.';
            return;
        }

        $this->extension = static::TYPES[$type];
    }

    protected static function isStoredName(?string $name): bool
    {
        return $name !== null
            && preg_match('/^[a-f0-9]{32}\.(' . implode('|', static::TYPES) . ')$/D', $name) === 1;
    }

    protected static function directory(): string
    {
        return rtrim(base_path(static::config()['directory']), '/');
    }

    protected static function config(): array
    {
        return App::resolve('config')['uploads'];
    }
}
