FROM php:8.1-fpm as base

WORKDIR /opt/app

RUN apt-get update \
    && apt-get install -y \
        curl \
        nano \
        git \
        unzip \
    && docker-php-ext-install pdo_mysql

ADD docker/fpm/php.ini /usr/local/etc/php/

FROM base as deps

ARG GITHUB_TOKEN

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN composer config --global --auth github-oauth.github.com ${GITHUB_TOKEN}

FROM deps as build

ADD src/ .
ADD composer.json .
ADD composer.lock .

RUN composer install

FROM base as test

# install test dependencies e.g. code coverage extension...

FROM base as fpm

ADD src/ /opt/app
ADD public/ /opt/app

COPY --from=build vendor /opt/app

# execute the actuall application