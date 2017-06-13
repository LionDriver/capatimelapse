#!/bin/bash
# capatimelapse installer
# For use on raspbian with raspberry pi camera
# version 0.0.8 6/12/2017
# TODO system items & database needs to be added
# TODO Pipoint options and setup
echo "This is the capatimelapse installer"
echo  "----------------------------------"
if [ "$EUID" -ne 0 ]
  then echo "Please run as root or using sudo"
  exit
fi

apt-get update && sudo apt-get -y dist-upgrade;
apt-get -y install lighttpd php5-common php5-cgi php5 php5-mysql python-picamera python3-picamera mysql-server python-mysqldb;
lighttpd-enable-mod fastcgi-php;
rm /var/www/html/index.lighttpd.html
cp -R /home/pi/capatimelapse/php /home/pi/capatimelapse/js /home/pi/capatimelapse/images /home/pi/capatimelapse/fonts /home/pi/capatimelapse/css /var/www/html/
cp /home/pi/capatimelapse/gallery.php /home/pi/capatimelapse/index.html /home/pi/capatimelapse/system.php  /var/www/html/
ln -s /home/pi/capatimelapse/pics /var/www/html/pics
chown -R www-data:www-data /home/pi/capatimelapse/pics
cp /home/pi/capatimelapse/capa-system/config.txt /var/www/html/
ln -s /var/www/html/config.txt /home/pi/capatimelapse/config.txt

echo '<?php $servername = "localhost"; $username = "monitor"; $password = "23rdqw"; $dbname = "images"; ?>' > db.php
mv db.php /var/www/html/
/etc/init.d/lighttpd force-reload

mysql -u root -p23rdqw -e "CREATE DATABASE images";
mysql -u root -p23rdqw -e "CREATE USER 'monitor'@'localhost' IDENTIFIED BY '23rdqw'";
mysql -u root -p23rdqw -e "GRANT ALL PRIVILEGES ON images.* TO 'monitor'@'localhost'";
mysql -u root -p23rdqw -e "FLUSH PRIVILEGES";
mysql -u root -p23rdqw images < /home/pi/capatimelapse/capa-system/images.sql

echo 'www-data ALL=(ALL) NOPASSWD:/sbin/shutdown -h now' | sudo EDITOR='tee -a' visudo
echo 'www-data ALL=(ALL) NOPASSWD:/sbin/reboot' | sudo EDITOR='tee -a' visudo
usermod -a -G video www-data
usermod -a -G pi www-data
usermod -a -G gpio www-data
sed -i "s/^www-data:x.*/www-data:x:33:33:www-data:\/var\/www:\/bin\/bash/g" /etc/passwd

if grep "start_x=1" /boot/config.txt
then
    exit
else
    sed -i "s/start_x=0/start_x=1/g" /boot/config.txt
fi
CUR_GPU_MEM=$(get_config_var gpu_mem /boot/config.txt)
if [ -z "$CUR_GPU_MEM" ] || [ "$CUR_GPU_MEM" -lt 128 ]; then
    set_config_var gpu_mem 128 /boot/config.txt
fi

