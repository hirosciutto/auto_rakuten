FROM php:8.1-fpm
RUN apt-get update
RUN apt-get install -y vim
RUN pecl install xdebug
RUN apt-get install -y zip unzip git libfreetype6-dev libjpeg62-turbo-dev libpng-dev
RUN git clone https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis

RUN docker-php-ext-install bcmath
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install redis
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd exif

RUN apt-get install -y --no-install-recommends libicu-dev
RUN rm -rf /var/lib/apt/lists/*

COPY php.ini /usr/local/etc/php/
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install intl


RUN apt-get update && apt-get install -y \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
	&& docker-php-ext-enable imagick

RUN apt-get update && apt-get install -y python3 python3-pip python3-venv --no-install-recommends \
    && python3 -m venv /opt/ddg-venv \
    && /opt/ddg-venv/bin/pip install ddgs selenium \
    && rm -rf /var/lib/apt/lists/*

# Selenium 用: Chromium と ChromeDriver（あらすじ取得スクリプトで使用）
RUN apt-get update && apt-get install -y --no-install-recommends \
    chromium \
    chromium-driver \
    && rm -rf /var/lib/apt/lists/*
