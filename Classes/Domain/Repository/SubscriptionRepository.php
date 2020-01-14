<?php
namespace Xms\XmsMautic\Domain\Repository;


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
 * The repository for Subscriptions
 */
class SubscriptionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	public function initializeObject() {
        	/** @var $defaultQuerySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
	        $defaultQuerySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
	        // add the pid constraint
        	$defaultQuerySettings->setRespectStoragePage(TRUE);
	}

	public function updateTags($valueset) {

		$query = $this->createQuery();
		$sql = "
SELECT DISTINCT t4.id as lead_id,t5.tag,t5.id as tag_id FROM t3web_kongressmedia_copy.tx_xmseventbooking_domain_model_booking as t1 
INNER JOIN t3web_kongressmedia_copy.tx_xmseventbooking_domain_model_customer as t2 ON t1.uid=t2.booking AND t1.booking_event_id=8 AND FIND_IN_SET('".$valueset["value"]."',t1.booking_addons)
INNER JOIN t3web_kongressmedia_copy.tx_xmsmautic_domain_model_subscription as t3 ON t2.email=t3.email 
INNER JOIN mauticweb.leads as t4 ON t3.mautic_context=t4.id
INNER JOIN mauticweb.lead_tags as t5 ON t5.tag='".$valueset["value"]."'
WHERE t4.id NOT IN (SELECT lead_id FROM mauticweb.lead_tags_xref as xref INNER JOIN mauticweb.lead_tags as tags ON tags.id=xref.tag_id AND tags.tag='".$valueset["value"]."');
		";

		$query->statement( $sql );

		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		return  $query->execute();

	

	}


	public function getSyncstate() {
		$query = $this->createQuery();
		$sql = "SELECT ( CASE WHEN last_syncdate=0 THEN 'tobedone' ELSE 'done' END) AS syncd, COUNT( * ) AS count FROM tx_xmsmautic_domain_model_subscription GROUP BY syncd";

		$query->statement( $sql );

		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		return $query->execute();
	}

	
	public function getSubscribers($args=array()) {
		$query = $this->createQuery();
		//WHERE tb.uid='".$uid."'
		$orderString = " ORDER BY crdate DESC";
		$queryArr = array("1=1");
		$limit = " LIMIT 0,50";

		if(isset($args["filter"])) {
		  foreach($args["filter"] as $key=>$val) {
			switch($key) {
			 case("syncquery"): 
				switch($val) {
				 case(2):
					$queryArr[] = "(last_syncdate<UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -1 SECOND)) AND validstate=0)";
					break;
				 default:
					$queryArr[] = "(last_syncdate=0 OR last_userupdate>last_syncdate OR tstamp>last_syncdate)";
					break;
				}
				break;
			 case("mauticLeadsOnly"): 
				$queryArr[] = "mautic_context <> 0";
				break;
			 default:
				$queryArr[]=$val;
			}
		  }
		}


		if(isset($args["orderby"])) $orderString = " ORDER BY ".$args["orderby"];
		if(isset($args["order"])) $orderString .= " ".strtoupper($args["order"]);

		if(isset($args["limit"])) $limit = " LIMIT ".$args["limit"];
		if(isset($args["limit"]) && isset($args["offset"])) $limit .= " OFFSET ".($args["offset"]>0?$args["offset"]:0);

		$sql = "SELECT * FROM tx_xmsmautic_domain_model_subscription as subs ".
			"WHERE ".implode(" AND ",$queryArr).
			$orderString.
			$limit;

		//echo $sql; exit;
		$query->statement( $sql );
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		return $query->execute();
	}

	public function findMagic($key,$value) {
		$query = $this->createQuery();
	
		$sql = "SELECT * FROM tx_xmsmautic_domain_model_subscription WHERE ".$key."='".$value."';";

		$query->statement( $sql );
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		//$query->getQuerySettings()->useQueryCache(FALSE);
		$res = $query->execute();
		return $res;
	}


	public function getByHash($hash,$returnObj=FALSE) {
		$query = $this->createQuery();
	
		$sql = "SELECT * FROM tx_xmsmautic_domain_model_subscription WHERE hash='".$hash."';";

		$query->statement( $sql );
		if($returnObj==FALSE) {
			$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
			//$query->getQuerySettings()->useQueryCache(FALSE);
		}
		$res = $query->execute();
		return $res;
	}

	public function getMauticLists($returnMode="commaseperated", $args=FALSE, $limittoMautic=TRUE) {
		$joins = "";
		$query = $this->createQuery();
		switch($limittoMautic) {
			case(FALSE):
				$queryArr=array("1=1");
				break;
			default:
				$queryArr = array("nl.mautic_id NOT IN (0)","nl.selfmanage=1");
				break;
		}
		if(!empty($args["context"])) $queryArr[] = "nl.event_context LIKE '%".$args["context"]."%'";
		if(!empty($args["context_set"])) {
			foreach (explode(",",$args["context_set"]) as $_context) $_orArr[] = "nl.event_context LIKE '%".$_context."%'";
			$queryArr[] = "(".implode(" OR ",$_orArr).")";
		}

		if(!empty($args["uid"])) $queryArr[] = "nl.uid = ".$args["uid"]."";
		if(!empty($args["uidlist"])) $queryArr[] = "nl.uid IN (".implode(",",array_filter(explode(",",$args["uidlist"]))).")";
		if(!empty($args["mauticlist"])) $queryArr[] = "nl.mautic_id IN (".implode(",",array_filter(explode(",",$args["mauticlist"]))).")";
		if(!empty($args["pid"])) $queryArr[] = "nl.pid = ".$args["pid"]."";
		if(!empty($args["typeset"])) $queryArr[] = "nl.type IN (".$args["typeset"].")";

		if($returnMode=="commaseperated") {
			$select = "GROUP_CONCAT(nl.mautic_id) as list";
		} elseif ($returnMode=="commaseperated_uids") {
			$select = "GROUP_CONCAT(nl.uid) as list";
		} elseif ($returnMode=="listArrayWithCount") {
			$joins = "INNER JOIN ( SELECT s2.uid, COUNT( * ) AS count FROM tx_xmsmautic_domain_model_subscription AS s INNER JOIN ( SELECT uid FROM tx_xmsmautic_domain_model_newsletter ) AS s2 ON FIND_IN_SET( s2.uid, s.subscriptions ) GROUP BY s2.uid ) AS n2 ON n2.uid = nl.uid";
			$select = "nl.uid,nl.mautic_id,nl.name, n2.count";

		} else {
			$select = "nl.uid,nl.mautic_id,nl.name";
		}

		$sql = "SELECT ".$select." FROM tx_xmsmautic_domain_model_newsletter as nl ".$joins." WHERE ".implode(" AND ",$queryArr);
		//print_r($args); echo $sql;exit;
		$query->statement( $sql );
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		$query->getQuerySettings()->useQueryCache(FALSE);

		$res = $query->execute();
		switch($returnMode) {
			case("listArrayWithCount"):
			case("array"):
				return $res;
			case("commaseperated"):
			default:
				return $res[0]["list"];
		}
	}


	/**
	 * Count users in storagefolder which have a field that contains the value
	 *
	 * @param string $field
	 * @param string $value
	 * @return integer
	 */
	public function countByField($field, $value) {
	    $query = $this->createQuery();
	    $query->getQuerySettings()->setIgnoreEnableFields(TRUE);

	    return $query
	    ->matching(
	            $query->logicalAnd(
        	            $query->equals($field, $value),
                	    $query->equals('deleted', 0)
	            )
	    )
	    ->setLimit(1)
	    ->execute()
	    ->count();
	}

	public function countByProperty($property, $value, $uid = 0) {
	    $query = $this->createQuery();
	    $query->getQuerySettings()->setIgnoreEnableFields(TRUE);
    
	    $constraints = array();
	    //$constraints[] = $query->equals('isParent', 0);
	    $constraints[] = $query->equals($property, $value);
	    if( $uid > 0) {
	        $constraints[] = $query->logicalNot($query->equals('uid', $uid));
	    }
	    $query->matching($query->logicalAnd($constraints));
	    return $query->execute()->count();
	}

}