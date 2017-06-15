#!/bin/bash
# capatimelapse INSTALL.sh
#
# For use on raspbian with raspberry pi camera
# Version 0.0.9 - 6/12/2017

# MIT License
# Copyright (c) 2017 SlumberMachine
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.

clear
echo "This is the CapaTimelapse Installer"
echo  "----------------------------------"
if [ "$EUID" -ne 0 ]; then 
  echo "Please run using sudo"
  exit
fi

SCRIPT=$(readlink -f $0)
SCRIPTPATH=`dirname $SCRIPT`

if [ "$SCRIPTPATH" != "/home/pi/capatimelapse" ]; then
  echo "Important: Please clone to /home/pi/ and run this installer from it's cloned location";
  exit
fi

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

cp ${SCRIPTPATH}/capa-system/capaSystem.service /lib/systemd/system/
chmod 644 /lib/systemd/system/capaSystem.service

echo "Granting permissions"
mysql -u root -p23rdqw -e "CREATE DATABASE images";
mysql -u root -p23rdqw -e "CREATE DATABASE system";
mysql -u root -p23rdqw -e "CREATE USER 'monitor'@'localhost' IDENTIFIED BY '23rdqw'";
mysql -u root -p23rdqw -e "GRANT ALL PRIVILEGES ON images.* TO 'monitor'@'localhost'";
mysql -u root -p23rdqw -e "GRANT ALL PRIVILEGES ON system.* TO 'monitor'@'localhost'";
mysql -u root -p23rdqw -e "FLUSH PRIVILEGES";
mysql -u root -p23rdqw images < ${SCRIPTPATH}/capa-system/images.sql
mysql -u root -p23rdqw system < ${SCRIPTPATH}/capa-system/cputemps.sql

echo 'www-data ALL=(ALL) NOPASSWD:/sbin/shutdown -h now' | sudo EDITOR='tee -a' visudo
echo 'www-data ALL=(ALL) NOPASSWD:/sbin/reboot' | sudo EDITOR='tee -a' visudo
echo 'www-data ALL=(ALL) NOPASSWD:/usr/bin/pkill' | sudo EDITOR='tee -a' visudo

usermod -a -G video www-data
usermod -a -G pi www-data
usermod -a -G gpio www-data
sed -i "s/^www-data:x.*/www-data:x:33:33:www-data:\/var\/www:\/bin\/bash/g" /etc/passwd
chown -R www-data:www-data /var/www/html

systemctl daemon-reload
systemctl enable capaSystem.service
systemctl start capaSystem.service

echo "Checking camera firmware"
if grep "start_x=1" /boot/config.txt
then
    echo "Nice! Camera is already set"
else
    sed -i "s/start_x=0/start_x=1/g" /boot/config.txt
    echo "Set Camera firmware On"
fi
echo "All done, please REBOOT, Have a nice day!"