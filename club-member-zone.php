<?php
/*
Plugin Name: Club Member Zone
Version: 0.3
Description: club member zone
Author: Rémi GATTAZ
Author URI: remi.gattaz@gmail.com
*/

global $wp_version;

if (version_compare($wp_version, "2.8", "<")){
	wp_die("This plugin requires Wordpess version 3.0 or higher.");
}
require_once(dirname (__FILE__).'/includes/models/ClubMemberZoneUUID.php');

class ClubMemberZoneController{

	var $pPlugin_url;

	//Constructeur
	function ClubMemberZoneController(){
		$this->pPlugin_url = plugin_dir_url(__FILE__);
		register_activation_hook(__FILE__, array(&$this,'install')); //Initialisation de la base de donnée à l'installation
		register_deactivation_hook( __FILE__, array(&$this,'uninstall')); //Suppression des élément de la base de donnée à la désinstallation
		add_action('admin_menu', array(&$this, 'admin_menu')); //On ajoute un menu dans l'espace admin
		add_action('delete_user', array(&$this, 'delete_user') );
	}

	public function install(){
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$wTableGroups		= $wpdb->prefix."clubMemberZone_groups";
		$wTableMembership	= $wpdb->prefix."clubMemberZone_memberships";
		$wTablePosts		= $wpdb->prefix."clubMemberZone_posts";

		$wQueries[0] = "CREATE TABLE IF NOT EXISTS $wTableGroups(
					uuid binary(36) NOT NULL,
					name tinytext NOT NULL,
					type tinytext NOT NULL,
					owner_id bigint(20) NOT NULL,
					description text,
					PRIMARY KEY (uuid)
					);";

		$wQueries[1] = "CREATE TABLE IF NOT EXISTS $wTableMembership(
					user_id bigint(20) NOT NULL,
					group_uuid binary(36) NOT NULL,
					accepted bit NOT NULL,
					reason text,
					PRIMARY KEY (user_id,group_uuid),

					CONSTRAINT fk_membership_groupUuid
				    FOREIGN KEY (group_uuid)
				    REFERENCES $wTableGroups(uuid)
				    ON DELETE CASCADE
					);";

		$wQueries[2] = "CREATE TABLE IF NOT EXISTS $wTablePosts(
					uuid binary(36) NOT NULL,
					group_uuid binary(36) NOT NULL,
					post_author bigint(20) NOT NULL,
					post_author_modified bigint(20),
					post_date datetime NOT NULL,
					post_date_modified datetime,
					post_content longtext NOT NULL,
					post_title text NOT NULL,
					PRIMARY KEY (uuid),

					CONSTRAINT fk_post_groupUuid
				    FOREIGN KEY (group_uuid)
				    REFERENCES $wTableGroups(uuid)
				    ON DELETE CASCADE
					);";

		foreach ($wQueries as $wQuery){
			$wpdb->query($wQuery);
			if($wpdb->last_error != ''){
			wp_die($wpdb->last_error);
			}
		}

		//add capabilities
		$wCapabilities = array(	array('edit_posts' 		, 	'clubMemberZone_edit_groups'),
								array('read'			,	'clubMemberZone_access_publishZone'),
								array('read'			,	'clubMemberZone_access_membership'),
								array('read'			,	'clubMemberZone_access_memberZone')
								);

		$wTabRoles = get_editable_roles();

		foreach ( $wTabRoles as $wTabRole ){
			$wRole = get_role(strtolower($wTabRole['name']));

			foreach ($wCapabilities as $wCapability){

				if($wRole->has_cap($wCapability[0])){
					$wRole->add_cap($wCapability[1], true);
				}else{
					$wRole->add_cap($wCapability[1], false);
				}

			}
		}
		//end adding capabilities

		//Create the 5 activities
		require_once(dirname (__FILE__).'/includes/models/ClubMemberZoneGroupFactory.php');
		require_once(dirname (__FILE__).'/includes/models/ClubMemberZoneMembershipFactory.php');
		$wActivities = array(
							array('name'=>'Club',			'type'=>'activity',	'description'=>'Club',			'owner_id'=>1),
							array('name'=>'Judo/Jiu-Jitsu',	'type'=>'activity',	'description'=>'Judo-Jiutsu',	'owner_id'=>1),
							array('name'=>'Aïkido',			'type'=>'activity',	'description'=>'Aïkido',		'owner_id'=>1),
							array('name'=>'Kendo',			'type'=>'activity',	'description'=>'Kendo',			'owner_id'=>1),
							array('name'=>'MMA',			'type'=>'activity',	'description'=>'MMA',			'owner_id'=>1)
		);
		foreach ($wActivities as $wActivity){
			$wGroup = ClubMemberZoneGroupFactory::loadInfosFromTab($wActivity);
			$wCatId = wp_create_category($wGroup->getName());
			$wGroup->save();

			$wMembership = new ClubMemberZoneMembership();
			$wMembership->setUser($wGroup->getOwner());
			$wMembership->setGroup($wGroup);
			$wMembership->setAccepted(1);
			$wMembership->setReason('owner');
			$wMembership->save();
		}
		//end create the 5 activities
	}

	public function uninstall(){
		global $wpdb;
		$wTableGroups		= $wpdb->prefix."clubMemberZone_groups";
		$wTableMembership	= $wpdb->prefix."clubMemberZone_memberships";
		$wTablePosts		= $wpdb->prefix."clubMemberZone_posts";

		//remove table in the database
		$wQueries[0] = "DROP TABLE IF EXISTS $wTableMembership;";
		$wQueries[1] = "DROP TABLE IF EXISTS $wTablePosts;";
		$wQueries[2] = "DROP TABLE IF EXISTS $wTableGroups;";
		foreach ($wQueries as $wQuery){
			$wpdb->query($wQuery);
			if($wpdb->last_error != ''){
			wp_die($wpdb->last_error);
			}
		}
		// end remove table

		//remove capabilities
		$wCapabilities = array(	array('edit_posts' 		, 	'clubMemberZone_edit_groups'),
								array('read'			,	'clubMemberZone_access_publishZone'),
								array('read'			,	'clubMemberZone_access_membership'),
								array('read'			,	'clubMemberZone_access_memberZone')
								);

		$wTabRoles = get_editable_roles();
		foreach ( $wTabRoles as $wTabRole ){
			$wRole = get_role(strtolower($wTabRole['name']));

			foreach ($wCapabilities as $wCapability){
				$wRole->remove_cap($wCapability[1]);
			}
		}
		//end remove capabilities
	}

	function delete_user($aUserId){
		global $wpdb;
		$wTableGroups		= $wpdb->prefix."clubMemberZone_groups";
		$wTableMembership	= $wpdb->prefix."clubMemberZone_memberships";
		$wTablePosts		= $wpdb->prefix."clubMemberZone_posts";

		$wQueries[0] = "DELETE FROM $wTableMembership WHERE user_id=$aUserId";
		$wQueries[1] = "UPDATE $wTableGroups SET owner_id=1 WHERE owner_id=$aUserId";
		$wQueries[2] = "UPDATE $wTablePosts SET post_author=1 WHERE post_author=$aUserId";
		$wQueries[3] = "UPDATE $wTablePosts SET post_author_modified=1 WHERE post_author_modified=$aUserId";

		foreach ($wQueries as $wQuery){
			$wpdb->query($wQuery);
			if($wpdb->last_error != ''){
				wp_die($wpdb->last_error);
			}
		}
	}

	function admin_menu(){ //Creation of the menu in the wordpress administration
		//Add the containing menu
		add_menu_page(
			'Club Manager',
			'Club Manager',
			'clubMemberZone_access_memberZone',
			'clubMemberZone-memberZone',
			array (&$this, 'load_view')
			//$this->pPlugin_url.'club-manager.png'
			);

		//add the submenus
		add_submenu_page( 'clubMemberZone-memberZone' , 	'Zone membre',		'Zone membre',		'clubMemberZone_access_memberZone',	'clubMemberZone-memberZone',	array(&$this, 'load_view'));
		add_submenu_page( 'clubMemberZone-memberZone' , 	'Mes groupes',		'Mes groupes',		'clubMemberZone_access_membership',	'clubMemberZone-memberships',	array(&$this, 'load_view'));
		add_submenu_page( 'clubMemberZone-memberZone' , 	'Zone publication',	'Zone publication',	'clubMemberZone_access_publishZone',	'clubMemberZone-publishZone',	array(&$this, 'load_view'));
		add_submenu_page( 'clubMemberZone-memberZone' , 	'Groupes',			'Groupes',			'clubMemberZone_edit_groups',			'clubMemberZone-groups',		array(&$this, 'load_view'));
	}

	public function load_view() { //Load views

		echo "<link rel='stylesheet' id='clubMemberZone-admin-css' href='".$this->pPlugin_url."includes/css/admin.css' type='text/css' media='all'>";

		switch ($_GET['page']){
			case "clubMemberZone-memberZone" :
				require_once(dirname (__FILE__).'/includes/controllers/clubMemberZone-memberZone.php');
				$wMemberZoneController = new ClubMemberZoneMemberZoneController();
				$wMemberZoneController->makeActions();

			break;
			case "clubMemberZone-memberships" :
				require_once(dirname (__FILE__).'/includes/controllers/clubMemberZone-memberships.php');
				$wMembershipsController = new ClubMemberZoneMembershipsController();
				$wMembershipsController->makeActions();

			break;
			case "clubMemberZone-publishZone" :
				require_once(dirname (__FILE__).'/includes/controllers/clubMemberZone-publishZone.php');
				$wPublishZoneController = new ClubMemberZonePublishZoneController();
				$wPublishZoneController->makeActions();


			break;
			case "clubMemberZone-groups" :
				require_once(dirname (__FILE__).'/includes/controllers/clubMemberZone-groups.php');
				$wGroupsController = new ClubMemberZoneGroupsController();
				$wGroupsController->makeActions();



			break;
			default :
			break;
		}
	}

}

new ClubMemberZoneController();