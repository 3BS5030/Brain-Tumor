FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    zip \
    unzip \
    python3 \
    python3-pip \
    python3-venv \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Install Node & build assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm ci \
    && npm run build \
    && apt-get purge -y nodejs \
    && apt-get autoremove -y

# Install Python dependencies (if requirements.txt exists)
RUN python3 -m venv /venv
RUN if [ -f "requirements.txt" ]; then \
        /venv/bin/pip install --upgrade pip && \
        /venv/bin/pip install -r requirements.txt; \
    fi

# Set Python venv in PATH
ENV PATH="/venv/bin:$PATH"

# Laravel setup - create .env
RUN if [ -f ".env.example" ]; then \
        cp .env.example .env; \
    else \
        printf 'APP_NAME=Laravel\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=http://localhost\nLOG_CHANNEL=stderr\nLOG_LEVEL=error\nDB_CONNECTION=sqlite\nDB_DATABASE=/var/www/database/database.sqlite\nCACHE_DRIVER=file\nSESSION_DRIVER=file\nQUEUE_CONNECTION=sync\n' > .env; \
    fi

# Generate APP_KEY directly into .env during build
RUN php artisan key:generate --force

# Create SQLite database file
RUN mkdir -p database && touch database/database.sqlite

# Set storage permissions
RUN mkdir -p storage/logs \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Expose port
EXPOSE 8080

# Start script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
