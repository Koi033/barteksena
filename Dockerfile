FROM php:8.2-apache

# 1. Instalar dependencias del sistema y extensiones de PHP para base de datos
# (libcurl + curl: necesarios para llamar a la API HTTP de Brevo en PasswordController)
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli curl

# 2. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Copiar el código del proyecto al servidor
COPY . /var/www/html/

WORKDIR /var/www/html

# 4. Instalar las dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# 5. Habilita el módulo de reescritura de Apache (para rutas y .htaccess)
RUN a2enmod rewrite

EXPOSE 80