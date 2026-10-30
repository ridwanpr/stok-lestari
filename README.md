# Stok Lestari

Stok Lestari is an inventory management web application designed for retail businesses. In addition to tracking stock and product batches, it automatically monitors expiration dates and enables store owners to publish near-expiry items to a public showcase at discounted prices, helping recover costs and reduce waste.

---

## Requirements

- PHP 8.3 or higher
- Composer
- Node.js & NPM
- SQLite, MySQL, or PostgreSQL database

---

## Installation & Setup

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd stok-lestari
    ```

2. **Install PHP and JavaScript dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Configure environment variables**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    Update your database settings and other credentials in `.env` as needed.

4. **Run migrations and seeders**

    ```bash
    php artisan migrate --seed
    ```

5. **Build front-end assets**

    ```bash
    npm run build
    ```

    _(For active development, run `npm run dev` instead)._

6. **Start the local development server**
    ```bash
    php artisan serve
    ```
    Or use `composer run dev` to run the web server and Vite compiler concurrently.

---

## Automated Expiry Checks (Cron Setup)

The application includes an automated process that identifies past-due product batches, marks their status as `expired`, and automatically deactivates any active flash sales linked to them.

You can configure this using either the server scheduler or an external webhook.

### Option 1: Server Crontab (VPS or Dedicated Hosting)

If your hosting environment provides shell and crontab access, add the standard Laravel scheduler entry to your server's crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

You can also run the expiration command directly on demand:

```bash
php artisan batches:expire
```

### Option 2: Webhook Cron (Shared Hosting / External Cron Services)

If you are deploying to shared hosting without terminal or crontab access, you can trigger the expiry check via an external cron service (such as [cron-job.org](https://cron-job.org/)):

1. Generate a secure token and set it in your `.env` file:

    ```env
    CRON_WEBHOOK_TOKEN=your-secret-token-here
    ```

2. In your external cron service, configure a scheduled job to trigger the endpoint daily (e.g., at midnight `00:05`):
    - **Method**: `GET` or `POST`
    - **URL**: `https://your-domain.com/cron/expire-batches?token=your-secret-token-here`
    - **Alternative Authentication**: Pass the token via the `X-Cron-Token` header or `Authorization: Bearer <token>` header instead of the query parameter.
    - **Expected Status**: `200`
