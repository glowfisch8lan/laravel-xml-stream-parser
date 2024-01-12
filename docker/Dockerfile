FROM php:8.1-fpm
WORKDIR /app
RUN apt-get update && apt-get install -y \
        curl \
        wget \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev libpng-dev libonig-dev \
	libzip-dev \
	sqlite3 \
	libmcrypt-dev \
	supervisor \
	libgmp-dev re2c libmhash-dev libmcrypt-dev file

ENV TZ=Europe/Moscow

RUN pecl install mcrypt-1.0.5 \
	&& docker-php-ext-enable mcrypt \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install -j$(nproc) gd \
        && docker-php-ext-install exif \
        && pecl install xdebug \
                       && docker-php-ext-enable xdebug
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini
COPY supervisor/supervisord.conf /etc/supervisor
ADD supervisor/conf.d/ /etc/supervisor/conf.d/
RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/local/include/
RUN docker-php-ext-configure gmp
RUN docker-php-ext-install gmp
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install simplexml
RUN ln -sf /bin/bash /bin/sh

RUN  apt-get update && \
     apt-get install -y ca-certificates curl gnupg && \
     mkdir -p /etc/apt/keyrings && \
     curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
ENV NODE_MAJOR=20
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list
RUN apt-get update && \
    apt-get install nodejs -y

EXPOSE 9000
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
#service supervisor restart
