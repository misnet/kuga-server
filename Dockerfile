FROM registry.cn-shenzhen.aliyuncs.com/depoga/dphp:1.1
WORKDIR /opt
ARG APP_ENV=prod

RUN if [ ! -d /opt/var ]; then \
mkdir /opt/var;\
fi \
&& if [ ! -d /opt/var/log ]; then \
mkdir /opt/var/log; \
fi \
&& if [ ! -d /opt/var/cache ]; then \
mkdir /opt/var/cache;\
fi \
&& if [ ! -d /opt/var/meta ]; then \
mkdir /opt/var/meta;\
fi \
&& if [ ! -d /opt/class ]; then \
mkdir /opt/class;\
fi
RUN chmod 777 /opt/var -R
ADD class/composer.json class/composer.json
ADD class/composer.phar class/composer.phar
RUN cd /opt/class && php /opt/class/composer.phar  config -g repo.packagist composer https://mirrors.aliyun.com/composer/
RUN if [ ${APP_ENV} = "dev" ]; then \
       yes|cp class/composer.beta.json class/composer.json; \
    fi
RUN cd /opt/class && php /opt/class/composer.phar  global require hirak/prestissimo
RUN cd /opt/class && php /opt/class/composer.phar  -vvv install

ADD . .
STOPSIGNAL SIGQUIT
EXPOSE 9000
CMD ["php-fpm"]