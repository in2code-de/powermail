<?php

declare(strict_types=1);
namespace In2code\Powermail\Tca;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;

/**
 * Class Marker
 * to extend tableview for tx_powermail_domain_model_field with a marker value e.g. {firstname}
 */
class Marker extends AbstractFormElement
{
    /**
     * Default field information enabled for this element.
     *
     * @var array<array<string>>
     */
    protected $defaultFieldInformation = [
        'tcaDescription' => [
            'renderType' => 'tcaDescription',
        ],
    ];

    public function render(): array
    {
        $result = $this->initializeResultArray();
        $result['html'] = $this->getHtml();
        return $result;
    }

    /**
     * Create individual marker for powermail field
     */
    protected function getHtml(): string
    {
        // Render the description of the field
        $fieldInformationResult = $this->renderFieldInformation();
        $content = $fieldInformationResult['html'];

        // if entry in db
        $marker = empty($this->data['databaseRow']['marker']) ? 'marker' : $this->data['databaseRow']['marker'];

        // field just generated
        if (StringUtility::startsWith((string)$this->data['databaseRow']['uid'], 'NEW')) {
            $content .= '<span style="background-color: #F4DA5C; padding: 5px 10px; display: block;">';
            $content .= 'Please save before...';
            $content .= '</span>';
        } else {
            // was saved before
            $content .= '<span style="background-color: #ddd; padding: 5px 10px; display: block;">';
            $content .= '{' . strtolower((string)$marker) . '}';
            $content .= '</span>';
            $content .= '<input type="hidden" name="data[' . Field::TABLE_NAME . '][' .
                $this->data['databaseRow']['uid'] . '][marker]" value="' . strtolower((string)$marker) . '" />';
        }

        // Add the label & legend
        return $this->wrapWithFieldsetAndLegend($content);
    }
}
