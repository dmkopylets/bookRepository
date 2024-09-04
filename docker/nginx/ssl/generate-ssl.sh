#!/bin/bash

#openssl req -x509 -out default.crt -keyout default.key \
#-newkey rsa:2048 -nodes -sha256 \
#-days 1024 \
#-subj '/CN=default' -extensions EXT -config <( \
#printf "[dn]\nCN=default\n[req]\ndistinguished_name = dn\n[EXT]\nsubjectAltName=DNS:default\nkeyUsage=digitalSignature\nextendedKeyUsage=serverAuth")

 openssl genrsa -out default.key 2048
 openssl req -new -key default.key -out default.csr -subj '/CN=default' -extensions EXT -config <( \
 printf "[dn]\nCN=default\n[req]\ndistinguished_name = dn\n[EXT]\nsubjectAltName=DNS:default\nkeyUsage=digitalSignature\nextendedKeyUsage=serverAuth")
 openssl x509 -req  -days 1024 -in default.csr -signkey default.key -out default.crt
 chmod 644 default.key
