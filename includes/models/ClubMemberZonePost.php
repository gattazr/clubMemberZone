<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

Class ClubMemberZonePost{
	private $_uuid;
	private $_group_uuid;

	private $_post_author;
	private $_post_author_modified;
	private $_post_date;
	private $_post_date_modified;
	private $_post_content;
	private $_post_title;


	public static $TABLE_NAME  = 'clubMemberZone_posts';

	public function __construct(){
		$this->setUuid(ClubMemberZoneUUID::v4());
		$this->setPostDate('0000-00-00 00:00:00');
		$this->setPostDateModified('0000-00-00 00:00:00');
	}

	public function getUuid(){
		return $this->_uuid;
	}
	public function setUuid($aUuid){
		$this->_uuid = $aUuid;
	}

	public function getGroupUuid(){
		return $this->_group_uuid;
	}
	public function setGroupUuid($aGroupUuid){
		$this->_group_uuid = $aGroupUuid;
	}

	public function getPostAuthor(){
		return $this->_post_author;
	}
	public function setPostAuthor($aPostAuthor){
		$this->_post_author = $aPostAuthor;
	}

	public function getPostAuthorModified(){
		return $this->_post_author_modified;
	}
	public function setPostAuthorModified($aPostAuthorModified){
		$this->_post_author_modified = $aPostAuthorModified;
	}

	public function getPostDate(){
		return $this->_post_date;
	}
	public function setPostDate($aPostDate){
		$this->_post_date = $aPostDate;
	}

	public function getPostDateModified(){
		return $this->_post_date_modified;
	}
	public function setPostDateModified($aPostDateModified){
		$this->_post_date_modified = $aPostDateModified;
	}

	public function getPostContent(){
		return $this->_post_content;
	}
	public function setPostContent($aPostContent){
		$this->_post_content = stripslashes($aPostContent);
	}

	public function getPostTitle(){
		return $this->_post_title;
	}
	public function setPostTitle($aPostTitle){
		$this->_post_title = stripslashes($aPostTitle);
	}

	public function save(){
		global $wpdb;

		$wTableName = $wpdb->prefix.ClubMemberZonePost::$TABLE_NAME;
		$wQuery = "INSERT INTO $wTableName(group_uuid, post_author, post_date, post_content, post_title, uuid) values(%s,%d,%s,%s,%s,%s)
					ON DUPLICATE KEY UPDATE group_uuid=%s, post_author_modified=%d, post_date_modified=%s, post_content=%s, post_title=%s;";

		$wpdb->query(
			$wpdb->prepare($wQuery,
						$this->getGroupUuid(), $this->getPostAuthor()->ID, 			$this->getPostDate(), 			$this->getPostContent(), $this->getPostTitle(), $this->getUuid(),
						$this->getGroupUuid(), $this->getPostAuthorModified()->ID, 	$this->getPostDateModified(), 	$this->getPostContent(), $this->getPostTitle()
						)
			);

		if($wpdb->last_error != ''){
			return $wpdb->last_error;
		}

		return null;
	}

	public function delete(){
		global $wpdb;

		$query="DELETE FROM ".$wpdb->prefix.ClubMemberZonePost::$TABLE_NAME." WHERE uuid=%s";
		$wpdb->query(
			$wpdb->prepare(
						$query, $this->getUuid()
				)
			);

		if($wpdb->last_error != ''){
			return $wpdb->last_error;
		}

		return null;
	}



}