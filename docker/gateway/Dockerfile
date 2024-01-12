FROM ubuntu:18.04
# install nginx
RUN apt-get update -y
RUN apt-get install -y software-properties-common
RUN add-apt-repository -y ppa:nginx/stable
RUN apt-get update -y
RUN apt-get install -y nginx
# deamon mode off
RUN chown -R www-data:www-data /var/lib/nginx
EXPOSE 80
ENV TZ=Europe/Moscow
ADD config/nginx.conf /etc/nginx/nginx.conf
WORKDIR /etc/nginx
CMD ["nginx"]
