# Ordering System 

<p>
    <a href="https://github.com/dev3k/orders/actions"><img alt="Tests passing" src="https://img.shields.io/badge/Tests-passing-green?style=for-the-badge&logo=github"></a>
    <a href="https://laravel.com"><img alt="Laravel v10.x" src="https://img.shields.io/badge/Laravel-v10.x-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://php.net"><img alt="PHP 8.1" src="https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php"></a>
</p>

---

This project is ordering system code challenge that has three main models: Product, Ingredient, and Order. The system keeps track of the stock of each ingredient and updates it as orders are placed.

## Installation

### Option 1: Manual Installation
1. Clone the repository
2. Run `composer install` to install dependencies
3. Create a copy of .env.example and rename it to .env
4. Set up your database connection details in the .env file
5. Run `php artisan key:generate` to generate an application key
6. Run `php artisan migrate` to set up the database

### Option 2: Using Docker and Laravel Sail
1. Clone the repository
2. Run `./vendor/bin/sail up` to start the Docker containers
3. Run `./vendor/bin/sail artisan migrate` to set up the database

## Usage

To place an order, make a POST request to `/orders` with the order details in the request payload. The payload should include a list of products and their quantities.

Example payload:

```json
{
  "products": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ]
}
```

## How it works

When an order is placed, the system updates the stock levels for the ingredients used in the ordered products. If the stock level for any ingredient falls below 50%, an email is sent to the merchant to alert them that they need to purchase more of that ingredient.

## Testing

This project includes several test cases[^1] to ensure that orders are correctly stored and the stock levels are correctly updated. Run `php artisan test` to run the test suite.

---

[^1]: At present, I use [Pest](https://pestphp.com) for testing in my projects. However, I am familiar with Laravel's default PHPUnit tests and can also use them if needed.


