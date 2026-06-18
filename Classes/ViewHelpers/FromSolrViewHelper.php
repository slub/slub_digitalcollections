<?php

namespace Slub\SlubDigitalcollections\ViewHelpers;

/***************************************************************
 *
 *  Copyright notice
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

use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * FromSolrViewHelper
 *
 * Gets a field value from a Solr record
 *
 */
class FromSolrViewHelper extends AbstractViewHelper
{
    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @var \Solarium\Client
     */
    protected static $solr = null;

    /**
     * Register arguments.
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('query', 'mixed', 'Solr querystring or array of query fields and their query values.', true);
        $this->registerArgument('operator', 'string', 'Solr query operator.', false, 'AND');
        $this->registerArgument('sortField', 'string', 'Sort field.', false);
        $this->registerArgument('sortOrder', 'string', 'Sort order ("asc" or "desc").', false, 'asc');
        $this->registerArgument('rows', 'integer', 'Number of rows to be returned.', false);
        $this->registerArgument('start', 'integer', 'Number of leading documents to skip.', false);
        $this->registerArgument('fields', 'string', 'Fields to be returned, comma separated if more than one field.', false);
        $this->registerArgument('numFoundOnly', 'boolean', 'Return numFound only, do not fetch Documents.', false, false);
    }

    /**
     * @return string
     */
    public static function renderStatic(
        array                     $arguments,
        \Closure                  $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    )
    {
        $templateVariableContainer = $renderingContext->getVariableProvider();

        $solrClient = self::getSolariumClient();

        switch (gettype($arguments['query'])) {
            case 'string':
                $query = self::createQuery($solrClient, $arguments['query'], $templateVariableContainer);
                break;
            case 'array':
                $query = self::createQuery($solrClient, implode(' ' . $arguments['operator'] . ' ', array_map(function ($k, $v) {
                    return $k . ':' . $v;
                }, array_keys($arguments['query']), array_values($arguments['query']))), $templateVariableContainer);
                break;
            default:
                $query = self::createQuery($solrClient, '*:*', $templateVariableContainer);
        }

        if (!is_null($arguments['sortField'])) {
            $query->addSort($arguments['sortField'], $arguments['sortOrder']);
        }

        if (!is_null($arguments['rows'])) {
            $query->setRows($arguments['rows']);
        }

        if (!is_null($arguments['start'])) {
            $query->setStart($arguments['start']);
        }

        if (!is_null($arguments['fields'])) {
            $query->clearFields();
            $query->addFields($arguments['fields']);
        }

        $solrClient->setOptions(self::getSolariumClientOptionsArray($templateVariableContainer, $query));

        /** @var Result $resultSet */
        $resultSet = static::$solr->select($query);

        /** @var array<int, Document> $results */
        $results = $resultSet->getDocuments();

        if ($results) {
            if ($templateVariableContainer->exists('numFound')) {
                $templateVariableContainer->remove('numFound');
            }
            $templateVariableContainer->add('numFound', $resultSet->getNumFound());

            if (!$arguments['numFoundOnly']) {
                if ($templateVariableContainer->exists('documents')) {
                    $templateVariableContainer->remove('documents');
                }
                $templateVariableContainer->add('documents', $results);
            }
        } else {
            if ($templateVariableContainer->exists('numFound')) {
                $templateVariableContainer->remove('numFound');
            }
            $templateVariableContainer->add('numFound', 0);

            if ($templateVariableContainer->exists('documents')) {
                $templateVariableContainer->remove('documents');
            }
            $templateVariableContainer->add('documents', NULL);
        }

        return $renderChildrenClosure();
    }

    /**
     * Check configuration for shards and when found create distributed search.
     */
    private static function createQueryComponents(Query $query, VariableProviderInterface $templateVariableContainer): void
    {
        $settings = $templateVariableContainer->get('settings');
        $shards = $settings['shards'] ?? [];

        // Shards
        if (is_array($shards) && count($shards)) {
            $distributedSearch = $query->getDistributedSearch();
            foreach ($shards as $name => $shard) {
                $distributedSearch->addShard($name, $shard);
            }
        }
    }

    /**
     * Adds filter queries configured in TypoScript to $query.
     */
    private static function addTypoScriptFilters(Query $query, VariableProviderInterface $templateVariableContainer): void
    {
        if (!empty($templateVariableContainer->get('settings')['additionalFilters'])) {
            foreach ($templateVariableContainer->get('settings')['additionalFilters'] as $key => $filterQuery) {
                $query->createFilterQuery('additionalFilter-' . $key)
                    ->setQuery($filterQuery);
            }
        }
    }

    /**
     * Creates a query for a document.
     */
    private static function createQuery(Client $solrClient, string $query, VariableProviderInterface $templateVariableContainer): Query
    {
        $queryObject = $solrClient->createSelect();
        self::addTypoScriptFilters($queryObject, $templateVariableContainer);

        $queryObject->setQuery($query);

        self::createQueryComponents($queryObject, $templateVariableContainer);

        return $queryObject;
    }

    /**
     * @return \Solarium\Client
     */
    private static function getSolariumClient()
    {
        if (null === static::$solr) {
            // create an HTTP adapter instance
            $adapter = new Curl();
            $eventDispatcher = new EventDispatcher();
            // create a client instance
            static::$solr = new Client($adapter, $eventDispatcher);
        }

        return static::$solr;
    }

    private static function getSolariumClientOptionsArray(VariableProviderInterface $templateVariableContainer, Query $query): array
    {
        $settings = $templateVariableContainer->get('settings');
        $connectionName = $settings['activeConnection'] ?? 'default';
        if (!isset($settings['connections'][$connectionName]['options']) && isset($settings['connections']['default']['options'])) {
            $connectionName = 'default';
        }
        $connection = $settings['connections'][$connectionName]['options'] ?? [];

        $configuration = array(
            'endpoint' => array(
                $connectionName => array(
                    'host' => $connection['host'],
                    'port' => intval($connection['port']),
                    'path' => $connection['path'],
                    'timeout' => $connection['timeout'],
                    'scheme' => $connection['scheme'],
                    'core' => $connection['core']
                )
            ),
            'solarium' => $query
        );

        return $configuration;
    }
}

