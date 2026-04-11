FROM dunglas/frankenphp

ENV SERVER_NAME=":80"

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

RUN chmod -R 777 storage bootstrap/cache

RUN php artisan optimize:clear || true
RUN php artisan config:cache || true
RUN php artisan route:cache || true

EXPOSE 80
