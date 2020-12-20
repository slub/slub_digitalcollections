<?php
namespace Slub\SlubDigitalcollections\Controller;

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

use Kitodo\Dlf\Common\Document;
use Slub\SlubDigitalcollections\Domain\Repository\KitodoDocumentRepository;
use Slub\SlubDigitalcollections\Domain\Repository\KitodoStructuresRepository;
use Slub\SlubDigitalcollections\Domain\Repository\KitodoCollectionsRepository;
use Slub\SlubDigitalcollections\Domain\Repository\KitodoMetadataRepository;

class SingleCollectionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * kitodoDocumentRepository
     *
     * @var \Slub\SlubDigitalcollections\Domain\Repository\KitodoDocumentRepository
     */
    protected $kitodoDocumentRepository;

    /**
     * kitodoStructuresRepository
     *
     * @var \Slub\SlubDigitalcollections\Domain\Repository\KitodoStructuresRepository
     */
    protected $kitodoStructuresRepository;

    /**
     * kitodoCollectionsRepository
     *
     * @var \Slub\SlubDigitalcollections\Domain\Repository\KitodoCollectionsRepository
     */
    protected $kitodoCollectionsRepository;

    /**
     * kitodoMetadataRepository
     *
     * @var \Slub\SlubDigitalcollections\Domain\Repository\KitodoMetadataRepository
     */
    protected $kitodoMetadataRepository;

    /**
     * @param \Slub\SlubDigitalcollections\Domain\Repository\KitodoDocumentRepository $kitodoDocumentRepository
     */
    public function injectKitodoDocumentRepository(KitodoDocumentRepository $kitodoDocumentRepository)
    {
        $this->kitodoDocumentRepository = $kitodoDocumentRepository;
    }

	/**
     * @param \Slub\SlubDigitalcollections\Domain\Repository\KitodoStructuresRepository $kitodoStructuresRepository
     */
    public function injectKitodoStructuresRepository(KitodoStructuresRepository $kitodoStructuresRepository)
    {
        $this->kitodoStructuresRepository = $kitodoStructuresRepository;
    }

	/**
     * @param \Slub\SlubDigitalcollections\Domain\Repository\KitodoCollectionsRepository $kitodoCollectionsRepository
     */
    public function injectKitodoCollectionsRepository(KitodoCollectionsRepository $kitodoCollectionsRepository)
    {
        $this->kitodoCollectionsRepository = $kitodoCollectionsRepository;
    }

	/**
     * @param \Slub\SlubDigitalcollections\Domain\Repository\KitodoMetadataRepository $kitodoMetadataRepository
     */
    public function injectKitodoMetadataRepository(KitodoMetadataRepository $kitodoMetadataRepository)
    {
        $this->kitodoMetadataRepository = $kitodoMetadataRepository;
    }

    /**
     * initializeAction
     *
     * @return
     */
    protected function initializeAction()
    {
    }

    /**
     * action show
     *
     * @return void
     */
    public function showAction() {

        // if search was triggered, get search parameters from POST variables
        $searchParams = $this->getParametersSafely('searchParameter');

        // set default sorting
        if (!isset($searchParams['orderBy'])) {
            $searchParams['orderBy'] = 'title_usi';
            $searchParams['order'] = 'asc';
        }

        $collections = $this->kitodoCollectionsRepository->findAllByUids(GeneralUtility::intExplode(',', $this->settings['collections'], true));

        // find all documents from Solr
        $documents = $this->kitodoDocumentRepository->findSolrByCollection($collections, $this->settings, $searchParams);

        // get all sortable Metadata from Kitodo.Presentation
        $metadata = $this->kitodoMetadataRepository->findByIsSortable(true);

        // Pagination of Results
        // pass the currentPage to the fluid template to calculate current index of search result
        $widgetPage = $this->getParametersSafely('@widget_0');
        if (empty($widgetPage)) {
            $widgetPage = ['currentPage' => 1];
        }

        $this->view->assign('collection', $collection);
        $this->view->assign('documents', $documents);
        $this->view->assign('metadata', $metadata);
        $this->view->assign('widgetPage', $widgetPage);
        $this->view->assign('lastSearch', $searchParams);

    }

    /**
     * Safely gets Parameters from request
     * if they exist
     *
     * @param string $parameterName
     *
     * @return null|string
     */
    protected function getParametersSafely($parameterName)
    {
        if ($this->request->hasArgument($parameterName)) {
            return $this->request->getArgument($parameterName);
        }
        return null;
    }

}
