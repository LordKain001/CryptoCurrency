#!/bin/bash
build-essential cmake  pkg-config          


sudo apt install git ssh  libreadline-dev libboost-all-dev autoconf libpcre3-dev rapidjson-dev check automake build-essential cmake pkg-config libunbound-dev libminiupnpc-dev libunwind8-dev liblzma-dev libldns-dev libexpat1-dev doxygen graphviz

git clone --recursive https://github.com/graft-project/graft-ng.git
cd graft-ng
git checkout alpha3
git submodule update --init --recursive
cd ..
mkdir -p supernode
cd supernode
cmake -DENABLE_SYSLOG=ON $HOME/graft-ng
make











