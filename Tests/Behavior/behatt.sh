#!/bin/sh

echo 'start behat for powermail and stop on the first failure with a tag'
../../.Build/vendor/behat/behat/bin/behat --stop-on-failure --tags=$1
