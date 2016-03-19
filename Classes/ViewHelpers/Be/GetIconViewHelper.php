<?php
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetIconViewHelper
 * @package In2code\Powermail\ViewHelpers\Be
 */
class GetIconViewHelper extends AbstractViewHelper
{

    /**
     * @param string $identifier icons identifier
     * @param string $size
     * @return string icon markup
     */
    public function render($identifier = 'actions-open', $size = 'small')
    {
        /** @var IconFactory $iconFactory */
        $iconFactory = ObjectUtility::getObjectManager()->get(IconFactory::class);
        $icon = $iconFactory->getIcon($identifier);
        $icon->setSize($size);
        return $icon->render();
    }
}
