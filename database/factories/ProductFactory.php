<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Company;
use App\Product;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'company_id' => Company::inRandomOrder()->first()->id,
            'name' => $this->faker->company,
            'short_description' => substr($this->faker->paragraph(), 0, 191),
            'long_description' => $this->faker->paragraph(4),
            'product_details' => $this->faker->paragraph(5),
            'image_path' => '/image/products/default/not-found.jpg',
            'cost' => $this->faker->randomNumber(2),
            'shippable' => mt_rand(0, 1),
            'free_delivery' => mt_rand(0, 1),
        ];
    }
}