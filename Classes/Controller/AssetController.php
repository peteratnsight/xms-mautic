<?php
namespace Xms\XmsMautic\Controller;

/***************************************************************
 *
 ***************************************************************/

/**
 * MauticController
 */

class AssetController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * subscriptionRepository 
	 *
	 * @var \Xms\XmsMautic\Domain\Repository\SubscriptionRepository
	 * @inject
	 */
	protected $subscriptionRepository;

	/**
	 * Debug Level
	 *
	 * @var int
	*/
	protected $debug = 0;
	protected $debugMail=0;


	/**
	 * @var \Xms\XmsMautic\Service\LeadService
	 * @inject
	 */
	 protected $leadService;

	/**
	 * @var \Xms\XmsMautic\Service\NewsletterService
	 * @inject
	 */
	 protected $newsletterService;

    public function indexAction() {
	die("indexAction");
    }

    public function getContentAction() {
	die("getContentAction");
    }
