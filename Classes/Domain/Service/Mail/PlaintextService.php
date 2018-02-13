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
    public function makePlain($content)
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
     *
     * @param string $content
     * @return string
     */
    protected function removeInvisibleElements($content)
    {
        $content = preg_replace(
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
                '', '', '', '', '', '', '', '', ''
            ],
            $content
        );
        return $content;
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
