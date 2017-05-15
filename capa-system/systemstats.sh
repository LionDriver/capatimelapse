#!/bin/bash
#
# A script to display Raspberry Pi on-board sensor data
#
# --- FREQ: Display current clock frequencies
FREQ () {
   echo -e "\n\t\t\t`tput smso`  C L O C K    F R E Q U E N C I E S  `tput rmso`\n"
   for src in arm core h264 isp v3d uart pwm emmc pixel vec hdmi dpi ;do
      echo -e "\t$src\t$(vcgencmd measure_clock $src) Hz"
   done | pr --indent=5 -r -t -2 -e3
   echo
}

POWER () {
   echo -e "\n\t\t\t`tput smso`  B A T T E R Y   V O L T A G E  `tput rmso`\n"
   BATTERYLEVEL=`lifepo4wered-cli get vbat`
   echo -e "\t$BATTERYLEVEL\t Volts"
   echo
}

# --- Main Procedure

# Diagnose the Bourne shell which is not supported
if [ `ps | tail -n 4 | sed -E '2,$d;s/.* (.*)/\1/'` = "sh" ]; then
   echo
   echo "*** Oops, the Bourne shell is not supported"
   echo
   exit 86
fi

# Make sure that `bc` is installed
flagbc=`which bc`
if [ -z $flagbc ]; then
   echo Installing bc # {small}
   sudo apt-get -y install bc
fi

# Do temperature calculations
TEMPC=$(/opt/vc/bin/vcgencmd measure_temp|awk -F "=" '{print $2}')      # Get Temp C
TEMPf=$(echo -e "$TEMPC" | awk -F "\'" '{print $1}' 2>/dev/null)        # Get numeric-only Temp C
OVRTMP=70                                                               # High-temp limit degrees C
ALRM=""
[[ `echo $TEMPC|cut -d. -f1` -gt ${OVRTMP:-70} ]] && ALRM="\n\t\t\t TOO HOT! \t TOO HOT! \t TOO HOT! "           # Check for over-temp: Max = 70C or 158F
TEMPB4OVER=$(echo "${OVRTMP:-70}-${TEMPf}"|bc -l)                               # Calculate the number of degrees before over-temp condition

# Display temperatures
echo -e "\n\t\t\t`tput smso`  S Y S T E M    T E M P E R A T U R E  `tput rmso`   `[[ -n $ALRM ]] || COLOR=green; setterm -foreground ${COLOR:-red}`${ALRM:-OK}"; setterm -foreground white
echo -e "\n\tThe BCM2835 SoC (CPU/GPU) temperature is: ${TEMPf}°C `tput smso;setterm -foreground red`$ALRM`setterm -foreground white;tput rmso`"
echo -e "\t(${OVRTMP:-70}°C HIGH-TEMP LIMIT will be reached in `tput smso`${TEMPB4OVER}°C`tput rmso` higher)\n"

# Display voltages
echo -e "\n\t\t\t`tput smso`  S Y S T E M    V O L T A G E S  `tput rmso`"
echo -e "\n\tThe Core voltage is:\t\t\c"
/opt/vc/bin/vcgencmd measure_volts core|awk -F "=" '{print $2}'
echo -e "\tThe sdram Core voltage is:\t\c"
/opt/vc/bin/vcgencmd measure_volts sdram_c|awk -F "=" '{print $2}'
echo -e "\tThe sdram I/O voltage is:\t\c"
/opt/vc/bin/vcgencmd measure_volts sdram_i|awk -F "=" '{print $2}'
echo -e "\tThe sdram PHY voltage is:\t\c"
/opt/vc/bin/vcgencmd measure_volts sdram_p|awk -F "=" '{print $2 "\n"}'

# Display frequencies
FREQ
POWER
exit
