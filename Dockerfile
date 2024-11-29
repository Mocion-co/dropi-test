FROM webdevops/php-nginx:8.3-alpine as builder

RUN rm -rf /var/lib/nginx/*
RUN apk upgrade --available && sync
RUN apk add --no-cache oniguruma-dev postgresql-dev libxml2-dev
RUN apk add php-bcmath \
  php-ctype php-fileinfo \
  php-json \
  php-mbstring \
  php-pdo_mysql \
  php-pdo_pgsql \
  php-tokenizer \
  php-xml dos2unix \
  tidyhtml-dev

RUN docker-php-ext-install \
  tidy

RUN docker-php-ext-enable tidy

RUN mkdir -p /var/lib/nginx/tmp /var/log/nginx
RUN chown -R application:application /var/lib/nginx /var/log/nginx
RUN chmod -R 755 /var/lib/nginx /var/log/nginx

# Copy Composer binary from the Composer official Docker image
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

ENV WEB_DOCUMENT_ROOT /app/web
# ENV APP_ENV production
WORKDIR /app

#we need to optimize this copy to only copy the things it requires
COPY . .
#COPY .docker/.env.demo /app/.env

FROM builder AS final

WORKDIR /app
ENV WEB_DOCUMENT_ROOT /app/web

RUN docker-php-ext-enable tidy

# COPY app config web .env /app//

RUN git config --global --add safe.directory /app

EXPOSE 80

# COPY ./docker/entrypoint.sh /entrypoint.d/apptega.sh
# RUN chmod +x /entrypoint.d/apptega.sh
# RUN dos2unix /entrypoint.d/apptega.sh
