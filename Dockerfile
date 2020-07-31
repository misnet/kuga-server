FROM registry.cn-shenzhen.aliyuncs.com/depoga/dphp:1.1
WORKDIR /opt
ARG APP_ENV=prod

COPY . .
RUN cd /opt/class && php /opt/class/composer.phar  config -g repo.packagist composer https://mirrors.aliyun.com/composer/
RUN if [ ${APP_ENV} = "dev" ]; then \
       yes|cp class/composer.beta.json class/composer.json; \
    fi
RUN cd /opt/class && php /opt/class/composer.phar  global require hirak/prestissimo
RUN cd /opt/class && yes|php /opt/class/composer.phar  -vvv install --no-dev --no-interaction --prefer-dist
RUN mkdir /opt/var && mkdir /opt/var/log && mkdir /opt/var/meta && mkdir /opt/var/cache && chmod 777 /opt/var -R
STOPSIGNAL SIGQUIT
EXPOSE 9000
CMD ["php-fpm"]