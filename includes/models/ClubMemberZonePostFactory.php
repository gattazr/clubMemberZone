
<?php
// no direct access to this file
if (preg_match ( '#' . basename ( __FILE__ ) . '#', $_SERVER ['PHP_SELF'] )) {
	die ( 'You are not allowed to call this page directly.' );
}
require_once (dirname ( __FILE__ ) . '/ClubMemberZonePost.php');

class ClubMemberZonePostFactory {

	public static function loadFromKey($aUuid) {
		global $wpdb;
		$wTableName = $wpdb->prefix . ClubMemberZonePost::$TABLE_NAME;

		$wQuery = "SELECT * FROM $wTableName WHERE uuid=%s";
		$wResults = $wpdb->get_results ( $wpdb->prepare ( $wQuery, $aUuid ) );

		if ($wResults) {
			$wPost = ClubMemberZonePostFactory::loadInfosFromRow ( $wResults [0] );
		} else {
			$wPost = new ClubMemberZonePost();
		}

		return $wPost;
	}

	public static function loadPosts($aField, $aValue, $wOrderBy='', $wLimitPage=0, $wLimitDuration=10){
		global $wpdb;
		$wTableName = $wpdb->prefix . ClubMemberZonePost::$TABLE_NAME;

		$wLimit = '';
		if($wLimitPage > 0){
			$wLimitFirst = ($wLimitPage-1) * $wLimitDuration;
			$wLimit = "LIMIT $wLimitFirst,$wLimitDuration";
		}

		$wOrderBy = ($wOrderBy !='')? "ORDER BY ".$wOrderBy : '';

		$wQuery = "SELECT * FROM $wTableName WHERE $aField=%s $wOrderBy  $wLimit;";
		$wRows = $wpdb->get_results(
						$wpdb->prepare(
								$wQuery,
								$aValue)
					);
		if($wRows){
			foreach ($wRows as $wRow) {
				$wPosts[] = ClubMemberZonePostFactory::loadInfosFromRow($wRow);
			}
			return $wPosts;
		}
		return null;

	}

	public static function countPosts($aField, $aValue){
		global $wpdb;
		$wTableName = $wpdb->prefix . ClubMemberZonePost::$TABLE_NAME;

		$wQuery = "SELECT count(*) as \"numberOfPosts\" FROM $wTableName WHERE $aField=%s;";
		$wRows = $wpdb->get_results(
					$wpdb->prepare(
							$wQuery, $aValue

						)
					);
		if($wRows){
			return $wRows[0]->numberOfPosts;
		}
		return 0;
	}


	public static function loadInfosFromRow($aRow) {
		$wPost = new ClubMemberZonePost ();

		if (isset ( $aRow->uuid )) {					$wPost->setUuid ( 				$aRow->uuid );					}
		if (isset ( $aRow->group_uuid )) {				$wPost->setGroupUuid(			$aRow->group_uuid);				}
		if (isset ( $aRow->post_author )) {				$wPost->setPostAuthor(			get_user_by('id', $aRow->post_author));			}
		if (isset ( $aRow->post_author_modified )) {	$wPost->setPostAuthorModified(	get_user_by('id', $aRow->post_author_modified));}
		if (isset ( $aRow->post_date )) {				$wPost->setPostDate(			$aRow->post_date);				}
		if (isset ( $aRow->post_date_modified )) {		$wPost->setPostDateModified(	$aRow->post_date_modified);		}
		if (isset ( $aRow->post_content )) {			$wPost->setPostContent(			$aRow->post_content);			}
		if (isset ( $aRow->post_title )) {				$wPost->setPostTitle(			$aRow->post_title);				}

		return $wPost;
	}

	public static function loadInfosFromTab($aTab) {
		$wPost = new ClubMemberZonePost ();

		if (isset ( $aTab['uuid'] )) {					$wPost->setUuid ( 				$aTab['uuid'] );				}
		if (isset ( $aTab['group_uuid'] )) {			$wPost->setGroupUuid(			$aTab['group_uuid']);			}
		if (isset ( $aTab['post_author'] )) {			$wPost->setPostAuthor(			get_user_by('id', $aTab['post_author']));			}
		if (isset ( $aTab['post_author_modified'] )) {	$wPost->setPostAuthorModified(	get_user_by('id', $aTab['post_author_modified']));	}
		if (isset ( $aTab['post_date'] )) {				$wPost->setPostDate(			$aTab['post_date']);			}
		if (isset ( $aTab['post_date_modified'] )) {	$wPost->setPostDateModified(	$aTab['post_date_modified']);	}
		if (isset ( $aTab['post_content'] )) {			$wPost->setPostContent(			$aTab['post_content']);			}
		if (isset ( $aTab['post_title'] )) {			$wPost->setPostTitle(			$aTab['post_title']);			}

		return $wPost;
	}

}
