FROM php:8.2-apache

# install extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy project
COPY . /var/www/html/

WORKDIR /var/www/html

# install dependencies
RUN composer install --no-dev --optimize-autoloader

# set public folder
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

EXPOSE 80