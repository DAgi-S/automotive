<?php include("partial-fronts/header-sidebar.php");?>
<script src="script.js"></script>

<?php

session_start();

if(isset($_SESSION['user_name']) AND isset($_SESSION['id'])){
?>

<main>
			<div class="head-title">
			<div class="left">
					<h1>Packages</h1>
                    <a href="add-package.php" style="display:flex;align-items:center;">
						<img src="img/k.png" alt="" width="90px">
						<span style="color: gray;font-size: 1.4em; font-weight:bold;">Add Package</span>
					</a>


				</div>

			</div>




			<div class="table-data">
				<div class="order">
					<div class="head">

					</div>
					<?php
					$db = new DB_con();
					$db->fetchpackage();
					
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