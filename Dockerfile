# Stage 1: build frontend with Node 20
FROM node:20.19.0 AS node-build
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY resources resources
COPY vite.config.* .
RUN npm run build

# Stage 2: php app
FROM php:8.2-apache
WORKDIR /var/www/html
# copy app files
COPY . .
# copy built assets from node stage into public/build
COPY --from=node-build /app/public/build public/build

# system deps needed for typical Laravel (adjust if different)
RUN apt-get update && apt-get install -y libzip-dev unzip git libonig-dev \
  && docker-php-ext-install pdo_mysql zip mbstring \
  && a2enmod rewrite \
  && rm -rf /var/lib/apt/lists/*

# permissions (adjust user if needed)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
  && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
CMD ["apache2-foreground"]