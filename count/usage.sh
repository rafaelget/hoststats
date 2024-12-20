#!/bin/bash

> usage_temp.json
docker stats --format '{{json .}}' --no-stream >> usage_temp.json
cp usage_temp.json usage.json

#echo "CPU: $cpu% - Mem: $memory% - Disk: $disk%"