<?php include("partial-fronts/header-sidebar.php");?>
<script src="script.js"></script>

<br><br>

<?php

session_start();

if(isset($_SESSION['user_name_worker']) AND isset($_SESSION['id_worker'])){
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
		$p_id = $_GET['p_id'];

		$db = new DB_con();
		$db->fetchorder($id,$p_id) ;
	}
	
}
else{
    header("location: ../index.php?error=login first");

}

?>