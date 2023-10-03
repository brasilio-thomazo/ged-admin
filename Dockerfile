ARG EXT="pdo pdo_sqlite pdo_pgsql pdo_odbc pdo_mysql pdo_dblib"
ARG PECL_EXT="redis protobuf mongodb memcache yaml"
ARG USER_UID=1000
ARG USER_GID=1000

#
# NODEJS
#
FROM alpine as node
ARG USER_UID
ARG USER_GID
ENV UID=${USER_GID}
ENV GID=${USER_GID}

COPY . /home/app/public_html
WORKDIR /home/app/public_html
RUN addgroup -g ${UID} app \
    && adduser -h /home/app -G app -u ${GID} -D app \
    && apk add --no-cache nodejs npm bash \
    && chown app:app /home/app -R \
    && su app -c 'npm i' \
    && su app -c 'npm run build'
USER app

FROM devoptimus/php-fpm as fpm
ARG EXT
ARG PECL_EXT
ARG USER_UID
ARG USER_GID
ENV UID=${USER_GID}
ENV GID=${USER_GID}

USER root
RUN install-php-ext ${EXT} \
    && install-php-pecl-ext ${PECL_EXT}
COPY resources/docker/www.ini /etc/php/fpm/pool.d/
COPY resources/docker/fpm-entrypoint /usr/local/bin/entrypoint


USER app
WORKDIR /home/app/public_html
COPY --chown=app composer.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY --chown=app . .
RUN composer install --no-dev --prefer-dist
COPY --from=node /home/app/public_html/public /home/app/public_html/public

RUN php artisan event:cache \
    && php artisan route:cache \
    && php artisan view:cache


#
# NGINX
#
FROM devoptimus/nginx as nginx
COPY resources/docker/default.template /etc/nginx/template.d/
COPY --from=node /home/app/public_html/public /home/app/public_html/public
WORKDIR /home/app/public_html/public
