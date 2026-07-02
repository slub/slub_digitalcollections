<?php

namespace Slub\SlubDigitalcollections\ViewHelpers\Find;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns additional parameters needed to create links for facets.
 *
 * Mirrors Subugoe's facet link arguments helper, but keeps the current
 * searchMode for add links so fulltext mode survives facet navigation.
 */
class FacetLinkArgumentsViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('facetID', 'string', 'ID of the facet to determine the selection status of', true);
        $this->registerArgument('facetTerm', 'string',
            'Term of the facet item to determine the selection status of; if NULL any facet with the given facetID matches',
            false, null);
        $this->registerArgument('activeFacets', 'array', 'Array of active facets', false, []);
        $this->registerArgument('mode', 'string', 'add|remove', false, 'add');
        $this->registerArgument('mergeActiveFacets', 'bool', 'Merge existing active facets into add arguments', false, false);
        $this->registerArgument('replaceFacetSelection', 'bool', 'Replace selection for current facetID when adding', false, false);
        $this->registerArgument('returnAsArguments', 'bool', 'For remove mode: return arguments array instead of exclusion list', false, false);
    }

    public function render(): array
    {
        $result = [];

        $facetID = $this->arguments['facetID'];
        $facetTerm = $this->arguments['facetTerm'];
        $activeFacets = $this->arguments['activeFacets'];
        $mode = $this->arguments['mode'];
        $mergeActiveFacets = (bool)$this->arguments['mergeActiveFacets'];
        $replaceFacetSelection = (bool)$this->arguments['replaceFacetSelection'];
        $returnAsArguments = (bool)$this->arguments['returnAsArguments'];

        $requestArguments = $this->getCurrentPluginArguments();

        if ('remove' === $mode && $activeFacets) {
            if ($returnAsArguments) {
                $result = $requestArguments;

                if (!isset($result['facet']) || !is_array($result['facet'])) {
                    $result['facet'] = is_array($activeFacets) ? $activeFacets : [];
                }

                if (isset($result['facet'][$facetID]) && is_array($result['facet'][$facetID])) {
                    if ($facetTerm !== null && array_key_exists($facetTerm, $result['facet'][$facetID])) {
                        unset($result['facet'][$facetID][$facetTerm]);
                    } elseif ($facetTerm === null) {
                        unset($result['facet'][$facetID]);
                    }

                    if (isset($result['facet'][$facetID]) && empty($result['facet'][$facetID])) {
                        unset($result['facet'][$facetID]);
                    }
                }

                if (isset($result['page'])) {
                    unset($result['page']);
                }

                if (isset($result['facet']) && empty($result['facet'])) {
                    unset($result['facet']);
                }

                return $result;
            }

            if (array_key_exists($facetID, $activeFacets)) {
                $itemToRemove = 'tx_find_find[facet]['.$facetID.']';

                if (array_key_exists($facetTerm, $activeFacets[$facetID])) {
                    $itemToRemove .= '['.$facetTerm.']';
                }

                $result[] = $itemToRemove;
            }

            // Go back to page 1.
            $result[] = 'tx_find_find[page]';
        } elseif ('add' === $mode) {
            if ($mergeActiveFacets) {
                $result = $requestArguments;

                if (isset($result['page'])) {
                    unset($result['page']);
                }

                if (isset($result['facet']) && is_array($result['facet'])) {
                    $facetSelection = $result['facet'];
                } else {
                    $facetSelection = is_array($activeFacets) ? $activeFacets : [];
                }

                if (
                    $replaceFacetSelection
                    || !isset($facetSelection[$facetID])
                    || !is_array($facetSelection[$facetID])
                ) {
                    $facetSelection[$facetID] = [$facetTerm => 1];
                } else {
                    $facetSelection[$facetID][$facetTerm] = 1;
                }

                $result['facet'] = $facetSelection;
            } else {
                $result['facet'] = [
                    $facetID => [$facetTerm => 1],
                ];
            }

            if (!empty($requestArguments['searchMode'])) {
                $result['searchMode'] = $requestArguments['searchMode'];
            }
        }

        return $result;
    }

    private function getCurrentPluginArguments(): array
    {
        $fluidArguments = [];
        if ($this->renderingContext->getVariableProvider()->exists('arguments')) {
            $fluidRaw = $this->renderingContext->getVariableProvider()->get('arguments');
            if (is_array($fluidRaw)) {
                $fluidArguments = $fluidRaw;
            }
        }

        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($request === null) {
            return $fluidArguments;
        }

        $queryParams = $request->getQueryParams();
        $bodyParams = $request->getParsedBody();

        $queryArguments = $queryParams['tx_find_find'] ?? [];
        $bodyArguments = is_array($bodyParams) ? ($bodyParams['tx_find_find'] ?? []) : [];

        if (!is_array($queryArguments)) {
            $queryArguments = [];
        }
        if (!is_array($bodyArguments)) {
            $bodyArguments = [];
        }

        $requestArguments = array_replace_recursive($queryArguments, $bodyArguments);

        // Use request args as primary source, but keep Fluid arguments as fallback.
        return array_replace_recursive($fluidArguments, $requestArguments);
    }
}
