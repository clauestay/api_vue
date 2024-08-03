FROM php:8.3.10-apache-bookworm

# Instalar dependencias del sistema
RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
            libaio1 \
            git \
            zip \
            unzip \
            curl \
            gnupg \
            libmemcached-dev \
            libz-dev \
            libpq-dev \
            libjpeg-dev \
            libpng-dev \
            libfreetype6-dev \
            libssl-dev \
            libwebp-dev \
            libxpm-dev \
            libmcrypt-dev \
            libonig-dev \
            libzip-dev; \
    rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias
RUN set -eux; \
    docker-php-ext-install pdo_pgsql; \
    docker-php-ext-configure gd \
            --prefix=/usr \
            --with-jpeg \
            --with-webp \
            --with-xpm \
            --with-freetype; \
    docker-php-ext-install gd; \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip

# CONFIGURACION ORACLE - OCI8
# Crear carpeta y copiar archivos oracle
RUN mkdir -p /opt/oracle
COPY ./docker/oracle /opt/oracle

# Entrar a carpeta y crear enlace
RUN cd /opt/oracle/instantclient_11_2 && \
    ln -s libclntsh.so.11.1 libclntsh.so

# Instalar oci8
RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/opt/oracle/instantclient_11_2 && docker-php-ext-install oci8

# Configurar PHP
RUN echo "extension=oci8.so" >> /usr/local/etc/php/php.ini && \
    echo /opt/oracle/instantclient_11_2 > /etc/ld.so.conf.d/oracle-instantclient.conf && \
    ldconfig

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos de la aplicación
COPY . .

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/

# Instalar dependencias de Composer
# RUN composer install --no-interaction --no-dev --optimize-autoloader

# Generar la clave de la aplicación
#RUN php artisan key:generate

# Configurar Sanctum
#RUN php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"


# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# Exponer el puerto 80
EXPOSE 80

CMD ["apache2-foreground"]