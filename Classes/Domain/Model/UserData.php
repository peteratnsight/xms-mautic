<?php
namespace Xms\XmsMautic\Domain\Model;

/***************************************************************
 *
 ***************************************************************/
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * UserData
 */
class UserData extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * file
	 *
	 * @var \PlusB\PbDownloadform\Domain\Model\FileReference
     *
	 */
	protected $file = NULL;

    /**
     * Was the checkbox for setting the cookie set?
     *
     * @var bool
     */
    protected $setCookie = FALSE;

    /**
     * We need a pointer to the File-object
     * to store a new FileReference
     *
     *
     * @var \TYPO3\CMS\Core\Resource\File
     *
     */
    protected $originalResource;

    /**
     * the path to the file for saving in the db
     *
     * @var string
     */
    protected $filepath;

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
	 * Returns the telephone
	 *
	 * @return string $telephone
	 */
	public function getTelephone() {
		return $this->telephone;
	}

	/**
	 * Sets the telephone
	 *
	 * @param string $telephone
	 * @return void
	 */
	public function setTelephone($telephone) {
		$this->telephone = $telephone;
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
	 * Returns the zip
	 *
	 * @return string $zip
	 */
	public function getZip() {
		return $this->zip;
	}

	/**
	 * Sets the zip
	 *
	 * @param string $zip
	 * @return void
	 */
	public function setZip($zip) {
		$this->zip = $zip;
	}

	/**
	 * Returns the city
	 *
	 * @return string $city
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * Sets the city
	 *
	 * @param string $city
	 * @return void
	 */
	public function setCity($city) {
		$this->city = $city;
	}

	/**
	 * Returns the file
	 *
	 * @return \PlusB\PbDownloadform\Domain\Model\FileReference $file
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * Sets the file
	 *
	 * @param \PlusB\PbDownloadform\Domain\Model\FileReference $file
	 * @return void
	 */
	public function setFile(\PlusB\PbDownloadform\Domain\Model\FileReference $file) {
		$this->file = $file;
	}

    /**
     * @return \TYPO3\CMS\Core\Resource\File
     */
    public function getOriginalResource() {
        return $this->originalResource;
    }

    /**
     * @param \TYPO3\CMS\Core\Resource\File $originalResource
     */
    public function setOriginalResource(\TYPO3\CMS\Core\Resource\File $originalResource) {
        $this->originalResource = $originalResource;
    }

    /**
     * @return boolean
     */
    public function getSetCookie(){
        return $this->setCookie;
    }

    /**
     * @param boolean $setCookie
     */
    public function setSetCookie($setCookie) {
        $this->setCookie = $setCookie;
    }

    /**
     * Returns the boolean state of setCookie
     *
     * @return boolean
     */
    public function isSetCookie() {
        return $this->setCookie;
    }

    /**
     * @return string
     */
    public function getFilepath() {
        return $this->filepath;
    }

    /**
     * @param string $filepath
     */
    public function setFilepath($filepath) {
        $this->filepath = $filepath;
    }

    /**
     *
     * Updates the property filepath
     *
     */
    public function updateFilepath(){
        if(!empty($this->originalResource)){
            $this->setFilepath($this->getOriginalResource()->getIdentifier());
        }
    }

}