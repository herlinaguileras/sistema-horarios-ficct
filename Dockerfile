# Multi-stage build para optimizar tamaño
FROM php:8.4-fpm as base

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-client \
    libpq-dev \
    nginx \
    supervisor \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Node.js 20 (solo una versión)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

WORKDIR /var/www

# Copiar archivos de dependencias primero (para cache)
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Instalar dependencias PHP
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Instalar dependencias Node
RUN npm ci --legacy-peer-deps

# Copiar el resto del código
COPY . .

# Generar autoload y compilar assets
RUN composer dump-autoload --optimize \
    && npm run build \
    && rm -rf node_modules

# Configurar permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Copiar configuraciones
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start

# Exponer puerto (Railway usa PORT variable)
EXPOSE ${PORT:-8080}

CMD ["/usr/local/bin/start"]
