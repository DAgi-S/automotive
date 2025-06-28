<?php include("partial-fronts/header-sidebar.php");?>
<script src="script.js"></script>

<?php

session_start();

if(isset($_SESSION['user_name']) AND isset($_SESSION['id'])){
?>
<main>
			<div class="head-title">
				<div class="left">
					<h1>Messages</h1>
				</div>

			</div>


			<div class="table-data">
				<div class="order">
					<div class="head">
						
					</div>
					<?php
					$db = new DB_con();
					$db->fetchmessage();
					
					?>
				</div>
				
			</div>
		</main>


<?php
}
else{
    header("location: index.php?error=login first");

}

?>