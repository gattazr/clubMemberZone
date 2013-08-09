<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

Class ClubMemberZoneGroupsView{

	public function __construct(){}

	private function manageTableHeader(){
		echo "<th class='manage-column column-cb check-column'><input id='cb-select-all-1' type='checkbox'></th>
				<th>Nom</th>
				<th>Description</th>
				<th>type</th>
				<th>Propriétaire</th>
				";
	}

	public function showGroupsManagement($wGroups){
		echo "<div class='wrap'>";
		echo "	<h2>Groupes</h2>";
		echo "</div>";
		echo "<div class='wrap'>";
		echo "	<form id='manageGroupForm' name='manageGroupForm' method='POST'>";
		echo "		<div class='tablenav top'>";
		echo "			<div class='alignleft actions'>";
		echo "				<select name='manage-action' id='manageAction'>";
		echo "					<option value='edit_group' >Modifier groupe</option>";
		echo "					<option value='delete_group' >Supprimer groupe</option>";
		echo "				</select>";
		echo "				<input type='submit' value='Appliquer' name='manage-group'>";
		echo "				<input type='submit' value='Nouveau groupe' name='manage-action'>";
		echo "			</div>";
		echo "		</div>";

		echo "		<table class='wp-list-table widefat fixed'>";
		echo "			<thead>";
		echo "				<tr>";
		 						$this->manageTableHeader();
		echo "				</tr>";
		echo "			</thead>";
		echo "			<tbody>";
						if($wGroups){
							foreach($wGroups as $wGroup){
		echo "				<tr>";
		echo "					<th class='check-column'><input type='checkbox' name='uuid[]' value='".$wGroup->getUuid()."'></th>";
		echo "					<td>".$wGroup->getName()."</td>";
		echo "					<td>".$wGroup->getDescription()."</td>";
								if($wGroup->getType() == 'team'){
		echo "					<td>Equipe</td>";
								}elseif($wGroup->getType() == 'activity'){
		echo "					<td>Activité</td>";
								}

		echo "					<td>".$wGroup->getOwner()->user_nicename."</td>";
		echo "				</tr>";
							}
						}
		echo "		</tbody>";
		echo "			<tfoot>";
		echo "				<tr>";
								$this->manageTableHeader();
		echo "				</tr>";
		echo "			</tfoot>";
		echo "		</table>";
		echo "	</form>";
		echo "</div>";
	}

	public function showGroupForm($aGroup, $aUsersList){
		echo "<div class='wrap'>";
		echo "	<h2>Groupes</h2>";
		echo "	<span><a href='?page=clubMemberZone-groups'>Retour</a></span>";
		echo "</div>";
		echo "<div class='wrap'>";
		echo  "	<form method='POST' name='groupForm' id='groupForm'>";
		echo "		<input name='uuid' type='hidden' value='".$aGroup->getUuid()."'>";
		echo "		<div class='row'>";
		echo "			<label>Nom</label>";
		echo "			<input class='formInput' name='name' type='text' value='".$aGroup->getName()."'>";
		echo "		</div>";
		echo "		<div class='row'>";
		echo "			<label>Description</label>";
		echo "			<textarea class='formInput' name='description' rows='4' cols='50'>".$aGroup->getDescription()."</textarea>";
		echo "		</div>";
		echo "		<div class='row'>";
		echo "			<label>Propriétaire</label>";
		echo "			<select class='formInput' name='owner_id'>";
							$wCurrentUser = wp_get_current_user();
							foreach ($aUsersList as $wUser){
								if( (!$aGroup->getName() && $wCurrentUser->ID == $wUser->ID) || $aGroup->getName() && $aGroup->getOwner()->ID == $wUser->ID){
									$wSelected  = 'selected';
								}else{
									$wSelected  = '';
								}
		echo "				<option value='".$wUser->ID."' $wSelected>".$wUser->user_nicename."</option>";
							}
		echo "			</select>";
		echo "		</div>";
		echo "		<div class='row'>";
		echo "			<label>Type</label>";
		echo "			<select class='formInput' name='type'>";
		echo "				<option value='team'>Equipe</option>";
		echo "				<option value='activity'>Activité</option>";
		echo "			</select>";
		echo "		</div>";
		echo "		<div class='row rowSubmit'>";
					$wValue = ($aGroup->getName())? 'Editer' : 'Enregister';
		echo "		<input type='submit' value='$wValue' name='save-group'>";
		echo "		</div>";
		echo "	</form>";
		echo "</div>";

	}

}