FROM alpine:3.22

WORKDIR /app

RUN apk update \
    && apk add --no-cache \
        inkscape \
        php \
        php-curl \
        php-intl \
        php-phar \
        php-iconv \
        php-openssl \
    && apk add gnu-libiconv=1.15-r3 --update --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/v3.13/community/

ENV LD_PRELOAD="/usr/lib/preloadable_libiconv.so php"

COPY . .

RUN --mount=type=bind,from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

RUN echo "*/30 * * * * php /app/scripts/update-cache.php > /dev/null 2>&1" > /etc/crontabs/nobody

RUN chmod +x docker-entrypoint.sh \
    && chown nobody:nogroup . \
    && chmod 700 . \
    && mkdir -p /var/cache/github-stats \
    && chown nobody:nogroup /var/cache/github-stats

USER nobody:nogroup

EXPOSE 8000

ENTRYPOINT ["/app/docker-entrypoint.sh"]
