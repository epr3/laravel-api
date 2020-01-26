FROM php:7.3-fpm-alpine

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="20000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="256" \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE="10"

RUN apk add --no-cache --virtual .build-dependencies libxml2-dev
RUN apk add --no-cache libpng-dev freetype-dev libjpeg-turbo-dev supervisor

RUN docker-php-ext-configure gd --with-jpeg-dir=/usr/include/ \
  --with-png-dir=/usr/include/ --with-freetype-dir=/usr/include/

RUN docker-php-ext-install gd bcmath pdo_mysql sockets soap opcache

RUN apk del --no-cache .build-dependencies

COPY ./containers/php/zzz-docker.conf /usr/local/etc/php-fpm.d/
COPY ./containers/php/*.ini $PHP_INI_DIR/conf.d/
COPY ./containers/php/supervisord.conf /etc/supervisord.conf

COPY ./src /var/www/html

RUN chown -R www-data:www-data \
  /var/www/html/storage \
  /var/www/html/bootstrap/cache

WORKDIR /var/www/html

EXPOSE 9000
