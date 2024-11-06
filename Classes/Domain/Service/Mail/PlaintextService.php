<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Service\Mail;

use In2code\Powermail\Utility\StringUtility;

/**
 * Class PlaintextService
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
    public function makePlain(string $content): string
    {
        $content = $this->removeInvisibleElements($content);
        $content = $this->removeLinebreaksAndTabs($content);
        $content = $this->addLineBreaks($content);
        $content = $this->addSpaceToTableCells($content);
        $content = $this->extractLinkForPlainTextContent($content);
        $content = $this->removeTags($content);
        $content = StringUtility::br2nl($content);
        return trim($content);
    }

    /**
     * Remove all invisible elements
     */
    protected function removeInvisibleElements(string $content): string
    {
        return preg_replace(
            [
                '/<head[^>]*?>.*?<\/head>/siu',
                '/<style[^>]*?>.*?<\/style>/siu',
                '/<script[^>]*?>.*?<\/script>/siu',
                '/<object[^>]*?>.*?<\/object>/siu',
                '/<embed[^>]*?>.*?<\/embed>/siu',
                '/<applet[^>]*?>.*?<\/applet>/siu',
                '/<noframes[^>]*?>.*?<\/noframes>/siu',
                '/<noscript[^>]*?>.*?<\/noscript>/siu',
                '/<noembed[^>]*?>.*?<\/noembed>/siu',
            ],
            [
                '', '', '', '', '', '', '', '', '',
            ],
            $content
        );
    }

    /**
     * Remove linebreaks and tabs
     */
    protected function removeLinebreaksAndTabs(string $content): string
    {
        return trim(str_replace(["\n", "\r", "\t"], '', $content));
    }

    /**
     * add linebreaks on some parts (</p> => </p><br />)
     */
    protected function addLineBreaks(string $content): string
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
            '</dt>',
        ];
        return str_replace($tags2LineBreaks, '</p><br />', $content);
    }

    /**
     * Add a space character to a table cell
     */
    protected function addSpaceToTableCells(string $content): string
    {
        return str_replace(['</td>', '</th>'], '</td> ', $content);
    }

    /**
     * Remove all tags but keep br and address
     */
    protected function removeTags(string $content): string
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
     */
    protected function extractLinkForPlainTextContent(string $content): string
    {
        $pattern = '/<a[^>]+href\s*=\s*["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/misu';
        return preg_replace_callback($pattern, fn ($matches): string => $matches[2] . ' [' . htmlspecialchars_decode((string)$matches[1]) . ']', $content);
    }
}
