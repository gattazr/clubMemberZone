<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

Class ClubMemberZonePublishZoneView{

	public function __construct(){}

	public function showGroupChooserForm($aGroups, $aCurrentGroup){

		echo "<div class='wrap'>";
		echo "	<h2>Zone publication</h2>";
		echo "</div>";
		echo "<div class='wrap'>";
		echo "	<div class='row'>";
		echo "		<form method='GET' action='?page=clubMemberZone-publishZone' name='groupChooser' id='groupChooser'>";
					foreach($_GET as $k=>$v) {
						if($k != 'group' && $k !='manage')
						echo "<input type=\"hidden\" name=\"".$k."\" value=\"".htmlspecialchars($v)."\" />";
					}
		echo "		<input type='hidden' name='manage' value='post'>";
		echo "			<select class='formInput' name='group'>";
						foreach ($aGroups as $wGroup){
							if($wGroup->getType()=='team'){
								$wType = 'Equipe';
							}elseif($wGroup->getType()=='activity'){
								$wType = 'Activité';
							}
							$wSelected = ($aCurrentGroup && $wGroup->getUuid() == $aCurrentGroup->getUuid())? 'selected' : '';

		echo "				<option value='".$wGroup->getUuid()."' $wSelected>".$wGroup->getName()." (".$wType.")"."</option>";
						}
		echo "			<input type='submit' value='Choisir'>";
		echo "		</form>";
		echo "	</div>";
		echo "</div'>";
	}

	public function showNothingToDo(){
		echo "<div class='wrap'>";
		echo "	<h2>Zone publication</h2>";
		echo "</div>";
		echo "<div class='wrap'>";
		echo "<p> Désolé mais il semblerait que vous ne puissiez rien faire ici.";
		echo "Cette zone vous sera accessible lorsque que vous serez membre d'une équipe ou propriétaire d'une activité</p>";
		echo "</div>";
	}
	public function showPagination($aNumberOfPage, $aCurrentPage){

		echo "<div class='pagination'>";

		$wBegin = ($aCurrentPage-5 < 1)? 1 : $aCurrentPage-5 ;
		$wEnd = ($aCurrentPage+5 > $aNumberOfPage)? $aNumberOfPage : $aCurrentPage+5 ;

		if($aCurrentPage != 1){
			echo "	<a href='?page=clubMemberZone-publishZone&manage=post&group=".$_GET['group']."&pageNumber=1'><span class='pagination-number'><<</span></a>";
		}

		$i = $wBegin;
		while($i <= $wEnd){
			$wCurrentClass = ($i == $aCurrentPage) ? 'current' : '';
			echo "	<a href='?page=clubMemberZone-publishZone&manage=post&group=".$_GET['group']."&pageNumber=$i'><span class='pagination-number $wCurrentClass'>".$i."</span></a>";
			$i++;
		}
		if($aCurrentPage != $aNumberOfPage){
			echo "	<a href='?page=clubMemberZone-publishZone&manage=post&group=".$_GET['group']."&pageNumber=$aNumberOfPage'><span class='pagination-number'>>></span></a>";
		}
		echo "</div>";
	}

	private function managePostTableHeader(){
		echo "<th class='manage-column column-cb check-column'><input id='cb-select-all-1' type='checkbox'></th>
				<th>Titre Article</th>
				<th>Création</th>
				<th>Derniere modification</th>
				";
	}

	public function showTabs($aGroup, $aCurrentTab){

		echo "<div class='wrap'>";
		echo '	<div id="icon-themes" class="icon32"><br></div>';
		echo '	<h2 class="nav-tab-wrapper">';

		echo "		<span>".$aGroup->getName()."</span>";
		$wActiveClass = (isset($_GET['manage']) && $_GET['manage'] == 'post')?'nav-tab-active':'';
		echo "		<a class='nav-tab $wActiveClass' href='?page=clubMemberZone-publishZone&group=".$aGroup->getUuid()."&manage=post'>Articles</a>";
		$wActiveClass = (isset($_GET['manage']) && $_GET['manage'] == 'member')?'nav-tab-active':'';
		echo "		<a class='nav-tab $wActiveClass' href='?page=clubMemberZone-publishZone&group=".$aGroup->getUuid()."&manage=member'>Membres</a>";

		echo '	</h2>';
		echo "</div>";

	}

	private function postTableHeader(){
		echo "<th class='manage-column column-cb check-column'><input id='cb-select-all-1' type='checkbox'></th>
				<th>Titre Article</th>
				<th>Création</th>
				<th>Derniere modification</th>
				";
	}

	public function showPostsManagement($aPosts){
		echo "<div class='wrap'>";
		echo "	<form id='managePosts' name='managePosts' method='POST'>";
		echo "		<div class='tablenav top'>";
		echo "			<div class='alignleft actions'>";
		echo "				<select name='manage-action'' id='manageAction'>";
		echo "					<option value='edit_post' >Editer Article</option>";
		echo "					<option value='delete_post' >Supprimer Article</option>";
		echo "				</select>";
		echo "				<input type='submit' value='Appliquer'>";
		echo "				<input type='submit' value='Nouvel Article' name='manage-action'>";
		echo "			</div>";
		echo "		</div>";
		echo "	<table class='wp-list-table widefat fixed'>";
		echo "		<thead>";
		echo "			<tr>";
							$this->postTableHeader();
		echo "			</tr>";
		echo "		</thead>";
		echo "		<tbody>";
				if($aPosts){
					foreach($aPosts as $wPost){
		echo "			<tr>";
		echo "				<th class='check-column'><input type='checkbox' name='uuid[]' value='".$wPost->getUuid()."'></th>";
		echo "				<td>".$wPost->getPostTitle()."</td>";
		echo "				<td>".$wPost->getPostAuthor()->user_nicename." - ".$wPost->getPostDate()."</td>";
						if($wPost->getPostAuthorModified()){
		echo "				<td>".$wPost->getPostAuthorModified()->user_nicename." - ".$wPost->getPostDateModified()."</td>";
						}else{
		echo "				<td></td>";
						}

		echo "				</tr>";
					}
				}

		echo "		</tbody>";
		echo "			<tfoot>";
		echo "				<tr>";
								$this->postTableHeader();
		echo "				</tr>";
		echo "			</tfoot>";
		echo "		</table>";
		echo "	</form>";
		echo "</div>";

	}

	public function showPostForm($aPost){

		echo "<div class='wrap'>";
		echo "	<form name='postForm' id='postForm' method='POST'>";
		echo "		<input type='hidden' name='group_uuid' value='".$_GET['group']."'>";
		echo "		<input type='hidden' name='uuid' value='".$aPost->getUuid()."'>";
		echo "		<h3><input type='text' class='titlewrap' name='title' size='50' placeholder='Title' value='".$aPost->getPostTitle()."'></h2>";


		$wSettings = array(
				'media_buttons' => false
		);
		wp_editor( $aPost->getPostContent(), 'content', $wSettings );


		echo "		<br>";
		$wValue = ($aPost->getPostTitle())? 'Editer' : 'Enregistrer';
		echo "		<input type='submit' value='".$wValue."' name='save-post'>";
		echo "	</form>";
		echo "</div>";
	}

	public function showMemberManagement($aMemberships, $aDemands){
		$this->showMemberships($aMemberships);
		$this->showDemands($aDemands);
	}

	public function showMemberships($aMemberships){
		echo "<div class='wrap'>";
		echo "	<h2>Les membres</h2>";
		echo "</div>";
		echo "<div class='wrap'>";
		echo "	<form id='manageMembershipForm' name='manageMembershipForm' method='POST'>";
		echo "		<div>";
		echo "			<div class='tablenav top'>";
		echo "				<div class='alignleft actions'>";
		echo "					<select name='manage-action' id='manageAction'>";
		echo "						<option value='delete_membership' >Supprimer membre</option>";
		echo "					</select>";
		echo "					<input type='submit' value='Appliquer' name='manage-membership'>";
		echo "					<input type='submit' value='Nouveau membre' name='manage-action'>";
		echo "				</div>";

		echo "			</div>";
		echo "			<table class='wp-list-table widefat fixed'>";
		echo "				<thead>";
		echo "					<tr>";
		$this->tableMembershipHeader();
		echo "					</tr>";
		echo "				</thead>";
		echo "				<tbody>";
		if($aMemberships){
			foreach($aMemberships as $wMembership){
				echo "					<tr>";
				echo "						<th class='check-column'><input type='checkbox' name='idMembership[]' value='".$wMembership->getGroup()->getUuid()."/".$wMembership->getUser()->ID."'></th>";
				echo "						<td>".$wMembership->getUser()->user_nicename."</td>";
				echo "						<td>".$wMembership->getReason()."</td>";
				echo "					</tr>";
			}
		}
		echo "				</tbody>";

		echo "				<tfoot>";
		echo "						<tr>";
		$this->tableMembershipHeader();
		echo "						</tr>";
		echo "					</tfoot>";
		echo "				</table>";
		echo "			</div>";
		echo "		</form>";
		echo "</div>";
	}

	public function showDemands($aDemands){
		echo "<div class='wrap'>";
		echo "	<h2>Les demandes</h2>";
		echo "</div>";
		echo "<div class='wrap'>";
		echo "	<form id='manageDemandForm' name='manageDemandForm' method='POST'>";
		echo "		<div>";
		echo "			<div class='tablenav top'>";
		echo "				<div class='alignleft actions'>";
		echo "					<select name='manage-action' id='manageAction'>";
		echo "						<option value='accept_demand' >Accepter demande</option>";
		echo "						<option value='delete_demand' >Supprimer demande</option>";
		echo "					</select>";
		echo "					<input type='submit' value='Appliquer' name='manage-demands'>";
		echo "				</div>";

		echo "			</div>";
		echo "			<table class='wp-list-table widefat fixed'>";
		echo "				<thead>";
		echo "					<tr>";
		$this->tableDemandHeader();
		echo "					</tr>";
		echo "				</thead>";
		echo "				<tbody>";
		if($aDemands){
			foreach($aDemands as $wDemand){
		echo "					<tr>";
		echo "						<th class='check-column'><input type='checkbox' name='idDemand[]' value='".$wDemand->getGroup()->getUuid()."/".$wDemand->getUser()->ID."'></th>";
		echo "						<td>".$wDemand->getUser()->user_nicename."</td>";
		echo "						<td>".$wDemand->getReason()."</td>";
		echo "					</tr>";
			}
		}
		echo "				</tbody>";

		echo "				<tfoot>";
		echo "					<tr>";
		$this->tableDemandHeader();
		echo "					</tr>";
		echo "				</tfoot>";
		echo "			</table>";
		echo "		</div>";
		echo "	</form>";
		echo "</div>";

	}

	private function tableMembershipHeader(){
		echo "<th class='manage-column column-cb check-column'><input id='cb-select-all-1' type='checkbox'></th>
				<th>User</th>
				<th>Raison ajout</th>
				";
	}

	private function tableDemandHeader(){
		echo "<th class='manage-column column-cb check-column'><input id='cb-select-all-1' type='checkbox'></th>
				<th>User</th>
				<th>Raison demande</th>
				";
	}

	public function showMemberForm($aUsers){
		echo "<div class='wrap'>";
		echo "	<form name='memberForm' id='memberForm' method='POST'>";
		echo "		<div class='row'>";
		echo "			<input type='hidden' name='group_uuid' value='".$_GET['group']."'>";
		echo "			<input type='hidden' name='accepted' value='true'>";
		echo "			<label>Membre</label>";
		echo "			<select class='formInput' name='user_id' id='userId'>";
		foreach ($aUsers as $wUser){
		echo "				<option value='".$wUser->ID."' >".$wUser->user_nicename."</option>";
		}
		echo "			</select>";
		echo "		</div>";
		echo "		<div class='row'>";
		echo "			<label>Raison ajout</label>";
		echo "			<textarea class='formInput' rows='4' cols='50' name='reason'></textarea>";
		echo "		</div>";
		echo "		<div class='row rowSubmit'>";
		echo "			<input type='submit' value='Ajouter' name='save-member'>";
		echo "		</div>";
		echo "	</form>";
		echo "</div>";

	}



}