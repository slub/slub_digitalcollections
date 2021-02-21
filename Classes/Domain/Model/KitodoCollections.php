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

class KitodoCollections extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject {

    /**
     * index_name - the index name of the structure
     *
     * @var string
     */
    protected $indexName;

    /**
     * label
     *
     * @var string
     */
    protected $label;

    /**
     * description
     *
     * @var string
     */
    protected $description;

    /**
     * Returns the index_name
     *
     * @return string $indexName
     */
    public function getIndexName() {
        return $this->indexName;
    }

    /**
     * Returns the label
     *
     * @return string $label
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription() {
        return $this->ladescriptionbel;
    }

}
