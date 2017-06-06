#!/bin/bash
#
# update text file with CPU temperature

TEMPERATURE=`cat /sys/class/thermal/thermal_zone0/temp`
TEMPERATURE=`echo -n ${TEMPERATURE:0:2}; echo -n .; echo -n ${TEMPERATURE:2}`
echo $TEMPERATURE > /home/pi/timelapse/cputemp/cputemp.txt 2>&1
