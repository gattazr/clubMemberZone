<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

Class ClubMemberZoneMembershipsView{

	public function __construct(){}

	public function showManageView($aMemberships, $aDemands){
		$this->showMemberships($aMemberships);
		$this->showDemands($aDemands);
	}

	private function tableMembershipHeader(){
		echo "<th class='manage-column column-cb check-column'><input id='cb-select-all-1' type='checkbox'></th>
				<th>Groupe</th>
				<th>Description</th>
				<th>type</th>
				<th>Propriétaire</th>
				<th>Raison ajout</th>
				";
	}

	private function tableDemandHeader(){
		echo "<th class='manage-column column-cb check-column'><input id='cb-select-all-1' type='checkbox'></th>
				<th>Groupe</th>
				<th>Description</th>
				<th>type</th>
				<th>Propriétaire</th>
				<th>Raison demande</th>
				";
	}

	public function showMemberships($aMemberships){
		echo "<div class='wrap'>";
		echo "	<h2>Mes groupes</h2>";
		echo "</div>";
		echo "<div class='wrap'>";
		echo "	<form id='manageMembershipForm' name='manageMembershipForm' method='POST'>";
		echo "		<div>";
		echo "			<div class='tablenav top'>";
		echo "				<div class='alignleft actions'>";
		echo "					<select name='manage-action' id='manageAction'>";
		echo "						<option value='delete_membership' >Supprimer appartenance</option>";
		echo "					</select>";
		echo "					<input type='submit' value='Appliquer' name='manage-membership'>";
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
		echo "						<td>".$wMembership->getGroup()->getName()."</td>";
		echo "						<td>".$wMembership->getGroup()->getDescription()."</td>";
										if($wMembership->getGroup()->getType() == 'team'){
		echo "						<td>Equipe</td>";
										}elseif($wMembership->getGroup()->getType() == 'activity'){
		echo "						<td>Activité</td>";
										}
		echo "						<td>".$wMembership->getGroup()->getOwner()->user_nicename."</td>";
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
		echo "	<h2>Mes demandes</h2>";
		echo "</div>";
		echo "<div class='wrap'>";
		echo "	<form id='manageDemandForm' name='manageDemandForm' method='POST'>";
		echo "		<div>";
		echo "			<div class='tablenav top'>";
		echo "				<div class='alignleft actions'>";
		echo "					<select name='manage-action' id='manageAction'>";
		echo "						<option value='delete_demand' >Supprimer demande</option>";
		echo "						<option value='edit_demand' >Editer demande</option>";
		echo "					</select>";
		echo "					<input type='submit' value='Appliquer' name='manage-demands'>";
		echo "					<input type='submit' value='Nouvelle demande' name='manage-action'>";
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
		echo "						<td>".$wDemand->getGroup()->getName()."</td>";
		echo "						<td>".$wDemand->getGroup()->getDescription()."</td>";
									if($wDemand->getGroup()->getType() == 'team'){
		echo "						<td>Equipe</td>";
									}elseif($wDemand->getGroup()->getType() == 'activity'){
		echo "						<td>Activité</td>";
									}
		echo "						<td>".$wDemand->getGroup()->getOwner()->user_nicename."</td>";
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

	public function showDemandForm($aDemand, $aGroups){
		echo "<div class='wrap'>";
		echo "	<h2>Mes demandes</h2>";
		echo "	<span><a href='?page=clubMemberZone-memberships'>Retour</a></span>";
		echo "</div>";
		echo "<div class='wrap'>";
		echo " 	<form id='demandForm' name='demandForm' method='POST'>";
		echo "		<input class='formInput' name='user_id' type=hidden value='".$aDemand->getUser()->ID."' >";
		echo "		<div class='row'>";
		echo "			<label>Groupe</label>";
		echo "			<select class='formInput' name='group_uuid' id='group_uuid'>";
						foreach($aGroups as $wGroup){
							if($wGroup->getType()=='team'){
								$wType = 'Equipe';
							}elseif($wGroup->getType()=='activity'){
								$wType = 'Activité';
							}

							$wSelected= ($aDemand->getGroup() && $wGroup->getUuid()==$aDemand->getGroup()->getUuid() )? 'selected': '';

		echo "				<option value='".$wGroup->getUuid()."' $wSelected>".$wGroup->getName()." (".$wType.")"."</option>";
					}
		echo "			</select>";
		echo "		</div>";
		echo "		<div class='row'>";
		echo "			<label>Raison demande</label>";
		echo "			<textarea class='formInput' rows='4' cols='50' name='reason'>".$aDemand->getReason()."</textarea>";
		echo "		</div>";
		echo "		<div class='row rowSubmit'>";
		$wValue = ($aDemand->getReason())? 'Editer appartenance' : 'Demande appartenance';
		echo "			<input type='submit' value='$wValue' name='save-demand'>";
		echo "		</div>";
		echo "	</form>";
		echo "</div>";
	}

}