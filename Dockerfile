# stage 1 - build frontend with Node (use Node 20.19+)
FROM node:20.19 AS node-build
WORKDIR /app

# copy package files first for caching
COPY package*.json ./

# install deps but skip postinstall scripts so build happens after full copy
RUN npm ci --silent --ignore-scripts

# copy rest of source (index.html, resources, vite config etc.)
COPY . .

# now run the frontend build
RUN npm run build:production --silent

# stage 2 - install PHP dependencies with Composer
FROM composer:2 AS composer-build
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts --no-progress
# copy vendor files (we'll copy full app in final stage)
COPY . /app

# stage 3 - runtime (PHP + Apache)
FROM php:8.2-apache
WORKDIR /var/www/html

# system deps needed for typical Laravel (adjust if different)
RUN apt-get update && apt-get install -y libzip-dev unzip git libonig-dev \
  && docker-php-ext-install pdo_mysql zip mbstring \
  && a2enmod rewrite \
  && rm -rf /var/lib/apt/lists/*

# copy built frontend assets and composer vendor
COPY --from=node-build /app/public/build /var/www/html/public/build
COPY --from=composer-build /app/vendor /var/www/html/vendor

# copy remaining app files
COPY . /var/www/html

# permissions (adjust user if needed)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
  && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
CMD ["apache2-foreground"]