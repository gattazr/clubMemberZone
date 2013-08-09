
<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

require_once(dirname (__FILE__).'/ClubMemberZoneMembership.php');
require_once(dirname (__FILE__).'/ClubMemberZoneGroupFactory.php');

Class ClubMemberZoneMembershipFactory{

public static function loadMemberships($aField='1', $aValue='1', $wOrderBy='', $aLimitBegin=false, $aLimitPerQuery=10){
		global $wpdb;

		$wpTable = $wpdb->prefix.ClubMemberZoneMembership::$TABLE_NAME;

		$wQuery =  "SELECT *
					FROM $wpTable
					WHERE $aField=%s and accepted=1;";

		$wRows = $wpdb->get_results(
										$wpdb->prepare(
														$wQuery,
														$aValue)
						);

		if($wRows){
			foreach ($wRows as $wRow) {
				$wBelongs[] = ClubMemberZoneMembershipFactory::loadInfosFromRow($wRow);
			}
			return $wBelongs;
		}
		return null;

	}

	public static function loadDemands($aField=1, $aValue=1){
		global $wpdb;

		$wpTable = $wpdb->prefix.ClubMemberZoneMembership::$TABLE_NAME;

		$wQuery =  "SELECT *
		FROM $wpTable
		WHERE $aField=%s and accepted=0;";

		$wRows = $wpdb->get_results(
				$wpdb->prepare(
						$wQuery,
						$aValue)
		);
		if($wRows){
		foreach ($wRows as $wRow) {
			$wDemands[] = ClubMemberZoneMembershipFactory::loadInfosFromRow($wRow);
			}
			return $wDemands;
		}
		return null;

	}

	public static function loadInfosFromRow($aRow){
		$wBelonging = new ClubMemberZoneMembership();
		if(isset($aRow->accepted))		{	$wBelonging->setAccepted(	$aRow->accepted);											}
		if(isset($aRow->reason))		{	$wBelonging->setReason(		$aRow->reason);												}
		if(isset($aRow->group_uuid))	{	$wBelonging->setGroup(		ClubMemberZoneGroupFactory::loadFromKey($aRow->group_uuid));
			if($wBelonging->getGroup()->getOwner()->ID == wp_get_current_user()->ID){
				$wBelonging->setIsOwner(true);
			}
		}else{
			$wBelonging->setIsOwner(false);
		}
		if(isset($aRow->user_id))		{	$wBelonging->setUser(		get_user_by('id', $aRow->user_id));											}

		return $wBelonging;
	}

	public static function loadInfosFromTab($aTab){
		$wBelonging = new ClubMemberZoneMembership();
		if(isset($aTab['accepted']))	{	$wBelonging->setAccepted(	$aTab['accepted']);											}
		if(isset($aTab['reason']))		{	$wBelonging->setReason(		$aTab['reason']);											}
		if(isset($aTab['group_uuid']))	{	$wBelonging->setGroup(		ClubMemberZoneGroupFactory::loadFromKey($aTab['group_uuid']));
			if($wBelonging->getGroup()->getOwner()->ID == wp_get_current_user()->ID){
				$wBelonging->setIsOwner(true);
			}
		}else{
			$wBelonging->setIsOwner(false);
		}
		if(isset($aTab['user_id']))		{	$wBelonging->setUser(		get_user_by('id', $aTab['user_id']));						}
		return $wBelonging;
	}

	public static function loadFromKey($GroupaUuid,$aUserId){
		global $wpdb;
		$wTableName = $wpdb->prefix.ClubMemberZoneMembership::$TABLE_NAME;

		$wQuery="SELECT * FROM $wTableName WHERE group_uuid=%s and user_id=%d";
		$wResults = $wpdb->get_results(
										$wpdb->prepare(
														$wQuery,
														$GroupaUuid,$aUserId)
						);

		if($wResults){
			$wActivity = ClubMemberZoneMembershipFactory::loadInfosFromRow($wResults[0]);
		}else{
			$wActivity = new ClubMemberZoneMembership();
		}

		return $wActivity;

	}


}