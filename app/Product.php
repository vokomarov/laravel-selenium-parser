<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price_main',
        'price_decimal',
        'rating',
        'description',
        'imageUrl',
    ];

    /**
     * @return string
     */
    public function getPriceAttribute(): string
    {
        return $this->price_main . ($this->price_decimal ? (',' . substr((string) $this->price_decimal, 2)) : '');
    }

    /**
     * @param float $price
     */
    public function setPriceAttribute(float $price)
    {
        $this->price_main = floor($price);
        $this->price_decimal = ($price - $this->price_main) * 100;
    }
}
