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

class KitodoDocumentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    // Order by default by title_sorting
    protected $defaultOrderings = array(
        'title_sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    );


    /**
     * Find all documents with given collection
     *
     * @param string $collections
     * @return objects
     */
    public function findSolrByCollection($collection) {

        $solrHost = 'http://sdvsolrsachsendigital.slub-dresden.de:8983/solr/dlfCore1';

        $context = stream_context_create(array(
            'http' => array(
                'timeout' => 5
                )
            )
        );
        $query = $solrHost . '/select?fq=toplevel%3Atrue%20AND%20partof%3A0&q=collection:(' . urlencode('"' . $collection->getIndexName() . '"') . ')&fl=uid&rows=10000&wt=csv';
        $apiAnswer = file_get_contents($query, false, $context);
        $uids = explode("\n", $apiAnswer);

        $documents = $this->findAllByUids($uids);

        foreach ($documents as $document) {
            // find all child documents
            $children = $this->findByPartof($document->getUid());
            $document->setChildren($children->toArray());
        }

        return $documents;
    }


    /**
     * Finds all datasets
     *
     * @param array
     *
     * @return objects
     */
    public function findAllByUids($uids)
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->in('uid', $uids);

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        return $query->execute();
    }


    /**
     * Find all records younger than the given timestamp
     *
     * @param int $timestamp
     * @return objects
     */
    public function findYoungerThan($timestamp) {

      $query = $this->createQuery();
      $constraints = [];

      $constraints[] = $query->greaterThan('tstamp', $timestamp);

      if (count($constraints)) {
          $query->matching($query->logicalAnd($constraints));
      }

      return $query->execute();
    }

}
