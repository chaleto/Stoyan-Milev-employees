FROM php:8.3-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    zip \
    mbstring \
    exif \
    pcntl \
    bcmath \
    opcache \
    intl \
    gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize --no-dev --classmap-authoritative
RUN chown -R www-data:www-data var/cache var/log
RUN yarn install
RUN yarn dev

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]