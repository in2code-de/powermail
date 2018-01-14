<?php
declare(strict_types=1);
namespace In2code\Powermail\Tca;

use In2code\Powermail\Domain\Model\Field;

/**
 * Class to extend tableview for fields with marker e.g. {firstname}
 */
class Marker
{

    /**
     * Create individual marker for powermail field
     *
     * @param array $params Config Array
     * @return string
     */
    public function createMarker(array $params): string
    {
        $content = '';

        // if entry in db
        if (isset($params['row']['marker']) && !empty($params['row']['marker'])) {
            $marker = $params['row']['marker'];
        } else {
            // no entry - take "marker"
            $marker = 'marker';
        }

        // field just generated
        if (stristr((string)$params['row']['uid'], 'NEW')) {
            $content .= '<span style="background-color: #F4DA5C; padding: 5px 10px; display: block;">';
            $content .= 'Please save before...';
            $content .= '</span>';
            // was saved before
        } else {
            $content .= '<span style="background-color: #ddd; padding: 5px 10px; display: block;">';
            $content .= '{' . strtolower($marker) . '}';
            $content .= '</span>';
            $content .= '<input type="hidden" name="data[' . Field::TABLE_NAME . '][' .
                $params['row']['uid'] . '][marker]" value="' . strtolower($marker) . '" />';
        }

        return $content;
    }

    /**
     * Workarround to only show a label and no field in TCA
     *
     * @return string empty
     */
    public function doNothing(): string
    {
        return '';
    }
}
