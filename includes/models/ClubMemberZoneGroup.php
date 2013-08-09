<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

Class ClubMemberZoneGroup{

	private $_uuid;
	private $_name;
	private $_type;
	private $_owner;
	private $_description;

	public static $TABLE_NAME  = 'clubMemberZone_groups';


	public function __construct(){
		$this->setUuid(ClubMemberZoneUUID::v4());
	}

	public function getUuid(){
		return $this->_uuid;
	}
	public function setUuid($aUuid){
		$this->_uuid = $aUuid;
	}

	public function getName(){
		return $this->_name;
	}
	public function setName($aName){
		$this->_name = stripslashes($aName);
	}

	public function getType(){
		return $this->_type;
	}
	public function setType($aType){
		$this->_type = $aType;
	}

	public function getOwner(){
		return $this->_owner;
	}
	public function setOwner($aOwner){
		$this->_owner = $aOwner;
	}

	public function getDescription(){
		return $this->_description;
	}
	public function setDescription($aDescription){
		$this->_description = stripslashes($aDescription);
	}

	public function save(){
		global $wpdb;

		$wQuery = "INSERT INTO ".$wpdb->prefix.ClubMemberZoneGroup::$TABLE_NAME."(uuid,name,type,owner_id,description) VALUES(%s,%s,%s,%d,%s)
				ON DUPLICATE KEY UPDATE name=%s, type=%s, owner_id=%s, description=%s;";
		$wpdb->query(
				$wpdb->prepare(
						$wQuery, $this->getUuid(), 	$this->getName(), $this->getType(), $this->getOwner()->ID, $this->getDescription(),
						$this->getName(), $this->getType(), $this->getOwner()->ID, $this->getDescription()
				)
		);

		if($wpdb->last_error != ''){
			return $wpdb->last_error;
		}

		return null;
	}

	public function delete(){
		global $wpdb;

		$query="DELETE FROM ".$wpdb->prefix.ClubMemberZoneGroup::$TABLE_NAME." WHERE uuid=%s";
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