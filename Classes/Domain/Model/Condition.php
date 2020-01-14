<?php
namespace Xms\XmsMautic\Domain\Model;


/***************************************************************
 ***************************************************************/

/**
 * Condition
 */
class Condition extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * ifsubject
	 *
	 * @var string
	 */
	protected $ifsubject = '';


	/**
	 * ifaction
	 *
	 * @var string
	 */
	protected $ifaction = '';


	/**
	 * ifdetails
	 *
	 * @var string
	 */
	protected $ifdetails = '';

	/**
	 * ifvalue
	 *
	 * @var string
	 */
	protected $ifvalue = '';

	/**
	 * thenaction
	 *
	 * @var string
	 */
	protected $thenaction = '';

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
	 * Returns the ifsubject
	 *
	 * @return string $ifsubject
	 */
	public function getIfsubject() {
		return $this->ifsubject;
	}

	/**
	 * Sets the ifsubject
	 *
	 * @param string $ifsubject
	 * @return void
	 */
	public function setIfsubject($ifsubject) {
		$this->ifsubject = $ifsubject;
	}

	/**
	 * Returns the ifaction
	 *
	 * @return string $ifaction
	 */
	public function getIfaction() {
		return $this->ifaction;
	}

	/**
	 * Sets the ifaction
	 *
	 * @param string $ifaction
	 * @return void
	 */
	public function setIfaction($ifaction) {
		$this->ifaction= $ifaction;
	}

	/**
	 * Returns the ifdetails
	 *
	 * @return string $ifdetails
	 */
	public function getIfdetails() {
		return $this->ifdetails;
	}

	/**
	 * Sets the ifdetails
	 *
	 * @param string $ifdetails
	 * @return void
	 */
	public function setIfdetails($ifdetails) {
		$this->ifdetails= $ifdetails;
	}


	/**
	 * Returns the ifvalue
	 *
	 * @return string $ifvalue
	 */
	public function getIfvalue() {
		return $this->ifvalue;
	}

	/**
	 * Sets the ifvalue
	 *
	 * @param string $ifvalue
	 * @return void
	 */
	public function setIfvalue($ifvalue) {
		$this->ifvalue= $ifvalue;
	}

	/**
	 * Returns the thenaction
	 *
	 * @return string $thenaction
	 */
	public function getThenaction() {
		return $this->thenaction;
	}

	/**
	 * Sets the thenaction
	 *
	 * @param string $thenaction
	 * @return void
	 */
	public function setThenaction($thenaction) {
		$this->thenaction= $thenaction;
	}
}