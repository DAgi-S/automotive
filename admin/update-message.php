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
                <h1>Message Detail</h1>
            </div>
        </div>

        <form action="" method="post" enctype="multipart/form-data">
        <ul class="box-info2">
            <li>
                <i class='bx bx-info-circle' ></i>
                <span class="text" style="width: 100%;">
                    <h4>Name:</h4>
                    <input type="text" name="name" required style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
                </span>
            </li>
            <li>
                <i class='bx bx-envelope' ></i>
                <span class="text" style="width: 100%;">
                    <h4>Email:</h4>
                    <input type="email" name="email" required style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
                </span>
            </li>
            <li>
                <i class='bx bx-text' style="align-self: start; padding-top: 0.3em;"></i>
                <span class="text" style="width: 100%;">
                    <h4>Subject:</h4>
                    <input type="text" name="subject" required style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
                </span>
            </li>
            <li>
                <i class='bx bx-file' style="align-self: start; padding-top: 0.3em;" ></i>
                <span class="text" style="width: 100%;">
                    <h4>Message:</h4>
                    <textarea name="s_article" id="" rows="5" style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;"></textarea>
                </span>
            </li>
            <li>
                <i class='bx bx-check-circle' ></i>
                <span class="text" style="width: 100%; align-items: center;">
                    <h4 style="margin-bottom: 0.8em;">Status:</h4>
                    <input type="radio" name="status" value="featured"> <span style="font-weight: 500;margin-right: 4em;">Featured</span>
                    <input type="radio" name="status" value="none"> <span style="font-weight: 500;">None</span>
                </span>
            </li>
            <li style="text-align: center; justify-content: center;">
                <input name="update" type="submit" value="UPDATE" style="background-color: #3C91E6; border: none; padding: 0.5em 2em; border-radius: 0.7em; font-size: 1em; color: white; font-weight: 500; cursor: pointer; text-align:center;">
            </li>

        </ul>
    </form>      


                    <?php
                    if(isset($_POST['update']))
                    { 
                        
                        $status =mysqli_real_escape_string($conn,validate($_POST['status']));
                        $id =mysqli_real_escape_string($conn,validate($_GET['id']));
                        $db = new DB_con();
                        $db->update_message($status,$id);
                        header("location:message.php");
                    }
                    ?>
</main>
    
    <?php
     if (isset($_POST['submit']))
     {
        if($_POST['password'] != $_POST['confirm_password'])
        {
            echo "password doesn't much try again";


        }
        else{
            $db = new DB_con();
            $full_name = $_POST['full_name'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            if($db->insert_admin($full_name,$username,$password))
            {
                header("location: team.php");
            }
            else{
                header("location: team.php");

            }
            
        }
     }
    }
    else{
        header("location: index.php?error=login first");

    }
    ?>