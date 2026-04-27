FROM php:8.5-cli

RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Xdebug for coverage (ignoring if pecl isn't ready for 8.5 yet, but usually we build from source or pecl works)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configure Xdebug for coverage
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /app
