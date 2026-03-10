FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libicu-dev \
    libonig-dev

RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    intl \
    mbstring \
    zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

WORKDIR /var/www/html

RUN composer install

# create writable folder if missing
RUN mkdir -p writable/cache writable/logs writable/session

# give permission
RUN chmod -R 777 writable

# set public folder
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

EXPOSE 80