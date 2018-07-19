FROM yiisoftware/yii2-php:7.1-apache

RUN a2enmod rewrite

ARG uid
ARG gid

RUN usermod -u $uid www-data

RUN chmod 755 /usr/local/bin/composer
RUN chown www-data:www-data /var/www

RUN pecl install xdebug-2.6.0 \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /usr/src/yii2-fias
VOLUME /usr/src/yii2-fias
RUN rm /var/www/html
RUN ln -s /usr/src/yii2-fias/tests/_app/web /var/www/html || true