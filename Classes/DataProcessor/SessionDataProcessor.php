<?php
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Utility\SessionUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class SessionDataProcessor
 * @package In2code\Powermail\DataProcessor
 */
class SessionDataProcessor extends AbstractDataProcessor
{

    /**
     * Save values to session to prefill forms if needed
     *
     * @return void
     */
    public function saveSessionDataProcessor()
    {
        if ($this->getActionMethodName() === 'createAction') {
            SessionUtility::saveSessionValuesForPrefill($this->getMail(), $this->getSettings());
        }
    }
}
