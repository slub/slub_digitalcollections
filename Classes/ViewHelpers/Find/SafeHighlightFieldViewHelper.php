<?php

declare(strict_types=1);

namespace Slub\SlubDigitalcollections\ViewHelpers\Find;

use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Safer variant of Find highlight field rendering.
 *
 * It mirrors Subugoe's HighlightFieldViewHelper behavior but guards against
 * missing highlight entries for individual documents.
 */
class SafeHighlightFieldViewHelper extends AbstractViewHelper
{
    /**
     * Registers own arguments.
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('results', Result::class, 'Query results', true);
        $this->registerArgument('document', Document::class, 'Result document to work on', true);
        $this->registerArgument('field', 'string', 'name of field in document to highlight', true);
        $this->registerArgument('alternateField', 'string', 'name of alternate field in document to use for highlighting', false, null);
        $this->registerArgument('index', 'int', 'if the field is an array: index of the single element to highlight', false);
        $this->registerArgument('idKey', 'string', 'name of the field in document that is its ID', false, 'id');
        $this->registerArgument('highlightTagOpen', 'string', 'opening tag to insert to begin highlighting', false, '<em class="highlight">');
        $this->registerArgument('highlightTagClose', 'string', 'closing tag to insert to end highlighting', false, '</em>');
        $this->registerArgument('raw', 'boolean', 'whether to not HTML escape the output', false, false);
    }

    /**
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext,
    ) {
        if ($arguments['document']) {
            $fields = $arguments['document']->getFields();
            $fieldContent = $fields[$arguments['field']] ?? '';
            if ($arguments['index'] !== null) {
                if (is_array($fieldContent) && count($fieldContent) > $arguments['index']) {
                    $fieldContent = $fieldContent[$arguments['index']];
                }
            }

            if (!is_array($fieldContent) && !is_string($fieldContent)) {
                $fieldContent = (string)$fieldContent;
            }

            return self::highlightField($fieldContent, $arguments);
        }

        return '';
    }

    /**
     * Returns string or array of strings with highlighted areas enclosed
     * by \ueeee and \ueeef.
     *
     * @param mixed $fieldContent content of the field to highlight
     * @param array        $arguments
     */
    protected static function highlightField($fieldContent, array $arguments): array|string
    {
        $highlightInfo = self::getHighlightInfo($arguments);

        if (is_array($fieldContent)) {
            $result = [];
            foreach ($fieldContent as $singleField) {
                $result[] = self::highlightSingleField((string)$singleField, $highlightInfo, $arguments);
            }
        } else {
            $result = self::highlightSingleField((string)$fieldContent, $highlightInfo, $arguments);
        }

        return $result;
    }

    /**
     * Returns highlight information for the document and field configured in
     * our arguments.
     *
     * @return array
     */
    protected static function getHighlightInfo(array $arguments): array
    {
        $highlightInfo = [];
        $documentID = $arguments['document'][$arguments['idKey']];
        if (!$documentID) {
            return $highlightInfo;
        }

        $highlighting = $arguments['results']->getHighlighting();
        if (!$highlighting) {
            return $highlightInfo;
        }

        $highlightResult = $highlighting->getResult($documentID);
        if ($highlightResult === null) {
            return $highlightInfo;
        }

        $fieldName = $arguments['alternateField'] ?: $arguments['field'];
        $fieldHighlights = $highlightResult->getField($fieldName);
        if (is_array($fieldHighlights)) {
            $highlightInfo += $fieldHighlights;
        }

        return $highlightInfo;
    }

    /**
     * Returns $fieldString with highlighted areas enclosed by \ueeee and \ueeef.
     *
     * @param string $fieldString   the string to highlight
     * @param array  $highlightInfo information provided by the index' highlighter
     * @param array  $arguments
     *
     * @return string
     */
    protected static function highlightSingleField(string $fieldString, array $highlightInfo, array $arguments): string
    {
        $result = null;

        foreach ($highlightInfo as $highlightItem) {
            $highlightItemStripped = str_replace(['\\ueeee', '\\ueeef'], ['', ''], (string)$highlightItem);
            if (strpos($fieldString, (string)$highlightItemStripped) !== false) {
                if (!$arguments['raw']) {
                    $highlightItem = htmlspecialchars((string)$highlightItem);
                }

                $highlightItemMarkedUp = str_replace(
                    ['\\ueeee', '\\ueeef'],
                    [$arguments['highlightTagOpen'], $arguments['highlightTagClose']],
                    (string)$highlightItem
                );
                $result = str_replace((string)$highlightItemStripped, (string)$highlightItemMarkedUp, $fieldString);
                break;
            }
        }

        if ($result === null) {
            $result = $arguments['raw'] ? $fieldString : htmlspecialchars($fieldString);
        }

        return $result;
    }
}
