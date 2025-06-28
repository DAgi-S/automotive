<?php include("db_con.php");?>
<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="style1.css">

	<title>Admin</title>
</head>
<body>


	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bxs-smile'></i>
			<span class="text">Worker</span>
		</a>
		<ul class="side-menu top">
			<li >
				<a href="dashbord.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="order.php">
					<i class='bx bxs-shopping-bag-alt' ></i>
					<span class="text">Orders</span>
				</a>
			</li>
			<!--<li>
				<a href="#">
					<i class='bx bxs-doughnut-chart' ></i>
					<span class="text">Admins</span>
				</a>
			</li>-->
			<!--<li>
				<a href="message.php">
					<i class='bx bxs-message-dots' ></i>
					<span class="text">Message</span>
				</a>
			</li>-->
			<!--<li>
				<a href="team.php">
					<i class='bx bxs-group' ></i>
					<span class="text">Team</span>
				</a>
			</li>-->
		</ul>
		<ul class="side-menu">
			<!--<li>
				<a href="#">
					<i class='bx bxs-cog' ></i>
					<span class="text">Settings</span>
				</a>
			</li>-->
			<li>
				<a href="logout.php" class="logout">
					<i class='bx bxs-log-out-circle' ></i>
					<span class="text">Logout</span>
				</a>
			</li>
		</ul>
	</section>
	<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu' ></i>
			<form action="search_result.php" method="post">
				<div class="form-input">
					<input type="search" name="search" placeholder="Search order...">
					<button type="submit"  class="search-btn"><i class='bx bx-search' ></i></button>
					
				</div>
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
			<!--<a href="#" class="notification">
				<i class='bx bxs-bell' ></i>
				<span class="num">8</span>
			</a>
			<a href="#" class="profile">
				<img src="img/people.png">
			</a>-->
		</nav>
		<!-- NAVBAR -->
