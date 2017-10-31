#!/bin/bash

#Creation de l'arborescence des fichier

mkdir $1
cd $1
mkdir "Admin"
for i in `seq 1 4`;
do
        mkdir "S$i"
        mkdir -p "S$i"/"csv"
        mkdir -p "S$i"/"excel"
        mkdir -p "S$i"/"json"
done