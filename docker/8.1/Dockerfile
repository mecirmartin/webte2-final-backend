FROM ubuntu:22.04

LABEL maintainer="Taylor Otwell"

ARG WWWGROUP
ARG NODE_VERSION=16
ARG POSTGRES_VERSION=14

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 \
    && mkdir -p ~/.gnupg \
    && chmod 600 ~/.gnupg \
    && echo "disable-ipv6" >> ~/.gnupg/dirmngr.conf \
    && apt-key adv --homedir ~/.gnupg --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys 0x14AA40EC0831756756D7F66C4F4EA0AAE5267A6C \
    && echo "deb https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y php8.1-cli php8.1-dev \
       php8.1-pgsql php8.1-sqlite3 php8.1-gd \
       php8.1-curl \
       php8.1-imap php8.1-mysql php8.1-mbstring \
       php8.1-xml php8.1-zip php8.1-bcmath php8.1-soap \
       php8.1-intl php8.1-readline \
       php8.1-ldap \
       php8.1-msgpack php8.1-igbinary php8.1-redis php8.1-swoole \
       php8.1-memcached php8.1-pcov php8.1-xdebug \
       octave \
       liboctave-dev \
    && octave-cli --eval "pkg install -forge control" \
    && php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && curl -sL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && echo "deb http://apt.postgresql.org/pub/repos/apt jammy-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && curl --silent -o - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - \
    && apt-get update \
    && apt-get install -y yarn \
    && apt-get install -y mysql-client \
    && apt-get install -y postgresql-client-$POSTGRES_VERSION \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Octave
# RUN apt-get update && apt-get upgrade -y \
#      && apt-get install -y \
#      g++ \
#      libbz2-dev \
#      libc-client-dev \
#      libcurl4-gnutls-dev \
#      libedit-dev \
#      libfreetype6-dev \
#      libicu-dev \
#     #  libjpeg62-turbo-dev \
#      libkrb5-dev \
#      libldap2-dev \
#      libldb-dev \
#      libmagickwand-dev \
#      libmcrypt-dev \
#      libmemcached-dev \
#      libpng-dev \
#      libpq-dev \
#      libsqlite3-dev \
#      libssl-dev \
#      libreadline-dev \
#      libxslt1-dev \
#      libzip-dev \
#      memcached \
#      wget \
#      unzip \
#      liboctave-dev \
#      zlib1g-dev \
#      && docker-php-ext-install -j$(nproc) \
#      bcmath \
#      bz2 \
#      calendar \
#      exif \
#      gettext \
#      mysqli \
#      opcache \
#      pdo_mysql \
#      pdo_pgsql \
#      pgsql \
#      soap \
#      xsl \
#      && octave-cli --eval "pkg install -forge control" \
#      && docker-php-ext-configure gd --with-freetype --with-jpeg \
#      && docker-php-ext-install -j$(nproc) gd \
#      && PHP_OPENSSL=yes docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
#      && docker-php-ext-install -j$(nproc) imap \
#      && docker-php-ext-configure intl \
#      && docker-php-ext-install -j$(nproc) intl \
#      && docker-php-ext-configure ldap \
#      && docker-php-ext-install ldap \
#      && docker-php-ext-configure zip \
#      && docker-php-ext-install zip \
#      && CFLAGS="$CFLAGS -D_GNU_SOURCE" docker-php-ext-install sockets \
#      && pecl install xmlrpc-1.0.0RC3 && docker-php-ext-enable xmlrpc \
#      && pecl install xdebug && docker-php-ext-enable xdebug \
#      && pecl install memcached && docker-php-ext-enable memcached \
#      && pecl install mongodb && docker-php-ext-enable mongodb \
#      && pecl install redis && docker-php-ext-enable redis \
#      && yes '' | pecl install imagick && docker-php-ext-enable imagick \
#      && docker-php-source delete \
#      && apt-get remove -y g++ wget \
#      && apt-get autoremove --purge -y && apt-get autoclean -y && apt-get clean -y \
#      && rm -rf /var/lib/apt/lists/* \
#      && rm -rf /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.1

RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.1/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8000

ENTRYPOINT ["start-container"]
