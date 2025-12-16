FROM php:5.6.40-apache-stretch
MAINTAINER Luiz Priotto luiz.priotto@amcom.com.br

# Altera o sources.list para utilizar o repositório de arquivamento
RUN rm /etc/apt/sources.list && touch /etc/apt/sources.list && echo "deb http://archive.debian.org/debian/ stretch main" | tee -a /etc/apt/sources.list \
&& echo "deb-src http://archive.debian.org/debian/ stretch main" | tee -a /etc/apt/sources.list \
&& echo "deb http://archive.debian.org/debian-security stretch/updates main" | tee -a /etc/apt/sources.list \
&& echo "deb-src http://archive.debian.org/debian-security stretch/updates main" | tee -a /etc/apt/sources.list

# Instala as dependências necessárias
RUN apt-get update && apt-get install --no-install-recommends --yes \
    cron \
    g++ \
    gettext \
    libicu-dev \
    libc-client-dev \
    libxml2-dev \
    libfreetype6-dev \
    libgd-dev \
    libmcrypt-dev \
    bzip2 \
    libbz2-dev \
    libtidy-dev \
    libz-dev \
    libmemcached-dev \
    libxslt-dev \
    libzip-dev \
    libmagickwand-dev \
    libmagickcore-dev \
    && rm -rf /var/lib/apt/lists/*

# Copia o php.ini-production para php.ini
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-configure mysqli --with-mysqli=mysqlnd

# Instala as extensões do PHP necessárias
RUN docker-php-ext-install \
    bcmath \
    bz2 \
    calendar \
    dba \
    exif \
    fileinfo \
    gd \
    gettext \
    intl \
    pdo \
    pdo_mysql \
    mysqli \
    soap \
    tidy \
    xmlrpc \
    xsl \
    zip \
    sockets

# Habilita os módulos do Apache
RUN a2enmod rewrite headers

# Instala a extensão Imagick
RUN pecl install imagick && docker-php-ext-enable imagick
