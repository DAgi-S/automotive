
<?php include("partial-fronts/db_con.php");?>
        <!--Menu section Ends-->

    <?php
    session_start();
      if(isset($_SESSION['user_name']) AND isset($_SESSION['id'])){
    
        
        $db = new DB_con();
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            if($db->delete_worker($id)){
                header("location: team.php");


                }
            else{
                header("location: team.php");

                
            }

        }
        



        
        
      }
      else{
        header("location: index.php?error=login first");
      }
    ?>
    
</body>
</html>