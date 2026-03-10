FROM php:8.2-apache

# install required packages
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libicu-dev \
    libonig-dev

# install php extensions
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    intl \
    mbstring \
    zip

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy project
COPY . /var/www/html/

WORKDIR /var/www/html

# install dependencies
RUN composer install

# set apache public folder
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

EXPOSE 80