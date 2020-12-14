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

        $collection = $this->kitodoCollectionsRepository->findByUid($this->settings['collections']);

        $documents = $this->kitodoDocumentRepository->findSolrByCollection($collection);

        // pass the currentPage to the fluid template to calculate current index of search result
        if ($this->request->hasArgument('@widget_0')) {
            $widgetPage = $this->request->getArgument('@widget_0');
        } else {
            $widgetPage = ['currentPage' => 1];
        }

        $this->view->assign('collection', $collection);
        $this->view->assign('documents', $documents);
        $this->view->assign('widgetPage', $widgetPage);


    }
}
