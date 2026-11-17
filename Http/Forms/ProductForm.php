<?php

namespace Http\Forms;

use Core\UploadedImage;
use Core\ValidationException;
use Core\Validator;

class ProductForm
{
    public const NAME_MAX = 255;
    public const DESCRIPTION_MAX = 5000;
    public const PRICE_MAX = 99999999.99; // limit of the DECIMAL(10,2) column

    protected array $errors = [];
    protected array $data = [];
    protected ?UploadedImage $image;

    public function __construct(public array $attributes, array $files = [])
    {
        $name = $this->input('name');
        $description = $this->input('description');
        $price = $this->input('price');
        $availabilityDate = $this->input('availability_date');

        if (!Validator::string($name, 1, self::NAME_MAX)) {
            $this->errors['name'] = 'A name of no more than ' . self::NAME_MAX . ' characters is required.';
        }

        if (!Validator::string($description, 0, self::DESCRIPTION_MAX)) {
            $this->errors['description'] = 'The description may not be longer than ' . number_format(self::DESCRIPTION_MAX) . ' characters.';
        }

        if ($price === '') {
            $this->errors['price'] = 'A price is required.';
        } elseif (!Validator::decimal($price, 0, self::PRICE_MAX)) {
            $this->errors['price'] = 'The price must be a number between 0 and ' . number_format(self::PRICE_MAX, 2) . ' with at most 2 decimals.';
        }

        if ($availabilityDate !== '' && !Validator::date($availabilityDate)) {
            $this->errors['availability_date'] = 'Please provide a valid availability date.';
        }

        $this->image = UploadedImage::from($files['image'] ?? null);

        if ($this->image?->error() !== null) {
            $this->errors['image'] = $this->image->error();
        }

        $this->data = [
            'name' => $name,
            'description' => $description !== '' ? $description : null,
            'price' => $price,
            'availability_date' => $availabilityDate !== '' ? $availabilityDate : null,
            'in_stock' => (int) filter_var($this->input('in_stock'), FILTER_VALIDATE_BOOLEAN),
        ];
    }

    public static function validate(array $attributes, array $files = []): static
    {
        $instance = new static($attributes, $files);
        return $instance->failed() ? $instance->throw() : $instance;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function image(): ?UploadedImage
    {
        return $this->image;
    }

    public function removesImage(): bool
    {
        return $this->image === null
            && filter_var($this->input('remove_image'), FILTER_VALIDATE_BOOLEAN);
    }

    public function throw(): never
    {
        ValidationException::throw($this->errors());
    }

    public function failed(): bool
    {
        return count($this->errors) > 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function error(string $field, string $message): static
    {
        $this->errors[$field] = $message;

        return $this;
    }

    protected function input(string $key): string
    {
        $value = $this->attributes[$key] ?? '';
        return is_string($value) ? trim($value) : '';
    }
}
