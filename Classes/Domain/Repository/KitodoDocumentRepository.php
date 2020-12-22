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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

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
        $this->documentStructures = $this->getDocumentStructures();;

        $context = stream_context_create(array(
            'http' => array(
                'timeout' => $settings['solr']['timeout']
                )
            )
        );

        $collecionsOrString = '';
        foreach ($collections as $index => $collection) {
            $collecionsOrString .= (($index > 0) ? ' OR ' : '') . '"' . $collection->getIndexName() . '"';
        }
        $metadataQuery = '';
        if (!empty($searchParams['query'])) {
            // replace-statement from Kitodo.Presentation Solr::escapeQuery($query)
            $queryString = urlencode(\Kitodo\Dlf\Common\Solr::escapeQuery($searchParams['query']));
            if ($searchParams['fulltext'] == '1') {
                // fulltext search
                //webapp=/solr path=/select params={q=fulltext:(haus)&json.nl=flat&omitHeader=true&fl=*,score&start=0&sort=score+desc&fq=collection_faceting:("LDP\:+Bestände+der+Sächsischen+Staatskapelle\/Staatsoper+Dresden"+OR+"FakeValueForDistinction")&rows=0&wt=json} hits=3 status=0 QTime=0
                $filterQuery ='fq=collection_faceting:(' . urlencode($collecionsOrString) .')';
                $highlight = 'hl=on&hl.fl=fulltext&hl.method=fastVector';
                $filterList = 'fl=uid,id,page,thumbnail,toplevel' . '&' . $highlight;
                $solrQuery = 'q=fulltext:("' . $queryString . '")';
            } else {
                // metadata search
                $filterQuery = 'fq=';
                $filterList = 'fl=uid,page,title,thumbnail,partof,toplevel,type';
                $solrQuery = 'q=collection:(' . urlencode($collecionsOrString) . ')%20AND%20' . $queryString;
            }
        } else {
            // collection listing
            $filterQuery = 'fq=toplevel%3Atrue%20AND%20partof%3A0';
            $filterList = 'fl=uid,toplevel';
            $solrQuery = 'q=collection:(' . urlencode($collecionsOrString) . ')';
        }

        // get 10.000 results maximum in JSON
        $query = $settings['solr']['host'] . '/select?' . $solrQuery . '&' . $filterQuery . '&' . $filterList . '&rows=10000&wt=json&omitHeader=true';

        // order the results as given or by title as default
        if (!empty($searchParams['orderBy'])) {
            $query .= '&sort=' . $searchParams['orderBy'] . '%20' . $searchParams['order'];
        } else {
            $query .= '&sort=year_usi%20asc%2C%20title_usi%20asc';
        }

        if (($result = $this->getSolrCache($query)) === false) {
            $apiAnswer = file_get_contents($query, false, $context);
            $result = json_decode($apiAnswer, true);
            if ($result) {
                $this->setSolrCache($query, $result);
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
                                $children = $this->findSolrByPartof($documentSet, $settings, $searchParams);
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
    public function findSolrByPartof($partOfUids, $settings, $searchParams) {

        $context = stream_context_create(array(
            'http' => array(
                'timeout' => $settings['solr']['timeout']
                )
            )
        );

        $filterQuery = '';
        // dirty hack to limit the URI size --> have to switch to solarium or POST!
        for ($i=0; $i < count($partOfUids) && $i < 1000 ; $i++) {
            $filterQuery .= ' ' . $partOfUids[$i];
        }
        $filterQuery .= ')';
        $filterQuery = 'fq=partof%3A(' . urlencode(trim($filterQuery));
        $filterList = 'fl=uid,page,partof';
        $solrQuery = 'q=*.*';

        // get 10.000 results maximum in JSON
        $query = $settings['solr']['host'] . '/select?' . $solrQuery . '&' . $filterQuery . '&' . $filterList . '&rows=10000&wt=json&json.nl=flat&omitHeader=true';

        // order the results as given or by title as default
        if (!empty($searchParams['orderBy'])) {
            $query .= '&sort=' . $searchParams['orderBy'] . '%20' . $searchParams['order'];
        } else {
            $query .= '&sort=year_usi%20asc%2C%20title_usi%20asc';
        }

        $documents = [];
        if (($result = $this->getSolrCache($query)) === false) {
            $apiAnswer = file_get_contents($query, false, $context);
            $result = json_decode($apiAnswer, true);
            if ($result) {
                $this->setSolrCache($query, $result);
            }
        }

        if ($result) {
            foreach ($result['response']['docs'] as $doc) {
                $documents[$doc['partof']][] = $doc['uid'];
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
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_dlf_documents');
        // Fetch document info for UIDs in $documentSet from DB
        $kitodoStructures = $queryBuilder
            ->select(
                'tx_dlf_structures.uid AS uid',
                'tx_dlf_structures.index_name AS indexName'
            )
            ->from('tx_dlf_structures')
            ->where(
                $queryBuilder->expr()->in('tx_dlf_structures.pid', $this->settings['storagePid'])
            )
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
        $cache = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('slub_digitalcollections_collections');
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
        $cache = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('slub_digitalcollections_collections');

        // Save value in cache
        $cache->set($cacheIdentifier, $value);

    }
}
