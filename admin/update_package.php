<?php include("partial-fronts/header-sidebar.php");?>
<?php include("db_conn.php");?>

<script src="script.js"></script>

<?php

session_start();

if(isset($_SESSION['user_name']) AND isset($_SESSION['id'])){
?>
    <main>            
<div class="table-data">
    


                    <?php
                    $id  = $_REQUEST['id'];
                    $sql = "SELECT * FROM tbl_package WHERE id = $id";
                    $result = mysqli_query($conn,$sql);
                    if($result){
                        while($row = mysqli_fetch_assoc($result)){
                            ?>
                                        <div class="left">
                <h1>Update Package</h1>
            </div>
        </div>

        <form action="" method="post" enctype="multipart/form-data">
        <ul class="box-info2">
            <li>
                <i class='bx bx-info-circle' ></i>
                <span class="text" style="width: 100%;">
                    <h4>Name:</h4>
                    <input type="text" name="name" required value="<?php echo $row['name']; ?>"
                    style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
                </span>
            </li>
            <li>
                <i class='bx bx-file' style="align-self: start; padding-top: 0.3em;" ></i>
                <span class="text" style="width: 100%;">
                    <h4>Description:</h4>
                    <textarea name="description" id="" rows="5" style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
                        <?php echo $row['description']; ?>
                    </textarea>
                </span>
            </li>
            <li>
                <i class='bx bx-dollar-circle' ></i>
                <span class="text" style="width: 100%;">
                    <h4>Price:</h4>
                    <input type="text" name="price" required value="<?php echo $row['price']; ?>"
                    style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
                </span>
            </li>
            <li>
                <i class='bx bx-image' ></i>
                <span class="text" style="width: 100%;">
                    <h4>Image:</h4>
                    <input type="file" name="my_image" required value="<?php echo $row['image']; ?>"
                    style="margin-top: 0.5em;">
                </span>
            </li>
            
            <li>
                <i class='bx bx-check-circle' ></i>
                <span class="text" style="width: 100%; align-items: center;">
                    <h4 style="margin-bottom: 0.8em;">Status:</h4>
                    <input type="radio" name="status" value="featured" checked> <span style="font-weight: 500;margin-right: 4em;">Featured</span>
                    <input type="radio" name="status" value="none"> <span style="font-weight: 500;">None</span>
                </span>
            </li>
            <li style="text-align: center; justify-content: center;">
                <input name="update" type="submit" value="UPDATE" style="background-color: #3C91E6; border: none; padding: 0.5em 2em; border-radius: 0.7em; font-size: 1em; color: white; font-weight: 500; cursor: pointer; text-align:center;">
            </li>

        </ul>
    </form>

                            <?php

                        }
                    }
                    
                    ?>
                    
                    <?php
                    if(isset($_POST['update']))
                    { 
                        
                        $name =mysqli_real_escape_string($conn,validate($_POST['name']));
                        $price =mysqli_real_escape_string($conn,validate($_POST['price']));
                        $description = mysqli_real_escape_string($conn,validate($_POST['description']));
                        $status =  $_POST['status'];
                        $db = new DB_con();
                        $db->update_package($name,$price,$description,$status,$id);
                        ?>
                        <script>
                              window.location.href = "package.php";
                        </script>
                        <?php
                    }
                    ?>
                           
            </div>
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