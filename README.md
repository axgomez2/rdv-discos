# RDV Discos

E-commerce application for record store with integrated shipping options.

## Features

- Product catalog and management
- Shopping cart and checkout process
- User account management
- Admin panel for store management
- Integrated shipping via:
  - Melhor Envio
  - Mercado Envio
  - Correios

## Requirements

- PHP 8.1+
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Node.js and npm
- Web server (Apache/Nginx)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/axgomez2/rdv-discos.git
   cd rdv-discos
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

5. Configure your database and API keys in the `.env` file

6. Generate application key:
   ```bash
   php artisan key:generate
   ```

7. Run migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```

8. Build frontend assets:
   ```bash
   npm run build
   ```

9. Create symbolic link for storage:
   ```bash
   php artisan storage:link
   ```

## Shipping Integration Setup

### Melhor Envio
1. Register at [Melhor Envio](https://melhorenvio.com.br/)
2. Create an application in the developer section
3. Set the following in your `.env` file:
   ```
   MELHORENVIO_CLIENT_ID=your_client_id
   MELHORENVIO_CLIENT_SECRET=your_client_secret
   MELHORENVIO_TOKEN=your_token
   MELHORENVIO_SANDBOX=true
   ```
4. Configure your shipping address information in the `.env` file

### MercadoPago
1. Register at [MercadoPago](https://www.mercadopago.com.br/)
2. Configure your `.env` file with the MercadoPago credentials

## VPS Deployment

### Server Requirements
- Ubuntu 20.04 LTS or later
- Nginx or Apache
- PHP 8.1+
- MySQL/MariaDB
- Composer
- Node.js and npm

### Deployment Steps

1. Set up your web server configuration
2. Clone the repository to your server
3. Follow the installation steps above
4. Configure your web server to point to the public directory
5. Set up SSL certificates (recommended)
6. Configure the proper file permissions:
   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

7. Configure a queue worker for background jobs (optional):
   ```bash
   php artisan queue:work --daemon
   ```

## Development

```bash
# Run development server
php artisan serve

# Watch for changes in development
npm run dev
```

## License

MIT
