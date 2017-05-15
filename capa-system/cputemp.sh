#!/bin/bash
#
# update .rrd database with CPU temperature

cd /home/pi/workspace

[ -f cputemp.rrd ] || {
rrdtool create cputemp.rrd -s 300 \
DS:cputemp:GAUGE:600:0:100 \
RRA:AVERAGE:0.5:1:3200 \
RRA:AVERAGE:0.5:6:3200 \
RRA:AVERAGE:0.5:36:3200 \
RRA:AVERAGE:0.5:144:3200 \
RRA:AVERAGE:0.5:1008:3200 \
RRA:AVERAGE:0.5:4320:3200
}

TEMPERATURE=`cat /sys/class/thermal/thermal_zone0/temp`
TEMPERATURE=`echo -n ${TEMPERATURE:0:2}; echo -n .; echo -n ${TEMPERATURE:2}`

rrdtool update cputemp.rrd N:$TEMPERATURE
rrdtool graph cputemp.png DEF:temp=cputemp.rrd:cputemp:AVERAGE LINE2:temp#00FF00:"CPU Temperature" 

cp /home/pi/workspace/cputemp.png /home/pi/workspace/cputemp/cputemp.png

