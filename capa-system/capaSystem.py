#!/usr/bin/python
#####################################################################
# Name             : capaSystem.py
# Description      : Read system data and update db for web display
# Environment      : Tested under Raspberry Pi Rasbian Jessie Summer 17
# Author           : Steve Osteen sosteen@gmail.com
######################################################################

import MySQLdb
import sys
import time
from subprocess import Popen, PIPE
import logging
import logging.handlers

log = logging.getLogger('CapaTimeLapseLog')
log.setLevel(logging.DEBUG)  # prod: logging.ERROR
handler = logging.handlers.SysLogHandler(address='/dev/log')
formatter = logging.Formatter('%(name)-12s %(levelname)-8s %(message)s')
handler.setFormatter(formatter)
log.addHandler(handler)

def get_temp():
    t1 = Popen(["cat","/sys/class/thermal/thermal_zone0/temp"], stdout=PIPE)
    output = t1.communicate()[0]
    clean = output.rstrip()
    clean = (float(clean) / 1000)
    return clean

def get_batt():
    try:
        t1 = Popen(['/usr/local/bin/lifepo4wered-cli', 'get', 'vbat'], stdout=PIPE)
        output = t1.communicate()[0]
        clean = output.rstrip()
        return clean
    except Exception as e:
        return ""

def insert_db(temp, battery):
    try:
        db = MySQLdb.connect("localhost", "monitor", "23rdqw", "cputemps")
    except Exception as e:
        log.critical('Error accessing database: %s', e)
        sys.exit('Error accessing database')
    try:
        cursor=db.cursor()
        line = "INSERT INTO tempdat values(0,CURRENT_DATE(),CURRENT_TIME(), %s, %s)" %(temp, battery)
        cursor.execute(line)
        db.commit()
    except Exception as e:
        db.rollback()
        log.critical('Error in database submission: %s', e)
    db.close()

def main():
    while True:
        battery = get_batt()
        temp = get_temp()
        insert_db(temp, battery)
        time.sleep(60)

if __name__ == '__main__':
    main()
