#!/bin/bash
sudo tar -Jxvf amdgpu-pro-17.40.2712-510357.tar.xz 
sudo chmod 777 -R amdgpu-pro-17.40.2712-510357
cd amdgpu-pro-17.40.2712-510357
./amdgpu-pro-install -y --compute

 clinfo -l #check gpus

sudo cp xmr-stak.service /etc/systemd/system
sudo cp xmr-stak.service /etc/systemd/system/multi-user.target.wants/

sudo cp -R xmr-stak /home/miner/
sudo cp -R MinerAlive /home/miner/


cd 
sudo chmod 777 -R xmr-stak
cd xmr-stak/
nano config.txt

#configure gpus


cd 
sudo chmod 777 -R MinerAlive
cd MinerAlive/
nano MinerAlive.php #Minername anpassen
crontab -e
 ** * * * * php /home/miner/MinerAlive/MinerAlive.php

 
 
 
 echo "vm.nr_hugepages=128" >> /etc/sysctl.conf
sysctl -p


echo "soft memlock 262144" >> /etc/security/limits.conf
echo "hard memlock 262144" >> /etc/security/limits.conf

