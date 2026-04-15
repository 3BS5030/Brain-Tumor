FROM php:8.2-cli

ENV APP_DIR=/app
WORKDIR ${APP_DIR}

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
    python3-pip \
    sqlite3 \
    supervisor \
    unzip \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install bcmath exif gd pcntl pdo pdo_sqlite zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN npm run build \
    && pip3 install --no-cache-dir -r app/Infrastructure/Prediction/Python/requirements.txt \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /usr/local/bin/start-app
RUN chmod +x /usr/local/bin/start-app

EXPOSE 8080

CMD ["/usr/local/bin/start-app"]
