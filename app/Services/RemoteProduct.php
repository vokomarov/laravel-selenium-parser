<?php

namespace App\Services;

/**
 * Class RemoteProduct
 *
 * @package App\Services
 * @property string $name
 * @property string $description
 * @property string $imageUrl
 * @property float $price
 * @property float $rating
 */
class RemoteProduct
{
    /**
     * @var \Illuminate\Support\Collection
     */
    public $attributes;

    /**
     * RemoteProduct constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = collect();

        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * @param  int|string  $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @param  int|string  $name
     * @param  mixed  $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }
}
