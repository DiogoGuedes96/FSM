#!/bin/bash

# Search for the process ID (PID) of a process containing "asterisk:ku" in the name
process_id=$(pgrep -f "asterisk:listener")

if [ -z "$process_id" ]; then
    echo "No process with 'asterisk:listener' in found."
    echo "Initiating new asterisk Connection!"
    nohup php artisan asterisk:listener > asterisk.log &
else
  # Kill the process using the obtained PID
    echo "Killing process with PID: $process_id"
    kill "$process_id"

    echo "Initiating new asterisk Connection!"
    nohup php artisan asterisk:listener > asterisk.log &
fi