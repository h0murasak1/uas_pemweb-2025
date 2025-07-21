FROM php:8.3-fpm

ENV PS1="\u@\h:\w\\$ "
ENV TZ="Asia/Jakarta"

ENV COMPOSER_MEMORY_LIMIT='-1'

RUN apt-get update && \
    apt-get install -y --force-yes --no-install-recommends \
        libmemcached-dev \
        iputils-ping \
        telnet \
        netcat-openbsd \
        net-tools \
        libmcrypt-dev \
        libreadline-dev \
        libgmp-dev \
        libzip-dev \
        libz-dev \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        libfreetype6-dev \
        libssl-dev \
        openssh-server \
        libmagickwand-dev \
        git \
        cron \
        nano \
        libxml2-dev \
        nodejs \
        npm 

# Install soap extention
RUN docker-php-ext-install soap

# Install for image manipulationdock
RUN docker-php-ext-install exif

# Install the PHP pcntl extention
RUN docker-php-ext-install pcntl

# Install the PHP intl extention
RUN docker-php-ext-install intl

# Install the PHP gmp extention
RUN docker-php-ext-install gmp

# Install the PHP zip extention
RUN docker-php-ext-install zip

# Install the PHP pdo_mysql extention
RUN docker-php-ext-install pdo_mysql

# Install the PHP pdo_pgsql extention
RUN docker-php-ext-install pdo_pgsql

# Install the PHP bcmath extension
RUN docker-php-ext-install bcmath

#####################################
# PHPRedis:
#####################################
# Install Redis extension from GitHub as fallback
RUN git clone https://github.com/phpredis/phpredis.git /tmp/phpredis \
    && cd /tmp/phpredis \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/phpredis
# Install MongoDB extension from GitHub as fallback
RUN git clone https://github.com/mongodb/mongo-php-driver.git /tmp/mongodb \
    && cd /tmp/mongodb \
    && git submodule update --init \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && docker-php-ext-enable mongodb \
    && rm -rf /tmp/mongodb
#####################################
# Imagick:
#####################################

# Try PECL first, then fallback to manual installation
RUN (pecl install imagick && docker-php-ext-enable imagick) || \
    (git clone https://github.com/Imagick/imagick.git /tmp/imagick && \
     cd /tmp/imagick && \
     phpize && \
     ./configure && \
     make && \
     make install && \
     docker-php-ext-enable imagick && \
     rm -rf /tmp/imagick)

#####################################
# GD:
#####################################

# Install the PHP gd library
RUN docker-php-ext-install gd && \
#    docker-php-ext-configure gd \
#        --with-jpeg-dir
#        --with-freetype-dir=/usr/include/freetype2 && \
     docker-php-ext-configure gd --with-freetype --with-jpeg  --with-webp && \
     docker-php-ext-install gd
#####################################
# xDebug:
#####################################

# Install the xdebug extension with fallback
RUN (pecl install xdebug && docker-php-ext-enable xdebug) || \
    (git clone https://github.com/xdebug/xdebug.git /tmp/xdebug && \
     cd /tmp/xdebug && \
     phpize && \
     ./configure && \
     make && \
     make install && \
     docker-php-ext-enable xdebug && \
     rm -rf /tmp/xdebug)

#####################################
# PHP Memcached:
#####################################

# Install the php memcached extension with fallback
RUN (pecl install memcached && docker-php-ext-enable memcached) || \
    (git clone https://github.com/php-memcached-dev/php-memcached.git /tmp/memcached && \
     cd /tmp/memcached && \
     phpize && \
     ./configure && \
     make && \
     make install && \
     docker-php-ext-enable memcached && \
     rm -rf /tmp/memcached)

#####################################
# Composer:
#####################################

# Install composer and add its bin to the PATH.
RUN curl -s http://getcomposer.org/installer | php && \
    echo "export PATH=${PATH}:/var/www/html/vendor/bin" >> ~/.bashrc && \
    mv composer.phar /usr/local/bin/composer
# Source the bash
RUN . ~/.bashrc

#####################################
# Laravel Schedule Cron Job:
#####################################

RUN echo "* * * * * root /usr/local/bin/php /var/www/html/artisan schedule:run >> /dev/null 2>&1"  >> /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler

#
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#

ADD ./local.ini /usr/local/etc/php/conf.d

#####################################
# Aliases:
#####################################
# docker-compose exec php-fpm dep --> locally installed Deployer binaries
#RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/vendor/bin/dep "$@"' > /usr/bin/dep RUN chmod +x 
#/usr/bin/dep
# docker-compose exec php-fpm art --> php artisan
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/artisan "$@"' > /usr/bin/art
RUN chmod +x /usr/bin/art
# docker-compose exec php-fpm migrate --> php artisan migrate
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/artisan migrate "$@"' > /usr/bin/migrate
RUN chmod +x /usr/bin/migrate
# docker-compose exec php-fpm fresh --> php artisan migrate:fresh --seed
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/artisan migrate:fresh --seed' > /usr/bin/fresh
RUN chmod +x /usr/bin/fresh
# docker-compose exec php-fpm t --> run the tests for the project and generate testdox
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/artisan config:clear\n/var/www/html/vendor/bin/phpunit -d memory_limit=2G --stop-on-error --stop-on-failure --testdox-text=tests/report.txt "$@"' > /usr/bin/t
RUN chmod +x /usr/bin/t
# docker-compose exec php-fpm d --> run the Laravel Dusk browser tests for the project
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/html/artisan config:clear\n/bin/bash\n/usr/local/bin/php /var/www/html/artisan dusk -d memory_limit=2G --stop-on-error --stop-on-failure --testdox-text=tests/report-dusk.txt "$@"' > /usr/bin/d
RUN chmod +x /usr/bin/d

RUN rm -r /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY ./docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN ln -s /usr/local/bin/docker-entrypoint.sh /
ENTRYPOINT ["docker-entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm"]
