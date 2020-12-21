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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

class KitodoDocumentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Find all documents with given collection from Solr
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $collections
     * @param array $settings
     * @param array $searchParams
     * @return array
     */
    public function findSolrByCollection($collections, $settings, $searchParams) {

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

        $documents = [];
        if (($result = $this->getSolrCache($query)) === false) {
            $apiAnswer = file_get_contents($query, false, $context);
            $result = json_decode($apiAnswer, true);
            if ($result) {
                $this->setSolrCache($query, $result);
            }
        }

        // Only continue if we got a valid result
        if ($result) {
            // as extbase does not keep the sorting of the uids, we have to do the expensive foreach() way...
            foreach ($result['response']['docs'] as $doc) {
                if ($doc['toplevel'] === false) {
                    // this maybe a chapter, article, ..., year
                    if ($doc['type'] == 'year') {
                        continue;
                    }
                    $document = $this->findByUid($doc['uid']);
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
                        $document->addSearchResult($searchResult);
                    }
                } else if ($doc['toplevel'] === true) {
                    $document = $this->findByUid($doc['uid']);
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
                            $document->addSearchResult($searchResult);
                        } else {
                            $document->setPage(1);
                            if (empty($searchParams['query'])) {
                                // find all child documents but not on active search
                                $children = $this->findSolrByPartof($doc['uid'], $settings, $searchParams);
                                $document->setChildren($children);
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
     * @param array $partOfUid
     * @param array $settings
     * @param array $searchParams
     * @return objects
     */
    public function findSolrByPartof($partOfUid, $settings, $searchParams) {

        $context = stream_context_create(array(
            'http' => array(
                'timeout' => $settings['solr']['timeout']
                )
            )
        );

        $filterQuery = 'fq=partof%3A' . $partOfUid;
        $filterList = 'fl=uid';
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
            // as extbase does not keep the sorting of the uids, we have to do the expensive foreach() way...
            foreach ($result['response']['docs'] as $doc) {
                $document = $this->findByUid($doc['uid']);
                $documents[] = $document;
            }
        }
        return $documents;
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
