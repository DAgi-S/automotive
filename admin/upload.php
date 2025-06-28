<?php include("db_conn.php")?>
<?php
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
            header("location: team.php?error=$em");

        }
        else{
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION ) ;
            $img_ex_lc = strtolower($img_ex);

            $allowed_exs = array("jpg","jpeg","png");

            if(in_array($img_ex_lc,$allowed_exs)){
                $new_img_name = uniqid("IMG-",true).'.'.$img_ex_lc;
                $img_upload_path = 'imgs/'.$new_img_name;
                move_uploaded_file($tmp_name,$img_upload_path);

                //insert into database 
                $sql = "INSERT INTO tbl_member SET 
                image_url = '$new_img_name'";
                mysqli_query($conn,$sql);
                header("location: team.php");


            }
            else{
                $em = "you can't upload files of this type!";
                header("location: team.php?error=$em");

            }
        }

    }
    else{
        $em = "unknown error occurred!";
        header("location: team.php?error=$em");
    }
}
else{

    header("location: home.php");
}
?>