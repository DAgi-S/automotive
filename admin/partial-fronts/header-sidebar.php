<?php include("db_con.php");?>
<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
	<!-- My CSS -->
	<link rel="stylesheet" href="style1.css">

	<title>Admin</title>
</head>
<body>
	<div id="sidebar-deactivator"></div>
            <span class="fas fa-times" id="close_btn">
              </span>

	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="dashbord.php" class="brand">
			<i class='bx bxs-user'></i>
			<span class="text">Nati Automotive</span>
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
					<i class='bx bxs-cart' ></i>
					<span class="text">Orders</span>
				</a>
			</li>
			<!--<li>
				<a href="#">
					<i class='bx bxs-doughnut-chart' ></i>
					<span class="text">Admins</span>
				</a>
			</li>-->
			
			<li>
				<a href="team.php">
					<i class='bx bxs-group' ></i>
					<span class="text">Users</span>
				</a>
			</li>
			<li>
				<a href="article.php">
					<i class='bx bxs-file' ></i>
					<span class="text">Articles</span>
				</a>
			</li>
			<li>
				<a href="notifications.php">
					<i class='bx bxs-bell' ></i>
					<span class="text">Notifications</span>
				</a>
			</li>
			<li>
				<a href="admin_chat.php">
					<i class='bx bxs-chat'></i>
					<span class="text">Chats</span>
				</a>
			</li>
			<!-- E-commerce Management -->
			<li class="divider">
				<div class="text-muted small text-uppercase px-3 mb-2">
					<br>
					E-commerce
				</div>
			</li>
			<li>
				<a href="../ecommerce/manage_products.php">
					<i class='bx bxs-package'></i>
					<span class="text">Products</span>
				</a>
			</li>
			<li>
				<a href="../admin/ecommerce/manage_categories.php">
					<i class='bx bxs-category'></i>
					<span class="text">Categories</span>
				</a>
			</li>
			<li>
				<a href="../admin/ecommerce/manage_brands.php">
					<i class='bx bxs-badge'></i>
					<span class="text">Brands</span>
				</a>
			</li>
			<!-- Vehicle Management -->
			<li class="divider">
				<div class="text-muted small text-uppercase px-3 mb-2">
					Vehicles
				</div>
			</li>
			<li>
				<a href="../manage_cars.php">
					<i class='bx bxs-car'></i>
					<span class="text">Cars</span>
				</a>
			</li>
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
			<!--<a href="message.php" class="notification">
				<i class='bx bxs-bell' ></i>
				<span class="num">8</span>
			</a>-->
			<div class="profile">
                    <svg width="2.3rem" height="2.3rem" viewBox="0 0 50 50" class="profile-photo">
                      <circle cx="25" cy="25" r="25" fill="#3C91E6"/>
                      <text x="50%" y="50%" fill="white" font-size="25" text-anchor="middle" dy=".3em">B</text>
                    </svg>
			</div>
		</nav>
