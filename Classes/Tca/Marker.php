<?php
namespace In2code\Powermail\Tca;

use In2code\Powermail\Domain\Model\Field;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class to extend Pi1 field marker e.g. {firstname}
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class Marker
{

    /**
     * Create individual marker for powermail field
     *
     * @param array $params Config Array
     * @return string
     */
    public function createMarker($params)
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
        if (stristr($params['row']['uid'], 'NEW')) {
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
    public function doNothing()
    {
        return '';
    }
}
