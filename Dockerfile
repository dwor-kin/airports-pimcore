FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    libssl-dev \
    unzip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libzip-dev \
    libsodium-dev \
    librabbitmq-dev \
    locales \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql exif gd intl zip bcmath sodium opcache \
    && pecl install amqp \
    && docker-php-ext-enable amqp

RUN apt-get update && apt-get install -y cron

# Set system locale to UTF-8
RUN sed -i '/en_US.UTF-8/s/^# //g' /etc/locale.gen && \
    locale-gen && \
    update-locale LANG=en_US.UTF-8

ENV LANG=en_US.UTF-8
ENV LC_ALL=en_US.UTF-8

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# set cron
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Upewnij się, że cron jest zainstalowany
RUN apt-get update && apt-get install -y cron

# Ustawienie entrypoint
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]