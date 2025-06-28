<?php include("partial-fronts/header-sidebar.php");?>
<script src="script.js"></script>

<br><br>

<?php

session_start();

if(isset($_SESSION['user_name_worker']) AND isset($_SESSION['id_worker'])){
?>

<main>
			<div class="head-title">
				<div class="left">
					<h1>Orders</h1>

				</div>

			</div>




			<div class="table-data">
				<div class="order">
					<div class="head">

					</div>
					<?php
					$db = new DB_con();
					$db->fetchallorder();
					
					?>
				</div>
		
			</div>
		</main>
<?php
}
else{
    header("location: ../index.php?error=login first");

}

?>