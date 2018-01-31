#!/bin/sh
cd ..
VERSION_TAG=0.3.0
tar cvfz be_ixf_drupal-8.x-$VERSION_TAG.tar.gz --exclude='.git' --exclude='vendor/*' --exclude='package.sh' be_ixf_drupal
cd -

