<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Utility\StringUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class PlaintextService
 * @package In2code\Powermail\Domain\Service
 */
class PlaintextService
{

    /**
     * Function makePlain() removes html tags and add linebreaks
     *        Easy generate a plain email bodytext from a html bodytext
     *
     * @param string $content HTML Mail bodytext
     * @return string $content
     */
    public function makePlain($content)
    {
        $content = $this->removeHeadElement($content);
        $content = $this->removeLinebreaksAndTabs($content);
        $content = $this->addLineBreaks($content);
        $content = $this->addSpaceToTableCells($content);
        $content = $this->extractLinkForPlainTextContent($content);
        $content = $this->removeTags($content);
        $content = StringUtility::br2nl($content);
        return trim($content);
    }

    /**
     * Remove head tag from content
     *
     * @param string $content
     * @return string
     */
    protected function removeHeadElement($content)
    {
        return preg_replace('/<head>(.*?)<\/head>/i', '', $content);
    }

    /**
     * Remove linebreaks and tabs
     *
     * @param string $content
     * @return string
     */
    protected function removeLinebreaksAndTabs($content)
    {
        $content = trim(str_replace(["\n", "\r", "\t"], '', $content));
        return $content;
    }

    /**
     * add linebreaks on some parts (</p> => </p><br />)
     *
     * @param string $content
     * @return array
     */
    protected function addLineBreaks($content)
    {
        $tags2LineBreaks = [
            '</p>',
            '</tr>',
            '<ul>',
            '</li>',
            '</h1>',
            '</h2>',
            '</h3>',
            '</h4>',
            '</h5>',
            '</h6>',
            '</div>',
            '</legend>',
            '</fieldset>',
            '</dd>',
            '</dt>'
        ];
        return str_replace($tags2LineBreaks, '</p><br />', $content);
    }

    /**
     * Add a space character to a table cell
     *
     * @param string $content
     * @return string
     */
    protected function addSpaceToTableCells($content)
    {
        return str_replace(['</td>', '</th>'], '</td> ', $content);
    }

    /**
     * Remove all tags but keep br and address
     *
     * @param string $content
     * @return string
     */
    protected function removeTags($content)
    {
        return strip_tags($content, '<br><address>');
    }

    /**
     * Extract uri from href attributes and decode it
     *
     *  replace links
     *      <a href="xyz">LINK</a>
     *      ->
     *      LINK [xyz]
     *
     * @param string $content
     * @return string
     */
    protected function extractLinkForPlainTextContent($content)
    {
        $pattern = '/<a[^>]+href\s*=\s*["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/misu';
        return preg_replace_callback($pattern, function ($matches) {
            return $matches[2] . ' [' . htmlspecialchars_decode($matches[1]) . ']';
        }, $content);
    }
}
