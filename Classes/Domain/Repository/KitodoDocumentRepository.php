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

    /**
     * Find all documents with given collection from Solr
     *
     * @param string $collections
     * @param array $settings
     * @param array $searchParams
     * @return objects
     */
    public function findSolrByCollection($collection, $settings, $searchParams) {

        $context = stream_context_create(array(
            'http' => array(
                'timeout' => $settings['solr']['timeout']
                )
            )
        );
        $query = $settings['solr']['host'] . '/select?fq=toplevel%3Atrue%20AND%20partof%3A0&q=collection:(' . urlencode('"' . $collection->getIndexName() . '"') . ')&fl=uid&rows=10000&wt=csv';

        // order if given
        if (!empty($searchParams['orderBy'])) {
            $query .= '&sort=' . $searchParams['orderBy'] . '%20' . $searchParams['order'];
        } else {
            // order by title asc by default
            $query .= '&sort=title_usi%20asc';
        }

        $apiAnswer = file_get_contents($query, false, $context);
        $uids = GeneralUtility::intExplode("\n", $apiAnswer);

        $documents = [];
        // as extbase does not keep the sorting of the uids, we have to do the expensive foreach() way...
        foreach ($uids as $uid) {
            $document = $this->findByUid($uid);
            if ($document) {
                // find all child documents
                $children = $this->findByPartof($uid);
                $document->setChildren($children->toArray());
                $documents[] = $document;
            }
        }

        return $documents;
    }

}
