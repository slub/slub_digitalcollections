<?php

namespace Slub\SlubDigitalcollections\ViewHelpers\Find;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class NonEmptyFacetValuesViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('values', 'array', 'Facet values keyed by term', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext,
    ): array {
        return array_filter(
            $arguments['values'],
            static fn (mixed $count, string|int $term): bool => !is_string($term) || trim($term) !== '',
            ARRAY_FILTER_USE_BOTH
        );
    }
}