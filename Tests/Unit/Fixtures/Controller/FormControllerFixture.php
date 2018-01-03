<?php
namespace In2code\Powermail\Tests\Unit\Fixtures\Controller;

use In2code\Powermail\Controller\FormController;

/**
 * Class FormControllerFixture
 */
class FormControllerFixture extends FormController
{

    /**
     * @param string $actionName
     * @param null $controllerName
     * @param null $extensionName
     * @param array|null $arguments
     * @return void
     */
    public function forward($actionName, $controllerName = null, $extensionName = null, array $arguments = null)
    {
        throw new \UnexpectedValueException('User will be forwarded', 1514993039679);
    }
}
