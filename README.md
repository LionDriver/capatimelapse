# Capatimelapse
The Capa Timelapse system for Raspberry pi.  Named after the famous WW2 photographer. 
![alt text](https://github.com/slumbermachine/capatimelapse/blob/master/images/sampleMain.png)


## About
Easy to use web-interface for easy timelapse setup and camera control, including photo gallery and all pi camera settings, and effects.  Great for finding the best settings for your shot.  Intended to be used remote as a captive portal access point which can be controlled by your phone, computer, or tablet.

## Installation
Start with a clean raspbian installation (lite or regular, we suggest lite as capatimelapse utilizes a web-interface).  From the home (/home/pi) run the following to install:
1. sudo apt-get install git
2. git clone https://github.com/slumbermachine/capatimelapse.git
3. cd capatimelapse
4. sudo ./INSTALL.sh
5. IMPORTANT - Reboot after install has completed.

## How to use
Once your pi has rebooted.  Connect to it by opening your webbrowser and pointing to the pi IP address or hostname.local (using the hostname of your pi). If you are unsure of your pi's IP address you can look for it using "sudo ip addr show"(eth0 if wired, wlan0 if on wifi) or by looking at your router's connected devices screen.

## Known Issues
1. Timelapse UI isn't controlled.  The timelapse will run, but nothing in the UI updates the progress.
2. Shutter speed setting doesn't seem to work
3. Need progress bar on creating tar/zip file
