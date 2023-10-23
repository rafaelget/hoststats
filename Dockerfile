FROM php:8.2-apache

ARG AUTH=${AUTH}
ENV AUTH=${AUTH}
ARG AUTH_LOGIN=${AUTH_LOGIN}
ENV AUTH_LOGIN=${AUTH_LOGIN}
ARG AUTH_PASSWORD=${AUTH_PASSWORD}
ENV AUTH_PASSWORD=${AUTH_PASSWORD}

COPY ./code/index.php /var/www/html/index.php
COPY ./count/containers.csv /var/www/html/count/containers.csv
