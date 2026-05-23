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

**Query Parmeters (Optional):**
  - `search` - Does a Full-text search on title and content columns
  - `price_from` - Filters products with price >= value
  - `price_to` - Filters products with price <= value

`GET /api/categories` - Returns all categories in the DB, paginated by 25. Each record shows how many products are linked to the specific category.

## Postman Collection

To make testing easier, I've included a **Postman Collection** which contains all requests and pre-saved response examples.

**Location:** `laravel-task.postman_collection.json`

**How to import:**
1. Open Postman
2. Click on **Import** (top left)
3. Choose **File** and select the collection
4. Set the environment collection variable `baseUrl` to `http://localhost/api`

## Thought process

Because the task said that the API should be able to filter products by `price` and search them by `title` and `content`, I immediately thought that those columns should be indexed.
The `price` column is indexed normally, while the `title` and `content` have a composite full-text index.

When presenting the products, instead of calling `$this->categories` on each record, I eager load the `categories` through Laravel's `with()` function, thus optimizing the queries.

I used `simplePaginate()` for the massive products dataset to eliminate slow `COUNT(*)` queries, but kept standard `paginate()` for the small categories table.

I've tried to optimize the `ProductSeeder` by mass inserting the records instead of calling `create()`, because doing the latter would result in as many queries to the DB as products we want to create.

After seeding the DB with a lot of data, during testing of the endpoints I've found out that `/categories` endpoint is very slow (6sec response) because of the `withCount('products')`. This made me drop this function call and refactor the models and seeders, implementing a denormalization technique. I've added `products_count` column to `Category` model which holds the count of the products linked to the category, thus speeding the response 400x faster (6sec vs 15ms).

As someone new to Laravel, my primary focus was making sure I adopted the framework's native tools and conventions correctly from the start

## Production upgrades

For the denormalization to work in a production environment, the `products_count` field should be maintained via Event/EventListener triggered whenever a product is attached to or detached from a category (pivot table updates).