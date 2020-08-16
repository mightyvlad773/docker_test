FROM php:7.3-fpm
  
WORKDIR /app

RUN apt-get update && apt-get install -y --fix-missing \
	cron \
    apt-utils \
    rsyslog \
    gnupg 

RUN echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list
RUN echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list
RUN curl -sS --insecure https://www.dotdeb.org/dotdeb.gpg | apt-key add -

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev
RUN docker-php-ext-install zip mysqli pdo pdo_mysql
  
RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

ADD crontab /etc/cron.d/
RUN chmod 777 /etc/cron.d/
RUN touch /var/log/test-cron.log
  
WORKDIR /app

EXPOSE 9005

CMD cron && tail -f /var/log/crm-cron.log