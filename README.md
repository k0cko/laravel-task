# Laravel Task

This is a back-end REST API given as a task for a job application.

## How to run

**Prerequisites:** Docker

**Steps:**
1. Clone the repo `git clone git@github.com:k0cko/laravel-task.git`
2. Go into repository `cd laravel-task`
3. Initialize the containers 
    ```sh
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/app" \
        -w /app \
        composer:latest \
        composer install --ignore-platform-reqs
4. Create .env file `cp .env.example .env`
5. Initialize the containers: `./vendor/bin/sail up -d`
6. Generate app key: `./vendor/bin/sail artisan key:generate`
7. Run migrations `./vendor/bin/sail artisan migrate`
8. Seed the DB `./vendor/bin/sail artisan db:seed` *Should take around 2-3 minutes, because we're creating > 1 mil. records*

## API Endpoints

`GET /api/products` - Returns all products in the DB, paginated by 25.
- Parameters
  - `search` - Does a fullText search on the title and content columns
  - `price_from` - Filters products with higher or equal price with the user request
  - `price_to` - Filters products with lower or equal price with the user request

`GET /api/categories` - Returns all categories in the DB, paginated by 25. Each record shows how many products are linked to the specific category.

## Thought process

Because the task said that the API should be able to filter products by `price` and search them by `title` and `content`, I immediately thought that those columns should be indexed.
The `price` column is indexed normally, while the `title` and `content` have a composite full-text index.

When presenting the products, instead of calling `$this->categories` on each record, I eager load the `categories` through Laravel's `with()` function, thus optimizing the queries.
Same goes for categories where i call `withCount()` on the products, to get how many products are linked to each category.

I used `simplePaginate()` for the massive products dataset to eliminate slow `COUNT(*)` queries, but kept standard `paginate()` for the small categories table.

As someone new to Laravel, my primary focus was making sure I adopted the framework's native tools and conventions correctly from the start


