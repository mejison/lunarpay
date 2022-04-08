FROM composer as LunarPayBuilder
MAINTAINER Jasser <jasser@apollo.inc>
WORKDIR /tmp/application
COPY composer.json  ./
RUN composer install

FROM php:7.4-apache
RUN docker-php-ext-install mysqli
RUN a2enmod rewrite
WORKDIR /tmp/application
COPY . /var/www/html/
COPY --from=LunarPayBuilder /tmp/application/vendor/ /var/www/html/vendor  

STOPSIGNAL SIGWINCH
EXPOSE 80

CMD ["apache2-foreground"]

