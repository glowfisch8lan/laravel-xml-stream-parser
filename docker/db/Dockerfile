FROM mysql:8.0
ADD conf.d/mysqld.cnf /etc/mysql/conf.d/
ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
EXPOSE 3306
CMD ["mysqld"]
