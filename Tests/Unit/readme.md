# How to start unit tests for powermail?

## On command line

* First of all got to powermail folder in console
* Then do a `composer update`
* After that you can call `/usr/bin/php .Build/vendor/phpunit/phpunit/phpunit --configuration /home/einpraegsam/PhpstormProjects/powermail/phpunit.xml.dist`

## In PhpStorm

* First of all got to powermail folder in console
* Then do a `composer update`
* After this, you should open the PhpStorm Settings and go to `Languages & Frameworks > PHP > Test frameworks`
* Choose `use composer autoloader`
* Add folder on `path to script` to `EXT:powermail/.Build/vendor/autoload.php`
* Finish: Right-Click on file `phpunit.xml.dist` with `Run 'phpunit.xml.dist'`
