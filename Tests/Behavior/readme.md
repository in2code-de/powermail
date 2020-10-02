# How to start behavior tests for powermail?

## Preperations

* First of all, do a `composer update` in powermail root folder
* You have to install a local TYPO3-instance in the next step and it should be available under `powermail.localhost.de`
* Move (or symlink) the powermail-Folder into typo3conf/ext/ and activate the extension.
* Then import the database dump from http://powermail.in2code.ws/fileadmin/behat/powermail.sql.gz

## Command line

### Start Selenium

* Download geckodriver from https://github.com/mozilla/geckodriver/releases, make it executable and move it to `/usr/local/bin/`
* Open a console and go to `EXT:powermail/Tests/Behavior/`
* Start a selenium server with `sh selenium.sh`
* As an alternative, you could specify which browser version should be used (if you installed a second firefox - probably older then quantum) - in my case:
`java -jar ../../.Build/vendor/se/selenium-server-standalone/bin/selenium-server-standalone.jar -Dwebdriver.firefox.bin="/var/www/Webtools/firefox/42/firefox"`

### Start Behat

* Open another console and go to `EXT:powermail/Tests/Behavior/`
* Start behat with `sh behat.sh` or with `sh behats.sh` (for stopping on first failure)
* As an alternative, you could specify a single test by its tag like `sh behatt.sh Pi1Default` (to start all tests @Pi1Default)

## Screenshot

Screen from the huge testparcours that has to be passed before every release:

<img src="https://s.nimbus.everhelper.me/attachment/1427661/h7antft74egdzrck2xq8/262407-yUdChFKtnZ475SlH/screen.png" width="200" />

## Behaviour tests with docker

### Preparation

* update docker-compose.yml with the following lines

```
services:
  behat:
    hostname: behat
    image: docksal/behat
    volumes:
      - ./:/app/:cached
    # Run a built-in web server for access to HTML reports
    entrypoint: "php -S 0.0.0.0:8000"

  browser:
    hostname: browser
    # Pick/uncomment one
    image: selenium/standalone-chrome
```

* run `docker-compose up -d --build` to fetch the images and start the container
* run `docker exec $(docker-compose ps -q behat) behat --colors --format=pretty --out=std --out=html_report.html --verbose --config "/app/packages/powermail/Tests/Behavior/behat-docker.yml"`

The path to the config file is the path within the docker container of behat. If you want to change some values in the
config file, you can copy it outside the repo and then use this file. Please pay attention not to change and commit the
defaults.
