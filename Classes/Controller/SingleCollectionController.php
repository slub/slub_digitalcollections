<?php
namespace Slub\SlubDigitalcollections\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Alexander Bigga <typo3@slub-dresden.de>
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

use Kitodo\Dlf\Common\SolrPaginator;
use Kitodo\Dlf\Controller\AbstractController;
use Kitodo\Dlf\Domain\Repository\StructureRepository;
use Kitodo\Dlf\Domain\Repository\CollectionRepository;
use Kitodo\Dlf\Domain\Repository\MetadataRepository;
use TYPO3\CMS\Core\Pagination\SimplePagination;

class SingleCollectionController extends AbstractController
{

    /**
     * StructureRepository
     *
     * @var StructureRepository
     */
    protected $structureRepository;

    /**
     * CollectionRepository
     *
     * @var CollectionRepository
     */
    protected $collectionRepository;

    /**
     * MetadataRepository
     *
     * @var MetadataRepository
     */
    protected $metadataRepository;

	/**
     * @param StructureRepository $structureRepository
     */
    public function injectStructureRepository(StructureRepository $structureRepository)
    {
        $this->structureRepository = $structureRepository;
    }

	/**
     * @param CollectionRepository $collectionRepository
     */
    public function injectCollectionRepository(CollectionRepository $collectionRepository)
    {
        $this->collectionRepository = $collectionRepository;
    }

	/**
     * @param MetadataRepository $metadataRepository
     */
    public function injectMetadataRepository(MetadataRepository $metadataRepository)
    {
        $this->metadataRepository = $metadataRepository;
    }

    /**
     * initializeAction
     *
     * @return void
     */
    protected function initializeAction()
    {
    }

    /**
     * action search
     *
     * @return void
     */
    public function searchAction()
    {

        // if search was triggered, get search parameters from POST variables
        $searchParams = $this->getParametersSafely('searchParameter');

        // output is done by show action
        $this->redirect('show', null, null, ['searchParameter' => $searchParams]);
    }

    /**
     * action show
     *
     * @return void
     */
    public function showAction()
    {
        // if search was triggered, get search parameters from POST variables
        $searchParams = $this->getParametersSafely('searchParameter');

        // set default sorting
        if (!isset($searchParams['orderBy'])) {
            $searchParams['orderBy'] = 'title_usi';
            $searchParams['order'] = 'asc';
        }

        // Get current page from request data because the parameter is shared between plugins
        $currentPage = $this->requestData['page'] ?? 1;

        $collections = $this->collectionRepository->findAllByUids(GeneralUtility::intExplode(',', $this->settings['collections'], true));

        // get all metadata records to be shown in results
        $listedMetadata = $this->metadataRepository->findByIsListed(true);

        // find all documents from Solr
        $solrResults = $this->documentRepository->findSolrByCollections($collections, $this->settings, $searchParams, $listedMetadata);

        $itemsPerPage = $this->settings['list']['paginate']['itemsPerPage'];
        if (empty($itemsPerPage)) {
            $itemsPerPage = 25;
        }
        $solrPaginator = new SolrPaginator($solrResults, $currentPage, $itemsPerPage);
        $simplePagination = new SimplePagination($solrPaginator);

        $pagination = $this->buildSimplePagination($simplePagination, $solrPaginator);
        $this->view->assignMultiple([ 'pagination' => $pagination, 'paginator' => $solrPaginator ]);

        // get all sortable Metadata from Kitodo.Presentation
        $metadata = $this->metadataRepository->findByIsSortable(true);

        $this->view->assign('documents', $solrResults);
        $this->view->assign('metadata', $metadata);
        $this->view->assign('page', $currentPage);
        $this->view->assign('lastSearch', $searchParams);
        $this->view->assign('rawResults', $solrResults->getSolrResults());

    }
}
