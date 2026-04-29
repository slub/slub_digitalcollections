<?php

namespace Slub\SlubDigitalcollections\Service;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2026 SLUB Dresden
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
 * ************************************************************* */

use Psr\Log\LoggerInterface;
use Solarium\Component\Result\Grouping\Result as GroupingResult;
use Subugoe\Find\Service\SolrServiceProvider;

/**
 * Extended Service Provider for Solr with advanced grouping support.
 * 
 * Provides full Solr grouping functionality including:
 * - Field-based grouping (group.field)
 * - Query-based grouping (group.query)
 * - Multiple grouping fields/queries
 * - Group sorting, formatting, and faceting
 * - Configurable via TypoScript and URL parameters
 * 
 * Compatible with both TYPO3 12.4 and 13.4.
 */
class GroupedSolrServiceProvider extends SolrServiceProvider
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $localLogger;

    /**
     * @var array
     */
    protected array $localSettings = [];

    /**
     * @var string
     */
    protected string $localConnectionName = '';

    /**
     * Constructor compatible with both TYPO3 12.4 (PHP 8.1) and 13.4 (PHP 8.4).
     * 
     * TYPO3 12.4: Expects 3 parameters (connectionName, settings, logger)
     * TYPO3 13.4: Expects 1 parameter (logger), uses setters for other values
     * 
     * Detection strategy: Checks if parent class has setConnectionName method (TYPO3 13 only)
     * 
     * @param mixed ...$args Variable arguments to support both versions
     */
    public function __construct(...$args)
    {
        if (count($args) === 1 && $args[0] instanceof LoggerInterface) {
            // TYPO3 13.4 style: only logger parameter
            // @phpstan-ignore-next-line (TYPO3 13.4 constructor signature differs)
            if (method_exists(parent::class, '__construct')) {
                parent::__construct($args[0]);
            }
            $this->localLogger = $args[0];
        } elseif (count($args) === 3) {
            // TYPO3 12.4 or 13.4 style with explicit parameters
            [$connectionName, $settings, $logger] = $args;
            
            // Detect TYPO3 version by checking if parent has setConnectionName method (13.4 only)
            // This is more reliable than checking constructor parameters
            $isTypo3v13 = method_exists(parent::class, 'setConnectionName');
            
            if ($isTypo3v13) {
                // TYPO3 13.4: Parent expects only logger, use setters for other values
                // @phpstan-ignore-next-line (TYPO3 13.4 constructor takes 1 parameter)
                parent::__construct($logger);
                // PHPStan: These methods only exist in TYPO3 13.4
                // @phpstan-ignore-next-line (Method exists in TYPO3 13.4)
                if (method_exists($this, 'setConnectionName')) {
                    $this->setConnectionName($connectionName);
                }
                // @phpstan-ignore-next-line (Method exists in TYPO3 13.4)
                if (method_exists($this, 'setSettings')) {
                    $this->setSettings($settings);
                }
            } else {
                // TYPO3 12.4: Parent expects 3 parameters
                // @phpstan-ignore-next-line (TYPO3 12.4 constructor takes 3 parameters)
                parent::__construct($connectionName, $settings, $logger);
            }
            
            $this->localLogger = $logger;
            $this->localSettings = $settings;
            $this->localConnectionName = $connectionName;
        } else {
            throw new \InvalidArgumentException(
                'Invalid constructor arguments. Expected either (LoggerInterface) or (string, array, LoggerInterface).'
            );
        }
    }

    /**
     * Override setSettings to keep local copy.
     * Only available in TYPO3 13.4+.
     */
    public function setSettings(array $settings): void
    {
        // @phpstan-ignore-next-line (Method exists in TYPO3 13.4)
        if (method_exists(parent::class, 'setSettings')) {
            parent::setSettings($settings);
        }
        $this->localSettings = $settings;
    }

    /**
     * Override setConnectionName to keep local copy.
     * Only available in TYPO3 13.4+.
     */
    public function setConnectionName(string $name): void
    {
        // @phpstan-ignore-next-line (Method exists in TYPO3 13.4)
        if (method_exists(parent::class, 'setConnectionName')) {
            parent::setConnectionName($name);
        }
        $this->localConnectionName = $name;
    }

    /**
     * Creates a query configured with all parameters set in the request's arguments.
     * Extends parent method to add grouping support.
     *
     * @param array $arguments request arguments
     */
    protected function createQueryForArguments(array $arguments): void
    {
        parent::createQueryForArguments($arguments);
        
        // Add Solr grouping configuration
        $this->addGrouping($arguments);
    }

    /**
     * Configures Solr grouping parameters on the query.
     * 
     * Supports both field-based and query-based grouping with extensive configuration options:
     * 
     * TypoScript Configuration Example:
     * grouping {
     *   // Basic options
     *   enabled = 1
     *   
     *   // Field-based grouping (one or multiple fields)
     *   fields {
     *     0 = uid
     *     1 = collection_id
     *   }
     *   // Or single field:
     *   field = uid
     *   
     *   // Query-based grouping
     *   queries {
     *     0 {
     *       query = year:[1900 TO 1950]
     *     }
     *     1 {
     *       query = year:[1951 TO 2000]
     *     }
     *   }
     *   
     *   // Advanced options
     *   limit = 100              // Results per group (default: -1 = all)
     *   offset = 0               // Start position within groups
     *   sort = title asc         // Sort order within groups
     *   format = grouped         // Result format: grouped or simple
     *   main = false             // If true, ungrouped results in main result list
     *   facets = true            // Calculate facets for grouped results
     *   truncate = true          // Truncate group count to limit
     *   cachePercentage = 0      // Query result cache as percentage (0-100)
     *   numberOfGroups = true    // Include group count in results
     * }
     *
     * URL Parameters (override TypoScript):
     * - groupField: Single field to group by
     * - groupFields[]: Multiple fields to group by
     * - groupQuery[]: Query-based grouping
     * - groupLimit: Results per group
     * - groupOffset: Start position within groups
     * - groupSort: Sort order within groups
     * - groupFormat: Result format
     *
     * @param array $arguments Request arguments from URL
     */
    protected function addGrouping(array $arguments): void
    {
        // Check if grouping is enabled in settings
        if (empty($this->localSettings['grouping'])) {
            return;
        }

        $groupSettings = $this->localSettings['grouping'];
        
        // Check if grouping is explicitly disabled
        if (isset($groupSettings['enabled']) && !$groupSettings['enabled']) {
            return;
        }

        $grouping = $this->query->getGrouping();
        
        // Configure field-based grouping
        $this->configureFieldGrouping($grouping, $groupSettings, $arguments);
        
        // Configure query-based grouping
        $this->configureQueryGrouping($grouping, $groupSettings, $arguments);
        
        // Configure general grouping parameters
        $this->configureGroupingParameters($grouping, $groupSettings, $arguments);
        
        $this->localLogger->debug('Solr grouping configured', [
            'settings' => $groupSettings,
            'arguments' => $arguments
        ]);
    }

    /**
     * Configures field-based grouping (group.field parameter).
     *
     * @param \Solarium\Component\Grouping $grouping The grouping component
     * @param array $groupSettings TypoScript grouping settings
     * @param array $arguments URL arguments
     */
    protected function configureFieldGrouping($grouping, array $groupSettings, array $arguments): void
    {
        $fields = [];
        
        // Priority 1: URL parameter (single field)
        if (!empty($arguments['groupField'])) {
            $fields[] = $arguments['groupField'];
        }
        // Priority 2: URL parameter (multiple fields)
        elseif (!empty($arguments['groupFields']) && is_array($arguments['groupFields'])) {
            $fields = $arguments['groupFields'];
        }
        // Priority 3: TypoScript fields array
        elseif (!empty($groupSettings['fields']) && is_array($groupSettings['fields'])) {
            $fields = array_values($groupSettings['fields']);
        }
        // Priority 4: TypoScript single field
        elseif (!empty($groupSettings['field'])) {
            $fields[] = $groupSettings['field'];
        }
        
        // Add all configured fields
        foreach ($fields as $field) {
            if (!empty($field) && is_string($field)) {
                $grouping->addField($field);
                $this->localLogger->debug('Added grouping field', ['field' => $field]);
            }
        }
    }

    /**
     * Configures query-based grouping (group.query parameter).
     *
     * @param \Solarium\Component\Grouping $grouping The grouping component
     * @param array $groupSettings TypoScript grouping settings
     * @param array $arguments URL arguments
     */
    protected function configureQueryGrouping($grouping, array $groupSettings, array $arguments): void
    {
        $queries = [];
        
        // Priority 1: URL parameters
        if (!empty($arguments['groupQuery']) && is_array($arguments['groupQuery'])) {
            $queries = $arguments['groupQuery'];
        }
        // Priority 2: TypoScript configuration
        elseif (!empty($groupSettings['queries']) && is_array($groupSettings['queries'])) {
            foreach ($groupSettings['queries'] as $queryConfig) {
                if (!empty($queryConfig['query'])) {
                    $queries[] = $queryConfig['query'];
                }
            }
        }
        
        // Add all configured queries
        foreach ($queries as $query) {
            if (!empty($query) && is_string($query)) {
                $grouping->addQuery($query);
                $this->localLogger->debug('Added grouping query', ['query' => $query]);
            }
        }
    }

    /**
     * Configures general grouping parameters.
     *
     * @param \Solarium\Component\Grouping $grouping The grouping component
     * @param array $groupSettings TypoScript grouping settings
     * @param array $arguments URL arguments
     */
    protected function configureGroupingParameters($grouping, array $groupSettings, array $arguments): void
    {
        // Limit (results per group)
        // Default: -1 means all results
        $limit = $arguments['groupLimit'] ?? $groupSettings['limit'] ?? -1;
        if (is_numeric($limit)) {
            $grouping->setLimit((int)$limit);
        }
        
        // Offset (start position within groups)
        $offset = $arguments['groupOffset'] ?? $groupSettings['offset'] ?? null;
        if ($offset !== null && is_numeric($offset)) {
            $grouping->setOffset((int)$offset);
        }
        
        // Sort (order within groups)
        $sort = $arguments['groupSort'] ?? $groupSettings['sort'] ?? null;
        if (!empty($sort) && is_string($sort)) {
            $grouping->setSort($sort);
        }
        
        // Format (grouped or simple)
        $format = $arguments['groupFormat'] ?? $groupSettings['format'] ?? null;
        if (!empty($format) && is_string($format)) {
            $grouping->setFormat($format);
        }
        
        // Main (return ungrouped results in main result list)
        // Note: setMain() is controlled via the format parameter in Solarium
        // Use format = 'simple' to get main results alongside grouped results
        
        // Facets (calculate facets for grouped results)
        // Note: Combining facets with grouping requires proper Solr schema configuration.
        // Fields used for grouping need docvalues type SORTED, not NUMERIC.
        // Only set if explicitly enabled to avoid "unexpected docvalues type" errors.

        if (isset($groupSettings['facets']) && 
                        ($groupSettings['facets'] === true || 
                         $groupSettings['facets'] === '1' || 
                         $groupSettings['facets'] === 'true')) {
            $grouping->setFacet(true);
        } else {
            $grouping->setFacet(false);
        }
        
        // Truncate (truncate group count to limit)
        $truncate = $groupSettings['truncate'] ?? null;
        if ($truncate !== null) {
            $grouping->setTruncate((bool)$truncate);
        }
        
        // Cache percentage (0-100)
        $cachePercentage = $groupSettings['cachePercentage'] ?? null;
        if ($cachePercentage !== null && is_numeric($cachePercentage)) {
            $grouping->setCachePercentage((int)$cachePercentage);
        }
        
        // Number of groups (include total group count)
        $numberOfGroups = $groupSettings['numberOfGroups'] ?? true;
        if ($numberOfGroups !== null) {
            $grouping->setNumberOfGroups((bool)$numberOfGroups);
        }
    }

    /**
     * Main starting point for default/blank index action.
     * Extends parent to process grouped results.
     *
     * @return array Result array with query results and metadata
     */
    public function getDefaultQuery(): array
    {
        $result = parent::getDefaultQuery();
        
        // If there was an error, return as-is
        if (!empty($result['error'])) {
            return $result;
        }
        
        // Process grouping if enabled
        if (!empty($this->localSettings['grouping']) && !empty($result['results'])) {
            $result = $this->processGroupedResults($result);
        }
        
        return $result;
    }

    /**
     * Processes and enriches grouped Solr results.
     * 
     * Extracts grouping information from Solr response and adds:
     * - groupedResults: Template-friendly structure with valueGroups array
     * - allDocuments: Flat array of all documents keyed by group value (UID)
     * - groupCount: Number of groups found
     * - matches: Total matches across all groups
     * - groupingActive: Flag indicating grouping is active
     *
     * Structure of groupedResults:
     * {
     *   matches: int,
     *   numberOfGroups: int,
     *   valueGroups: [
     *     {
     *       value: string,        // Group identifier (e.g., UID)
     *       numFound: int,        // Documents in this group
     *       start: int,           // Start position
     *       documents: array      // Array of Solr documents
     *     }
     *   ]
     * }
     *
     * @param array $result Result array from parent::getDefaultQuery()
     * @return array Enriched result array
     */
    protected function processGroupedResults(array $result): array
    {
        try {
            $results = $result['results'];
            $grouping = $results->getGrouping();
            
            if (!$grouping) {
                return $result;
            }
            
            $groupSettings = $this->localSettings['grouping'];
            $groupedResults = [];
            $allDocuments = [];
            $totalMatches = 0;
            $totalGroups = 0;
            
            // Get the primary grouping field for template compatibility
            $fields = $this->getConfiguredFields($groupSettings);
            $primaryField = !empty($fields) ? $fields[0] : null;
            
            // Process field-based grouping
            foreach ($fields as $field) {
                $fieldGroup = $grouping->getGroup($field);
                if ($fieldGroup) {
                    $totalMatches += $fieldGroup->getMatches();
                    $totalGroups += $fieldGroup->getNumberOfGroups();
                    
                    // For the primary field, create template-friendly structure
                    if ($field === $primaryField) {
                        $groupedResults = $this->extractTemplateData($fieldGroup, $allDocuments);
                    }
                }
            }
            
            // Process query-based grouping
            $queries = $this->getConfiguredQueries($groupSettings);
            foreach ($queries as $idx => $query) {
                $queryGroup = $grouping->getGroup($query);
                if ($queryGroup) {
                    $totalMatches += $queryGroup->getMatches();
                }
            }
            
            // Enrich result array with template-friendly structure
            $result['groupedResults'] = $groupedResults;
            $result['allDocuments'] = $allDocuments;
            $result['groupCount'] = $totalGroups;
            $result['matches'] = $totalMatches;
            $result['groupingActive'] = true;
            
            // Fetch additional title information for documents without title
            $additionalTitleInfo = $this->fetchAdditionalTitleInfo($allDocuments);
            $result['additionalTitleInfo'] = $additionalTitleInfo;
            
            $this->localLogger->debug('Grouped results processed', [
                'totalGroups' => $totalGroups,
                'totalMatches' => $totalMatches,
                'fields' => $fields,
                'queryCount' => count($queries),
                'additionalTitles' => count($additionalTitleInfo)
            ]);
            
        } catch (\Exception $e) {
            $this->localLogger->error('Error processing grouped results', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return $result;
    }

    /**
     * Extracts template-friendly data from Solarium group result.
     * Creates structure compatible with existing Fluid templates.
     *
     * @param mixed $fieldGroup Solarium group result
     * @param array &$allDocuments Reference to collect all documents by UID
     * @return array Template-friendly group structure
     */
    protected function extractTemplateData($fieldGroup, array &$allDocuments): array
    {
        $data = [
            'matches' => $fieldGroup->getMatches(),
            'numberOfGroups' => $fieldGroup->getNumberOfGroups(),
            'valueGroups' => []
        ];
        
        // Extract groups and documents
        foreach ($fieldGroup as $group) {
            $groupValue = $group->getValue();
            $documents = [];
            
            // Extract documents from group
            foreach ($group as $document) {
                $documents[] = $document;
                
                // Add to flat allDocuments array, keyed by group value
                if (!isset($allDocuments[$groupValue])) {
                    $allDocuments[$groupValue] = $document;
                }
            }
            
            // Create template-friendly group structure
            $data['valueGroups'][] = [
                'value' => $groupValue,
                'numFound' => $group->getNumFound(),
                'start' => method_exists($group, 'getStart') ? $group->getStart() : 0,
                'documents' => $documents
            ];
        }
        
        return $data;
    }

    /**
     * Fetches additional title information for documents without a title field.
     * 
     * For documents that have no title but reference a parent document (via partof field),
     * this method queries Solr to fetch the parent's title and returns it in brackets.
     * First tries to find toplevel documents, then fallback to volume documents.
     * 
     * @param array $documents Array of documents keyed by UID
     * @return array Array of additional title info keyed by document UID
     */
    protected function fetchAdditionalTitleInfo(array $documents): array
    {
        $titleRequiredForDocuments = [];
        
        // Find documents without title that have a parent reference
        foreach ($documents as $uid => $doc) {
            if (empty($doc['title']) && !empty($doc['partof']) && ($doc['type'] ?? '') !== 'year') {
                $titleRequiredForDocuments[] = [
                    'uid' => $uid,
                    'partof' => $doc['partof']
                ];
            }
        }
        
        // Early return if no additional titles needed
        if (empty($titleRequiredForDocuments)) {
            return [];
        }
        
        // Build query to fetch parent titles
        $parentUids = array_unique(array_column($titleRequiredForDocuments, 'partof'));
        $query = implode(' OR ', array_map(function($uid) {
            return 'uid:' . $uid;
        }, $parentUids));

        $additionalTitleInfo = [];
        
        try {
            // First attempt: Query for toplevel parent documents
            $selectQuery = $this->connection->createSelect();
            $selectQuery->setQuery($query);
            $selectQuery->setFields(['uid', 'title']);
            $selectQuery->createFilterQuery('onlyTopLevel')->setQuery('toplevel:true');
            
            /** @var \Solarium\QueryType\Select\Result\Result $titlesResult */
            $titlesResult = $this->connection->execute($selectQuery);
            
            foreach ($titlesResult as $doc) {
                if (!empty($doc['title'])) {
                    $additionalTitleInfo[$doc['uid']] = [
                        'uid' => $doc['uid'],
                        'title' => '[' . $doc['title'] . ']'
                    ];
                }
            }

            // Second attempt: If some titles are still missing, try volume documents
            $missingUids = array_diff($parentUids, array_keys($additionalTitleInfo));
            if (!empty($missingUids)) {
                $volumeQuery = implode(' OR ', array_map(function($uid) {
                    return 'uid:' . $uid;
                }, $missingUids));
                
                $volumeSelectQuery = $this->connection->createSelect();
                $volumeSelectQuery->setQuery($volumeQuery);
                $volumeSelectQuery->setFields(['uid', 'title', 'type']);
                $volumeSelectQuery->createFilterQuery('volumeType')->setQuery('type:volume');
                
                /** @var \Solarium\QueryType\Select\Result\Result $volumeResult */
                $volumeResult = $this->connection->execute($volumeSelectQuery);
                
                foreach ($volumeResult as $doc) {
                    if (!empty($doc['title'])) {
                        $additionalTitleInfo[$doc['uid']] = [
                            'uid' => $doc['uid'],
                            'title' => '[' . $doc['title'] . ']'
                        ];
                    }
                }
                
                $this->localLogger->debug('Volume query executed', [
                    'missing' => count($missingUids),
                    'foundVolume' => iterator_count($volumeResult)
                ]);
            }
            
            $this->localLogger->debug('Fetched additional title info', [
                'requested' => count($titleRequiredForDocuments),
                'foundToplevel' => iterator_count($titlesResult),
                'total' => count($additionalTitleInfo)
            ]);

            return $additionalTitleInfo;
            
        } catch (\Exception $e) {
            $this->localLogger->error('Error fetching additional title info', [
                'exception' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Gets configured grouping fields from settings.
     *
     * @param array $groupSettings Grouping configuration
     * @return array List of field names
     */
    protected function getConfiguredFields(array $groupSettings): array
    {
        $fields = [];
        
        if (!empty($groupSettings['fields']) && is_array($groupSettings['fields'])) {
            $fields = array_values($groupSettings['fields']);
        } elseif (!empty($groupSettings['field'])) {
            $fields[] = $groupSettings['field'];
        }
        
        return array_filter($fields, function($field) {
            return !empty($field) && is_string($field);
        });
    }

    /**
     * Gets configured grouping queries from settings.
     *
     * @param array $groupSettings Grouping configuration
     * @return array List of query strings
     */
    protected function getConfiguredQueries(array $groupSettings): array
    {
        $queries = [];
        
        if (!empty($groupSettings['queries']) && is_array($groupSettings['queries'])) {
            foreach ($groupSettings['queries'] as $queryConfig) {
                if (!empty($queryConfig['query'])) {
                    $queries[] = $queryConfig['query'];
                }
            }
        }
        
        return $queries;
    }
}
