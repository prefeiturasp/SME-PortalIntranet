FROM registry.sme.prefeitura.sp.gov.br/wordpress/base:7.4-fpm-apache

# Base: imagem oficial php:7.4-apache (mod_php), gerada a partir do Dockerfile.php7

# Diretório servido pelo Apache (usado no modsecurity/000-default.conf)
ENV WEBHOME="/var/www/html"
ENV DOCUMENT_ROOT="/var/www/html"

# Timeout do Apache, em segundos (usado no modsecurity/000-default.conf)
ENV PHP_MAX_EXECUTION_TIME="120"

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
RUN chmod +x /usr/local/bin/docker-php-entrypoint && dos2unix /usr/local/bin/docker-php-entrypoint && sed -i '/disable ghostscript format types/,+6d' /etc/ImageMagick-6/policy.xml
RUN rm -Rf phpconf modsecurity

# Healthcheck: GET /ping responde "pong"
RUN mkdir -p /usr/share/healthcheck && echo pong > /usr/share/healthcheck/ping

ENTRYPOINT [ "/usr/local/bin/docker-php-entrypoint" ]
# Definir ENTRYPOINT apaga o CMD herdado da imagem base, então ele é declarado de novo aqui
CMD [ "apache2-foreground" ]
