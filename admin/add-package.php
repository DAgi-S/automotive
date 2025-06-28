<?php include("partial-fronts/header-sidebar.php");?>
<?php include("db_conn.php");?>
<script src="script.js"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin</title>
</head>
<body>
    <?php
     session_start();
     if(isset($_SESSION['user_name']) AND isset($_SESSION['id'])){
    
    ?>
        <main>            

<div class="head-title">
            <div class="left">
                <h1>Add Package</h1>
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
                <i class='bx bx-file' style="align-self: start; padding-top: 0.3em;" ></i>
                <span class="text" style="width: 100%;">
                    <h4>Description:</h4>
                    <textarea name="description" id="" rows="5" style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;"></textarea>
                </span>
            </li>
            <li>
                <i class='bx bx-dollar-circle' ></i>
                <span class="text" style="width: 100%;">
                    <h4>Price:</h4>
                    <input type="text" name="price" required style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
                </span>
            </li>
            <li>
                <i class='bx bx-image' ></i>
                <span class="text" style="width: 100%;">
                    <h4>Image:</h4>
                    <input type="file" name="my_image" required style="margin-top: 0.5em;"></span>
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

    echo"<pre>";
    print_r($_FILES['my_image']);
    echo"</pre>";
    $img_name = $_FILES['my_image']['name'];
    $img_size = $_FILES['my_image']['size'];
    $tmp_name = $_FILES['my_image']['tmp_name'];
    $error = $_FILES['my_image']['error'];
    if($error === 0 ){
        if($img_size > 1250000){
            $em = "Sorry, your file is too large.";
            ?>
            <script>
            window.location.href = "package.php?package=Article added successfully";
         </script>
         <?php

        }
        else{
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION ) ;
            $img_ex_lc = strtolower($img_ex);

            $allowed_exs = array("jpg","jpeg","png");

            if(in_array($img_ex_lc,$allowed_exs)){
                $new_img_name = uniqid("IMG-",true).'.'.$img_ex_lc;
                $img_upload_path = '../img/'.$new_img_name;
                move_uploaded_file($tmp_name,$img_upload_path);

                //insert into database 
                if(1 !=1)
                {
                    echo "Password Doesn't Match, Please Try Again";
        
        
                }
                else{
                    $db = new DB_con();
                    $name =mysqli_real_escape_string($conn,validate($_POST['name']));
                    $description = mysqli_real_escape_string($conn,validate($_POST['description']));
                    $price =mysqli_real_escape_string($conn,validate($_POST['price']));
                    $status=mysqli_real_escape_string($conn,validate($_POST['status']));

                    
                    
                        if($db->insert_package($name,$price,$description,$status,$new_img_name))
                    {   
                        ?>
                        
                               <script>
                                   window.location.href = "package.php?package=Article added successfully";
                                </script>
                        <?php
                        
                    }
                    else{
                        ?>
                        
                        <script>
                            window.location.href = "package.php?message=Try again";
                         </script>
                 <?php
                    }
        
                    
                    
        
                    
                    
                    
                }
                


            }
            else{
                $em = "you can't upload files of this type!";
                ?>
                        
                <script>
                    window.location.href = "package.php?message=<?php echo $em?>";
                 </script>
         <?php
            }
        }

    }
    else{
        $em = "unknown error occurred!";
        header("location: package.php?error=$em");
    }
}
else{

    header("location: package.php");
}
       
     }
    }
    else{
        header("location: index.php?error=login first");
    
    }
    ?>
    
</body>
</html>