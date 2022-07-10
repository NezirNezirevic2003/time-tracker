FROM pliguori/lamp
LABEL Pietro Liguori <pliguori@gmail.com>

# Install prerequisites
RUN apt-get update
RUN apt-get install -y wget unzip

# Download and unzip Concrete5 
RUN wget -O /tmp/kimai.zip https://github.com/kevinpapst/kimai2/archive/refs/tags/1.20.4.zip
RUN rm -fr /var/www/html/*
RUN unzip /tmp/kimai.zip  -d /var/www/html/

RUN chmod 755 /*.sh

# Create database
#RUN service mysql restart; mysqladmin -uadmin -ppass create kimai

# Update file ownership
RUN chown -R www-data.www-data /app
RUN chmod -R g+r /app
RUN chmod -R g+rw var/

# Allow ports
EXPOSE 80

CMD ["/run.sh"]