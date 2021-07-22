FROM php:7.4.15

# set working directory
WORKDIR /app

ENV APP_ENV dev
ENV APP_DEBUG 1

# Run updates
RUN apt-get update --fix-missing \
    && apt-get install -y \
    curl \
    ssh \
    git \
    libzip-dev \
    unzip \
    libyaml-dev \
    locales \
    locales-all

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install php extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions xsl

# install app dependencies
COPY composer*.json ./
COPY ./src/ ./src/

CMD composer install --no-interaction --no-progress --profile
