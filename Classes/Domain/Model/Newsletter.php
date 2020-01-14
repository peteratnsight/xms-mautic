<?php
namespace Xms\XmsMautic\Domain\Model;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016
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
 * Newsletter
 */
class Newsletter extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * mauticId
	 *
	 * @var int
	 */
	protected $mauticId = 0;

	/**
	 * name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * languageCode
	 *
	 * @var string
	 */
	protected $languageCode = '';


	/**
	 * eventContext
	 *
	 * @var string
	 */
	protected $eventContext = '';


	/**
	 * Returns the mauticId
	 *
	 * @return int $mauticId
	 */
	public function getMauticId() {
		return $this->mauticId;
	}

	/**
	 * Sets the mauticId
	 *
	 * @param int $mauticId
	 * @return void
	 */
	public function setMauticId($mauticId) {
		$this->mauticId = $mauticId;
	}

	/**
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the languageCode
	 *
	 * @return string $languageCode
	 */
	public function getLanguageCode() {
		return $this->languageCode;
	}

	/**
	 * Sets the languageCode
	 *
	 * @param string $languageCode
	 * @return void
	 */
	public function setLanguageCode($languageCode) {
		$this->languageCode = $languageCode;
	}

	/**
	 * Returns the eventContext
	 *
	 * @return string $eventContext
	 */
	public function getEventContext() {
		return $this->eventContext;
	}

	/**
	 * Sets the eventContext
	 *
	 * @param string $eventContext
	 * @return void
	 */
	public function setEventContext($eventContext) {
		$this->eventContext = $eventContext;
	}


}