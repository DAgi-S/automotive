<?php include("partial-fronts/header-sidebar.php");?>
<?php
if(isset($_POST['search']))
{ 
    $search = $_POST['search'];


    ?>
    <main>
			<div class="head-title">
				<div class="left">
					<h1>Search Result For <?php echo $search;?></h1>

				</div>

			</div>




			<div class="table-data">
				<div class="order">
					<div class="head">

					</div>
					<?php
                        $db = new DB_con();
                        $db->search($search);
					?>
				</div>
		
			</div>
		</main>
    <?php
}
?>