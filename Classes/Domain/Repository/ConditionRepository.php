<?php
namespace Xms\XmsMautic\Domain\Repository;


/***************************************************************
 ***************************************************************/

/**
 * The repository for Conditions
 */
class ConditionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	
	public function findMagic($args) {
		$query = $this->createQuery();
		$searchArr = array();
		foreach($args as $key=>$value) $searchArr[] = $key."='".$value."'";
		$sql = "SELECT * FROM tx_xmsmautic_domain_model_condition WHERE deleted=0 AND hidden=0". (count($searchArr)>0 ? " AND ".implode(" AND ", $searchArr) : "") ." ORDER BY sorting ASC;";

		$query->statement( $sql );
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		//$query->getQuerySettings()->useQueryCache(FALSE);
		$res = $query->execute();
		return $res;
	}

	public function checkBooking($mautic_id,$event_id,$not=FALSE) {
		$query = $this->createQuery();
		$sql = "SELECT 1 FROM tx_xmseventbooking_domain_model_booking as b INNER JOIN tx_xmseventbooking_domain_model_customer as c ON c.booking=b.uid AND b.booking_event_id IN (".str_replace("|",",",$event_id).") AND (b.booking_mautic_id =".$mautic_id." OR c.mautic_id=".$mautic_id.") GROUP BY c.mautic_id";

		$query->statement( $sql );
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		//$query->getQuerySettings()->useQueryCache(FALSE);
		$res = $query->execute();
//echo $sql;die();
		switch(TRUE) {
		 case(!empty($res) && $not===FALSE):
			return 1;
		 case(empty($res) && $not=="not"):
			return 1;
		 default:
			return 0;
		}
	}
}