<?php
namespace Xms\XmsMautic\Validation\Validator;

/**
 * A validator for unique email
 *
 * @scope singleton
 */
class UniqueValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

	/**
	 * subscriptionRepository 
	 *
	 * @var \Xms\XmsMautic\Domain\Repository\SubscriptionRepository
	 * @inject
	 */
	protected $subscriptionRepository = NULL;


    /**
     * If the email is unique
     *
     * @param string $value The value
     * @return boolean
     */
    public function isValid($value) {
        $result = TRUE;

	// edit
	$paramsEdit = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_xmsmautic_app');
	$paramsEdit = isset($paramsEdit['subscription']) ? $paramsEdit['subscription'] : '';
	if(!empty($paramsEdit)) {
		$uid = isset($paramsEdit['__identity']) ? $paramsEdit['__identity'] : '';
		if ($this->subscriptionRepository->countByProperty('email', $value, $uid)) {
		        $ret = $this->addError(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("LLL:EXT:xms_mautic/Resources/Private/Language/locallang.xlf:validator.emailnotunique", 'xmsmautic'), '1301599608');
        		$result = FALSE;
    		}
	}


	// new
	$paramsNew = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_xmsmautic_app');
	$paramsNew = isset($paramsNew['newSubscription']) ? $paramsNew['newSubscription'] : '';
	if(!empty($paramsNew)) {           
	    if ($this->subscriptionRepository->countByProperty('email', $value)) {
	        $ret = $this->addError(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("LLL:EXT:xms_mautic/Resources/Private/Language/locallang.xlf:validator.emailnotunique", 'xmsmautic'), '1301599608');
	        $result = FALSE;
	    }
	}

	return $result;
    }
}
?>