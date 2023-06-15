FROM php:8.1-fpm

LABEL maintainer="romanlev4yk@gmail.com"

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV DEBIAN_FRONTEND noninteractive
ENV EDITOR=/usr/bin/nano
ENV COMPOSER_MEMORY_LIMIT=-1

RUN additionalPackages=" \
        apt-transport-https \
        ca-certificates \
        msmtp-mta \
        libcurl4-gnutls-dev \
        ssh \
        mc \
        htop \
        wget \
        curl \
        cron \
        nano \
        dos2unix \
        ffmpeg \
        sox \
    " \
    buildDeps=" \
        freetds-dev \
        libbz2-dev \
        libc-client-dev \
        libfreetype6-dev \
        libgmp3-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libkrb5-dev \
        libldap2-dev \
        libmcrypt-dev \
        libpq-dev \
        libpspell-dev \
        librabbitmq-dev \
        libsasl2-dev \
        libsnmp-dev \
        libssl-dev \
        libtidy-dev \
        libxml2-dev \
        libxpm-dev \
        libxslt1-dev \
        zlib1g-dev \
    " \
    && runDeps=" \
        libc-client2007e \
        libfreetype6 \
        libjpeg62-turbo \
        libmcrypt4 \
        libpng-dev \
        libzip-dev \
        libpng16-16 \
        libpq5 \
        libsybdb5 \
        libx11-6 \
        libxpm4 \
        libxslt1.1 \
        gnupg \
        openssh-client \
        rsync \
    " \
    && phpModules=" \
        bcmath \
        bz2 \
        calendar \
        dba \
        exif \
        gd \
        gettext \
        gmp \
        imap \
        intl \
        ldap \
        mysqli \
        opcache \
        pcntl \
        pdo_dblib \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        pspell \
        shmop \
        snmp \
        soap \
        sysvmsg \
        sysvsem \
        sysvshm \
        tidy \
        xsl \
        zip \
    " \
    && echo "deb http://security.debian.org/debian-security bullseye-security main contrib non-free" > /etc/apt/sources.list.d/additional.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends $additionalPackages \
    && apt-get install -y --no-install-recommends $buildDeps \
    && apt-get install -y --no-install-recommends $runDeps \
    && docker-php-source extract \
    && cd /usr/src/php/ext/ \
    && ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h \
    && ln -s /usr/lib/x86_64-linux-gnu/libldap_r.so /usr/lib/libldap.so \
    && ln -s /usr/lib/x86_64-linux-gnu/libldap_r.a /usr/lib/libldap_r.a \
    && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.a /usr/lib/libsybdb.a \
    && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.so /usr/lib/libsybdb.so \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ --with-xpm=/usr/include/ \
    && docker-php-ext-configure imap --with-imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-configure ldap --with-ldap-sasl \
    && php -m \
    && docker-php-ext-install $phpModules \
    && printf "\n" \
    && for ext in $phpModules; do \
           rm -f /usr/local/etc/php/conf.d/docker-php-ext-$ext.ini; \
       done \
    && docker-php-source delete \
    && docker-php-ext-enable $phpModules

# Install pecl modules
RUN pecl install igbinary \
    && pecl install -o -f redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

# Install XDebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install composer and prestissimo plugin and put binary into $PATH
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install tools
RUN /usr/local/bin/composer global require phpunit/phpunit &&\
    /usr/local/bin/composer global require deployer/recipes && \
    curl -LO https://deployer.org/deployer.phar && \
    mv deployer.phar /usr/local/bin/dep && \
    chmod +x /usr/local/bin/dep

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y unzip nodejs build-essential \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false -o APT::AutoRemove::SuggestsImportant=false $buildDeps \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && . ~/.bashrc

# Set timezone
RUN rm /etc/localtime \
  && ln -s /usr/share/zoneinfo/UTC /etc/localtime \
  && "date"

# Setup working directory
WORKDIR /var/www

COPY . /var/www
COPY .docker/docker-entrypoint /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-entrypoint \
    && chmod +x /usr/local/bin/docker-entrypoint

RUN chmod -R 755 storage/ \
     && chmod -R 755 public/ \
     && chmod -R 755 bootstrap/ \
     && chmod -R 755 storage/

#####################################
# Laravel Schedule Cron Job:
#####################################
RUN echo "* * * * * root /usr/local/bin/php /var/www/artisan schedule:run >> /dev/null 2>&1"  >> /etc/cron.d/laravel-scheduler \
    && chmod 0644 /etc/cron.d/laravel-scheduler

CMD ["/bin/bash", "-c", "cron && tail -f /var/log/cron.log"]

EXPOSE 8080 8443 9000 9003

ENTRYPOINT ["docker-entrypoint"]

CMD ["php-fpm"]
