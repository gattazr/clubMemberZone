
<?php
// no direct access to this file
if (preg_match ( '#' . basename ( __FILE__ ) . '#', $_SERVER ['PHP_SELF'] )) {
	die ( 'You are not allowed to call this page directly.' );
}

require_once (dirname ( __FILE__ ) . '/ClubMemberZoneGroup.php');
class ClubMemberZoneGroupFactory {

	public static function loadInfosFromRow($aRow) {
		$wGroup = new ClubMemberZoneGroup ();
		if (isset ( $aRow->uuid )) {		$wGroup->setUuid ( $aRow->uuid );							}
		if (isset ( $aRow->name )) {		$wGroup->setName ( $aRow->name );							}
		if (isset ( $aRow->type )) {		$wGroup->setType ( $aRow->type );							}
		if (isset ( $aRow->owner_id )) {	$wGroup->setOwner ( get_user_by('id', $aRow->owner_id ));	}
		if (isset ( $aRow->description )) {	$wGroup->setDescription ( $aRow->description );				}

		return $wGroup;
	}
	public static function loadInfosFromTab($aTab) {
		$wGroup = new ClubMemberZoneGroup ();

		if (isset ( $aTab['uuid'] )) {			$wGroup->setUuid ( $aTab['uuid'] );							}
		if (isset ( $aTab['name'] )) {			$wGroup->setName ( $aTab['name'] );							}
		if (isset ( $aTab['type'] )) {			$wGroup->setType ( $aTab['type'] );							}
		if (isset ( $aTab['owner_id'] )) {		$wGroup->setOwner ( get_user_by('id', $aTab['owner_id'] ));	}
		if (isset ( $aTab['description'] )) {	$wGroup->setDescription ( $aTab['description'] );			}

		return $wGroup;
	}
	public static function loadGroups($wOrderBy='', $aField='1', $aValue='1' ,$aLimitBegin=false, $aLimitPerQuery=10) {
		global $wpdb;
		$wTableName = $wpdb->prefix . ClubMemberZoneGroup::$TABLE_NAME;

		$wLimit = '';
		if(is_numeric($aLimitBegin) && is_numeric($aLimitPerQuery)){
			$wLimit = "LIMIT $aLimitBegin, $aLimitPerQuery";
		}

		$wOrderBy = ($wOrderBy !='')? "ORDER BY ".$wOrderBy : '';

		$wQuery = "SELECT * FROM $wTableName WHERE $aField=%s $wOrderBy  $wLimit;";
		$wRows = $wpdb->get_results (
					$wpdb->prepare($wQuery, $aValue
								)
					);

		if ($wRows) {
			foreach ( $wRows as $wRow ) {
				$wGroups [] = ClubMemberZoneGroupFactory::loadInfosFromRow($wRow);
			}
			return $wGroups;
		}
		return null;
	}

	public static function loadFromKey($aUuid) {
		global $wpdb;
		$wTableName = $wpdb->prefix . ClubMemberZoneGroup::$TABLE_NAME;

		$wQuery = "SELECT * FROM $wTableName WHERE uuid=%s";
		$wResults = $wpdb->get_results ( $wpdb->prepare ( $wQuery, $aUuid ) );

		if ($wResults) {
			$wGroup = ClubMemberZoneGroupFactory::loadInfosFromRow ( $wResults [0] );
		} else {
			$wGroup = new ClubMemberZoneGroup ();
		}

		return $wGroup;
	}
}
