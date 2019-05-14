FROM lsp/php-fpm:latest

ARG NODE_VERSION=12.2.0
ENV NODE_VERSION ${NODE_VERSION}

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
    && rm -rf /var/lib/apt/lists/*

RUN git config --global http.sslVerify false;

RUN pecl install xdebug-2.7.0 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=1\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart=1\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.profiler_enable=1\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_port=9001\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo "export PATH=${PATH}:/var/www/vendor/bin" >> ~/.bashrc

RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.1/install.sh | bash && \
    . /root/.nvm/nvm.sh && \
    nvm install ${NODE_VERSION} && \
    nvm use ${NODE_VERSION} && \
    nvm alias ${NODE_VERSION} && \
    npm install -g vue-cli && \
    echo "" >> ~/.bashrc && \
    echo 'export NVM_DIR="${HOME}/.nvm"' >> ~/.bashrc && \
    echo '[ -s "/root/.nvm/nvm.sh" ] && . "/root/.nvm/nvm.sh"  # This loads nvm' >> ~/.bashrc && \
    echo "export PATH=${PATH}:/bin/versions/node/v${NODE_VERSION}/bin" >> ~/.bashrc

ENV PATH $PATH:/bin/versions/node/v${NODE_VERSION}/bin

CMD ["php-fpm"]

EXPOSE 9000 9001

