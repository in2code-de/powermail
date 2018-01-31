# How to start unit tests for powermail?

## On command line

* First of all go to powermail folder in console
* Then do a `composer update`
* After that you can call `/usr/bin/php .Build/vendor/phpunit/phpunit/phpunit --configuration phpunit.xml.dist`

## In PhpStorm

* First of all got to powermail folder in console
* Then do a `composer update`
* After this, you should open the PhpStorm Settings and go to `Languages & Frameworks > PHP > Test frameworks`
* Choose `use composer autoloader`
* Add folder on `path to script` to `EXT:powermail/.Build/vendor/autoload.php`
* Finish: Right-Click on file `phpunit.xml.dist` with `Run 'phpunit.xml.dist'`

## With code coverage

* You need to have xdebug installed and configured on your test environment
* On command line you can run it like `/usr/bin/php -dxdebug.coverage_enable=1 .Build/vendor/phpunit/phpunit/phpunit --configuration phpunit.xml.dist --coverage-text`
* In PhpStorm you could simply right-click on file `phpunit.xml.dist` with `Run 'phpunit.xml.dist with Coverage'`

## Screenshot

Example of code coverage in PhpStorm:

<img src="https://s.nimbus.everhelper.me/attachment/1353365/kcg8w0a40ppr7htreayb/262407-oC7bipiqa5MZw9F4/screen.png" />
