#!/bin/bash
echo "---------install-packages----------"
sudo apt install -y libmicrohttpd-dev libssl-dev cmake build-essential libhwloc-dev lm-sensors git ssh php php7.0-curl clinfo
echo "---------install-dist-upgrade----------"
sudo apt dist-upgrade -y
echo "---------install-update----------"
sudo apt update -y
echo "---------install-upgrade----------"
sudo apt upgrade -y
echo "---------reboot----------"
#sudo reboot
