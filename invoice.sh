#!/bin/bash

# Default values
directory=""
logName=""
pidName=""

# Help message
help_message() {
  echo "Usage: $0 [OPTIONS]"
  echo ""
  echo "OPTIONS:"
  echo "  -d, --directory  Set the directory where the log file is located (default: /shared/httpd/bms)"
  echo "  -f, --file       Set the name of the log file (default: logs.txt)"
  echo "  -p, --pid        Set the name of the pid file (default: pid.txt)"
  echo "  -h, --help       Display this help message"
  exit 1
}

# Parse command line arguments
while [ $# -gt 0 ]
do
  key="$1"

  case $key in
    -d|--directory)
      directory="$2"
      shift
      shift
      ;;
    -f|--file)
      logName="$2"
      shift
      shift
      ;;
    -p|--pid)
      pidName="$2"
      shift
      shift
      ;;
    -h|--help)
      help_message
      ;;
    *)
      echo "Invalid option: $1"
      echo "See \"$0 --help\" for usage information"
      exit 1
      ;;
  esac
done

if [ ! $logName ]; then
  logName="logs.txt"
fi

if [ ! $pidName ]; then
  pidName="pid.txt"
fi

if [ ! $directory ]; then
  directory="/shared/httpd/bms"
fi

directory=$(echo "$directory" | sed 's#/$##')

logFile="$directory/$logName"
pidFile="$directory/$pidName"

if [ ! -f $pidFile ]; then
  touch $pidFile
  echo "$pidFile created"
else
  echo "$pidFile already exists"
fi

if [ ! -f $logFile ]; then
  touch $logFile
  echo "$logFile created"
else
  echo "$logFile already exists"
fi

# Function to kill the last process
kill_last_process() {
  last_pid=$(cat $pidFile)
  if [ ! -z "$last_pid" ]; then
      kill -9 $last_pid
      echo "Killing previous process $last_pid"
  fi
}

# Kill the last process
kill_last_process

# Get the last client ID from the log file
get_client_id() {
  # echo "$1" | awk '{ if ($4 == "Invoice") { print $6 } else if ($1 == "Client:") { print $2 } }'
  if echo "$1" | grep -q "Invoice Saved"; then
    echo "$1" | awk '{ print $NF }'
  elif echo "$1" | grep -q "Client:"; then
    echo "$1" | awk '{ print $2 }'
  else
    echo ""
  fi
}

if [ -f $logFile ]; then
  last_client_id=$(tac $logFile | while read -r line; do
      client_id=$(get_client_id "$line")
      if [ ! -z "$client_id" ]; then
        echo "$client_id"
        break
      fi
  done)
fi

echo "Starting from client: $last_client_id"

if [ ! -z "$last_client_id" ]; then
  command="php $directory/artisan primavera:invoices:updateorcreate orders 2021 $last_client_id"
else
  command="php $directory/artisan primavera:invoices:updateorcreate orders 2021"
fi

echo $command

# Run the command in the background and save the PID
# nohup $command >> $logFile 2>&1 &
nohup $command 2>&1 | head -n 5 > $logFile.tmp && mv $logFile.tmp $logFile &
echo $! > $directory/pid.txt
