FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    coreutils \
    gettext \
    php82-fpm \
    php82-ctype \
    php82-curl \
    php82-dom \
    php82-gd \
    php82-iconv \
    php82-json \
    php82-intl \
    php82-fileinfo\
    php82-mbstring \
    php82-opcache \
    php82-openssl \
    php82-pdo \
    php82-pdo_mysql \
    php82-mysqli \
    php82-xml \
    php82-xsl \
    php82-zlib \
    php82-phar \
    php82-tokenizer \
    php82-session \
    php82-simplexml \
    php82-zip \
    php82-xmlwriter \
    php82-sodium \
    php82-pecl-apcu \
    icu-dev \
    libzip-dev \
    git \
    make \
    curl \
    bash

RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip
RUN docker-php-ext-install intl pdo pdo_mysql

# Enable php8-xdebug if $PHP_XDEBUG_MODE is not empty
ARG PHP_XDEBUG_MODE=off
ARG PHP_XDEBUG_CLIENT_PORT=5902
ARG PHP_XDEBUG_CLIENT_HOST=host.docker.internal
COPY xdebug.ini  /etc/php82/conf.d/xdebug.ini.template
RUN if [[ "$PHP_XDEBUG_MODE" != "" ]]; then \
        apk add --no-cache php82-pecl-xdebug; \
        export PHP_XDEBUG_MODE=$PHP_XDEBUG_MODE; \
        export PHP_XDEBUG_CLIENT_PORT=$PHP_XDEBUG_CLIENT_PORT; \
        export PHP_XDEBUG_CLIENT_HOST=$PHP_XDEBUG_CLIENT_HOST; \
        envsubst < /etc/php82/conf.d/xdebug.ini.template > /etc/php82/conf.d/xdebug.ini; \
    fi
RUN rm -f /etc/php82/conf.d/xdebug.ini.template

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv ~/.symfony5/bin/symfony /usr/local/bin/symfony

# RUN apk add --update nodejs npm \
#     && npm install --global yarn

COPY symfony.ini /etc/php82/conf.d/
COPY symfony.ini /etc/php82/cli/conf.d/

COPY symfony.pool.conf /etc/php82/php-fpm.d/

ARG GIT_USER_EMAIL
ARG GIT_USER_NAME
RUN git config --global user.email "$GIT_USER_EMAIL" \
    && git config --global user.name "$GIT_USER_NAME"

WORKDIR /var/www/symfony

CMD ["php-fpm82", "-F"]

EXPOSE 9001