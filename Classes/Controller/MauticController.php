<?php
namespace Xms\XmsMautic\Controller;

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
 * MauticController
 */

class MauticController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * subscriptionRepository 
	 *
	 * @var \Xms\XmsMautic\Domain\Repository\SubscriptionRepository
	 * @inject
	 */
	protected $subscriptionRepository;

	/**
	* fileRepository
	*
	* @var \Xms\XmsMautic\Domain\Repository\FileRepository
	* @inject
	*/
	protected $fileRepository = NULL;

	/**
	* fileDataRepository
	*
	* @var \Xms\XmsMautic\Domain\Repository\FileDataRepository
	* @inject
	*/
	protected $fileDataRepository = NULL;


	/**
	* conditionRepository
	*
	* @var \Xms\XmsMautic\Domain\Repository\ConditionRepository
	* @inject
	*/
	protected $conditionRepository = NULL;

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

    public function formIncluderAction() {

	$this->view->assign('mautic_url', $this->settings['mautic_url']);
	$this->view->assign('path', "formext/generate.js");

	$_params = $this->settings['flexform']['includespex'];
	$_params["id"] = $this->settings['flexform']['general']['itemId'];
	$this->view->assign('params', $this->getFormParams($_params,1));
    }

    public function assetFormAction() {
	if(!empty($this->args["mid"])) {
		$fileObj = $this->fileRepository->findByUid($this->args["sha1ident"]);
		$this->sendFile($fileObj,"show");
	}

	$formid = FALSE;
	if(empty($formid) && !empty($this->args["formid"])) $formid = $this->args["formid"];
	if(empty($formid) && !empty($this->settings['assetform']['form_id'])) $formid = $this->settings['assetform']['form_id'];
	if($formid===FALSE) return "ERROR";

	//print_r($fileObj->getName());
	//die($mauticid);

	//68134
	//http://marketing.kongressmedia.de/manage/generate
	$conf = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
	$layout = "Mautic/FormIncluder";
	//$layout = "Mautic/AssetForm";

	$templateFile =  \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:'.$this->request->getControllerExtensionKey().'/Resources/Private/Templates/'.$layout.'.html');

	$this->view->setTemplatePathAndFilename($templateFile);
 	$this->view->assign('mautic_url', $this->settings['mautic_url']);
	$params = array();
	$_params["assetid"] = $this->args["sha1ident"];
	$_params["assettype"] = (!empty($this->args["assettype"])?$this->args["assettype"]:"pdfagenda");
	$_params["tags"] = (!empty($this->settings['assetform']['tags'])?$this->settings['assetform']['tags']:"assetDownload");
	$_params["lang"] = (!empty($this->settings['assetform']['lang'])?$this->settings['assetform']['lang']:$GLOBALS['TSFE']->config['config']['language']);
	$_params["subscribetolist"] = (!empty($this->settings['assetform']['subscribetolist'])?$this->settings['assetform']['subscribetolist']:FALSE);
	$_params["refererurl"] = $this->args["requestReferrer"];
	$_params["project"] = $this->getRootPageConfig(); //$this->settings['projectHandle'];
	$_params["id"] = $formid;
	$_params["gid"] = (!empty($this->settings["googleID"]) ? $this->settings["googleID"]: false);

	$this->view->assign('path', "formext/generate.js");
	$this->view->assign('params', $this->getFormParams($_params,1));
	$this->view->assign('googleTrackCode',(!empty($this->settings["googleID"]) ? 1: false));
	$this->view->assign('googleId',(!empty($this->settings["googleID"]) ? $this->settings["googleID"]: false));


	//$this->view->assign('form_id', (!empty($this->settings['assetform']['form_id'])?$this->settings['assetform']['form_id']:$this->settings['mautic_standardform'])); //12
	//$this->view->assign('tags', (!empty($this->settings['assetform']['tags'])?$this->settings['assetform']['tags']:"assetDownload"));
	//$this->view->assign('lang', (!empty($pathsegments[1])&&in_array($pathsegments[1],array("en","de","fr","ch","at"))?$pathsegments[1]:(!empty($urlparams["lang"])?$urlparams["lang"]:(!empty($this->settings['assetform']['lang'])?$this->settings['assetform']['lang']:"de"))));
	//$this->view->assign('subscribetolist', (!empty($this->settings['assetform']['subscribetolist'])?$this->settings['assetform']['subscribetolist']:FALSE));
	//$this->view->assign('refererurl', $this->args["requestReferrer"]);
	//$this->view->assign('asset_url', $this->args["requestReferrer"]);
	//print_r($requesturl); print_r($pathsegments);

    }

    private function getFormParams($settings,$add_form_id=FALSE) {

	$requesturl = parse_url($this->args["requestReferrer"]);
	parse_str($requesturl["path"],$urlparams);
	$pathsegments = explode("/",$requesturl["path"]);

	parse_str($requesturl["query"],$queryparams);


	$params = array();
	if($add_form_id==1) $params["id"] = $settings['id'];
	if(!empty($queryparams["hash"])) $params["hash"]=$queryparams["hash"];
	if(!empty($settings["assetid"])) $params["assetid"]=$settings["assetid"];
	if(!empty($settings["assettype"])) $params["assettype"]=$settings["assettype"];

	if(!empty($settings["pcode"])) $params["pcode"]=$settings["pcode"];
	if(!empty($settings["form_context"])) $params["context"]=$settings["form_context"];

	$params["tags"] = (!empty($settings['tags'])?$settings['tags']:$this->settings['fallback']['tags']);
	$params["lang"] = (!empty($settings['lang'])?$settings['lang']:
				(!empty($urlparams["lang"])?$urlparams["lang"]:
					(!empty($pathsegments[1])&&in_array($pathsegments[1],array("en","de","fr","ch","at"))?$pathsegments[1]:
						(!empty($GLOBALS['TSFE']->config['config']['language'])?$GLOBALS['TSFE']->config['config']['language']:
							$this->settings['fallback']['lang']))));
	$params["subscribetolist"] = (!empty($settings['subscribetolist'])?$settings['subscribetolist']:$this->settings['fallback']['subscribetolist']);
	$params["refererurl"] = (!empty($settings['refererurl'])?$settings['refererurl']:$this->uriBuilder->getRequest()->getRequestUri());
	$params["project"] =  (!empty($settings['project'])?$settings['project']:$this->getRootPageConfig()); //
	$params["gid"] = (!empty($this->settings["googleID"]) ? $this->settings["googleID"]: false);

	return http_build_query($params);
    }

    public function apiIncluderAction() {
	$params = $this->settings['flexform']['apirequest'];
	switch($params["caller"]) {
	 case("listEmails"):
		$emailApi = $this->leadService->getMauticContext("emails");
		$res = $emailApi->getList($params["searchfilter"], $params["start"], $params["limit"], $params["orderby"], $params["orderbydir"], $params["publishedOnly"], $params["minimal"]); //$params["orderbydir"], $params["publishedOnly"], $params["minimal"]);
		$total = $res["total"];
		$data = $res["emails"];
		$layout="listemails";
		break;
	 default:
		$layout = "default";
		$data = array("Method Not Supported Or Data Not Found!");
		break;
	}

	$this->view->assign('data', $data);
	$this->view->assign('params', $params);
	$this->view->assign('layout', $layout);

	$this->view->assign('total', $total);

    }

    public function getRootPageConfig() {
	// Get the root page in current page tree
	//$pid = intVal($_GET['id']);
	$page = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
	$rootline = $page->getRootLine(intVal($GLOBALS['TSFE']->id));
	foreach($rootline as $rootlinePage) 
		if($rootlinePage["is_siteroot"]==1)
			return $rootlinePage["tx_realurl_pathsegment"];
		
	return $this->settings['projectHandle'];
    }

    public function dynamicContentAction() {
	$parsedContent = "";

	
	if($this->settings["flexform"]["dynamiccontent"]["items"]>0 && !empty($_COOKIE['mtc_id'])) {
		$cObjData = $this->configurationManager->getContentObject()->data;
		$dcItems = $this->conditionRepository->findMagic(array( "cobj_uid"=>$cObjData["uid"] ));
		$parsedContent = $this->checkCondition($dcItems,$_COOKIE['mtc_id']);

	}

	if(isset($_GET["debugFocusCondition"])) { echo ">>>".$parsedContent."<<<"; exit; }

	// FALLBACK ROUTINE
	if(empty($parsedContent) && !empty($this->settings["flexform"]["dynamiccontent"]["fallback"])) {
		list($mode,$item) = explode(":",$this->settings["flexform"]["dynamiccontent"]["fallback"]);
		$parsedContent = $this->getDynamicContentObject($mode,$item);
	}

	$this->view->assign('content', $parsedContent);
	$this->view->assign('cobj_uid', $cObjData["uid"]);
	$this->view->assign('mautic_url', $this->settings['mautic_url']);
    }

    public function ajaxDynamicContentCallerAction($cObj,$mauticId=FALSE) { 
	$parsedContent="";
	$cObjConf = $this->getConfig($cObj);

	if($cObjConf["dynamiccontent"]["items"]==0) return "No Content Found!";

	$dcItems = $this->conditionRepository->findMagic( array( "cobj_uid"=>$cObj ));
	$parsedContent = $this->checkCondition($dcItems,$mauticId);

	// FALLBACK ROUTINE
	if(empty($parsedContent) && !empty($cObjConf["dynamiccontent"]["fallback"])) {
		list($mode,$item) = explode(":",$cObjConf["dynamiccontent"]["fallback"]);
		$parsedContent = $this->getDynamicContentObject($mode,$item);
	}

	return $parsedContent;

   }


   public function checkCondition($dcItems=array(),$mauticId=FALSE, $scriptMode=FALSE, $orMode=FALSE) {

	if(empty($dcItems)) return FALSE;

	//print_r($dcItems); exit;
	/*
		array('--NONE--', 'none'),
		array('Anonymous', 'anonymous'),
		array('Identified', 'identified'),
		array('Confirmed Subscriber', 'subscriber'),
		array('DNC', 'dnc'),
					array('Click Total Count', 'clickcounttotal'),
					array('Click Session Count', 'clickcountsession'),
					array('Re-Visit Time', 'visittime'),
					array('Points', 'points'),
					array('Tags', 'tags'),
	*/
	$runLog = array();
	foreach($dcItems as $condItem) {
	  $state = 0;
	  $ifSubjects = explode(",",$condItem["ifsubject"]);
	  parse_str($condItem["ifdetails"],$ifDetails);
	  $ifValues = explode(",",$condItem["ifvalue"]);
	  foreach($ifSubjects as $runkey=>$ifsubject) {	
		$this_detail = $ifDetails[$runkey];
		$this_value = $ifValues[$runkey];

		switch($ifsubject) {
			case("alwaystrue"):
				$state = 1;
				break;
			case("notanonymous"):
				$state = $this->leadService->checkLead($mauticId,"isAnonymous",1);
				break;
			case("anonymous"):
				$state = $this->leadService->checkLead($mauticId,"isAnonymous",0);
				break;
			case("isassigned"):
				$state = $this->leadService->checkLead($mauticId,"isAssigned",$this_value);
				break;
			case("notassigned"):
				$state = $this->leadService->checkLead($mauticId,"notAssigned",$this_value);
				break;
			case("notconfirmed"):
				$state = $this->leadService->checkLead($mauticId,"notConfirmed",$this_value);
				break;
			case("isconfirmed"):
				$state = $this->leadService->checkLead($mauticId,"isConfirmed",$this_value);
				break;
			case("notbooked"):
				$state = $this->conditionRepository->checkBooking($mauticId,$this_value,"not");
				break;
			case("isbooked"):
				$state = $this->conditionRepository->checkBooking($mauticId,$this_value);
				break;
			case("isdnc"):
				$d = $this->leadService->checkLead($mauticId,"getDNC");
				if(!empty($d["contact"]["doNotContact"])) {
					$state = 1;
				} else {
					$state = 0;
				}
				break;
			case("hastags"):
				$state = $this->leadService->checkLead($mauticId,"hasTag",$this_value);
				break;
			case("nottags"):
				$state = $this->leadService->checkLead($mauticId,"hasnotTag",$this_value);
				break;
			case("lesspoints"):
				$state = $this->leadService->checkLead($mauticId,"lessPoints",$this_value);
				break;
			case("greaterpoints"):
				$state = $this->leadService->checkLead($mauticId,"greaterPoints",$this_value);
				break;
			case("lesshitstotal"):
			case("greaterhitstotal"):
			case("lessdomainhits"):
			case("greaterdomainhits"):
			case("lesspagehits"):
			case("greaterpagehits"):
				$state = $this->leadService->checkLead($mauticId,$ifsubject,explode("|",$this_value));
				break;				
			case("isnewvisit"):
				$state = $this->leadService->checkLead($mauticId,"isnewvisit",explode("|",$this_value));
				break;
			case("random"):
				$rand = rand(1,100);
				if(isset($_GET["debugFocusConditionItems"])) echo "RAND: ".$rand. "/".$this_value;
				if($rand <= $this_value) $state=1;
				break;
			case("_clickcountsession"):
			default:
				$state = 0;
		}
		$runLog[] = array($ifsubject => $state );
		if($orMode===FALSE && $state==0) break; // if first condition is false => then break in andMode (!orMode).

	  }
	  if(isset($_GET["debugFocusConditionItems"])) { echo "<h2>".$mauticId."</h2><pre>"; print_r($dcItems); echo "<hr />Final State: ".$state."<hr />"; print_r( $condItem ); print_r( $runLog );  echo "</pre>";  }
	  if(isset($_GET["debugFocusCondition"]) && $scriptMode===FALSE) { echo "Final State: ".$state; print_r( $condItem ); print_r( $runLog ); }

	  if($state==1) {
		foreach(explode(",",$condItem["ifaction"]) as $_idx => $action) {
		 switch($condItem["ifaction"]) {
		 case("addtocampaign"):
		 case("assigntolist"):
		 case("addtags"):
			if(!empty($ifDetails["tags"]))
				$this->leadService->addLeadTags($mauticId,$ifDetails["tags"]);
			break;
		 case("showinfocus"):
			if(isset($_GET["debugFocusItems"])) { exit; }
			list($mode,$item) = explode(":",$condItem["thenaction"]);
			return $this->getDynamicContentObject($mode,$item,(!empty($condItem["thenparams"]) ? $condItem["thenparams"] : array()));

		 case("showelement"):
		  default:
			if(isset($_GET["debugFocusCondition1"])) { exit; }
			// Exec $condItem["thenaction"]
			if(isset($_GET["debugFocusCondition"]) && $scriptMode===FALSE) echo "<hr />".$condItem["thenaction"];
			list($mode,$item) = explode(":",$condItem["thenaction"]);
			return $this->getDynamicContentObject($mode,$item);
		 }
		}
	  }

	
	}
	return FALSE;
    }

    private function getDynamicContentObject($mode="mauticform",$item=1,$_params=FALSE,$content="") {

	switch($mode) {
		case("ttcontent"):

			$tt_content_conf = array(
				'tables' => 'tt_content',
				'source' => $item,
				'dontCheckPid' => 1
			);
			if ($this->settings["flexform"]["dynamiccontent"]['renderWrap']) {
				$tt_content_conf['wrap'] = $this->replaceVars($this->settings["flexform"]["dynamiccontent"]['renderWrap']);
			}
			if ($this->settings["flexform"]["dynamiccontent"]['renderstdWrap.']) {
				$tt_content_conf['stdWrap.'] = $this->replaceVars($this->settings["flexform"]["dynamiccontent"]['renderstdWrap.']);
			}
			if ($this->conf['renderConf.']) {
				$tt_content_conf['conf.'] = $this->replaceVars($this->settings["flexform"]["dynamiccontent"]['renderConf.']);
			}
			if(isset($_GET["debugFocusCondition"]) && $scriptMode===FALSE) echo "<hr />".print_r($tt_content_conf,-1);

			$content .= $GLOBALS['TSFE']->cObj->RECORDS($tt_content_conf);
			break;
		case("mauticinfocus"):
			$path = "focus/".$item.".js";
			$layout = "Script";
		case("mauticform"):
			if($mode=="mauticform") {
				$layout = "Default";
				$path = "form/generate.js";
				$_params = $this->settings['flexform']['includespex'];
				$_params["id"] = $item;
			}
			
			$template = "Mautic/FormIncluder";
			$renderer = $this->getViewRenderer($template);
			$renderer->assign('mautic_url', $this->settings['mautic_url']);
			$renderer->assign('layout', $path);
			$renderer->assign('path', $path);
			$renderer->assign('params', $this->getFormParams($_params,1));
			$content .= $renderer->render();
			break;
		case("mauticpage"):
		case("mauticdata"):
		case("url"):
			$layout = "Mautic/UrlIncluder";
			$renderer = $this->getViewRenderer($layout);
			$renderer->assign('url', $item);
			$_params = $this->settings['flexform']['includespex'];
			$renderer->assign('params', $this->getFormParams($_params,1));
			$content .= $renderer->render();
	}
	return $content;
    }

    function getViewRenderer($layout) {
	$renderer = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
	$renderer->setFormat("html");
	$renderer->setControllerContext($this->controllerContext);
	// find the view-settings and set the template-files
	$conf = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
	$renderer->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:'.$this->request->getControllerExtensionKey().'/Resources/Private/Templates/'.$layout.'.html'));
	$renderer->setLayoutRootPaths(array(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['layoutRootPath'])));
	$renderer->setPartialRootPaths(array(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['partialRootPath'])));
	return $renderer;
    }


    function replaceVars($content) {
	foreach(array("header","bodytext") as $field) {
		
		$content = str_replace("%".$field."%",$this->cObj->data[$field],$content);
	}
	return $content;
    }

    public function ajaxCallerAction() {
	switch($this->args["method"]) {
		case("dynamiccontent"):
		case("dynamic"):
		case("dc"):
			//return print_r($this->args,-1);
			$return = $this->ajaxDynamicContentCallerAction($this->args["value"],$this->args["option"]);
			return $return;
		default:
			print_r($this->args); 
			print_r($this->request->getControllerActionName());
			die("callerAction");
	}
	
    }

	public function initializeAction() {
		$this->args = $this->request->getArguments();
		$this_debug_action = $this->request->getControllerActionName();
//if(!in_array($this_debug_action,array("tracker","assetForm"))) { print_r($this->args); print_r($this->request->getControllerActionName()); exit; }

		if (isset($this->args["requestFormat"]) && $this->args["requestFormat"]=="ajax") {
			header("Access-Control-Allow-Origin: *");
		}
		if($this->args["requestReferrer"]=="") {
			$this->args["requestReferrer"]=$this->request->getRequestUri();
		}
	}

	public function initializeSubmitAction() {
		$this->args = $this->request->getArguments();

		if ($this->request->hasArgument('formcheck') && $this->request->getArgument('formcheck')!='') {
			$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			   'This form input is not valid.',
			   'Invalid Form Submission', // the header is optional
			   \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			   TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
			);
			$this->redirectToUri($this->args["requestReferrer"]);
		}
	
		if ($this->request->hasArgument('subscription')) {

			$this->subscriptionData = $this->request->getArgument('subscription');

			$reqPropConf = $this->arguments->getArgument('subscription')->getPropertyMappingConfiguration();
			//$reqPropConf->allowAllProperties();

			$reqPropConf->allowProperties('subscriptions');
			$reqPropConf->setTargetTypeForSubProperty('subscriptions', 'array');
		}


	}

	/**
	 * action Submit
	 *
	 * @param \Xms\XmsMautic\Domain\Model\Subscription $subscription
	 * @return void
	 */
    	public function submitAction(\Xms\XmsMautic\Domain\Model\Subscription $subscription=NULL) {
		$this->args = $this->request->getArguments();
		
		if($subscription===NULL) {
			$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			   'This form input is not valid.',
			   'Invalid Form Submission', // the header is optional
			   \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			   TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
			);
			//die("noElement");
			$this->redirect("subscribe");
		}

		// Data Management on Submit
		$history = explode(";",$subscription->getHistory());
		$flowstate="onUpdate";
		$subscription->setRemoteIp($_SERVER['REMOTE_ADDR']);
		$subscription->setHash($this->getHash(array("email"=>$subscription->getEmail(),"firstname"=>$subscription->getFirstname(),"lastname"=>$subscription->getLastname())));
		$subscription->setPid(6920);

		$subscriptions = $subscription->getSubscriptions();
		if($subscriptions!=$this->args["subscriptions_old"]) {
			$history[] = "Update(Old:Subscriptions:".$this->args["subscriptions_old"]."):".time();
		}
		$emailchck = $subscription->getEmail();
		if($emailchck!=$this->args["email_old"]) {
			$history[] = "EmailChange(Old:".$this->args["email_old"]."):".time();
		}
		if(count($subscriptions)==0 || $subscriptions[0]=="") {
			$subscription->setOptinState('unlisted');
			$subscription->setValidState(FALSE);
			$history[] = "Update(Unlisted):".time();
			$flowstate="onUnlisting";
		}
		$optinstate = $subscription->getOptinstate();
		if($optinstate=="") {
			$subscription->setValidstate(TRUE);
			$subscription->setOptinstate('registered');
			$history[] = "Update(Registered):".time();
			$flowstate="onSubscription";
		}
		$subscription->setHistory(implode(";",$history));
		$subscription->setLastUserupdate(time());

		$this->runPersist = TRUE;
		$this->doPersist($subscription,"add",$this->runPersist);
		//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($subscription); die("submit:".print_r($subscriptions,-1).count($subscriptions));

		// Mautic Management on Submit
		$this->updateMautic($flowstate,$subscription);


		// Optin/out Communications on Submit
		$emailData = array(
			"gender"=>$subscription->getGender(),
			"firstname"=>$subscription->getFirstname(),
			"lastname"=>$subscription->getLastname(),
			"company"=>$subscription->getCompany(),
			"email"=>$subscription->getEmail(),
			);
		if($emailData["email"]!="") {
			$emailData = $this->loadEmailData($emailData);
			$sysmailLog = explode(";",$subscription->getSysmailLog());

			switch($subscription->getOptinstate()) {
			 case("registered"):
				// Send DOI Mail;
				$sendMail="optin_mail";
				if(!in_array($sendMail,$sysmailLog))
					$mailresponse = $this->sendMailAction($emailData,array("emailtype"=>"optin","rendertype"=>"text/html"));
				break;
			 case("unlisted"):
				// Send Optout Mail
				$sendMail="optout_mail";
				if(!in_array($sendMail,$sysmailLog))
					$mailresponse = $this->sendMailAction($emailData,array("emailtype"=>"optout","rendertype"=>"text/html"));
				break;
			 default:
				break;
			}
		
			if(is_array($mailresponse) && $mailresponse[0]==1) {
				$sysmailLog[]=$sendMail;
				$subscription->setSysmailLog(implode(";",$sysmailLog));
				$history[] = "Send(".$sendMail."):".time();
				$subscription->setHistory(implode(";",$history));
				$this->doPersist($subscription,"add",$this->runPersist);
			
				$response = $mailresponse[1];
			} elseif (is_array($mailresponse) && $mailresponse[0]==0) {
				$response = $mailresponse[1];
			}
		}
		$this->view->assign('response',$this->getTranslation((!empty($response)?$response:"submitaction.default.response")));	
	}

	public function updateMautic($event="onUpdate",$obj=NULL) {
		if($obj==NULL) return FALSE;
		$mauticUser = $obj->getMauticContext();
		$subscriptionList = implode(",",$obj->getSubscriptions());
		$event="onSubscription";

		$mauticData = array(
				"firstname"=>$obj->getFirstname(),
				"lastname"=>$obj->getLastname(),
				"company"=>$obj->getCompany(),
				"position"=>$obj->getPosition(),
				"phone"=>$obj->getPhone(),
				"email"=>$obj->getEmail(),
				"gender"=>$obj->getGender(),
				"tags"=>"Newsletter-Subscription",
				"remove_lists_before"=>$this->subscriptionRepository->getMauticLists("commaseperated"),
				"add_list_id"=>$this->subscriptionRepository->getMauticLists("commaseperated",array("uidlist"=>$subscriptionList)),
		);


		switch($event) {
		  case("onSubscription"):
			$return = $this->leadService->checkLeadByArguments($mauticUser,$mauticData);
			if($return===FALSE) { // New IdentifiedLead
				$return = $this->leadService->updateLead($mauticUser,$mauticData);
				$updateState="newLead";
			} else {
				switch(TRUE) {
				 case(count($return)==1 && key($return)==$mauticUser): 
					$return = $this->leadService->updateLead($mauticUser,$mauticData);
					$updateState="identicalLead";
					break;
				 case(count($return)==1 && key($return)!=$mauticUser):
					$mauticUser = key($return);
					$obj->setMauticContext($mauticUser);
					$this->doPersist($obj,"update",$this->runPersist);
					$return = $this->leadService->updateLead($mauticUser,$mauticData);
					$updateState="updatedIdenticalLead";
					break;
				 case(count($return)>1 && in_array($mauticUser,array_keys($return))):
					unset($return[$mauticUser]);
					$obj->setMauticContextVariants(implode(",",array_keys($return)));
					$this->doPersist($obj,"update",$this->runPersist);
					$return = $this->leadService->updateLead($mauticUser,$mauticData);
					$updateState="updatedPrimerLead";
					break;
				 case(count($return)>1 && !in_array($mauticUser,array_keys($return))):
					$_mauticUser = array_shift(array_keys($return));
					$obj->setMauticContext($_mauticUser);
					$obj->setMauticContextVariants(implode(",",array_keys($return).",".$mauticUser));
					$this->doPersist($obj,"update",$this->runPersist);
					$return = $this->leadService->updateLead($_mauticUser,$mauticData);
					$updateState="updatedOtherLead";
					break;
				 default:
					break;
				}
			}
			break;
		  case("onUpdate"):
		  case("onUnlisting"):
				$return = $this->leadService->updateLead($mauticUser,$mauticData);
				$updateState="updatedIdenticalLead";
		  default:
		}
		
		return TRUE;
	}


	private function doPersist($obj,$method="add",$execPersist=TRUE) {
		if($execPersist===TRUE) {
			$this->subscriptionRepository->$method($obj);
			$persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
 			$persistenceManager->persistAll();
		}
	}


	public function optinconfirmAction() {

		if(!empty($_GET["hash"]) && empty($this->args["hash"])) $this->args["hash"]=$_GET["hash"];

		if(isset($this->args["hash"]) && $this->args["hash"]!="") {
			$getter = $this->subscriptionRepository->getByHash($this->args["hash"]);
			$subscriber = $this->subscriptionRepository->findByUid($getter[0]["uid"]);
			$this->args["requestMode"] = 2;
			
			$history = $subscriber->getHistory();
			$action = "Update(confirmed-optin):".time();
		
			$subscriber->setHistory($history.";".$action);
			$subscriber->setOptinstate("confirmed-optin");
			$subscriber->setValidstate(1);
			$this->doPersist($subscriber,"update");

			
			$emailData = array(
				"gender"=>$subscriber->getGender(),
				"firstname"=>$subscriber->getFirstname(),
				"lastname"=>$subscriber->getLastname(),
				"company"=>$subscriber->getCompany(),
				"email"=>$subscriber->getEmail(),
			);

			$emailData = $this->loadEmailData($emailData);
			$ret = $this->sendMailAction($emailData,array("emailtype"=>"welcome","rendertype"=>"text/html"));

			//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($subscriber); die("submit");
		}

		$this->redirectToUri("/index.html");

	}

	public function redounsubscribeAction() {

	}
	
	public function identifyAction() {

		header("Access-Control-Allow-Origin: *");

		$data = $this->args["subscription"];
		$data = $this->loadEmailData($data);

		$ret = $this->sendMailAction($data,array("emailtype"=>"manage","rendertype"=>"text/html"));
		die(($this->debugMail==1?"DEBUG: ".$ret[1]." to ".$this->args["subscription"]["email"]."| ":"").\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("email.".$ret[1],"xmsmautic"));
	}



	public function manageAction() {

		if(!empty($_GET["hash"]) && empty($this->args["hash"])) $this->args["hash"]=$_GET["hash"];

		if(isset($this->args["hash"]) && $this->args["hash"]!="") {

			// Was wenn $_GET HASH nicht existent?
			$subscription = $this->subscriptionRepository->getByHash($this->args["hash"]);
			if(empty($subscription)) {
				$this->view->assign('validdata',1);
			} else {
				$data=$subscription[0];
	
				$data["subscriptions_version"] = $data["subscriptions"];
				$data["subscriptions"] = explode(",",$data["subscriptions"]);

				$data["defaults"]["newsletter"] = $this->subscriptionRepository->getMauticLists("array");
				$data["defaults"]["newsletter_count"] = count($data["defaults"]["newsletter"]);

				$this->view->assign('validdata',0);
			}
		} else {
			$this->view->assign('validdata',1);
		}			

		$data = $this->loadData($data);
		$this->view->assign('data',$data);
		


		//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($data);
	}

	private function getHash($data) {
		$hash = md5($data["email"]);
		return $hash;
	}


	private function loadEmailData($data=array()) {
		$data["config"] = $this->settings;

		$data["config"]["sender_name"] = "Thomas Koch";
		$data["config"]["sender_mail"] = "newsletter@kongressmedia.de";

		$requestHost = parse_url($this->args["requestReferrer"], PHP_URL_HOST);
		switch($this->args["requestMode"]) {
			case(2): // auto mode
				$data["subscription_dashboard_link"] = $requestHost."/newsletter/mautic/m/manage/".$this->getHash($data);
				$data["subscription_confirmation_link"] = $requestHost."/newsletter/mautic/m/optinconfirm/".$this->getHash($data)."/newsletter-redirect.html";
				$data["subscription_undone_unsubscription_link"] = $requestHost."/newsletter/mautic/m/redounsubscribe/".$this->getHash($data)."/newsletter-redirect.html";
				break;

			case(1): // included at marketing.kongressmedia.de
				$data["subscription_dashboard_link"] = $this->args["requestReferrer"]."?hash=".$this->getHash($data);
				//redirect auto links to www.
				$requestHost = "https://".str_replace("marketing.kongressmedia.de","www.kongressmedia.de",$requestHost);
				$data["subscription_confirmation_link"] = $requestHost."/newsletter/mautic/m/optinconfirm/".$this->getHash($data)."/newsletter-redirect.html";
				$data["subscription_undone_unsubscription_link"] = $requestHost."/newsletter/mautic/m/redounsubscribe/".$this->getHash($data)."/newsletter-redirect.html";
				break;
			default:
			case(0):
				$data["subscription_dashboard_link"] = str_replace(".html","",$this->args["requestReferrer"])."/".$this->getHash($data);
				$data["subscription_confirmation_link"] = $requestHost."/newsletter/mautic/m/optinconfirm/".$this->getHash($data)."/newsletter-redirect.html";
				$data["subscription_undone_unsubscription_link"] = $requestHost."/newsletter/mautic/m/redounsubscribe/".$this->getHash($data)."/newsletter-redirect.html";
				break;
		}

		$data["fullname"] = (!empty($data["firstname"])?$data["firstname"]." ":"").$data["lastname"];
		if(empty($data["fullname"])) $data["fullname"]=0;
		if(!isset($data["gender"])) $data["gender"]=0;

		if(!isset($data["config"]["message"])) $data["config"]["message"]="";
		return $data;
	}

    	private function loadData($data = array()) {

		$rootLineArray = $GLOBALS['TSFE']->sys_page->getRootLine($GLOBALS["TSFE"]->id);

		foreach($rootLineArray as $key=>$item) 
			if($item["is_siteroot"]==1) { $root=$item; break; }

		$data["defaults"]["mauticContext"]=0;
		$data["defaults"]["eventContext"] = $root["tx_realurl_pathsegment"];
		$data["defaults"]["formcheck"] = "formCheck";
		$this->contentObj = $this->configurationManager->getContentObject();
		$data["cObjId"] = $this->contentObj->data["uid"];
		$data["defaults"]["requestReferrer"] = (!empty($_GET["referer"])?$_GET["referer"]:$this->request->getRequestUri());
		$data["defaults"]["requestMode"] = "ajax";
		$data["defaults"]["redirected"] = (!empty($_GET["referer"])?1:0);

		$data["defaults"]["gender"] = array(
			1=>$this->getTranslation("subscription_form.field.gender.option.male"),
			2=>$this->getTranslation("subscription_form.field.gender.option.female"),
			);


		return $data;
    	}


	/**
	 * action Subscribe
	 *
	 * @param \Xms\XmsMautic\Domain\Model\Subscription $newSubscription
	 * @return void
	 */
	public function subscribeAction(\Xms\XmsMautic\Domain\Model\Subscription $subscription=NULL) {
		$args = $this->request->getArguments();

		$data = $this->loadData();

		//$this->settings["flexform"]["misc"]["context_set"]="corporate,edsummit";
		if(!empty($this->settings["flexform"]["misc"]["context_set"]) && $this->settings["flexform"]["misc"]["context_set"]!="") {

			$data["defaults"]["newsletter"] = $this->subscriptionRepository->getMauticLists("array",array("context_set"=>$this->settings["flexform"]["misc"]["context_set"]));
		} else {
			$data["defaults"]["newsletter"] = $this->subscriptionRepository->getMauticLists("array",array("context"=>$data["defaults"]["eventContext"]));
		}
		$data["defaults"]["newsletter_count"] = count($data["defaults"]["newsletter"]);

		$this->view->assign('data', $data);

	}

	/**
	* action _error
	*
	* @return string
	* @api
	*/
	protected function _errorAction() {

		$errors=array();
		if($this->arguments->getValidationResults()->hasErrors()){
			foreach($this->arguments->getValidationResults()->getFlattenedErrors() as $key => $error){
				$errors[] = str_replace('booking.','',$key);
			}
		}

		//$response['status'] = 'validation';
		//$response['errors'] = $errors;
		//return json_encode($response);
		echo "ERROR ACTION"; print_r($errors); exit;
	}


	/**
	* @return string The rendered view
	*/
	public function trackerAction() {
        	$this->view->assign('mautic_url', str_replace("http://","",str_replace("https://","",$this->settings['mautic_url'])));

		$jsonload = array();
		$jsonload["page_url"]=$this->uriBuilder->getRequest()->getRequestUri();
		$jsonload["page_title"]=$GLOBALS['TSFE']->page['nav_title'];
		$jsonload['language'] = $GLOBALS['TSFE']->config['config']['htmlTag_langKey'];
		$jsonload['referrer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $jsonload["page_url"];

		$jsonload["tag"]="webhit-".$this->settings["projectHandle"].(!empty($this->settings['add_specific_tags'])?",".$this->settings['add_specific_tags']:"");
		parse_str($_SERVER["QUERY_STRING"],$refInfo);
		if(in_array("keyword",array_keys($refInfo))) { $page["utm_source"]="Google"; $page["utm_medium"]="GoogleAd"; }

		$this->view->assign('page_json', json_encode($jsonload) );


		$mauticSess = array();
		$mauticSess = $GLOBALS['TSFE']->fe_user->getKey('ses','mautic');
		if(!empty($mauticSess) && isset($mauticSess["id"])) {
			$this->view->assign('query_string_ct', urlencode(base64_encode(serialize(array("lead"=>$mauticSess["id"])))));
			//."&debug=".$mauticSess["id"]
			$this->view->assign('set_query_string_ct', 1);
		}

		if($_GET["debugm"]==1) { print_r($page); print_r($this->settings); exit; }
		$this->view->assign('query_string_d', urlencode(base64_encode(serialize($page))));

		$_params = array();
		$callInFocusScript= 0;
		if($_GET["debugInFocusPre"]==1) { 
			echo $_COOKIE['mtc_id']; 
			$mauticSelf = $this->getMauticData(); print_r($mauticSelf);
			echo $GLOBALS['TSFE']->page['tx_mauticpush_flags']."::". $this->settings['mauticInFocus'];  
			print_r($this->settings); 
			if($GLOBALS['TSFE']->page['tx_mauticpush_flags']==0 && !empty($this->settings['infocus'])) { echo "TEST SUCCEEDED"; }
			exit;
		}

		//checkCondition($dcItems=array(),$mauticId=FALSE
		if($GLOBALS['TSFE']->page['tx_mauticpush_flags']==0 && !empty($this->settings['infocus'])) {
			$mauticSelf = $this->getMauticData();
			$mid = (!empty($_COOKIE['mtc_id'])? $_COOKIE['mtc_id']: $mauticSelf->hash);

			// Get via API if MauticID is identified!
			//$initquery["target"]=(!empty($this->settings['mautic_target'])?$this->settings['mautic_target']:"_modalBox");
			//$initquery["sensitivity"]=(!empty($this->settings['mautic_sensitivity'])?$this->settings['mautic_sensitivity']:5);
			//if(!empty($this->settings['mautic_aggressivemode'])) $initquery["ouibMode"]=$this->settings['mautic_aggressivemode'];
			//if(!empty($this->settings['mautic_delaytime'])) $initquery["ouibDelay"]=$this->settings['mautic_delaytime'];
			//if(!empty($this->settings['mautic_cookiedomain'])) $initquery["ouibCookiedomain"]=$this->settings['mautic_cookiedomain'];
			//if(!empty($this->settings['mautic_sitewide'])) $initquery["ouibSitewide"]=$this->settings['mautic_sitewide'];
			//if(!empty($this->settings['mautic_pushcondition'])) $initquery["pushCondition"]=$this->settings['mautic_pushcondition'];
			//$initquery["page_url"]=(!empty($this->settings['add_specific_targetpage'])?$this->settings['add_specific_targetpage']:$this->uriBuilder->getRequest()->getRequestUri());
			//$this->view->assign('infocusObj', $this->settings['mauticInFocus']);
			//$this->view->assign('initquery', urlencode(base64_encode(serialize($initquery))));
			//if($_GET["debugFocusScript"]) { $this->settings['mauticInFocusMode']=1; }
			//if(empty($this->settings['mauticInFocusMode'])) $this->settings['mauticInFocusMode']=3; // 1: always, 2: if Ident, 3: if Not Ident

			if($_GET["debugFocusScript"]) { $this->settings['infocus'][1]["ifsubject"]="alwaystrue"; }
			$infocuscontent = $this->checkCondition($this->settings['infocus'],$mid,1);
			if($_GET["debugInFocus"]) { print_r($_COOKIE); die($infocuscontent . " --- grrrr".$_COOKIE['mtc_id']); } 

			$this->view->assign('infocuscontent', $infocuscontent);
			$callInFocusScript = 1; //$this->settings['mauticInFocusMode'];

		}

		$this->view->assign('mtc_id', (!empty( $_COOKIE['mtc_id'] ) ?  $_COOKIE['mtc_id'] : 0 ));
		$this->view->assign('include_infocus_script', $callInFocusScript);
		$this->view->assign('include_mautic', $this->settings['mautic_switch']);
		$this->view->assign('info', print_r($refInfo,-1));
	}

	private function getMauticData($url = "https://marketing.kongressmedia.de/context/request/js") {

		$curl = curl_init(); 
		$headers = array(); 
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); 
		curl_setopt($curl, CURLOPT_HEADER, 0); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_URL, $url); 
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); 
		$json = curl_exec($curl); 
		curl_close($curl); 

		return json_decode($json);
	}

	private function getTranslation($key) {
		return  \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("LLL:EXT:xms_mautic/Resources/Private/Language/locallang.xlf:" . $key , 'xmsmautic');
	}

	private function sendMailAction($data=array(),$args=array()) {

		foreach($args as $key=>$value) $data["config"][$key]=$value;

		$renderer = $this->getRenderer("Email/", ucfirst($data["config"]["emailtype"])."Mail", "html");
		$renderer->assign('email', $data);
		$message = $renderer->render();


		$subject = $this->getTranslation("email.subject.".strtolower($data["config"]["emailtype"]));
		$mailObj = new \TYPO3\CMS\Core\Mail\MailMessage();

		if($data["config"]["setBcc"]==1) {
			//$message->setBcc("register@kongressmedia.de");
			$mailObj->addBcc("bn@kongressmedia.de","BCC NewsletterService");
		}
		$mailObj->setSubject($subject);
		$mailObj->setFrom(array($data["config"]["sender_mail"] => $data["config"]["sender_name"]));
		$mailObj->setReplyTo(array($data["config"]["sender_mail"] => $data["config"]["sender_name"]));
		$mailObj->setTo(array($data["email"] => $data["fullname"]));
		$mailObj->setBody($message,(!empty($data["config"]["rendertype"])?$data["config"]["rendertype"]:"text/html"));

		$mailObj->send();

		if($mailObj->isSent()) {	
			$res = array(1,"sending.".$data["config"]["emailtype"].".success");
		} else {
			$res = array(0,"sending.".$data["config"]["emailtype"].".failure");
		}
		//$this->flashMessageContainer->add($this->getTranslation("email.".$res[1]));
		return $res;

	}

	protected function getRenderer($subTemplatePath = "Email/", $templateName = 'GenericMail', $templateType="html") {
		// create another instance of Fluid
  		$renderer = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
  		$renderer->setFormat($templateType);
  		$renderer->setControllerContext($this->controllerContext);
   		// find the view-settings and set the template-files
  		$conf = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
  		$renderer->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['templateRootPath']) . $subTemplatePath . '/' . $templateName . '.' . $templateType);
		$renderer->setLayoutRootPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['layoutRootPath']));
		$renderer->setPartialRootPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['partialRootPath']));
		$renderer->assign('settings', $this->settings);
		return $renderer;
	}

    /**
     *
     * Sends the given file to the user and exits
     */
   private function sendFile($file,$mode="show"){
	
	switch($mode) {
	 case("download"):
	        header('Content-Description: File Transfer');
        	header('Content-Type: ' . $file->getMimeType());
	        header('Content-Disposition: attachment; filename=' . $file->getName());
		break;
	 case("show"):
	 default:
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename=' . $file->getName());
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
		break;
	}

       	header('Expires: 0');
        header('Cache-Control: must-revalidate');
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit       	header('Pragma: public');
        header('Content-Length: ' . $file->getSize());

	ob_clean();
        flush();
       	echo $file->getContents();

        exit;
    }

	private function getConfig($uid) {
		$cObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( 
				"pi_flexform",
				"tt_content",
				"uid=".intval(trim($uid))
				);

		if($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			$data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ;
			$data = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($data["pi_flexform"]);
			$_data=array();
			foreach($data["data"] as $key=>$val) $_data = $_data + $val["lDEF"];
			foreach($_data as $key=>$val) {
				list($d,$f,$g,$k) = explode(".",$key);
				if($g!="" && $k!="") 
					$_d[$g][$k]=$val["vDEF"];
			}
			return $_d;	 
		} else {
			return FALSE;
		}
		return FALSE;
	}

}