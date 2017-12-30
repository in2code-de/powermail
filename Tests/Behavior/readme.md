# How to start behavior tests for powermail?

## Preperations

* First of all, do a `composer update` in powermail root folder
* You have to install a local TYPO3-instance (8.7) next and it should be available under `powermail.localhost.de`
* A dump is available under http://powermail.in2code.ws/fileadmin/behat/powermail.sql.gz

## Command line

### Start Selenium

* Open a console and go to `EXT:powermail/Tests/Behavior/`
* Start a selenium server with `sh selenium.sh`
* As an alternative, you could specify which browser version should be used (if you installed a second firefox - probably older then quantum) - in my case: 
`java -jar ../../.Build/vendor/se/selenium-server-standalone/bin/selenium-server-standalone.jar -Dwebdriver.firefox.bin="/var/www/Webtools/firefox/42/firefox"`

### Start Behat

* Open another console and go to `EXT:powermail/Tests/Behavior/`
* Start behat with `sh behat.sh` or with `sh behats.sh` (for stopping on first failure)
