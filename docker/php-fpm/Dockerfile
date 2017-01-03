FROM phpdockerio/php71-fpm:latest

MAINTAINER vibby <vincent@beauvivre.fr>

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install php-memcached php7.1-pgsql \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
	&& composer --version

RUN echo 'alias sf="sudo -u www-data php /var/www/germ/app/console"' >> ~/.bashrc 

# RUN composer install
# RUN sf assets:install web

WORKDIR "/var/www/germ"
