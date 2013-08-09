<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }


class ClubMemberZoneMembership{

	private $_user;
	private $_group;
	private $_isOwner;
	private $_accepted;
	private $_reason;

	public static $TABLE_NAME  = 'clubMemberZone_memberships';

	public function __construct(){
		$this->setAccepted(0);
	}

	public function getUser(){
		return $this->_user;
	}
	public function setUser($aUser){
		$this->_user = $aUser;
	}

	public function getGroup(){
		return $this->_group;
	}
	public function setGroup($aGroup){
		$this->_group = $aGroup;
	}

	public function getIsOwner(){
		return $this->_isOwner;
	}
	public function setIsOwner($aIsOwner){
		$this->_isOwner = $aIsOwner;

	}

	public function getAccepted(){
		return $this->_accepted;
	}
	public function setAccepted($aAccepted){
		$this->_accepted = $aAccepted;

	}
	public function getReason(){
		return $this->_reason;
	}
	public function setReason($aReason){
		$this->_reason = stripslashes($aReason);
	}

	public function isOwner(){
		return $this->getIsOwner();
	}

	public function save(){
		global $wpdb;

		$wTableName = $wpdb->prefix.ClubMemberZoneMembership::$TABLE_NAME;
		$wQuery="INSERT INTO $wTableName (accepted, reason, group_uuid, user_id ) VALUES (%s,%s, %s,%d)
		ON DUPLICATE KEY UPDATE accepted=%s, reason=%s";

		$wpdb->query(
			$wpdb->prepare(
					$wQuery,  $this->getAccepted(), $this->getReason(), $this->getGroup()->getUuid(), $this->getUser()->ID,
							  $this->getAccepted(), $this->getReason()
			)
		);

		if($wpdb->last_error != ''){
			return $wpdb->last_error;
		}

		return null;
	}

	public function delete(){
		global $wpdb;
		$wTableName = $wpdb->prefix.ClubMemberZoneMembership::$TABLE_NAME;

		$wQuery="DELETE FROM $wTableName WHERE group_uuid=%s and user_id=%d";
		$wpdb->query(
			$wpdb->prepare(
						$wQuery, $this->getGroup()->getUuid(), $this->getUser()->ID
				)
			);

		if($wpdb->last_error != ''){
			return $wpdb->last_error;
		}

		return null;
	}


}