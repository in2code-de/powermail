<?php
use In2code\Powermail\Exception\FileNotFoundException;

if (empty($webRoot = getenv('TYPO3_PATH_WEB'))) {
    putenv('TYPO3_PATH_WEB=' . $webRoot = realpath(__DIR__ . '/../../.Build/Web') . '/');
} else {
    $webRoot = rtrim($webRoot, '/') . '/';
}
$buildRoot = realpath($webRoot . '/..');
$autoload = $buildRoot . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    throw new FileNotFoundException('Can not find autoload path', 1579187349);
}

$bootstrapLoaded = false;
$bootstrap = $webRoot . '/typo3/sysext/core/Build/UnitTestsBootstrap.php';
$bootstrapNimut = $buildRoot . '/vendor/nimut/testing-framework/res/Configuration/UnitTestsBootstrap.php';
if (file_exists($bootstrap)) {
    require($bootstrap);
    $bootstrapLoaded = true;
} elseif (file_exists($bootstrapNimut)) {
    require($bootstrapNimut);
    $bootstrapLoaded = true;
}
if ($bootstrapLoaded === false) {
    throw new FileNotFoundException(
        'Can not find unit test bootstrap file. Did you do a composer update?',
        1579187344
    );
}
