#!/usr/bin/python
#####################################################################
# Name             : capaMain.py
# Version          : 0.0.8
# Description      : Raspberry Pi camera capture controller
# Pre-requisites   : Python, PiCamera(Copyright 2013-2015 Dave Jones)
# Comments         : See capatimelapse.com for info 2017
# Environment      : Tested under Raspberry Pi Rasbian Jessie Summer 17
# Author           : Steve Osteen sosteen@gmail.com
######################################################################

import time
import sys
import os
import MySQLdb
import logging
import logging.handlers

basedir = os.path.abspath("../") + "/capatimelapse/"
log = logging.getLogger('PiCamLog')
log.setLevel(logging.DEBUG)  # prod: logging.ERROR
handler = logging.handlers.SysLogHandler(address='/dev/log')
formatter = logging.Formatter('%(name)-12s %(levelname)-8s %(message)s')
handler.setFormatter(formatter)
log.addHandler(handler)

# Write pid file to know we are running
pid = str(os.getpid())
pidfile = "/tmp/picam.pid"

if os.path.isfile(pidfile):
    log.debug('%s is already running', pidfile)

open(pidfile, 'w').write(pid)
log.debug('Camera control started')


def readConfig():
    # config.txt is a symlinked file written by the php web interface
    # This gets the camera settings
    try:
        configfile = open(basedir + "config.txt", 'r')
        content = configfile.read()
        content = content.rstrip()
        settings = content.split("\n")
        setting_list = []
        setting_dict = {}
        for setting in settings:
            s = setting.split('=')
            setting_list.append(s[1])
            setting_dict[s[0]] = s[1]
        configfile.close()
    except IOError as e:
        log.critical('error reading config.txt %s', e)
        sys.exit("Error reading config.txt")
    return setting_dict


def getsettings(camera, setting_dict):
    # set the UI settings to camera
    hflip = setting_dict["hflip"]
    vflip = setting_dict["vflip"]
    if (hflip == "true"):
        camera.hflip = hflip
    if (vflip == "true"):
        camera.vflip = vflip
    camera.resolution = setting_dict["resolution"]
    camera.awb_mode = setting_dict["whitebalance"]
    camera.exposure_mode = setting_dict["exposure"]
    camera.meter_mode = setting_dict["metering"]
    camera.image_effect = setting_dict["effects"]
    camera.drc_strength = setting_dict["drc"]
    camera.sharpness = int(setting_dict["sharpness"])
    camera.contrast = int(setting_dict["contrast"])
    camera.brightness = int(setting_dict["brightness"])
    camera.saturation = int(setting_dict["saturation"])
    camera.iso = int(setting_dict["iso"])
    camera.shutter_speed = int(setting_dict["ss"])


def dbinsert(imageurl, nice, setting_dict):
    try:
        db = MySQLdb.connect("localhost", "monitor", "23rdqw", "images")
    except Exception as e:
        log.critical('Error accessing database: %s', e)
        sys.exit('Error accessing database')

    try: 
        cursor = db.cursor()
        line = 'INSERT INTO imgdat (imgURL, imgNice, imgDate, imgRes, imgAwb, imgEx, imgMeter, imgEffect, '\
         'imgDrc, imgSharpness, imgContrast, imgBrightness, imgSaturation, imgIso, imgSS, imgJpg) VALUES ("'\
          + imageurl + '","' + nice + '", NOW(),"' + setting_dict["resolution"] + '","' + setting_dict["whitebalance"] \
          + '","' + setting_dict["exposure"] + '","' + setting_dict["metering"] + '","' + setting_dict["effects"] \
          + '","' + setting_dict["drc"] + '","' + setting_dict["sharpness"] + '","' + setting_dict["contrast"] \
          + '","' + setting_dict["brightness"] + '","' + setting_dict["saturation"] + '","' + setting_dict["iso"] + '","' + setting_dict["ss"] + '","' + setting_dict["jpgquality"] + '")'
        cursor.execute(line)
        db.commit()
    except Exception as e:
        db.rollback()
        log.critical('error in submission: %s', e)
    finally:
        db.close()


def snapcap(settings):
    try:
        from picamera import PiCamera
        camera = PiCamera()
        time.sleep(2)  # Warm up the sensor
        getsettings(camera, settings)
        timenow = time.strftime("%Y-%m-%d_%H%M%S")
        filename = basedir + "pics/" + timenow + ".jpg"
        nice = "pics/" + timenow + ".jpg"
        camera.capture(filename, format="jpeg", quality=int(settings["jpgquality"]), thumbnail=(320, 240, 80))
        log.debug('capture taken: %s', filename)
        dbinsert(filename, nice, settings)
    finally:
        log.debug('Camera control done')
        camera.close()


def fastinterval(interval, duration, settings):
    try:
        from picamera import PiCamera
        camera = PiCamera()
        time.sleep(1)
        getsettings(camera, settings)
        time.sleep(2)
        for i in range(duration):
            timenow = time.strftime("%Y-%m-%d_%H%M%S")
            filename = basedir + "pics/" + timenow + "%02d.jpg" % i
            nice = "pics/" + timenow + "%02d.jpg" % i
            camera.capture(filename, format="jpeg", quality=int(settings["jpgquality"]), thumbnail = (320, 240, 80))
            log.debug('capture taken: %s', filename)
            dbinsert(filename, nice, settings)
            time.sleep(interval)
    finally:
        log.debug('Camera control done')
        camera.close()


def longinterval(interval, duration, settings):
    for i in range(duration):
        from picamera import PiCamera
        camera = PiCamera()
        getsettings(camera, settings)
        timenow = time.strftime("%Y-%m-%d_%H%M%S")
        filename = basedir + "pics/" + timenow + "%02d.jpg" % i
        nice = "pics/" + timenow + "%02d.jpg" % i
        time.sleep(5)  # warm up sensor
        camera.capture(filename, format="jpeg", quality=int(settings["jpgquality"]),thumbnail = (320, 240, 80))
        camera.close()
        dbinsert(filename, nice, settings)
        log.debug('capture taken: %s', filename)
        time.sleep(interval - 5)  # subtrack the sensor sleep from the loop

    log.debug('Camera control done')


def main():
    # master control

    settings = readConfig()
    if "interval" in settings:
        interval = settings["interval"]
    else:
        interval = "0"
    if "duration" in settings:
        duration = settings["duration"]
    else:
        duration = "5 min"

    durationsec = {
        "1 min": 60,
        "5 min": 300,
        "10 min": 600,
        "30 min": 1800,
        "1 hour": 3600,
        "6 hours": 21600,
        "12 hours": 43200,
        "24 hours": 86400,
        "5 days": 432000
    }

    try:
        duration = durationsec[duration]
    except KeyError:
        duration = 60

    if (interval == "0"):
        snapcap(settings)
    elif (interval == "2 sec"):
        interval = 2
        if (duration > 21600):
            duration = 21600
        duration = duration / interval
        fastinterval(interval, duration, settings)
    elif (interval == "5 sec"):
        interval = 5
        if (duration > 21600):
            duration = 21600
        duration = duration / interval
        fastinterval(interval, duration, settings)
    elif (interval == "10 sec"):
        interval = 10
        if (duration > 21600):
            duration = 21600
        duration = duration / interval
        fastinterval(interval, duration, settings)
    elif (interval == "30 sec"):
        interval = 30
        if (duration > 43200):
            duration = 43200
        duration = duration / interval
        longinterval(interval, duration, settings)
    elif (interval == "1 min"):
        interval = 60
        if (duration > 43200):
            duration = 43200
        duration = duration / interval
        longinterval(interval, duration, settings)
    elif (interval == "5 min"):
        interval = 300
        if (duration < interval):
            duration = 300
        duration = duration / interval
        longinterval(interval, duration, settings)
    elif (interval == "10 min"):
        interval = 600
        if (duration < interval):
            duration = 600
        duration = duration / interval
        longinterval(interval, duration, settings)
    elif (interval == "15 min"):
        interval = 900
        if (duration < interval):
            duration = 900
        duration = duration / interval
        longinterval(interval, duration, settings)
    elif (interval == "30 min"):
        interval = 1800
        if (duration < interval):
            duration = 1800
        duration = duration / interval
        longinterval(interval, duration, settings)
    elif (interval == "1 hour"):
        interval = 3600
        if (duration < interval):
            duration = 3600
        duration = duration / interval
        longinterval(interval, duration, settings)
    elif (interval == "6 hour"):
        interval = 21600
        if (duration < interval):
            duration = 21600
        duration = duration / interval
        longinterval(interval, duration, settings)
    elif (interval == "12 hour"):
        interval = 43200
        if (duration < interval):
            duration = 43200
        duration = duration / interval
        longinterval(interval, duration, settings)
    elif (interval == "24 hour"):
        interval = 86400
        if (duration < interval):
            duration = 86400
        duration = duration / interval
        longinterval(interval, duration, settings)
    else:
        interval = 60
        duration = 300
        duration = duration / interval
        longinterval(interval, duration, settings)

    os.unlink(pidfile)


if __name__ == "__main__":
    main()
