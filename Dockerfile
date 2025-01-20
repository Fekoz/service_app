ARG PHP_VERSION=8.0

FROM php:${PHP_VERSION}-alpine AS symfony_php

RUN apk add --update --no-cache build-base gcc g++ pcre pcre-dev libc6-compat supervisor

RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
		gnu-libiconv \
	;

ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

RUN apk add openssl \
	&& ln -s /usr/local/etc/openssl@1.1 /usr/local/etc/openssl

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		zlib-dev \
		libpng-dev \
	; \
	docker-php-ext-configure zip; \
	pecl install oauth; \
	apk add --no-cache --virtual .libmemcached-deps \
		libmemcached-dev \
	; \
	pecl install memcached; \
	docker-php-ext-enable oauth; \
	docker-php-ext-enable memcached; \
	docker-php-ext-install -j$(nproc) \
		intl \
		zip \
		gd \
		sockets \
		pcntl \
	; \
	pecl install apcu-5.1.20; \
	docker-php-ext-enable apcu; \
	docker-php-ext-enable opcache; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps;
	
RUN apk add --no-cache \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd;

ARG APP_TYPE="prod"
ENV APP_TYPE ${APP_TYPE}

# Устанавливаем необходимые пакеты и расширения, если APP_TYPE равен "dev"
RUN if [ "$APP_TYPE" = "dev" ]; then \
    apk add --no-cache autoconf g++ make linux-headers \
    && pecl install xdebug; \
fi

COPY ./cfg/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

ARG APP_TYPE="prod"
ENV APP_TYPE ${APP_TYPE}

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY ./cfg/php/conf.d/symfony.${APP_TYPE}.ini $PHP_INI_DIR/conf.d/symfony.ini

COPY ./cfg/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

VOLUME /var/run/php

COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/app

ARG SKELETON="symfony/skeleton"
ENV SKELETON ${SKELETON}

ARG STABILITY="stable"
ENV STABILITY ${STABILITY}

ARG SYMFONY_VERSION=""
ENV SYMFONY_VERSION ${SYMFONY_VERSION}

RUN composer create-project "${SKELETON} ${SYMFONY_VERSION}" . --stability=$STABILITY --prefer-dist --no-dev --no-progress --no-interaction; \
	composer clear-cache

###> recipes ###
###> doctrine/doctrine-bundle ###
RUN apk add --no-cache --virtual .pgsql-deps postgresql-dev; \
	docker-php-ext-install -j$(nproc) pdo_pgsql; \
	apk add --no-cache --virtual .pgsql-rundeps so:libpq.so.5; 
###< doctrine/doctrine-bundle ###
###< recipes ###

### THIS DIR
ARG SUPERVISOR="supervisor_dev"
ENV SUPERVISOR ${SUPERVISOR}
COPY ./src .
COPY ./cfg/$SUPERVISOR/supervisord.conf /etc/supervisord.conf
COPY ./cfg/$SUPERVISOR/conf.d/* /etc/supervisor/conf.d/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer symfony:dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /srv/app/var

RUN ln -s /usr/local/bin/

ENTRYPOINT ["docker-entrypoint"]

CMD ["/usr/bin/supervisord"]