#!/bin/sh
startdir=$PWD
workdir=$(dirname $1)
source=$(basename $1)
target=$2

echo Change working directory to $workdir
cd $workdir
#echo Packing "$source" as backup.tar.gz
echo Packing files...
tar -c $source | gzip -c > backup.tar.gz
echo Return to origin directory $startdir
cd $startdir
mv $workdir/backup.tar.gz  $target
