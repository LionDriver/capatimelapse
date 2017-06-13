#!/bin/bash
# capatimelapse INSTALL.sh
# For use on raspbian with raspberry pi camera
# Version 0.0.9 SlumberMachine 6/12/2017
# TODO system items & database needs to be added
# TODO Pipoint options and setup
echo "This is the capatimelapse installer"
echo  "----------------------------------"
if [ "$EUID" -ne 0 ]
  then echo "Please run as root or using sudo"
  exit
fi

SCRIPT=$(readlink -f $0)
SCRIPTPATH=`dirname $SCRIPT`

echo "Running updates and installing Dependencies"
apt-get update && sudo apt-get -y dist-upgrade;
apt-get -y install lighttpd php5-common php5-cgi php5 php5-mysql python-picamera python3-picamera python-mysqldb;
debconf-set-selections <<< 'mysql-server mysql-server/root_password password 23rdqw'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password 23rdqw'
apt-get -y install mysql-server

echo "Moving things into place"
lighttpd-enable-mod fastcgi-php;
rm /var/www/html/index.lighttpd.html
cp -R ${SCRIPTPATH}/php ${SCRIPTPATH}/js ${SCRIPTPATH}/images ${SCRIPTPATH}/fonts ${SCRIPTPATH}/css /var/www/html/
cp ${SCRIPTPATH}/gallery.php ${SCRIPTPATH}/index.html ${SCRIPTPATH}/system.php  /var/www/html/
ln -s ${SCRIPTPATH}/pics /var/www/html/pics
chown -R www-data:www-data ${SCRIPTPATH}/pics
cp ${SCRIPTPATH}/capa-system/config.txt /var/www/html/
ln -s /var/www/html/config.txt ${SCRIPTPATH}/config.txt

echo '<?php $servername = "localhost"; $username = "monitor"; $password = "23rdqw"; $dbname = "images"; ?>' > db.php
mv db.php /var/www/html/
/etc/init.d/lighttpd force-reload

echo "Granting permissions"
mysql -u root -p23rdqw -e "CREATE DATABASE images";
mysql -u root -p23rdqw -e "CREATE USER 'monitor'@'localhost' IDENTIFIED BY '23rdqw'";
mysql -u root -p23rdqw -e "GRANT ALL PRIVILEGES ON images.* TO 'monitor'@'localhost'";
mysql -u root -p23rdqw -e "FLUSH PRIVILEGES";
mysql -u root -p23rdqw images < ${SCRIPTPATH}/capa-system/images.sql

echo 'www-data ALL=(ALL) NOPASSWD:/sbin/shutdown -h now' | sudo EDITOR='tee -a' visudo
echo 'www-data ALL=(ALL) NOPASSWD:/sbin/reboot' | sudo EDITOR='tee -a' visudo
echo 'www-data ALL=(ALL) NOPASSWD:/usr/bin/pkill' | sudo EDITOR='tee -a' visudo

usermod -a -G video www-data
usermod -a -G pi www-data
usermod -a -G gpio www-data
sed -i "s/^www-data:x.*/www-data:x:33:33:www-data:\/var\/www:\/bin\/bash/g" /etc/passwd
chown -R www-data:www-data /var/www/html

echo "Checking camera firmware"
if grep "start_x=1" /boot/config.txt
then
    echo "Nice! Camera is already set"
else
    sed -i "s/start_x=0/start_x=1/g" /boot/config.txt
    echo "Set Camera firmware On"
fi
echo "All done, please REBOOT, Have a nice day!"