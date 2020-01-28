#!/bin/sh

echo 'start behat for powermail and stop on the first failure'
../../.Build/vendor/behat/behat/bin/behat --stop-on-failure
