<?php

namespace Slub\SlubDigitalcollections\Service;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Ingo Pfennigstorf <pfennigstorf@sub-goettingen.de>
 *      Goettingen State Library
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
use Solarium\Client;
use Solarium\Component\Highlighting\Field;
use Solarium\Component\Result\Analytics\Facet;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Adapter\Http;
use Solarium\Exception\HttpException;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;
use Subugoe\Find\Service\SolrServiceProvider;
use Subugoe\Find\Utility\FrontendUtility;
use Subugoe\Find\Utility\LoggerUtility;
use Subugoe\Find\Utility\SettingsUtility;
use Subugoe\Find\Utility\UpgradeUtility;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;


/**
 * Service provider for solr.
 */
class SlubSolrServiceProvider extends SolrServiceProvider
{
    public function __construct(string $connectionName, array $settings, LoggerInterface $logger)
    {
        parent::__construct($connectionName, $settings, $logger);
    }
    
    /**
     * Creates a query configured with all parameters set in the request’s arguments.
     *
     * @param array $arguments request arguments
     */
    protected function createQueryForArguments(array $arguments): void
    {
        parent::createQueryForArguments($arguments);
    
        // add grouping
        $this->addGrouping($arguments);
    }

    /**
     * Sets up $query’s grouping parameters from URL arguments or the TypoScript default.
     *
     * @param Query $query
     * @param array $arguments request arguments
     */
    private function addGrouping ($arguments) {
        if (!empty($this->settings['grouping'])) {
            $limit = -1;
            $field = null;

            $groupSetting = $this->settings['grouping'];
            
            $field = $arguments["groupfield"] ?? $groupSetting["field"];
            $limit = $arguments["grouplimit"] ?? $groupSetting["limit"];
            
            if (!empty($field)) {
                $grouping = $this->query->getGrouping();
                $grouping->setLimit((int)$limit);
                $grouping->addField($field);
                $grouping->setNumberOfGroups(true);
            }
        }
	}

    /**
     * Main starting point for blank index action.
     */
    public function getDefaultQuery(): array
    {
        $parentResult = parent::getDefaultQuery();
        if (array_key_exists('error', $parentResult) && !empty($parentResult["error"])) {
            return $parentResult;
        }

        if (!empty($this->settings['grouping'])) {
            $field = $this->settings['grouping']["field"];

            // handle grouping
            $results = $parentResult['results'];
            $grouping = $results->getGrouping();

            $uidGroup = $grouping->getGroup($field);
            $docs = [];
            foreach ($uidGroup as $group) {
                foreach ($group as $record) {
                    $docs[$group->getValue()] = $record;
                }
            }

            $parentResult["groupedResults"] = $uidGroup;
            $parentResult["allDocuments"] = $docs;
            $parentResult["resultGroupingActive"] = true;


            /* 
             * if the current document has no title attached, we fetch the title of its "parent document"
             * TODO required? only queries the direct parents, which is insufficient in general, but following transitive relations is costly
             * TODO add caching or rework index 
             * 
             */
            $titleRequiredForDocuments = [];
            foreach ($docs as $doc) {
                if (empty($doc["title"]) && $doc["type"] != "year"){
                    if (!empty($doc["partof"])) {
                        $titleRequiredForDocuments[]= $doc;
                    }
                }
            }

            $q = "";
            $numtitlesrequired = count($titleRequiredForDocuments);
            for ($i=0; $i < $numtitlesrequired; $i++) { 
                $doc = $titleRequiredForDocuments[$i];
                $q.="uid:".$doc["partof"];
                if ($i != $numtitlesrequired - 1) {
                    $q.= " OR ";
                }
            }

            // query titles
            $selectQuery = $this->connection->createSelect();
            $selectQuery->setQuery($q);
            $selectQuery->setFields(["uid", "title"]);
            $selectQuery->createFilterQuery('onlyTopLevel')->setQuery("toplevel:true");
            $titlesResult = $this->connection->execute($selectQuery);

            $additionalTitleInfo = [];
            foreach ($titlesResult->getDocuments() as $d) {
                if (empty($d["title"])) continue;

                $additionalTitleInfo[$d["uid"]] = [
                    "uid" => $d["uid"],
                    "title" => "[".$d["title"]."]"
                ];
            }
            // hand over to templates
            $parentResult["additionalTitleInfo"] = $additionalTitleInfo;
            /* 
             * END add title of parent document
             */


            
            /* 
             * 
             * Code adapted from https://github.com/kitodo/kitodo-presentation/blob/main/Classes/Common/Solr/SolrSearch.php (methods: submit, fetchToplevelMetadataFromSolr, searchSolr)
             * 
             * TODO: Currently deactivated (see also Index.html); needs to be verified if correct and required.
             * 
             */
            /*$documents= [];

            foreach ($docs as $doc) {
                if ($doc['toplevel'] === false) {
                        // this maybe a chapter, article, ..., year
                    if ($doc['type'] === 'year') {
                        continue;
                    }
                    if (!empty($doc['page'])) {
                        // it's probably a fulltext or metadata search
                        $searchResult = [];
                        $searchResult['page'] = $doc['page'];
                        $searchResult['thumbnail'] = $doc['thumbnail'];
                        $searchResult['structure'] = $doc['type'];
                        $searchResult['title'] = $doc['title'];
                        $documents[$doc['uid']]['searchResults'][] = $searchResult;
                    }
                } elseif ($doc['toplevel'] === true) {
                    $documents[$doc['uid']]['page'] = 1;

                    $metadataOf = $this->fetchToplevelMetadataFromSolr(
                        [
                            'query' => 'partof:' . $doc['uid'],
                            'start' => 0,
                            'rows' => 100,
                        ]
                    );

                    foreach ($metadataOf as $docChild) {
                        $documents[$doc['uid']]['children'][$docChild['uid']] = $docChild;
                    }
                    
                }
            }
            $parentResult["allDocuments"] = $documents;*/
        }

        return $parentResult;
    }

    /**
     * Find all listed metadata using specified query params.
     *
     * @access protected
     *
     * @param mixed[] $queryParams
     *
     * @return mixed[]
     */
/*    protected function fetchToplevelMetadataFromSolr(array $queryParams): array
    {
        // Prepare query parameters.
        $params = $queryParams;
        $metadataArray = [];

        // Restrict the fields to the required ones.
        $params['fields'] = '*';

        // Set filter query to just get toplevel documents.
        $params['filterquery'][] = ['query' => 'toplevel:true'];

        $this->createQuery();
        $selectQuery = $this->query;
        $selectQuery->setQuery($params["query"]??"*:*");
        $selectQuery->createFilterQuery('onlyTopLevel')->setQuery("toplevel:true");

        $grouping = $selectQuery->getGrouping();
        $grouping->addField('uid');
        $grouping->setLimit(100); // Results in group (TODO: check)
        $grouping->setNumberOfGroups(true);

        $connection = $this->getConnection();
        $result = $connection->execute($this->query);

        $resultSet = [
            'documents' => [],
            'numberOfToplevels' => 0,
            'numFound' => 0,
        ];

        // TODO: Call to an undefined method Solarium\Core\Query\Result\ResultInterface::getGrouping().
        // @phpstan-ignore-next-line
        $uidGroup = $result->getGrouping()->getGroup('uid');

        foreach ($uidGroup as $group) {
            foreach ($group as $record) {
                $resultSet['documents'][] = $record;
            }
        }

        foreach ($resultSet['documents'] as $doc) {
            $metadataArray[$doc['uid']] = $doc;
        }

        return $metadataArray;
    }*/
}
