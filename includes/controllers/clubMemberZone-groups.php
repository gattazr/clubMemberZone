<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

require_once(dirname (__FILE__).'/../models/ClubMemberZoneGroupFactory.php');
require_once(dirname (__FILE__).'/../models/ClubMemberZoneMembership.php');
require_once(dirname (__FILE__).'/../views/clubMemberZone-groups.php');

Class ClubMemberZoneGroupsController{

	private $_view;

	public function __construct(){
		$this->_view = new ClubMemberZoneGroupsView();
	}

	public function makeActions(){
		$wAffichage = 'manage_view';

		if(isset($_POST['save-group'])){
			$wGroup = ClubMemberZoneGroupFactory::loadInfosFromTab($_POST);
			$wGroup->save();

			$wMembership = new ClubMemberZoneMembership();
			$wMembership->setUser($wGroup->getOwner());
			$wMembership->setGroup($wGroup);
			$wMembership->setAccepted(1);
			$wMembership->setReason('owner');
			$wMembership->save();
		}

		if(isset($_POST['manage-action'])){
			if($_POST['manage-action'] =='Nouveau groupe'){
				$wAffichage = 'form_view';
				$wGroup = new ClubMemberZoneGroup();

			}elseif($_POST['manage-action'] =='edit_group'){
				$wAffichage = 'form_view';
				$wGroup = ClubMemberZoneGroupFactory::loadFromKey($_POST['uuid'][0]);

			}elseif($_POST['manage-action'] =='delete_group'){
				foreach ($_POST['uuid'] as $wUuid){
					$wGroup = ClubMemberZoneGroupFactory::loadFromKey($wUuid);
					$wGroup->delete();
				}
			}
		}


		if($wAffichage == 'manage_view'){
			$this->showGroupsManagement();

		}elseif($wAffichage == 'form_view'){
			$this->showGroupForm($wGroup);
		}

	}

	public function showGroupsManagement(){
		$wGroups = ClubMemberZoneGroupFactory::loadGroups('name');
		$this->_view->showGroupsManagement($wGroups);
	}

	public function showGroupForm($aGroup){
		$wUsersList = get_users();
		$this->_view->showGroupForm($aGroup, $wUsersList);
	}


}