<?php
namespace Slub\SlubDigitalcollections\Domain\Model;

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

class KitodoDocument extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject {

    /**
     * title - actually not used
     *
     * @var string
     */
    protected $title;

    /**
     * location of METS-file
     *
     * @var string
     */
    protected $location;

    /**
     * URL to thumbnail image
     *
     * @var string
     */
    protected $thumbnail;

    /**
     * Title Metadata
     *
     * @var string
     */
    protected $metadata;

    /**
     * structure
     *
     * @var \Slub\SlubDigitalcollections\Domain\Model\KitodoStructures
     */
    protected $structure;

    /**
     * Parent Document
     *
     * @var \Slub\SlubDigitalcollections\Domain\Model\KitodoDocument
     */
    protected $partof;

    /**
     * Child documents
     *
     * @var array \Slub\SlubDigitalcollections\Domain\Model\KitodoDocument
     */
    protected $children;

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Returns the location
     *
     * @return string $location
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Returns the thumbnail
     *
     * @return string $thumbnail
     */
    public function getThumbnail() {
        return $this->thumbnail;
    }

    /**
     * Returns the metadata
     *
     * @return string $metadata
     */
    public function getMetadata() {
        return unserialize($this->metadata);
    }

    /**
     * Sets the location
     *
     * @param string $location
     * @return void
     */
    public function setLocation($location) {
        $this->location = $location;
    }

    /**
     * Returns the structure
     *
     * @return int $structure
     */
    public function getStructure() {
        return $this->structure;
    }

    /**
     * Returns the parent document
     *
     * @return int $partof
     */
    public function getPartof() {
        return $this->partof;
    }

    /**
     * Returns the child documents
     *
     * @return array \Slub\SlubDigitalcollections\Domain\Model\KitodoDocument $children
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Returns the child documents
     *
     * @return void
     */
    public function setChildren(array $children) {
        $this->children = $children;
    }

}
