#!/usr/bin/python
# ipdisplay.py - A simple script to display current IP in GUI

from Tkinter import *
import tkMessageBox as mbox
import socket
import fcntl
import struct

def get_ip_address(ifname):
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    return socket.inet_ntoa(fcntl.ioctl( 
    	s.fileno(),
        0x8915,  # SIOCGIFADDR
        struct.pack('256s', ifname[:15])
    	)[20:24])

myip = get_ip_address('eth0')

root = Tk()
root.wm_title('IP Display')

#Change width to match display
label = Label(root, fg="red", bg='black', text=myip, width=800, font=('Helvetica', 64))
label.pack()
label.focus_set()

button = Button(root, width=100, text='OK', command=quit)
button.pack()

#Change geometry to match display
root.geometry('800x480')
root.mainloop()
