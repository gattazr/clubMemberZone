<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

require_once(dirname (__FILE__).'/../models/ClubMemberZoneMembershipFactory.php');
require_once(dirname (__FILE__).'/../models/ClubMemberZoneGroupFactory.php');
require_once(dirname (__FILE__).'/../views/clubMemberZone-memberships.php');

Class ClubMemberZoneMembershipsController{

	private $_view;

	public function __construct(){
		$this->_view = new ClubMemberZoneMembershipsView();

	}

	public function makeActions(){
		$wAffichage = 'manage_view';

		if(isset($_POST['save-demand'])){
			$wDemand = ClubMemberZoneMembershipFactory::loadFromKey($_POST['group_uuid'], $_POST['user_id']);
			$wDemand->setReason($_POST['reason']);
			$wDemand->setGroup(ClubMemberZoneGroupFactory::loadFromKey($_POST['group_uuid']));
			$wDemand->setUser(get_user_by('id', $_POST['user_id']));

			$wDemand->save();
		}

		if(isset($_POST['manage-action'])){
			if($_POST['manage-action'] == 'Nouvelle demande'){
				$wAffichage = 'form_view';
				$wDemand = new ClubMemberZoneMembership();
				$wCurrentUser = wp_get_current_user();
				$wDemand->setUser($wCurrentUser);

			}elseif($_POST['manage-action'] == 'edit_demand'){
				$wAffichage = 'form_view';
				list($wUuid, $wUserId) = split('/', $_POST['idDemand'][0]);
				$wDemand = ClubMemberZoneMembershipFactory::loadFromKey($wUuid, $wUserId);

			}elseif($_POST['manage-action'] == 'delete_membership'){
				foreach( $_POST['idMembership'] as $wUuid){
					list($wUuid, $wUserId) = split('/', $wUuid);
					$wMembership = ClubMemberZoneMembershipFactory::loadFromKey($wUuid, $wUserId);
					$wMembership->delete();
				}

			}elseif($_POST['manage-action'] == 'delete_demand'){
				foreach( $_POST['idDemand'] as $wUuid){
					list($wUuid, $wUserId) = split('/', $wUuid);
					$wDemand = ClubMemberZoneMembershipFactory::loadFromKey($wUuid, $wUserId);
					$wDemand->delete();
				}
			}

		}

		if($wAffichage == 'manage_view'){
			$wCurrentUser = wp_get_current_user();
			$this->showManageView($wCurrentUser);
		}elseif($wAffichage == 'form_view'){
			$this->showDemandForm($wDemand);
		}
	}


	public function showManageView($aUser){
		$wMemberships = ClubMemberZoneMembershipFactory::loadMemberships('user_id', $aUser->ID);
		$wDemands = ClubMemberZoneMembershipFactory::loadDemands('user_id', $aUser->ID);
		$this->_view->showManageView($wMemberships,$wDemands);
	}

	public function showDemandForm($aDemand){
		$wGroups = ClubMemberZoneGroupFactory::loadGroups();
		$this->_view->showDemandForm($aDemand, $wGroups);


	}
}