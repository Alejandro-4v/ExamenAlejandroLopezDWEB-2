FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y git unzip libzip-dev && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /api

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli zip

CMD ["bash"]