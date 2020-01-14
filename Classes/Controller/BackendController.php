<?php
namespace Xms\XmsMautic\Controller;

class BackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * subscriptionRepository 
	 *
	 * @var \Xms\XmsMautic\Domain\Repository\SubscriptionRepository
	 * @inject
	 */
	protected $subscriptionRepository;


	/**
	 * bookingRepository 
	 *
	 * @var \Xms\XmsEventbooking\Domain\Repository\BookingRepository
	 * @inject
	 */
	protected $bookingRepository;



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

	protected $auth_filename="authfile.log";
	protected $session_filename="sessionfile.log";
	private $debuglvl=0;
   
	// TypoScript settings
	protected $settings = array();

	// id of selected page
	protected $tsid;

	// info of selected page
	protected $tspageinfo;
 
	protected function initializeAction() {
	    if (TYPO3_MODE === 'BE') {
		$this->tsid = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');
		$this->tspageinfo = \TYPO3\CMS\Backend\Utility\BackendUtility::readPageAccess($this->id, $GLOBALS['BE_USER']->getPagePermsClause(1));

		$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\BackendConfigurationManager');
 
		$this->settings = $configurationManager->getConfiguration(
			$this->request->getControllerExtensionName(),
			$this->request->getPluginName()

		);

		$this->moduleConf = $this->settings["settings"];
		$this->moduleView = $this->settings["view"];
	    } else {
		$this->moduleConf = $this->settings;
	    }

	}
 
	/**
	* action
	*
	* @return void
	*/
	public function indexAction() {
		//print_r($this->moduleView); exit;
		//die("index");
	}

	public function mauticConsoleAction() {
		$args = $this->request->getArguments();

		// Preparing View		
		$config["link"] = $this->moduleConf["mautic_url"];
		$config["allowedTasks"] = array(
		    "clear"=>'cache:clear',
		    "leadlist_update"=>'mautic:leadlists:update',
		    "campaign_update"=>'mautic:campaigns:update',
		    "campaign_trigger"=>'mautic:campaigns:trigger',
		    "email_process"=>'mautic:email:process',
		    "fetch_email"=>'mautic:fetch:email',
		    "docmigrate"=>'doctrine:migrations:migrate',
		    "docschemaupdate"=>'doctrine:schema:update&dump-sql=1',
		    "docschemaforce"=>'doctrine:schema:update&force=1'
		);

		if(!empty($args["task"]) && in_array($args["task"],array_keys($config["allowedTasks"]))) {
			$response = $this->execConsole($config["allowedTasks"][$args["task"]]);

		}
		


		$this->view->assign("args",$args);
		$this->view->assign("conf",$config);

	
	}

	private function execConsole($command) {
		$mauticDir = "/srv/www/mautic_web";
		//require_once $mauticDir.'/app/bootstrap.php.cache';
		//require_once $mauticDir.'/app/AppKernel.php';
		// 
		//die("debug");


	}


	public function listSubscribersAction() {
		$args = $this->request->getArguments();

		$list_total = $this->subscriptionRepository->getMauticLists("listArrayWithCount",FALSE,FALSE);
		foreach($list_total as $key=>$item) $_list_total[$item["uid"]]=$item["name"]." (".$item["count"].")";
		$this->view->assign("lists",$_list_total);
		

		$searchArgs=array();
		if(!empty($args["selectList"])) $searchArgs["filter"]["selectList"] = "FIND_IN_SET(".$args["selectList"].",subscriptions)";
		$data = $this->subscriptionRepository->getSubscribers($searchArgs);
				
		$this->view->assign("debug",array("IN"=>$args,"SEARCH"=>$searchArgs));
		$this->view->assign("args",$args);
	
		$conf["viewfields"] = array("uid","firstname","lastname","email","crdate","last_userupdate","last_syncdate","validstate","imported","optinstate");

		$this->view->assign("data",$data);
		$this->view->assign("conf",$conf);


	}


	public function syncWithMauticAction() {
		$args = $this->request->getArguments();


		$list_total = $this->subscriptionRepository->getMauticLists("listArrayWithCount",FALSE,FALSE);
		foreach($list_total as $key=>$item) $_list_total[$item["uid"]]=$item["name"]." (".$item["count"].")";
		$this->view->assign("lists",$_list_total);

		$searchArgs=array();
		if(!empty($args["selectList"])) $searchArgs["filter"]["selectList"] = "FIND_IN_SET(".$args["selectList"].",subscriptions)";
		
		if(count($searchArgs)!=0) {
			$searchArgs["filter"]["syncquery"]=(!empty($args["forceall"])&&$args["forceall"]==1?2:1);
			$searchArgs["limit"]=50;
			$data = $this->subscriptionRepository->getSubscribers($searchArgs);
		}


		if(!empty($data)) {
			// if deleted or hidden unsubscribe all lists
			// i
			$leadApi = $this->leadService->getMauticContext("leads");
			$istApi = $this->leadService->getMauticContext("lists");


			foreach($data as $key=>$item) {

			   $subObj = $this->subscriptionRepository->findByUid($item["uid"]);
  			   
			   $mauticData = array(
				"t3subscriber_uid"=>$item["uid"],
				"t3subscriber"=>1,
				"firstname"=>$item["firstname"],
				"lastname"=>$item["lastname"],
				"company"=>$item["company"],
				"position"=>$item["position"],
				"phone"=>$item["phone"],
				"email"=>$item["email"],
				"gender"=>$item["gender"],
				"subscription_status"=>$item["optinstate"],
				"ipAddress"=>$item["remote_ip"],
				"tags"=>"Newsletter-Subscription",
				"remove_lists_before"=>$this->subscriptionRepository->getMauticLists("commaseperated"),
				"add_list_id"=>$this->subscriptionRepository->getMauticLists("commaseperated",array("uidlist"=>$item["subscriptions"])),
			     );
	

			  switch(TRUE) {
				case($item["mautic_context"]==0 && $item["deleted"]==0 && $item["hidden"]==0 && $item["validstate"]==0):
					unset($mauticData["add_list_id"]);
					$mauticData["subscription_status"]="permanent-blocked";
				case($item["mautic_context"]==0 && $item["deleted"]==0 && $item["hidden"]==0):
					//Create
					$return = $this->leadService->createLead($mauticData,FALSE);

					if($return!==FALSE && !empty($return["resReceived"]["lead"]["id"]))
						$subObj->setMauticContext($return["resReceived"]["lead"]["id"]);
					break;
				case($item["mautic_context"]!=0 && $item["validstate"]==0):
				case($item["mautic_context"]!=0 && $item["email"]==""):
				case($item["mautic_context"]!=0 && ($item["deleted"]==1 || $item["hidden"]==1)):
					//UNLIST FROM Mautic && Delete Email
					unset($mauticData["add_list_id"]);
					$mauticData["subscription_status"]="permanent-blocked";
					$return = $this->leadService->updateLead($item["mautic_context"],$mauticData,FALSE);
					break;
				default:
					//Update Data
					$return = $this->leadService->updateLead($item["mautic_context"],$mauticData,FALSE);

					break;
			   }
		 
			   $subObj->setLastSyncdate(time());
			   $this->subscriptionRepository->update($subObj);
			   $this->doPersist($subObj);
			   //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($subObj); die("new");

			}
			//print_r($data); die("debug");

		}

		$syncstate = $this->subscriptionRepository->getSyncstate();
		foreach($syncstate as $key=>$item) $_syncstate[$item["syncd"]]=$item["count"];
		$this->view->assign("syncstate",$_syncstate);
		$this->view->assign("data",$data );
		$this->view->assign("count",count($data));
		$this->view->assign("args",$args);

	}

	public function updateSubscribersFromBookingAction() {
		$args = $this->request->getArguments();

		switch($args["method"]) {
		 case("addons"):
		 case("options"):
			$data = $this->bookingRepository->getStats("list_of_bookingaddons");
			$r = array();
			foreach($data as $dkey=>$valueset) $r = array_merge($r,$this->subscriptionRepository->updateTags($valueset));
			if(count($r)>0) {
				$leadApi = $this->leadService->getMauticContext("leads");
				foreach($r as $rkey=>$updateData) {
					echo $updateData["lead_id"]."-".$updateData["tag"];
					//$ret[] = $leadApi->edit($updateData["lead_id"], array("tags"=>$updateData["tag"]),FALSE);
				}
			}
			print_r($r);
			break;
		}
		die("dieupdatedebug");
	}
		

	public function integrateSubscribersFromBookingAction() {
		$args=array();
		$args["filter"]["clause"]="tc.email NOT IN (SELECT email FROM tx_xmsmautic_domain_model_subscription WHERE email<>'') AND tc.email<>''";

		$data = $this->bookingRepository->getBookings($args);
	

		// Muss noch dynamisiert werden! Event <-> Newsletter Mapping
		$listMapping2Event = array(
			1 => "2,19,17", // EDS Paris
			2 => "12,17", // KMG Akademie
			4 => "9,17", // ERT
			5 => "5,17", // RKT
			6 => "8,11,10,17", // D2M
			7 => "1,19,17", // IOM
			8 => "5,17", // DMX
			9 => "16,17", // EDA
			10 => "16,17", // ECM
			33 => "7,11,10,17", // somofo
			);

		foreach($data as $key=>$item) {
			if($item["email"]=="") continue;
			$dataArray = $item;
			$dataArray["mauticContext"] = $item["booking_mautic_id"];
			$dataArray["eventContext"] = "event_id::".$item["booking_event_id"];
			$dataArray["remoteIp"] = $item["booking_ip"];
			$dataArray["validstate"] = True;
			$dataArray["optinstate"] = "registered";
			$dataArray["add_subscriptions"]=$listMapping2Event[$item["booking_event_id"]];

			$_data[$item["uid"]]=$dataArray;
			$this->doDataHandling($dataArray);


		}

		$this->view->assign("data",$_data );
		$this->view->assign("count",count($_data));

	}


	public function integrateSubscribersFromMauticAction() {

		$leadApi = $this->leadService->getMauticContext("leads");
		$mauticData = $leadApi->getList("!is:anonymous !t3subscriber:1",0,200,"ID","desc"); // AND !t3subscriber:1
		
		if($mauticData["total"]!=0) {


		foreach($mauticData["leads"] as $key=>$item) {

			if($item["fields"]["all"]["email"]=="") continue;
			
			$dataArray = $item["fields"]["all"];
			$dataArray["mauticContext"] = $item["id"];
			$dataArray["eventContext"] = "mauticImport";
			$dataArray["remoteIp"] = key($item["ipAddresses"]);
			$dataArray["validstate"] = True;
			$dataArray["optinstate"] = "registered";
			
			//$mauticListArr = $leadApi->getLeadLists($item["id"]);
			//$_this_mauticListIds = implode(",",array_keys($mauticListArr["lists"]));
			//$dataArray["mauticSubscriptions"] = $this->subscriptionRepository->getMauticLists("commaseperated_uids",array("mauticlist"=>$_this_mauticListIds),FALSE);
			

			//if(in_array($key,array(0,1))) continue;
			//echo $key; print_r($dataArray); exit;

			$_data[$item["id"]]=$dataArray;
			$this->doDataHandling($dataArray);


		}

		$this->view->assign("data",$_data );
		$this->view->assign("count",count($_data));

		} else { // No Leads to Integrate Found.
			$this->view->assign("data",array());
			$this->view->assign("count",$mauticData["total"]);
		}

	}

	private function doDataHandling($dataArray=NULL) {
		if($dataArray==NULL) return FALSE;

		$found = $this->subscriptionRepository->findMagic("email",$dataArray["email"]);

		if(!empty($found[0])) {

			$subObj = $this->subscriptionRepository->findByUid($found[0]["uid"]);
			//continue;
			//$this->subscriptionRepository->update($subObj);
		} else {
			$subObj = $this->objectManager->get('Xms\\XmsMautic\\Domain\\Model\\Subscription' );

			$subObj->setFirstname($dataArray["firstname"]);
			$subObj->setLastname($dataArray["lastname"]);
			$subObj->setCompany($dataArray["company"]);
			$subObj->setEmail($dataArray["email"]);
			$subObj->setMauticContext($dataArray["mauticContext"]);
			$subObj->setHash(md5($dataArray["email"]));

			$subObj->setRemoteIp($dataArray["remoteIp"]);
			$subObj->setEventContext($dataArray["eventContext"]);
			$subObj->setValidstate(TRUE);
			$subObj->setOptinstate("registered");
			//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($subObj); die("new");
			$this->subscriptionRepository->add($subObj);
			$this->doPersist($subObj);
			//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($subObj); die("new");
		}		

		// List Merge Management
		// Case (Mautic Lists)
		if(!empty($dataArray["mauticContext"])) {
			$leadApi = $this->leadService->getMauticContext("leads");

			$lists = $leadApi->getLeadLists($dataArray["mauticContext"]);
			$mauticSubscriptions = array_keys($lists["lists"]);
			if($mauticSubscriptions!="") {
				$nlSubscriptionsList = $this->subscriptionRepository->getMauticLists("commaseperated_uids",array("mauticlist"=>implode(",",$mauticSubscriptions)));
				$setSubscriptions = $subObj->getSubscriptions();
				if($setSubscriptions[0]=="") $setSubscriptions = array();
				if($nlSubscriptionsList!="") foreach(explode(",",$nlSubscriptionsList) as $_list_id) if(!in_array($_list_id,$setSubscriptions)) $setSubscriptions[]=$_list_id;
	
				$subObj->setSubscriptions($setSubscriptions);
				$this->subscriptionRepository->update($subObj);
				$this->doPersist($subObj);

			}
		}
		if(!empty($dataArray["add_subscriptions"])) {
			$setSubscriptions = $subObj->getSubscriptions();
			if($setSubscriptions[0]=="") $setSubscriptions = array();
			if($dataArray["add_subscriptions"]!="") foreach(explode(",",$dataArray["add_subscriptions"]) as $_list_id) if(!in_array($_list_id,$setSubscriptions)) $setSubscriptions[]=$_list_id;
	
			$subObj->setSubscriptions($setSubscriptions);
			$this->subscriptionRepository->update($subObj);
			$this->doPersist($subObj);
		}


		$user_uid = $subObj->getUid();
		if(!empty($dataArray["mauticContext"]) && $dataArray["t3subscriber_uid"]==0 && !empty($user_uid)) {
			$ret = $leadApi->edit($mauticUserId, array("t3subscriber_uid"=>$user_uid,"t3subscriber"=>1),FALSE);
		}
		//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($subObj);die("check");
	}



	private function doPersist($obj,$execPersist=TRUE) {
		if($execPersist===TRUE) {
			//$method = (!empty($obj->getUid())?"update":"add");
			
			$persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
 			$persistenceManager->persistAll();
		}
	}


	public function updateDataAction() {
		$args = $this->request->getArguments();
		$user_id = (!empty($args["user_id_alt"])?$args["user_id_alt"]:(!empty($args["user_id"])?$args["user_id"]:''));
		if(!empty($args["data"]) && !empty($user_id)) {
			$return = $this->leadService->updateLead($user_id,$args["data"],FALSE);
			$this->view->assign("data",$args["data"]);
			$this->view->assign("user_id_alt",$user_id);
			//die("mautic: ".print_r($return));
		} elseif(!empty($args["data"])) {
			$this->view->assign("data",$args["data"]);
		}

		$lists = $this->leadService->getLists();
		array_walk($lists,function(&$val,$key){$val=$val["name"]; });

		$this->view->assign("lists",$lists);
		$this->view->assign("debug",array("args"=>$args,"return"=>$return));
	}


	public function requestDataAction() {
		$contextArr = array(
			"Leads"=>"contacts",
			"Forms"=>"forms",
			"Pages"=>"pages",
			"Lists"=>"lists",
			"Campaigns"=>"campaigns",
			"Assets" => "assets",
		);
					
		$context = "contacts";
		$query = array("method"=>"get","id"=>178);

		if(!empty($context) && !empty($query)) {
			$authConf = $this->getAuthConfig();
			$auth = \Mautic\Auth\ApiAuth::initiate($authConf);
			$endpoint = $this->moduleConf["mautic_url"];

			$contextApi  = \Mautic\MauticApi::newApi($context, $auth, $this->getMauticBaseUrl($this->moduleConf["mautic_url"],TRUE));
						$reqMethod = $query["method"];
						unset($query["method"]);
						$data = $contextApi->$reqMethod($query["id"]);
						$output = "<pre>".print_r($data,-1)."</pre>".$endpoint;
					}
					return $output;
				}


	public function callbackAction() {
		$args = $_GET;
		$sessionData = $this->get_SessionData();
		if($sessionData["oauth"]["token"]!=$args["oauth_token"]) die("TOKEN IS INVALID");

		if(isset($_GET["oauth_verifier"])) {
			$_SESSION["oauth"] = $sessionData["oauth"];
		}
		\Xms\XmsMautic\Utility\AutoLoader::init();

		$this->mauticAuthAction();
	}

    // Mautic Authentication 

	public function mauticAuthAction() {
		$authConf = $this->getAuthConfig();
		$auth = \Mautic\Auth\ApiAuth::initiate($authConf);

		if (!empty($_SESSION[$authConf["version"]])) {
			$auth->setAccessTokenDetails($_SESSION[$authConf["version"]]);
		}

		$auth->enableDebugMode();

		if ($auth->validateAccessToken()) {
			if ($auth->accessTokenUpdated()) {

				$accessTokenData = $auth->getAccessTokenData();

				$this->store_TokenData($accessTokenData);							
						
				$output = "<h3>Successful Authentication to Mautic</h3>";
				$output .= "<pre>".print_r($accessTokenData,-1)."</pre>"; 

			} else {
				$output = "debug: is already authorized";
			}
		} else {
			$output = "debug: authorization failed";
		}

		$this->view->assign('output', $output);

	}

	private function getAuthConfig() {
		if(!isset($this->moduleConf["auth"]) ||empty($this->moduleConf["auth"]["clientKey"]) || 
			empty($this->moduleConf["auth"]["clientKey"])) die("Mautic Auth Failed b/c of Missing Credentials");

		$authConf = $this->moduleConf["auth"];
		$authConf["baseUrl"] = $this->getMauticBaseUrl($this->moduleConf["mautic_url"],FALSE);
		$authConf["parent"] = $this;

		if (($accessTokenData = $this->get_TokenData())!==FALSE) {
			$authConf['accessToken']        = $accessTokenData['access_token'];
			$authConf['accessTokenSecret']  = $accessTokenData['access_token_secret'];
			$authConf['accessTokenExpires'] = $accessTokenData['expires'];
		}
		return $authConf;
	}



	public function storeSession($arr) {
		//$GLOBALS["BE_USER"]->setAndSaveSessionData ('tx_xmsmautic', $arr); 

		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('xms_mautic');

		if (!$handle = fopen($extPath.$this->session_filename, "w")) {
		        // echo help cant write file
			die("Can't Open Session File! >>".$this->session_filename);
		}
		if (!fwrite($handle, serialize($arr))) {
			die("Can't Write Cache File!");
			// echo help cant write data
		}

		fclose($handle);
		return TRUE;

	}

	public function get_SessionData() {
		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('xms_mautic');
		if (empty($this->session_filename) || !is_file($extPath.$this->session_filename)) return FALSE;

		$read_sess_data = file_get_contents($extPath.$this->session_filename); // $this->cObj->fileResource($this->cache_obj_filename)

		if ($read_sess_data=="") return FALSE;
		return unserialize($read_sess_data);
	}


	public function store_TokenData($data) {
		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('xms_mautic');

		if (!$handle = fopen($extPath.$this->auth_filename, "w")) {
		        			// echo help cant write file
				die("Can't Open Cache File! >>".$this->$this->auth_filename);
			}
			if (!fwrite($handle, serialize($data))) {
				die("Can't Write Cache File!");
		        			// echo help cant write data
			}

			fclose($handle);
			return TRUE;
	}

	public function get_TokenData($data) {
		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('xms_mautic');
		if (empty($this->auth_filename) || !is_file($extPath.$this->auth_filename)) return FALSE;

		$read_cache_data = file_get_contents($extPath.$this->auth_filename); // $this->cObj->fileResource($this->cache_obj_filename)

		if ($read_cache_data=="") return FALSE;
		return unserialize($read_cache_data);
	}


	public function getMauticBaseUrl($url,$with_api_path=TRUE) {
		return trim($url, ' \t\n\r\0\x0B/') . ($with_api_path===TRUE? "/api/" : "");
	}



}