<?php

namespace Xms\XmsMautic\Service;
use Mautic\MauticApi;
use Mautic\Auth\ApiAuth;

class LeadService implements \TYPO3\CMS\Core\SingletonInterface {

     /**
     * @var mixed
     */
    protected $settings = NULL;
    
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $injectedpluginconfigManager;


    public function checkLead($mid, $checkAction=FALSE, $condvalue=FALSE) {
	if(empty($mid) && $checkAction!="isAnonymous") return 0;
	if(empty($mid) && $checkAction=="isAnonymous") return 1;

	$inverse=0;
	switch($checkAction) {
	 case("isAnonymous"):
		$leadApi = $this->getMauticContext("contacts");
		$data = $leadApi->get($mid);
		switch(TRUE) {
		 case(empty($data["contact"]["dateIdentified"]) && $condvalue==0):
			return 1;
			break;
		 case(!empty($data["contact"]["dateIdentified"]) && $condvalue==1):
			return 1;
			break;
		 default:
			return 0;
			break;
		}
		break;
	 case("notConfirmed"):
		$inverse=1;
	 case("isConfirmed"):
		$leadApi = $this->getMauticContext("contacts");
		$data = $leadApi->get($mid);
		switch(TRUE) {
		 case(!empty($data["contact"]["confirmeddate"]) && $inverse==0):
			return 1;
			break;
		 case(empty($data["contact"]["confirmeddate"]) && $inverse==1):
			return 1;
			break;
		 default:
			return 0;
			break;
		}
		break;
	 case("notAssigned"):
		$inverse=1;
	 case("isAssigned"):
		$leadApi = $this->getMauticContext("contacts");
		$segments = $leadApi->getContactSegments($mid);
		array_walk($segments["lists"], function(&$val){ $val = $val["id"]; });
		switch(TRUE) {
		 case(in_array($condvalue, $segments["lists"]) && $inverse==0):
			return 1;
			break;
		 case(!in_array($condvalue, $segments["lists"]) && $inverse==1):
			return 1;
			break;
		 default:
			return 0;
			break;
		}
		break;
	case("lessPoints"):
		$inverse=1;
	case("greaterPoints"):
		$leadApi = $this->getMauticContext("contacts");
		$data = $leadApi->get($mid);
		switch(TRUE) {
		 case($data["contact"]["points"]>=$condvalue && $inverse==0):
			return 1;
			break;
		 case($data["contact"]["points"]<=$condvalue && $inverse==1):
			return 1;
			break;
		 default:
			return 0;
			break;
		}
		break;
	 case("isIdentified"):
		$leadApi = $this->getMauticContext("contacts");
		$data = $leadApi->get($mid);
		if(
			$data["contact"]["dateIdentified"]!=0 
			&& $data["contact"]["fields"]["all"]["optinprocstate"]!="" 
			&& !in_array($data["contact"]["fields"]["all"]["optinprocstate"],array("none"))
			) {
			return TRUE;
		} else {
			return FALSE;
		}
	 case("hasnotTag"):
		$inverse=1;
	 case("hasTag"):
		$leadApi = $this->getMauticContext("contacts");
		$data = $leadApi->get($mid);
		$tags = $data["contact"]["tags"];
		array_walk($tags, function(&$val){ $val = $val["tag"]; });
		
		$mode="AND";
		if(strpos($condvalue,":")) { $mode="OR"; $condvalue = str_replace(":","|",$condvalue); }		
		$searchTags = explode("|",$condvalue);

		$_sIdx=array();

		foreach($searchTags as $_k=>$_sTag) {

		 if(in_array($_sTag,array_values($tags))) {
			$_sIdx[]=1;
		 }
		}

		switch(TRUE) {
		 case($mode=="AND" && count($_sIdx)==count($searchTags) && $inverse==0):
			return 1;
		 case($mode=="AND" && count($_sIdx)!=count($searchTags) && $inverse==1):
			return 1;
		 case($mode=="OR" && count($_sIdx)>0 && $inverse==0):
			return 1;
		 default:
			return 0;
		}
	 case("lesshitstotal"):
	 	$inverse=1;
	 case("greaterhitstotal"):
		$detailApi = $this->getMauticContext("details");
		$data = $detailApi->getPageHits($mid,(!empty($condvalue[1])?$condvalue[1]:"null"));
		switch(TRUE) {
		 case($condvalue[0]<$data["totalhits"]):
			return 1;
		 case($condvalue[0]>$data["totalhits"] && $inverse==1):
			return 1;
		 default:
			return 0;
		}
	 case("lessdomainhits"):
	 	$inverse=1;
	 case("greaterdomainhits"):
		$detailApi = $this->getMauticContext("details");
		$data = $detailApi->getPageHits($mid,(!empty($condvalue[1])?$condvalue[1]:"null"));

		//if($_GET["serviceReturn"]==1) { echo $condvalue[0]; print_r($data); exit; }
		switch(TRUE) {
		 case($condvalue[0]<$data["domainhits"]):
			return 1;
		 case($condvalue[0]>$data["domainhits"] && $inverse==1):
			return 1;
		 default:
			return 0;
		}
	 case("lesspagehits"):
	 	$inverse=1;
	 case("greaterpagehits"):
		$detailApi = $this->getMauticContext("details");
		$data = $detailApi->getPageHits($mid,(!empty($condvalue[1])?$condvalue[1]:"null"));
		switch(TRUE) {
		 case($condvalue[0]<$data["pagehits"]):
			return 1;
		 case($condvalue[0]>$data["pagehits"] && $inverse==1):
			return 1;
		 default:
			return 0;
		}
	 case("getPageHits"):
		$detailApi = $this->getMauticContext("details");
		$data = $detailApi->getPageHits($mid,(!empty($condvalue[1])?$condvalue[1]:"null"));
		return $data;
	 case("isnewvisit"):
		$detailApi = $this->getMauticContext("details");
		$data = $detailApi->isNewVisit($mid,(!empty($condvalue[0])?$condvalue[0]:"null"),(!empty($condvalue[1])?$condvalue[1]:FALSE));
		switch(TRUE) {
		 case($data["result"]===TRUE):
			return 1;
		 default:
			return 0;
		}
	 case("random"):

		break;
	 default:
		$leadApi = $this->getMauticContext("contacts");
		$data = $leadApi->get($mid);
	}
	return $data;
    }


    public function checkLeadByArguments($leadId=FALSE,$args) {
	if(empty($leadId)) return FALSE;

	$leadApi = $this->getMauticContext("contacts");
	$mauticData = $leadApi->getList("email:".$args["email"]);

	if(count($mauticData["lead"])>0) { // Austin we have a problem
		foreach($mauticData["lead"] as $lead) {
			$found_data[$lead["id"]] = $lead["fields"]["all"];
		}
		return $found_data;
	}

	return FALSE;
    }

    public function createLead($args,$updateip=FALSE) {
	$leadApi = $this->getMauticContext("contacts");

	$baseFields = array("firstname","lastname","email");
	$acceptedUpdateFields = array("company","gender","address","city","country","t3subscriber_uid","t3subscriber","subscription_status");
	foreach($args as $key=>$value) {
	  if($key=="tags" && !empty($value) && $value!="") {
		$tagsArr = explode(",",$value);
		$data[$key] = implode(",",$tagsArr);
	  }

	  if(in_array($key,$baseFields)) {
		$data[$key]=$value;
	  }
	  if(in_array($key,$acceptedUpdateFields)) {
		$data[$key]=$value;
	  }
	}
	if(empty($data)) return FALSE;

	if($updateip===TRUE) $data["ipAddress"] = $_SERVER['REMOTE_ADDR'];
	$res = $leadApi->create($data);
	
	if(!empty($res["contact"]["id"])) 
		$listApiResult = $this->doListManagement($res["contact"]["id"],$args);
	//$res = $contextApi->create($data);
    	return array("dataSend"=>$data,"resReceived"=>$res,"listReceived"=>$listApiResult);
    }

    public function addLeadTags($leadId=FALSE,$tags=FALSE,$forceTagsReset=FALSE) {
	if(empty($leadId) || empty($tags)) return FALSE;

	$leadApi = $this->getMauticContext("contacts");
	$data = $leadApi->get($leadId);

	if(!is_array($tags)) { $tagsArr = explode(",",$tags); } else { $tagsArr = $tags; }
	foreach($tagsArr as $key=>$val) { $tagsArr[$key] = array("tag"=>$val); }
	
	if(isset($data["contact"]["tags"])) { $tagsSet = $data["contact"]["tags"]; } else { $tagsSet = array(); }


	//if($forceTagsReset===FALSE) 
	//	$tagsArr = array_merge($tagsSet,$tagsArr);

	$editData["tags"] = $tags;

	$editData["lastActive"] = date("Y-m-d H:i:s");
	$editData["ipAddress"] = $_SERVER['REMOTE_ADDR'];

	$res = $leadApi->edit($leadId, $editData);
	if(isset($_GET["debugFocusCondition2"])) die("goforit2: ".print_r($res,-1));

	return TRUE;
    }



    public function updateLead($leadId=FALSE,$args,$updateip=TRUE,$forceBaseUpdate=FALSE,$forceTagsReset=FALSE){
	//if(empty($leadId)) return FALSE;
	$_in_leadId = $leadId;

$fp = fopen($_SERVER["DOCUMENT_ROOT"].'/mauticdebug.txt', 'a+');
	if(isset($fp)) fwrite($fp, "\n########## DEBUGGER #############\n");

	$leadApi = $this->getMauticContext("contacts");
	$found = $leadApi->checkEmail($args["email"]);

	if(!empty($found) && isset($found["id"]) && $found["id"]!=$leadId) {
	 // Achtung ID ist nicht ID
	 $foundData = $leadApi->get($found["id"]);
	 $checkData = $leadApi->get($leadId);
	 // Check Data zu LeadId aus Process hat keine Email (sprich ist leer!) - also Merge
	 if(empty($checkData["contact"]["fields"]["all"]["email"])) {
		$leadId = $leadApi->mergeLeads($leadId,$found["id"]);
	 }
	 if(!empty($checkData["contact"]["fields"]["all"]["email"]) && $checkData["contact"]["fields"]["all"]["email"]==$foundData["contact"]["fields"]["all"]["email"] && $checkData["contact"]["fields"]["all"]["last_update"]<=$foundData["contact"]["fields"]["all"]["last_update"]) {
		$leadId = $leadApi->mergeLeads($found["id"],$leadId);
	 }
	 if(!empty($checkData["contact"]["fields"]["all"]["email"]) && $checkData["contact"]["fields"]["all"]["email"]==$foundData["contact"]["fields"]["all"]["email"] && $checkData["contact"]["fields"]["all"]["last_update"]>$foundData["contact"]["fields"]["all"]["last_update"]) {
		$leadId = $leadApi->mergeLeads($leadId,$found["id"]);
	 }
	 if(!empty($checkData["contact"]["fields"]["all"]["email"]) && $checkData["contact"]["fields"]["all"]["email"]!=$foundData["contact"]["fields"]["all"]["email"]) {
		$leadId = $found["id"];
	 }
	 $leadApi->updateCookie($leadId);		

	} 

	if(isset($fp)) fwrite($fp, "\n >> Debug: LeadID (after check): ".$leadId);
	$mauticData = $leadApi->get($leadId);
	//if(isset($fp)) fwrite($fp, "\n >> Debug: Preset (before resetting): ".print_r($mauticData,-1) );


	// Reset LeadId & Preset Data if Email (as Identifier) Not Equal
	if(!empty($mauticData["contact"]["fields"]["all"]["email"]) && 
		$mauticData["contact"]["fields"]["all"]["email"]!=$args["email"]) {
		$leadId = 0;
		$mauticData = array();
	}

	if(isset($fp)) fwrite($fp, "\n >> Debug: LeadID (before dataloading): ".$leadId);
	if(isset($fp)) fwrite($fp, "\n >> Debug: Preset: ".print_r($mauticData,-1));

	//echo $leadId; print_r($res); print_r($mauticData); exit;

	$baseFields = array("firstname","lastname","email");
	$acceptedUpdateFields = array("company","position","title","gender","zipcode","address1","city","country","t3subscriber_uid","t3subscriber","subscription_status");
	//"gender",

	$data = array();
	if(!empty($mauticData["contact"])) $preset=$mauticData["contact"];
	foreach($args as $key=>$value) {
	  if($value=="") continue;
	  if($key=="tags" && !empty($value) && $value!="") {
		$tagsArr = explode(",",$value);
		if(isset($preset["tags"])) { $tagsSet = $preset["tags"]; } else { $tagsSet = array(); }
		if($forceTagsReset===FALSE) 
			$tagsArr = array_merge(array_keys($tagsSet),$tagsArr);
		//array_walk($tagsArr,function(&$_val,$_key) { $_val = "+".$_val; });
		$data[$key] = implode(",",$tagsArr);
	  }

	  if(in_array($key,$baseFields)) {
		$data[$key]=$preset["fields"]["all"][$key];
		if(empty($data[$key]) && $value!="") $data[$key]=$value;
		if($forceBaseUpdate===TRUE) $data[$key]=$value;
	  }
	  if(in_array($key,$acceptedUpdateFields)) {
		$data[$key]=$preset["fields"]["all"][$key];
		if(!empty($value) && $value!="") {
			$data[$key]=$value;
		}
	  }
	}
	if(empty($data)) return FALSE;

	$data["lastActive"] = date("Y-m-d H:i:s");
	if($updateip===TRUE) $data["ipAddress"] = $_SERVER['REMOTE_ADDR'];

	if($leadId==0) {
		$res = $leadApi->create($data);
		$leadId = $res["contact"]["id"];
	} else {
		$res = $leadApi->edit($leadId, $data);
	}
	$listApiResult = $this->doListManagement($leadId,$args);
	$actResult = $this->createActivity($leadId,$args);

	$mauticSess = $res["contact"]["fields"]["all"];
	$mauticSess["id"]=$leadId;
	$GLOBALS['TSFE']->fe_user->setKey('ses', 'mautic', $mauticSess); 
	$GLOBALS["TSFE"]->storeSessionData();

	if(isset($fp)) fwrite($fp, "\n>> ".$leadId. "IN: ".$_in_leadId ."| Found: ".$found["id"] ."| Check: ".$checkData["lead"]["id"] ."<<\n");
	if(isset($fp)) fwrite($fp, "\n--------- ARGS -------------\n");
	if(isset($fp)) fwrite($fp, print_r($args,-1));
	if(isset($fp)) fwrite($fp, "\n---------- DATA SEND ------------\n");
	if(isset($fp)) fwrite($fp, print_r($data,-1));
	if(isset($fp)) fwrite($fp, "\n---------- RES ------------\n");
	if(isset($fp)) fwrite($fp, print_r($res,-1));
	if(isset($fp)) fwrite($fp, "\n---------- LIST MGMT ------------\n");
	if(isset($fp)) fwrite($fp, print_r($listApiResult,-1));
	if(isset($fp)) fwrite($fp, "\n--------- ACT MGMT -------------\n");
	if(isset($fp)) fwrite($fp, print_r($actResult,-1));
	if(isset($fp)) fwrite($fp, "\n--------- CLOSING -------\n");
	if(isset($fp)) fclose($fp);

    	return array("mauticId"=>$leadId,"dataSend"=>$data,"mauticSet"=>$mauticData,"resReceived"=>$res,"listReceived"=>$listApiResult, "activityLogger"=>$actResult);
    }

    private function doListManagement($leadId,$args) {
	$listApiResult=[];

	if(isset($args["remove_list_id"])) {
		$listApi = $this->getMauticContext("segments");
		foreach(explode(",",$args["remove_list_id"]) as $listId) {
			$listApiResult = $listApi->removeContact($listId,$leadId);
		}

	}

	$consensusList=[];
	if(isset($args["add_list_id"])) {
		$listApi = $this->getMauticContext("segments");
		foreach(explode(",",$args["add_list_id"]) as $listId) {
			if(substr($listId,0,2)=="c:") {
				$consensusList[] = str_replace("c:","",$listId);
				continue;
			}
			$listApiResult[0] = $listApi->addContact($listId,$leadId);
		}

	}

	if($args["newsletterConsent"]==1)
		$listApiResult[1] = $this->submitForm(
					$args["mauticFormId"],
					$leadId,
					array(
						"privacyconsent"=>$args["newsletterConsent"],
						"subscribetolist"=>implode(",",$consensusList),
						"email"=>$args["email"],
						"tags"=>$args["formTag"],
						"refererurl"=>$args["refererUrl"],
						"lang"=>$args["lang"],
						"project"=>$args["project"]
					));

	return $listApiResult;
    }

    public function getLists() {
	$listApi = $this->getMauticContext("segments");
	return $listApi->getList();
    }

    public function createActivity($leadId=FALSE,$args,$activityType="activity") {
	if(empty($leadId)) return FALSE;
	if(empty($args["activityStmt"])) return FALSE;

	$activityApi = $this->getMauticContext("activities");
	$data = array(
		"type"=>$activityType,
		"text"=>(isset($args["activityStmt"])?$args["activityStmt"]:"Lead Service Logger")
		);

	$res = $activityApi->createActivity($leadId, $data);
    	return array("dataSend"=>$data,"resReceived"=>$res);
    }

    public function submitForm($formId=FALSE,$mauticId=FALSE,$postData) {
	if(empty($formId)) return FALSE;
	if(empty($mauticId)) return FALSE;
	$url = "https://marketing.kongressmedia.de/form/submit?ajax=1&formId=".$formId;

	$request = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
	        'TYPO3\\CMS\\Core\\Http\\HttpRequest',
	        $url,"POST"
	);
	$request->addPostParameter("mauticform[formId]", $formId);
	$request->addPostParameter("ajax", 1);
	$request->addPostParameter("mauticform[id]", $mauticId);

	foreach($postData as $key=>$val)
		$request->addPostParameter("mauticform[".$key."]", $val);
	return $request->send()->getBody();
    }

    public function getMauticContext($context) {
	$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
	$settings = $configurationManager->getConfiguration( 'Settings',"xmsmautic","app");		
	$authConf = $this->getAuthConfig($settings);

	//\Xms\XmsMautic\Utility\AutoLoader::init();
	$auth = \Mautic\Auth\ApiAuth::initiate($authConf);

	return \Mautic\MauticApi::newApi($context, $auth, $this->getMauticBaseUrl($settings["mautic_url"],TRUE));
    }
    

	private function getAuthConfig($conf) {
		if(!isset($conf["auth"]) ||empty($conf["auth"]["clientKey"]) || 
			empty($conf["auth"]["clientKey"])) die("Mautic Auth Failed b/c of Missing Credentials");

		$authConf = $conf["auth"];
		$authConf["baseUrl"] = $this->getMauticBaseUrl($conf["mautic_url"],FALSE);
		$authConf["parent"] = $this;

		if (($accessTokenData = $this->get_TokenData())!==FALSE) {
			$authConf['accessToken']        = $accessTokenData['access_token'];
			$authConf['accessTokenSecret']  = $accessTokenData['access_token_secret'];
			$authConf['accessTokenExpires'] = $accessTokenData['expires'];
		}
		return $authConf;
	}

	public function requestDataAction() {
		$contextArr = array(
			"Context"=>"context",
			"Leads"=>"leads",
			"Forms"=>"forms",
			"Pages"=>"pages",
			"Lists"=>"lists",
			"Campaigns"=>"campaigns",
			"Assets" => "assets",
		);
					
		$context = "context";
		$query = array("method"=>"get"); //,"id"=>1);

		if(!empty($context) && !empty($query)) {
			$authConf = $this->getAuthConfig();
			$auth = \Mautic\Auth\ApiAuth::initiate($authConf);

			$contextApi  = \Mautic\MauticApi::newApi($context, $auth, $this->getMauticBaseUrl($this->moduleConf["mautic_url"],TRUE));
						$reqMethod = $query["method"];
						unset($query["method"]);
						$data = $contextApi->$reqMethod($query["id"]);
						$output = "<pre>".print_r($data,-1)."</pre>";
					}
					return $output;
				}

	public function get_TokenData() {
		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('xms_mautic');
		if (!is_file($extPath."authfile.log")) return FALSE;

		$read_cache_data = file_get_contents($extPath."authfile.log"); // $this->cObj->fileResource($this->cache_obj_filename)

		if ($read_cache_data=="") return FALSE;
		return unserialize($read_cache_data);
	}

	public function getMauticBaseUrl($url,$with_api_path=TRUE) {
		$host = parse_url($url, PHP_URL_HOST);
		return "https://" . trim($host, ' \t\n\r\0\x0B/') . ($with_api_path===TRUE? "/api/" : "");
	}

	public function debug() {
		return "debug";
	}


}