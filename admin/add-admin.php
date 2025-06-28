<?php include("partial-fronts/header-sidebar.php");?>
<?php include("db_conn.php");?>
<script src="script.js"></script>

    <?php
     session_start();
     if(isset($_SESSION['user_name']) AND isset($_SESSION['id'])){
    
    ?>
    <main>            

    <div class="head-title">
				<div class="left">
					<h1>Add User</h1>
				</div>
			</div>

            <form action="" method="post" enctype="multipart/form-data">
            <ul class="box-info2">
				<li>
					<i class='bx bxs-info-circle' ></i>
					<span class="text" style="width: 100%;">
						<h4>Full Name:</h4>
						<input type="text" name="full_name" required style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
					</span>
				</li>
				<li>
					<i class='bx bxs-user' ></i>
					<span class="text" style="width: 100%;">
						<h4>Username:</h4>
						<input type="text" name="username" required style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
					</span>
				</li>
                <li>
					<i class='bx bxs-lock' ></i>
					<span class="text" style="width: 100%;">
						<h4>Password:</h4>
						<input type="password" name="password" required style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
					</span>
				</li>
				<li>
					<i class='bx bxs-lock' ></i>
					<span class="text" style="width: 100%;">
						<h4>Confirm Password:</h4>
						<input type="password" name="confirm_password" required style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
					</span>
				</li>
                <li>
					<i class='bx bxs-briefcase' ></i>
					<span class="text" style="width: 100%; align-items: center;">
						<h4 style="margin-bottom: 0.8em;">Position:</h4>
                        <input type="radio" name="position" value="admin"> <span style="font-weight: 500;margin-right: 4em;">Admin</span>
                        <input type="radio" name="position" value="worker"> <span style="font-weight: 500;">Worker</span>
					</span>
				</li>
				<li style="text-align: center; justify-content: center;">
					<input name="submit" type="submit" value="SUBMIT" style="background-color: #3C91E6; border: none; padding: 0.5em 2em; border-radius: 0.7em; font-size: 1em; color: white; font-weight: 500; cursor: pointer; text-align:center;">
				</li>

			</ul>
        </form>

</main>
    
    <?php
    
     if (isset($_POST['submit']))
     {
        if(isset($_POST['submit']) AND isset($_FILES['my_image']))
        {
            $img_name = $_FILES['my_image']['name'];
            $img_size = $_FILES['my_image']['size'];
            $tmp_name = $_FILES['my_image']['tmp_name'];
            $error = $_FILES['my_image']['error'];

            // Get form data
            $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $position = isset($_POST['position']) ? trim($_POST['position']) : '';

            // Validate form data
            if(empty($full_name) || empty($username) || empty($password) || empty($position)) {
                ?>
                <script>
                    window.location.href = "team.php?message=All fields are required";
                </script>
                <?php
                exit();
            }

            // Validate password strength
            if(strlen($password) < 8) {
                ?>
                <script>
                    window.location.href = "team.php?message=Password must be at least 8 characters long";
                </script>
                <?php
                exit();
            }

            if($error === 0){
                if($img_size > 1250000){
                    ?>
                    <script>
                        window.location.href = "team.php?message=Sorry, your file is too large";
                    </script>
                    <?php
                    exit();
                }
                else{
                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                    $img_ex_lc = strtolower($img_ex);
                    $allowed_exs = array("jpg","jpeg","png");

                    if(in_array($img_ex_lc, $allowed_exs)){
                        $new_img_name = uniqid("IMG-",true).'.'.$img_ex_lc;
                        $img_upload_path = '../img/'.$new_img_name;
                        
                        // Check if directory exists and is writable
                        if(!is_dir('../img/') || !is_writable('../img/')) {
                            ?>
                            <script>
                                window.location.href = "team.php?message=Upload directory error";
                            </script>
                            <?php
                            exit();
                        }

                        if(move_uploaded_file($tmp_name, $img_upload_path)){
                            if($position==="worker")
                            {
                                if($db->insert_worker($full_name, $username, $password, $position, $new_img_name))
                                {
                                    ?>
                                    <script>
                                        window.location.href = "team.php?message=Worker added successfully";
                                    </script>
                                    <?php
                                }
                                else{
                                    ?>
                                    <script>
                                        window.location.href = "team.php?message=Database error occurred";
                                    </script>
                                    <?php
                                }
                            }
                        } else {
                            ?>
                            <script>
                                window.location.href = "team.php?message=Failed to upload image";
                            </script>
                            <?php
                        }
                    }
                    else{
                        ?>
                        <script>
                            window.location.href = "team.php?message=You can only upload JPG, JPEG or PNG files";
                        </script>
                        <?php
                    }
                }
            }
            else{
                ?>
                <script>
                    window.location.href = "team.php?message=Unknown error occurred during file upload";
                </script>
                <?php
            }
        }
    }
    else{
        header("location: index.php?error=login first");
    
    }
    }
    ?>  