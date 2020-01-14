<?php
namespace Xms\XmsMautic\Domain\Repository;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Samir Rachidi <sr@plusb.de>, Plus B
 *
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

/**
 *
 * The repository for the fileData
 *
 */
class FileDataRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	public function getFile($sha1ident) {
		$query = $this->createQuery();
		$sql = "SELECT * FROM sys_file WHERE sha1='".$sha1ident."'";

		$query->statement( $sql );

		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		return $query->execute();
	}

    public function getStoragePid(){
        return reset($this->createQuery()->getQuerySettings()->getStoragePageIds());
    }
}