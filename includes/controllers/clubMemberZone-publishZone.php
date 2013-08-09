<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

require_once(dirname (__FILE__).'/../controllers/clubMemberZone-memberships.php');
require_once(dirname (__FILE__).'/../models/ClubMemberZonePostFactory.php');
require_once(dirname (__FILE__).'/../models/ClubMemberZoneMembershipFactory.php');
require_once(dirname (__FILE__).'/../models/ClubMemberZoneGroupFactory.php');
require_once(dirname (__FILE__).'/../views/clubMemberZone-publishZone.php');

Class ClubMemberZonePublishZoneController{

	private $_view;

	public function __construct(){
		$this->_view = new ClubMemberZonePublishZoneView();
	}

	public function makeActions(){
		$wAffichage = '';
		$wAuthorizedGroups = $this->showGroupChooser(wp_get_current_user());

		if(isset($_POST['save-post'])){
			$wPost = ClubMemberZonePostFactory::loadFromKey($_POST['uuid']);
			$wPost->setGroupUuid($_POST['group_uuid']);
			$wPost->setPostContent($_POST['content']);
			if($wPost->getPostTitle()){
				$wPost->setPostAuthorModified(wp_get_current_user());
				$wDate = new DateTime();
				$wPost->setPostDateModified($wDate->format('Y-m-d H:i:s'));

			}else{
				$wPost->setPostAuthor(wp_get_current_user());
				$wDate = new DateTime();
				$wPost->setPostDate($wDate->format('Y-m-d H:i:s'));
			}
			$wPost->setPostTitle($_POST['title']);
			$wPost->save();

		}elseif(isset($_POST['save-member'])){
			$wMembership = ClubMemberZoneMembershipFactory::loadFromKey($_POST['group_uuid'], $_POST['user_id']);
			if(!$wMembership->getAccepted()){
				$wMembership = ClubMemberZoneMembershipFactory::loadInfosFromTab($_POST);
				$wMembership->save();
			}
		}

		if(isset($_GET['group']) && isset($_GET['manage']) ){
			$wGroup = ClubMemberZoneGroupFactory::loadFromKey($_GET['group']);
			if(in_array($wGroup, $wAuthorizedGroups)){
				if($_GET['manage'] == 'post'){
					$wAffichage = 'post_manage_all';
				}elseif($_GET['manage'] == 'member'){
					$wAffichage = 'member_manage_all';
				}
			}

		}

		if(isset($_POST['manage-action'])){
			if($_POST['manage-action']=='Nouvel Article'){
				$wAffichage = 'post_manage_one';
				$wPost =  new ClubMemberZonePost();

			}elseif($_POST['manage-action']=='edit_post'){
				$wAffichage = 'post_manage_one';
				$wPost = ClubMemberZonePostFactory::loadFromKey($_POST['uuid'][0]);

			}elseif($_POST['manage-action']=='delete_post'){
				foreach ($_POST['uuid'] as $wUuid){
					$wPost = ClubMemberZonePostFactory::loadFromKey($wUuid);
					$wPost->delete();
				}

			}elseif ($_POST['manage-action'] == 'Nouveau membre'){
				$wAffichage = 'member_manage_one';

			}elseif($_POST['manage-action'] == 'delete_membership'){
				foreach( $_POST['idMembership'] as $wUuid){
					list($wUuid, $wUserId) = split('/', $wUuid);
					$wMembership = ClubMemberZoneMembershipFactory::loadFromKey($wUuid, $wUserId);
					$wMembership->delete();
				}

			}elseif($_POST['manage-action'] == 'accept_demand'){
				foreach( $_POST['idDemand'] as $wUuid){
					list($wUuid, $wUserId) = split('/', $wUuid);
					$wDemand = ClubMemberZoneMembershipFactory::loadFromKey($wUuid, $wUserId);
					$wDemand->setAccepted(true);
					$wDemand->save();
				}

			}elseif($_POST['manage-action'] == 'delete_demand'){
				foreach( $_POST['idDemand'] as $wUuid){
					list($wUuid, $wUserId) = split('/', $wUuid);
					$wDemand = ClubMemberZoneMembershipFactory::loadFromKey($wUuid, $wUserId);
					$wDemand->delete();
				}
			}
		}

		if($wAffichage == 'post_manage_all'){
			$this->showTabs($wGroup);
			$this->showPostManagement($wGroup);

		}elseif($wAffichage == 'post_manage_one'){
			$this->showTabs($wGroup);
			$this->showPostForm($wPost);

		}elseif($wAffichage == 'member_manage_all'){
			$this->showTabs($wGroup);
			$this->showMemberManagement($wGroup);

		}elseif($wAffichage == 'member_manage_one'){
			$this->showTabs($wGroup);
			$this->showMemberForm();
		}


	}

	public function showMemberForm(){
		$wMembers = get_users();
		$this->_view->showMemberForm($wMembers);
	}

	public function showPostForm($aPost){
		$this->_view->showPostForm($aPost);
	}

	public function showTabs($aGroup){
		$wCurrentTab = (isset($_GET['manage']))? $_GET['manage'] : '';
		$this->_view->showTabs($aGroup, $wCurrentTab);
	}

	public function showGroupChooser($aUser){

		if($aUser->has_cap('clubMemberZone_edit_groups')){
			$wGroups = ClubMemberZoneGroupFactory::loadGroups();
		}else{

			$wMemberships = ClubMemberZoneMembershipFactory::loadMemberships('user_id', $aUser->ID);
			$wGroups = array();
			foreach ($wMemberships as $wMembership){
				if($wMembership->isOwner() || $wMembership->getGroup()->getType()=='team'){
					$wGroups[] = $wMembership->getGroup();
				}
			}
		}
		if($wGroups){
			$wGroupUuid = (isset($_GET['group']))? $_GET['group'] :  '';
			$wGroupUuid = (!$wGroupUuid && isset($_POST['group']))? $_POST['group'] :  $wGroupUuid;
			$wCurrentGroup = ClubMemberZoneGroupFactory::loadFromKey($wGroupUuid);
			$this->_view->showGroupChooserForm($wGroups, $wCurrentGroup);
		}else{
			$this->_view->showNothingToDo();
		}
		return $wGroups;
	}

	public function showPostManagement($aGroup){
		$wPageNumber = (isset($_GET['pageNumber']))? $_GET['pageNumber'] : 1;
		$wPosts = ClubMemberZonePostFactory::loadPosts('group_uuid', $aGroup->getUuid(), 'post_date DESC', $wPageNumber, 10);
		$wNumberOfPosts = ClubMemberZonePostFactory::countPosts('group_uuid', $aGroup->getUuid());

		if($wNumberOfPosts > 10){
			$wCurrentPage = (isset($_GET['pageNumber']))? $_GET['pageNumber'] : 1;
			$this->_view->showPagination(ceil($wNumberOfPosts/10), $wCurrentPage);
			$this->_view->showPostsManagement($wPosts);
			$this->_view->showPagination(ceil($wNumberOfPosts/10), $wCurrentPage);
		}else{
			$this->_view->showPostsManagement($wPosts);
		}
	}

	public function showMemberManagement($aGroup){

		$wMemberships = ClubMemberZoneMembershipFactory::loadMemberships('group_uuid', $aGroup->getUuid());
		$wDemands = ClubMemberZoneMembershipFactory::loadDemands('group_uuid', $aGroup->getUuid());
		$this->_view->showMemberManagement($wMemberships,$wDemands);
	}
}