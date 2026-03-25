FROM blendbyte/docker-php-cli:7.4

LABEL maintainer="Raza Mehdi<srmk@outlook.com>"

ENV DEBIAN_FRONTEND=noninteractive

# Set apps home directory.
ENV APP_DIR /server/http

# Define current working directory.
WORKDIR ${APP_DIR}

RUN apt-get -y autoclean \
  && apt-get -y autoremove \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY supervisor.conf /etc/supervisor/conf.d/supervisord.conf  

CMD ["/usr/bin/supervisord"]