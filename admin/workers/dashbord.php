<?php include("partial-fronts/header-sidebar.php");?>
<script src="script.js"></script>

<?php
$db = new DB_con();

session_start();

if(isset($_SESSION['user_name_worker']) AND isset($_SESSION['id_worker'])){
?>
<main>
			<div class="head-title">
				<div class="left">
					<h1>Dashboard</h1>

				</div>

			</div>

			<ul class="box-info">
				
				<li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<h3>  <?php echo $db->countorder();?></h3>
						<p>Order</p>
					</span>
				</li>

			</ul>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Recent Orders</h3>

					</div>
					<table>
            <thead>
    
              <tr>
                <th>sl no</th>
  
                <th scope="col">name</th>
                <th scope="col">email</th>
                <th scope="col">Carbrand</th>
                <th scope="col">year</th>
  
  
                
              </tr>				
                </thead>
                            <tbody>
					<?php
					$db->fetchorderrecent();
					
					?>
					</tbody>
					</table>
				</div>

			</div>
		</main>
		<!-- MAIN -->


<?php
}
else{
    header("location: ../index.php?error=login first");

}

?>