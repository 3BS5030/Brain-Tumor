FROM php:8.2-cli

# Set working directory
ENV APP_DIR=/app
WORKDIR ${APP_DIR}

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libsqlite3-dev \
    libzip-dev \
    nodejs \
    npm \
    pkg-config \
    python3 \
    python3-venv \
    sqlite3 \
    supervisor \
    unzip \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install bcmath exif gd pcntl pdo pdo_sqlite zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

# Install Node dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy project files
COPY . .

# 🔥 Create Python virtual environment
RUN python3 -m venv /opt/venv

# Activate venv globally
ENV PATH="/opt/venv/bin:$PATH"

# Build frontend + install Python deps + fix permissions
RUN npm run build \
    && pip install --upgrade pip \
    && pip install --no-cache-dir -r app/Infrastructure/Prediction/Python/requirements.txt \
    && mkdir -p storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        storage/app/public \
        bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Start script
COPY docker/start.sh /usr/local/bin/start-app
RUN chmod +x /usr/local/bin/start-app

# Expose port
EXPOSE 8080

# Run app
CMD ["/usr/local/bin/start-app"]