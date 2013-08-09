<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

require_once(dirname (__FILE__).'/../models/ClubMemberZonePostFactory.php');
require_once(dirname (__FILE__).'/../models/ClubMemberZoneMembershipFactory.php');
require_once(dirname (__FILE__).'/../views/clubMemberZone-memberZone.php');

Class ClubMemberZoneMemberZoneController{

	private $_view;

	public function __construct(){
		$this->_view = new ClubMemberZoneMemberZoneView();
	}

	public function makeActions(){
		$wAffichage = '';

		$wCurrentUser = wp_get_current_user();
		$this->showTabs($wCurrentUser);

		if(isset($_GET['group'])){
			$wGroup = ClubMemberZoneGroupFactory::loadFromKey($_GET['group']);
			$wAffichage = 'post_view';
		}

		if($wAffichage == 'post_view'){
			$this->showPosts($wGroup);
		}
	}

	public function showTabs($aUser){

		$wMemberships = ClubMemberZoneMembershipFactory::loadMemberships('user_id', $aUser->ID);

		$wCurrentGroupUuid = (isset($_GET['group']))? $_GET['group'] : '';
		$this->_view->showTabs($wMemberships,$wCurrentGroupUuid);
	}

	public function showPosts($aGroup){
		$wCurrentPage = (isset($_GET['pageNumber']))? $_GET['pageNumber'] : 1;

		$wPosts = ClubMemberZonePostFactory::loadPosts('group_uuid', $aGroup->getUuid(), 'post_date DESC', $wCurrentPage, 10);
		$wNumberOfPosts = ClubMemberZonePostFactory::countPosts('group_uuid', $aGroup->getUuid());
		if($wNumberOfPosts > 10){
			$this->_view->showPagination(ceil($wNumberOfPosts/10), $wCurrentPage);
			$this->_view->showPosts($wPosts);
			$this->_view->showPagination(ceil($wNumberOfPosts/10), $wCurrentPage);
		}elseif($wPosts){
			$this->_view->showPosts($wPosts);
		}

	}

}