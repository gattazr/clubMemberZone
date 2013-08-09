<?php
//no direct access to this file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

Class ClubMemberZoneMemberZoneView{

	public function __construct(){}

	public function showTabs($aMemberships,$aCurrentGroupUuid){

		echo "<div class='wrap'>";
		echo "	<h2>Zone membre</h1>";
		echo '		<div id="icon-themes" class="icon32"><br></div>';
		if($aMemberships){
		echo '		<h2 class="nav-tab-wrapper">';
			foreach( $aMemberships as $wMembership){
				$wClass = ($wMembership->getGroup()->getUuid() == $aCurrentGroupUuid)?  'nav-tab-active': '';
		echo "			<a class='nav-tab $wClass' href='?page=clubMemberZone-memberZone&group=".$wMembership->getGroup()->getUuid()."'>".$wMembership->getGroup()->getName()."</a>";
			}
		echo '	</h2>';
		}
		echo "</div>";

	}

	public function showPosts($aPosts){

		foreach ($aPosts as $wPost){
			echo "<div class='post'>";
			echo "<h2>".$wPost->getPostTitle()."</h2>";
			echo "<p>".$wPost->getPostContent()."</p>";
			echo "<span>Publié le ".$wPost->getPostDate()." par ".$wPost->getPostAuthor()->user_nicename." </span>";
			if($wPost->getPostDateModified() != '0000-00-00 00:00:00'){
				echo " - <span>Dernière modification le ".$wPost->getPostDateModified()." par ".$wPost->getPostAuthorModified()->user_nicename." </span>";
			}

			echo "</div>";
			echo "<hr>";
		}
	}
	public function showPagination($aNumberOfPage, $aCurrentPage){

			echo "<div class='pagination'>";
			$wCurrentTab = $_GET['group'];

			$wBegin = ($aCurrentPage-5 < 1)? 1 : $aCurrentPage-5 ;
			$wEnd = ($aCurrentPage+5 > $aNumberOfPage)? $aNumberOfPage : $aCurrentPage+5 ;


			if($aCurrentPage != 1){
				echo "	<a href='?page=clubMemberZone-memberZone&group=".$wCurrentTab."&pageNumber=1'><span class='pagination-number'><<</span></a>";
			}

			$i = $wBegin;
			while($i <= $wEnd){
				$wCurrentClass = ($i == $aCurrentPage) ? 'current' : '';
				echo "	<a href='?page=clubMemberZone-memberZone&group=".$wCurrentTab."&pageNumber=$i'><span class='pagination-number $wCurrentClass'>".$i."</span></a>";
				$i++;
			}
			if($aCurrentPage != $aNumberOfPage){
			echo "	<a href='?page=clubMemberZone-memberZone&group=".$wCurrentTab."&pageNumber=".$aNumberOfPage."'><span class='pagination-number'>>></span></a>";
			}
			echo "</div>";
		}

}