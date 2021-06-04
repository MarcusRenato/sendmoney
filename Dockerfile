FROM php:8.0-fpm

# Argumentos definidos em docker-compose.yml
ARG user
ARG uid

# Instalando dependencias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar estensões php
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário do sistema para rodar composer e comandos do artisan
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Diretório de trabalho
WORKDIR /var/www

USER $user