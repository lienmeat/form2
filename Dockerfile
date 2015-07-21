# MY Dev Drupal
#
# VERSION       1

# 
FROM lune/dev-drupal

MAINTAINER Eric Lien liener.meat@gmail.com

#docker is dumb and doesn't know what term to use sometimes...
#so FORCE IT cause we will be using this shell a LOT
RUN echo "export TERM=xterm" > /root/.bashrc

RUN apt-get update

#any extra packages we need for lb and maybe others
RUN apt-get install -y php-http-request2 php5-imagick poppler-utils python-pdfminer nano php5-mcrypt

RUN php5enmod mcrypt

#because our code is hardcoded to look for pdf2txt.py...idk why...really annoying.
RUN ln -s /usr/bin/pdf2txt /usr/bin/pdf2txt.py

#because drupal7 templates are idiodic
RUN sed -i.bak 's/short_open_tag = Off/short_open_tag = On/g' /etc/php5/apache2/php.ini

#email catching quick and dirty:
RUN mkdir /var/log/mail

#add php script to replace sendmail and chmod it
ADD sendmail /usr/local/bin/sendmail

#replace sendmail_path with our script in php.ini
RUN sed -i.bak 's/^;sendmail_path =.*\|^sendmail_path =.*/sendmail_path = \/usr\/local\/bin\/sendmail/g' /etc/php5/apache2/php.ini

#re-own it
RUN chmod 755 /usr/local/bin/sendmail && chmod 777 /var/log/mail

#xdebug config
RUN echo xdebug.remote_enable=1 >> /etc/php5/apache2/conf.d/20-xdebug.ini;\
  echo xdebug.remote_autostart=0 >> /etc/php5/apache2/conf.d/20-xdebug.ini;\
  echo xdebug.remote_connect_back=1 >> /etc/php5/apache2/conf.d/20-xdebug.ini;\
  echo xdebug.remote_port=9000 >> /etc/php5/apache2/conf.d/20-xdebug.ini;\
  echo xdebug.remote_log=/var/www/php5-xdebug.log >> /etc/php5/apache2/conf.d/20-xdebug.ini;

EXPOSE 80 9000

#/project volume created by lune/dev-drupal
RUN mkdir /project/www