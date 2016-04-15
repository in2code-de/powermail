<?php
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Imaging\Icon;
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
     * @param string $size "small", "default" or "large"
     * @param string $overlay set overlay icon - e.g. "overlay-hidden"
     * @return string icon markup
     */
    public function render($identifier = 'actions-open', $size = Icon::SIZE_SMALL, $overlay = null)
    {
        /** @var IconFactory $iconFactory */
        $iconFactory = ObjectUtility::getObjectManager()->get(IconFactory::class);
        $icon = $iconFactory->getIcon($identifier);
        if ($overlay) {
            $icon->setOverlayIcon($iconFactory->getIcon($overlay));
        }
        $icon->setSize($size);
        return $icon->render();
    }
}
