<?php
namespace Slub\SlubDigitalcollections\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Alexander Bigga <alexander.bigga@slub-dresden.de>
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

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class KitodoDocumentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Local copy of settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Array of all document structures
     *
     * @var array
     */
    protected $documentStructures;

    /**
     * Find all documents with given collection from Solr
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $collections
     * @param array $settings
     * @param array $searchParams
     * @return array
     */
    public function findSolrByCollection($collections, $settings, $searchParams) {

        $this->settings = $settings;
        $this->documentStructures = $this->getDocumentStructures();

        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $configuration = [
            'timeout' => $this->settings['solr']['timeout'],
            'headers' => [
                'Content-type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json'
            ],
        ];

        $collecionsOrString = '';
        foreach ($collections as $index => $collection) {
            $collecionsOrString .= (($index > 0) ? ' OR ' : '') . '"' . $collection->getIndexName() . '"';
        }
        $metadataQuery = '';
        $highlight = [];
        if (!empty($searchParams['query'])) {
            // replace-statement from Kitodo.Presentation Solr::escapeQuery($query)
            $queryString = \Kitodo\Dlf\Common\Solr::escapeQuery($searchParams['query']);
            if ($searchParams['fulltext'] == '1') {
                // fulltext search
                $filterQuery ='collection_faceting:(' . $collecionsOrString .')';
                $highlight = [
                    'hl' => 'on',
                    'hl.fl' => 'fulltext',
                    'hl.method' => 'fastVector'
                ];
                $filterList = 'uid,id,page,thumbnail,toplevel';
                $solrQuery = 'fulltext:("' . $queryString . '")';
            } else {
                // metadata search
                $filterQuery = '';
                $filterList = 'uid,page,title,thumbnail,partof,toplevel,type';
                $solrQuery = 'collection:(' . $collecionsOrString . ') AND ' . $queryString;
            }
        } else {
            // collection listing
            $filterQuery = 'toplevel:true AND partof:0';
            $filterList = 'uid,toplevel';
            $solrQuery = 'collection:(' . $collecionsOrString . ')';
        }

        // order the results as given or by title as default
        if (!empty($searchParams['orderBy'])) {
            $querySort = $searchParams['orderBy'] . ' ' . $searchParams['order'];
        } else {
            $querySort = 'year_usi asc, title_usi asc';
        }

        $configuration['form_params'] = [
            'q' => $solrQuery,
            'fq' => $filterQuery,
            'fl' => $filterList,
            'rows' => 10000,
            'wt' => 'json',
            'json.nl' => 'flat',
            'omitHeader' => 'true',
            'sort' => $querySort
        ];
        if (count($highlight)>0) {
            $configuration['form_params'] += $highlight;
        }

        if (($result = $this->getSolrCache('findSolrByCollection' . serialize($configuration))) === false) {
            $response = $requestFactory->request($this->settings['solr']['host'] . '/select?', 'POST', $configuration);
            $content  = $response->getBody()->getContents();
            $result = json_decode($content, true);
            if ($result) {
                $this->setSolrCache('findSolrByCollection' . serialize($configuration), $result);
            }
        }

        $documents = [];
        // Only continue if we got a valid result
        if ($result) {
            // Initialize array
            $documentSet = [];
            // flat array with uids from Solr search
            $documentSet = array_unique(array_column($result['response']['docs'], 'uid'));

            if (empty($documentSet)) {
                // return nothing found
                return ['solrResults' => [], 'documents' => []];
            }

            $allDocuments = $this->findAllByUids($documentSet);
            $children = $this->findSolrByPartof($documentSet, $searchParams);

            foreach ($result['response']['docs'] as $doc) {
                if ($doc['toplevel'] === false) {
                    // this maybe a chapter, article, ..., year
                    if ($doc['type'] == 'year') {
                        continue;
                    }
                    $document = $allDocuments[$doc['uid']];
                    if (!empty($doc['page'])) {
                        // it's probably a fulltext or metadata search
                        $searchResult = [];
                        $searchResult['page'] = $doc['page'];
                        $searchResult['thumbnail'] = $doc['thumbnail'];
                        $searchResult['structure'] = $doc['type'];
                        $searchResult['title'] = $doc['title'];
                        if ($searchParams['fulltext'] == '1') {
                            $hightlightSnippet = $result['highlighting'][$doc['id']]['fulltext'];
                            $searchResult['highlighting'] = $hightlightSnippet;
                            // get the emphasized word between <em></em> to take it for word highlighting
                            $highlightWord = substr($hightlightSnippet[0], strpos($hightlightSnippet[0], '<em>') + 4);
                            $highlightWord = substr($highlightWord, 0, strpos($highlightWord, '</em>'));
                            $searchResult['highlight_word'] = $highlightWord;
                        }
                        $document['searchResults'][] = $searchResult;
                    }
                } else if ($doc['toplevel'] === true) {
                    $document = $allDocuments[$doc['uid']];
                    if ($document) {
                        if ($searchParams['fulltext'] == '1') {
                            // page is only set on fulltext search
                            $searchResult = [];
                            $searchResult['page'] = $doc['page'];
                            $searchResult['thumbnail'] = $doc['thumbnail'];
                            $hightlightSnippet = $result['highlighting'][$doc['id']]['fulltext'];
                            $searchResult['highlighting'] = $hightlightSnippet;
                            // get the emphasized word between <em></em> to take it for word highlighting
                            $highlightWord = substr($hightlightSnippet[0], strpos($hightlightSnippet[0], '<em>') + 4);
                            $highlightWord = substr($highlightWord, 0, strpos($highlightWord, '</em>'));
                            $searchResult['highlight_word'] = $highlightWord;
                            $document['searchResults'][] = $searchResult;
                        } else {
                            $document['page'] = 1;
                            if (empty($searchParams['query'])) {
                                // find all child documents but not on active search
                                if (is_array($children[$document['uid']])) {
                                    $document['children'] = $this->findAllByUids($children[$document['uid']]);
                                }
                            }
                        }
                    }
                }
                $documents[] = $document;
            }
        }
        return ['solrResults' => $result['response']['docs'], 'documents' => $documents];
    }

    /**
     * Find all parent documents from Solr
     *
     * @param array $partOfUids
     * @param array $settings
     * @param array $searchParams
     * @return objects
     */
    private function findSolrByPartof($partOfUids, $searchParams) {

        if (($documents = $this->getSolrCache('findSolrByPartof' . serialize($partOfUids))) === false) {
            /** @var RequestFactory $requestFactory */
            $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
            $configuration = [
                'timeout' => $this->settings['solr']['timeout'],
                'headers' => [
                    'Content-type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ],
            ];

            // split partOfUids into 1000 values as maximum as Solr has default maxBooleanClauses=1024
            for ($i=0; $i<count($partOfUids); $i+=1000) {
                $partOfUidsSlices[$i] = array_slice($partOfUids, $i, 1000);
            }

            $documents = [];
            foreach ($partOfUidsSlices as $partOfUidsSlice) {
                $partOfSerialize = '';
                foreach ($partOfUidsSlice as $uid) {
                    $partOfSerialize .= ' ' . $uid;
                }

                $configuration['form_params'] = [
                    'q' => '*.*',
                    'fq' => 'partof:(' . (trim($partOfSerialize)) . ')',
                    'fl' => 'uid,page,partof',
                    'rows' => 10000,
                    'wt' => 'json',
                    'json.nl' => 'flat',
                    'omitHeader' => 'true',
                    'sort' => 'year_usi asc, title_usi asc'
                ];
                $response = $requestFactory->request($this->settings['solr']['host'] . '/select?', 'POST', $configuration);
                $content  = $response->getBody()->getContents();
                $result = json_decode($content, true);
                if ($result) {
                    foreach ($result['response']['docs'] as $doc) {
                        $documents[$doc['partof']][] = $doc['uid'];
                    }
                }
            }
            if ($result) {
                $this->setSolrCache('findSolrByPartof' . serialize($partOfUids), $documents);
            }
        }
        return $documents;
    }

    /**
     * Finds all collections
     *
     * @param string $uids separated by comma
     *
     * @return objects
     */
    private function findAllByUids($uids)
    {
        // get all documents from db we are talking about
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_dlf_documents');
        // Fetch document info for UIDs in $documentSet from DB
        $kitodoDocuments = $queryBuilder
            ->select(
                'tx_dlf_documents.uid AS uid',
                'tx_dlf_documents.title AS title',
                'tx_dlf_documents.structure AS structure',
                'tx_dlf_documents.thumbnail AS thumbnail',
                'tx_dlf_documents.metadata AS metadata',
                'tx_dlf_documents.volume_sorting AS volumeSorting',
                'tx_dlf_documents.mets_orderlabel AS metsOrderlabel',
                'tx_dlf_documents.partof AS partOf'
            )
            ->from('tx_dlf_documents')
            ->where(
                $queryBuilder->expr()->in('tx_dlf_documents.pid', $this->settings['storagePid']),
                $queryBuilder->expr()->in('tx_dlf_documents.uid', $uids)
            )
            ->addOrderBy('tx_dlf_documents.volume_sorting', 'asc')
            ->addOrderBy('tx_dlf_documents.mets_orderlabel', 'asc')
            ->execute();

        $allDocuments = [];
        // Process documents in a usable array structure
        while ($resArray = $kitodoDocuments->fetch()) {
            $resArray['metadata'] = unserialize($resArray['metadata']);
            $resArray['structure'] = $this->documentStructures[$resArray['structure']];
            $allDocuments[$resArray['uid']] = $resArray;
        }

        return $allDocuments;
    }

    /**
     * Get all document structures as array
     *
     * @return array
     */
    private function getDocumentStructures()
    {
        // make lookup-table of structures uid -> indexName
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_dlf_structures');
        // Fetch document info for UIDs in $documentSet from DB
        $kitodoStructures = $queryBuilder
            ->select(
                'tx_dlf_structures.uid AS uid',
                'tx_dlf_structures.index_name AS indexName'
            )
            ->from('tx_dlf_structures')
            ->execute();

        $allStructures = $kitodoStructures->fetchAll();
        // make lookup-table uid -> indexName
        $allStructures = array_column($allStructures, 'indexName', 'uid');

        return $allStructures;
    }

    /**
     * get Cache Hit for $query
     *
     * @param string $query
     * @return array|false
     */
    private function getSolrCache(string $query) {

        $cacheIdentifier = md5($query);
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('slub_digitalcollections_collections');
        $cacheHit = $cache->get($cacheIdentifier);

        return $cacheHit;
    }

    /**
     * set Cache for $query
     *
     * @param string $query
     * @param array $value
     * @return void
     */
    private function setSolrCache(string $query, array $value) {

        $cacheIdentifier = md5($query);
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('slub_digitalcollections_collections');

        // Save value in cache
        $cache->set($cacheIdentifier, $value);

    }
}
