FROM wordpress:latest

RUN apt-get update && apt-get install -y less wget subversion mysql-client ssmtp mailutils
# RUN apt-get install -y php-pear libyaml-dev && pecl install yaml-1.3.1
# RUN echo "extension=yaml.so" > /usr/local/etc/php/conf.d/docker-php-ext-yaml.ini

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x wp-cli.phar && \
    mv wp-cli.phar /usr/local/bin/wp

RUN wget http://cs.sensiolabs.org/download/php-cs-fixer-v2.phar -O php-cs-fixer && \
    chmod a+x php-cs-fixer && \
    mv php-cs-fixer /usr/local/bin/php-cs-fixer

COPY devenv/wp-config.php /var/www/html/wp-config.php
COPY devenv/php.ini /usr/local/etc/php/conf.d/php.ini
COPY devenv/config/ssmtp.conf /etc/ssmtp/ssmtp.conf
