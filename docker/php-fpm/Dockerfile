FROM php:8.3-fpm-alpine AS php_fpm_alpine
MAINTAINER uncle.dima <dm.kopylets@gmail.com>
LABEL mainterner="uncle.dima <dm.kopylets@gmail.com>"
LABEL description="Alpine based image with php8.3-fpm"

ENV COMPOSER_MEMORY_LIMIT='-1'
ENV TZ="Europe/Kiev"
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apk update \
 && apk upgrade \
 && apk add --no-cache \
 autoconf \
 bash \
 build-base \
 coreutils \
 curl \
 git \
 freetype-dev \
 libjpeg-turbo-dev \
 libpng-dev \
 libzip-dev \
 postgresql-dev \
 nodejs \
 npm \
 zlib-dev

# install the PHP extensions we need

RUN docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql && \
  docker-php-ext-install pgsql

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
  docker-php-ext-install gd

RUN docker-php-ext-install intl opcache pdo_pgsql zip

COPY ./opcache-recommended.ini /usr/local/etc/php/conf.d/opcache-recommended.ini

RUN pecl install apcu

RUN docker-php-ext-enable apcu pdo_pgsql sodium

RUN apk add --update linux-headers

#RUN pecl install xdebug  && docker-php-ext-enable xdebug

#COPY ./docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ADD ./docker-custom.ini /usr/local/etc/php/conf.d


ARG PUID=1000
ARG PGID=1000
RUN apk --no-cache add shadow && \
    groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

RUN chown -R www-data:www-data /var/www && chmod 755 /var/www

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash

RUN apk add symfony-cli

WORKDIR /var/www

#####################################
# Composer:
#####################################

RUN curl -s http://getcomposer.org/installer | php && \
    echo "export PATH=${PATH}:/var/www/vendor/bin" >> ~/.bashrc && \
    mv composer.phar /usr/local/bin/composer

# Change current user to www-data
USER www-data

EXPOSE 9000

CMD ["php-fpm"]
