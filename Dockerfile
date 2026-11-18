FROM registry.sme.prefeitura.sp.gov.br/wordpress/base:8.3.36-apache-trixie

# sets GID and UID
ENV PUID=33
ENV PGID=33

# Sets the directory from which Apache will serve files
ENV WEBHOME="/var/www/html"

# Set Apache root folder within $WEBHOME
# E.g: APACHE_DOCUMENT_ROOT="/public"
ENV APACHE_DOCUMENT_ROOT=""

# Usado no modsecurity/000-default.conf (antes era definido pela imagem base antiga)
ENV DOCUMENT_ROOT="/var/www/html"

# Set to "true" to fix permission for whole $WEBHOME
ENV FIX_WEBHOME_PERMISSION="false"

# ==========================================================

# Sets the limit on the number of connections
# that an individual child server process will handle
ENV APACHE_MAX_CONNECTIONS_PER_CHILD="0"

# Sets the limit on the number of simultaneous requests that will be served
ENV APACHE_MAX_REQUEST_WORKERS="150"

# Maximum number of idle threads
ENV APACHE_MAX_SPARE_THREADS="75"

# Minimum number of idle threads to handle request spikes
ENV APACHE_MIN_SPARE_THREADS="10"

# Sets the number of child server processes created on startup
ENV APACHE_START_SERVERS="2"

# Set the maximum configured value for ThreadsPerChild
# for the lifetime of the Apache httpd process
ENV APACHE_THREAD_LIMIT="64"

# This directive sets the number of threads created by each child process
ENV APACHE_THREADS_PER_CHILD="25"

# Automatically create index.php
ENV AUTO_CREATE_INDEX_FILE="false"

ENV PHP_MAX_EXECUTION_TIME="120"

# ==========================================================
## Instalação do APM Agent
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

ARG APM_AGENT_VERSION=1.17.1

RUN curl -L -O https://github.com/elastic/apm-agent-php/releases/download/v${APM_AGENT_VERSION}/apm-agent-php_${APM_AGENT_VERSION}_amd64.deb \
    && dpkg -i apm-agent-php_${APM_AGENT_VERSION}_amd64.deb \
    && rm apm-agent-php_${APM_AGENT_VERSION}_amd64.deb

# You can easily change PHP-FPM configurations
# by using pre-defined Docker's environment variables.
# Learn more: https://code.shin.company/php#customize-docker-image

COPY phpconf/php.ini "/usr/local/etc/php/php.ini"
COPY --chown=www-data:www-data . /var/www/html/

#modsecurity
COPY modsecurity/000-default.conf /etc/apache2/sites-enabled/
COPY modsecurity/apache2.conf /etc/apache2/apache2.conf
COPY modsecurity/crs-setup.conf /etc/modsecurity/crs/
COPY modsecurity/modsecurity.template /etc/modsecurity/
#Arquivo criado via K8S (ConfigMap)
#COPY modsecurity/location_match.modsecurity.example /etc/apache2/locationmatch/location_match.modsecurity
COPY modsecurity/docker-php-entrypoint /usr/local/bin/

RUN mkdir -p /etc/modsecurity/crs/before && mkdir -p /etc/modsecurity/crs/after
#modsecurity DOS setup
RUN mkdir -p /etc/modsecurity/crs/dos
#load crs
COPY modsecurity/owasp-crs.conf /usr/share/modsecurity-crs/owasp-crs.load

RUN find /var/www/html -type d -exec chmod 755 {} \; && find /var/www/html -type f -exec chmod 644 {} \; && chmod 660 /var/www/html/wp-config.php && chmod 664 /var/www/html/.htaccess
RUN chmod +x /usr/local/bin/docker-php-entrypoint && dos2unix /usr/local/bin/docker-php-entrypoint
RUN rm -Rf phpconf modsecurity

# open_basedir (inclui diretórios do APM)
RUN echo 'open_basedir = "/var/www:/tmp:/dev/stdout:/proc/self:/opt/elastic"' > /usr/local/etc/php/conf.d/zz-open-basedir.ini

RUN a2enmod remoteip

ENTRYPOINT [ "/usr/local/bin/docker-php-entrypoint" ]
CMD [ "apache2-foreground" ]