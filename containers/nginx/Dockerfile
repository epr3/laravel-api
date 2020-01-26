FROM nginx:alpine

COPY ./src /var/www/html

COPY ./containers/nginx/default.conf /etc/nginx/conf.d/default.conf

COPY ./containers/nginx/custom/*.conf /etc/nginx/custom/
