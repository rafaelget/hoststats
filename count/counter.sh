#!/bin/bash

#calcular o uso de CPU
cpu=$[100-$(vmstat 1 2|tail -1|awk '{print $15}')]

#calcular o uso de memoria
# grab the second line of the ouput produced by the command: free -g (displays output in Gb)
secondLine=$(free -g | sed -n '2p')

#split the string in secondLine into an array
read -ra ADDR <<< "$secondLine"

#get the total RAM from array
totalRam="${ADDR[1]}"

#get the used RAM from array
usedRam="${ADDR[2]}"

# calculate and display the percentage
memory="$(($usedRam*100/$totalRam))"

#calcular o uso de disco
disk=`df --output=pcent / | grep -o '[0-9]*' |awk '{print $1}'`

#contar numero de containers rodando
#containers=`docker ps -q $1 | wc -l`

> containers_temp.csv

echo "$cpu,$memory,$disk" >> containers_temp.csv

docker ps --format "{{.Names}}##{{.Image}}##{{.RunningFor}}" >> containers_temp.csv

cp containers_temp.csv containers.csv

echo "CPU: $cpu% - Mem: $memory% - Disk: $disk%"

> usage_temp.json
docker stats --format '{{json .}}' --no-stream >> usage_temp.json
cp usage_temp.json usage.json