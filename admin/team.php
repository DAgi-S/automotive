<?php include("partial-fronts/header-sidebar.php");?>
<script src="script.js"></script>

<?php

session_start();

if(isset($_SESSION['user_name']) AND isset($_SESSION['id'])){
?>
<main>
			<div class="head-title">
				<div class="left">
					<h1>Admins</h1>
					<a style="display:flex;align-items:center;" href="add-admin.php"><img src="img/add_admin.png" alt="" width="90px">
					<span style="color: gray;font-size: 1.4em; font-weight:bold;">Add User</span>
					</a>

				</div>

			</div>


			<div class="table-data">
				<div class="order">
					<div class="head">
						
					</div>
					<?php
					$db = new DB_con();
					$db->fetchadmin();

					
					?>
				</div>
				
			</div>
		</main>


		<main>
			<div class="head-title">
				<div class="left">
					<h1>Staff</h1>

				</div>

			</div>


			<div class="table-data">
				<div class="order">
					<div class="head">
						
					</div>
					<?php
					$db = new DB_con();
					$db->fetchworker();

					
					?>
				</div>
				
			</div>
		</main>
<!--
		<div id="modal" class="modal">
  <div class="modal-content">
    <div class="close-button" id="close-button">&times;</div>
    <div>
      <h2>Are you sure you want to delete this User?</h2>
      <div class="popup-prompt-answer">
        <div id="delete-prompt-answer-no" class="prompt-answer-no">NO</div>
        <div id="delete-prompt-answer-yes" class="prompt-answer-yes">YES</div>
      </div>
    </div>
  </div>
</div>


<?php
}
else{
    header("location: index.php?error=login first");

}

?>