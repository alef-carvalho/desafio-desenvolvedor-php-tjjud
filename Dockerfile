FROM php:8.3-fpm

# 1 - Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif intl pcntl bcmath gd zip

# 2 - Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3 - Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# 4 - Define working directory
WORKDIR /var/www

# 5 - Setup entrypoint
COPY docker/scripts/deploy.sh /usr/local/bin/deploy
RUN chmod +x /usr/local/bin/deploy

EXPOSE 9000

CMD ["deploy"]
