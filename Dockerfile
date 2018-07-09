FROM php:7.0.30-cli-alpine
WORKDIR /usr/src/yii2-fias

ARG uid
ARG gid

RUN echo $uid

RUN addgroup -g $gid hostgroup
RUN adduser -D -u $uid -G hostgroup hostuser

RUN apk add --no-cache libxml2 libxml2-dev $PHPIZE_DEPS

RUN docker-php-ext-install soap && \
    docker-php-ext-enable soap && \
    pecl install xdebug-2.6.0 && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-enable pdo_mysql