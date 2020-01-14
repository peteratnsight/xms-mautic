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
 * Subscription
 */
class Subscription extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * gender
	 *
	 * @var int
	 */
	protected $gender = 0;

	/**
	 * firstname
	 *
	 * @var string
	 */
	protected $firstname = '';

	/**
	 * lastname
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $lastname = '';

	/**
	 * company
	 *
	 * @var string
	 */
	protected $company = '';

	/**
	 * email
	 *
	 * @var string
	 * @validate NotEmpty
	 * @validate EmailAddress
	 * @validate \Xms\XmsMautic\Validation\Validator\UniqueValidator
	 */
	protected $email = '';

	/**
	 * hash
	 *
	 * @var string
	 */
	protected $hash = '';


	/**
	 * phone
	 *
	 * @var string
	 */
	protected $phone = '';

	/**
	 * position
	 *
	 * @var string
	 */
	protected $position = '';

	/**
	 * eventContext
	 *
	 * @var string
	 */
	protected $eventContext = '';

	/**
	 * mauticContext
	 *
	 * @var int
	 */
	protected $mauticContext = 0;

	/**
	 * mauticContextVariants
	 *
	 * @var string
	 */
	protected $mauticContextVariants = '';

	/**
	 * lastSyncdate
	 *
	 * @var int
	 */
	protected $lastSyncdate = 0;

	/**
	 * lastUserupdate
	 *
	 * @var int
	 */
	protected $lastUserupdate = 0;

	/**
	 * remoteIp
	 *
	 * @var string
	 */
	protected $remoteIp = '';

	/**
	 * validstate
	 *
	 * @var bool
	 */
	protected $validstate = FALSE;

	/**
	 * optinstate
	 *
	 * @var string
	 */
	protected $optinstate = 0;

	/**
	 * history
	 *
	 * @var string
	 */
	protected $history = '';

	/**
	 * sysmailLog
	 *
	 * @var string
	 */
	protected $sysmailLog = '';

	/**
	 * subscriptions
	 *
	 * @var string
	 */
	protected $subscriptions = '';

	/**
	 * Returns the gender
	 *
	 * @return int $gender
	 */
	public function getGender() {
		return $this->gender;
	}

	/**
	 * Sets the gender
	 *
	 * @param int $gender
	 * @return void
	 */
	public function setGender($gender) {
		$this->gender = $gender;
	}

	/**
	 * Returns the firstname
	 *
	 * @return string $firstname
	 */
	public function getFirstname() {
		return $this->firstname;
	}

	/**
	 * Sets the firstname
	 *
	 * @param string $firstname
	 * @return void
	 */
	public function setFirstname($firstname) {
		$this->firstname = $firstname;
	}

	/**
	 * Returns the lastname
	 *
	 * @return string $lastname
	 */
	public function getLastname() {
		return $this->lastname;
	}

	/**
	 * Sets the lastname
	 *
	 * @param string $lastname
	 * @return void
	 */
	public function setLastname($lastname) {
		$this->lastname = $lastname;
	}

	/**
	 * Returns the company
	 *
	 * @return string $company
	 */
	public function getCompany() {
		return $this->company;
	}

	/**
	 * Sets the company
	 *
	 * @param string $company
	 * @return void
	 */
	public function setCompany($company) {
		$this->company = $company;
	}

	/**
	 * Returns the email
	 *
	 * @return string $email
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Sets the email
	 *
	 * @param string $email
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Returns the hash
	 *
	 * @return string $hash
	 */
	public function getHash() {
		return $this->hash;
	}

	/**
	 * Sets the hash
	 *
	 * @param string $hash
	 * @return void
	 */
	public function setHash($hash) {
		$this->hash = $hash;
	}


	/**
	 * Returns the phone
	 *
	 * @return string $phone
	 */
	public function getPhone() {
		return $this->phone;
	}

	/**
	 * Sets the phone
	 *
	 * @param string $phone
	 * @return void
	 */
	public function setPhone($phone) {
		$this->phone = $phone;
	}

	/**
	 * Returns the position
	 *
	 * @return string $position
	 */
	public function getPosition() {
		return $this->position;
	}

	/**
	 * Sets the position
	 *
	 * @param string $position
	 * @return void
	 */
	public function setPosition($position) {
		$this->position = $position;
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

	/**
	 * Returns the mauticContext
	 *
	 * @return int $mauticContext
	 */
	public function getMauticContext() {
		return $this->mauticContext;
	}

	/**
	 * Sets the mauticContext
	 *
	 * @param int $mauticContext
	 * @return void
	 */
	public function setMauticContext($mauticContext) {
		$this->mauticContext = $mauticContext;
	}

	/**
	 * Returns the mauticContextVariants
	 *
	 * @return string $mauticContextVariants
	 */
	public function getMauticContextVariants() {
		return $this->mauticContextVariants;
	}

	/**
	 * Sets the mauticContextVariants
	 *
	 * @param string $mauticContextVariants
	 * @return void
	 */
	public function setMauticContextVariants($mauticContextVariants) {
		$this->mauticContextVariants = $mauticContextVariants;
	}

	/**
	 * Returns the lastSyncdate
	 *
	 * @return int $lastSyncdate
	 */
	public function getLastSyncdate() {
		return $this->lastSyncdate;
	}

	/**
	 * Sets the lastSyncdate
	 *
	 * @param int $lastSyncdate
	 * @return void
	 */
	public function setLastSyncdate($lastSyncdate) {
		$this->lastSyncdate= $lastSyncdate;
	}
	/**
	 * Returns the lastUserupdate
	 *
	 * @return int $lastUserupdate
	 */
	public function getLastUserupdate() {
		return $this->lastUserupdate;
	}

	/**
	 * Sets the lastUserupdate
	 *
	 * @param int $lastUserupdate
	 * @return void
	 */
	public function setLastUserupdate($lastUserupdate) {
		$this->lastUserupdate= $lastUserupdate;
	}





	/**
	 * __construct
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
	}

	/**
	 * Returns the remoteIp
	 *
	 * @return string $remoteIp
	 */
	public function getRemoteIp() {
		return $this->remoteIp;
	}

	/**
	 * Sets the remoteIp
	 *
	 * @param string $remoteIp
	 * @return void
	 */
	public function setRemoteIp($remoteIp) {
		$this->remoteIp = $remoteIp;
	}

	/**
	 * Returns the validstate
	 *
	 * @return bool $validstate
	 */
	public function getValidstate() {
		return $this->validstate;
	}

	/**
	 * Sets the validstate
	 *
	 * @param bool $validstate
	 * @return void
	 */
	public function setValidstate($validstate) {
		$this->validstate = $validstate;
	}

	/**
	 * Returns the boolean state of validstate
	 *
	 * @return bool
	 */
	public function isValidstate() {
		return $this->validstate;
	}

	/**
	 * Returns the optinstate
	 *
	 * @return string $optinstate
	 */
	public function getOptinstate() {
		return $this->optinstate;
	}

	/**
	 * Sets the optinstate
	 *
	 * @param string $optinstate
	 * @return void
	 */
	public function setOptinstate($optinstate) {
		$this->optinstate = $optinstate;
	}


	/**
	 * Returns the history
	 *
	 * @return int $history
	 */
	public function getHistory() {
		return $this->history;
	}

	/**
	 * Sets the history
	 *
	 * @param int $history
	 * @return void
	 */
	public function setHistory($history) {
		$this->history= $history;
	}

	/**
	 * Returns the sysmailLog
	 *
	 * @return int $sysmailLog
	 */
	public function getSysmailLog() {
		return $this->sysmailLog;
	}

	/**
	 * Sets the sysmailLog
	 *
	 * @param int $sysmailLog
	 * @return void
	 */
	public function setSysmailLog($sysmailLog) {
		$this->sysmailLog= $sysmailLog;
	}


	/**
	 * Returns the subscriptions
	 *
	 * @return array $subscriptions
	 */
	public function getSubscriptions() {
		return explode(',', $this->subscriptions);
	}

	/**
	 * Sets the subscriptions
	 *
	 * @param array $subscriptions
	 * @return void
	 */
	public function setSubscriptions(array $subscriptions) {
		$this->subscriptions = implode(',', $subscriptions);
	}



}