<?php
class DB_con {
 
 private $host;
 private $username;
 private $password;
 private $database;
 private $conn;

public function __construct() {
 if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
  $this->host = 'localhost';
  $this->username = 'root';
  $this->password = '';
  $this->database = 'automotive2';
 } else {
  $this->host = 'localhost'; // or your host's DB host if different
  $this->username = 'nati';
  $this->password = 'Nati-0911';
  $this->database = 'automotive';
 }
 $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

if ($this->conn->connect_error) {
die("Connection failed: " . $this->conn->connect_error);
 }
 }


 public function insert_admin($full_name,$username,$password,$position,$new_img_name,$description) {
  $sql = "INSERT INTO tbl_admin (full_name,username,password,position,image_url,description) VALUES ('$full_name','$username','$password','$position','$new_img_name','$description')";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
  return false;
  }
 }
 public function validate($data){
  $data = trim($data);
  $data = htmlspecialchars($data);
  return $data;
}
 public function insert_article($new_img_name,$title,$s_article,$article,$writer,$date,$status) {
  
  $sql = "INSERT INTO tbl_blog (image_url,title,s_article,article,writer,date,status) VALUES ('$new_img_name','$title','$s_article','$article','$writer','$date','$status')";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
  return false;
  }
 }
 public function insert_package($name,$price,$description,$status,$new_img_name) {
  $sql = "INSERT INTO tbl_package (name,price,description,status,image_url) VALUES ('$name','$price','$description','$status','$new_img_name')";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
  return false;
  }
 }
 public function insert_worker($full_name,$username,$password,$position,$new_img_name) {
  $sql = "INSERT INTO tbl_worker (full_name,username,password,position,image_url) VALUES ('$full_name','$username','$password','$position','$new_img_name')";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
  return false;
  }
 }
 public function fetchworker() {
  $sql = "SELECT * FROM tbl_worker";
   $result = $this->conn->query($sql);
   ?>
   <table  class="tbl-full">
     
   <thead>
   <tr>
  <th scope="col">sl no</th>
      <th scope="col">full name</th>
      <th scope="col">username</th>
      <th scope="col">position</th>
      <th scope="col">action</th>
      <!--<th scope="col">operations</th>-->


  </tr>
  <tr>
       <th scope="row">--</th>
               <td>--</td>
               <td>--</td>
               <td>--<td>

           </tr>
</thead>
<tbody>


   <?php
   $n = 1;
  if ($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $name = $row['full_name'];
            $username = $row['username'];
            $password = $row['password'];
            $position = $row['position'];

      ?>
       <tr>
       <th scope="row"><?php echo $n++;?></th>
               <td><?php echo $name;?></td>
               <td><?php echo $username;?></td>
               <td><?php echo $position;?></td>
               <td><a href="delete-worker.php?id=<?php echo $id;?>"><img src="img/delete.png" alt=""></a></td>

           </tr>
      
      <?php
  }
  ?>
      </tbody> 
      </table>

  <?php
   } else {
   echo "No results found";
   }
  }
 public function fetchadmin() {
  $sql = "SELECT * FROM tbl_admin";
   $result = $this->conn->query($sql);
   ?>
   <table  class="tbl-full">
     
   <thead>
  <tr>
  <th scope="col">sl no</th>
      <th scope="col">full name</th>
      <th scope="col">username</th>
      <th scope="col">position</th>
      <th scope="col">action</th>
      <!--<th scope="col">operations</th>-->


  </tr>
  <tr>
       <th scope="row">--</th>
               <td>--</td>
               <td>--</td>
               <td>--<td>

           </tr>
</thead>
<tbody>


   <?php
   $n = 1;
  if ($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $name = $row['full_name'];
            $username = $row['username'];
            $password = $row['password'];
            $position = $row['position'];

      ?>
       <tr>
       <th scope="row"><?php echo $n++;?></th>
               <td><?php echo $name;?></td>
               <td><?php echo $username;?></td>
               <td><?php echo $position;?></td>
               <?php
               if($position==="admin"){
                ?>
                <td><a href="delete-admin.php?id=<?php echo $id;?>"><img src="img/delete.png" alt=""></a></td>

                <?php
               }
               else if($position==="worker"){
                ?>
                <td><a href="delete-worker.php?id=<?php echo $id;?>"><img src="img/delete.png" alt=""></a></td>
                <?php

               }
               ?>

           </tr>
      
      <?php
  }
  ?>
      </tbody> 
      </table>

  <?php
   } else {
   echo "No results found";
   }
  }

  public function fetchmessage() {
    $sql = "SELECT * FROM tbl_message ORDER BY id DESC";
     $result = $this->conn->query($sql);
     ?>
     <table>
          <thead>
  
            <tr>
              <th>sl no</th>

              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">Subject</th>
              <th scope="col">Message</th>
              <th scope="col">Status</th>

              <th scope="col">Action</th>





              
            </tr>				
              </thead>
                          <tbody>
  
  
     <?php
     $n = 1;
    if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
              $id = $row['id'];
              $name = $row['name'];
              $email = $row['email'];
              $subject = $row['subject'];
              $message = $row['message'];
              $status = $row['status'];

  
        ?>
         <tr>
         <th scope="row"><?php echo $n++;?></th>
                 <td><?php echo $name;?></td>
                 <td><?php echo $email;?></td>
                 <td><?php echo $subject;?></td>
                 <td><?php echo $message;?></td>
                 <td><?php echo $status;?></td>


                 <td><a href="update-message.php?id=<?php echo $id;?>"><img src="img/update.png" alt=""></a></td>
  
                 
             </tr>
        
        <?php
    }
    ?>
        </tbody> 
        </table>
  
    <?php
     } else {
     echo "No results found";
     }
    }

    public function fetcharticle() {
      $sql = "SELECT * FROM tbl_blog ORDER BY id DESC";
       $result = $this->conn->query($sql);
       ?>
       <table>
            <thead>
    
              <tr>
                <th>sl no</th>
  
                <th scope="col">Title</th>
                <th scope="col">Article</th>
                <th scope="col">Writer</th>
                <th scope="col">Date</th>
                <th scope="col">status</th>

  
                <th scope="col">Action</th>
  
  
  
  
  
                
              </tr>				
                </thead>
                            <tbody>
    
    
       <?php
       $n = 1;
      if ($result->num_rows > 0) {
       while($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $title = $row['title'];
                $article = $row['article'];
                $writer = $row['writer'];
                $date = $row['date'];
                $status = $row['status'];
  
    
          ?>
           <tr>
           <th scope="row"><?php echo $n++;?></th>
                   <td><?php echo $title;?></td>
                   <td><textarea readonly name="" id="" cols="30" rows="10"><?php echo $article;?></textarea></td>
                   <td><?php echo $writer;?></td>
                   <td><?php echo $date;?></td>
                   <td><?php echo $status;?></td>

  
  
                   <td><a href="update-article.php?id=<?php echo $id;?>"><img src="img/update.png" alt=""></a></td>
    
                   
               </tr>
          
          <?php
      }
      ?>
          </tbody> 
          </table>
    
      <?php
       } else {
       echo "No results found";
       }
      }


    public function countmessage() {
        $sql = "SELECT * FROM tbl_message";
         $result = $this->conn->query($sql);
         ?>
         <?php
         $n = 0;
        if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
            $n++;
        }
        ?>
        <?php
         } else {
         $n=0;
         }
         return $n;
        }

        public function countuser() {
          $sql = "SELECT * FROM tbl_user";
           $result = $this->conn->query($sql);
           ?>
           <?php
           $n = 0;
          if ($result->num_rows > 0) {
           while($row = $result->fetch_assoc()) {
              $n++;
          }
          ?>
          <?php
           } else {
           $n=0;
           }
           return $n;
          }
  
        public function countorder() {
            $sql = "SELECT * FROM tbl_info";
             $result = $this->conn->query($sql);
             ?>
             <?php
             $n = 0;
            if ($result->num_rows > 0) {
             while($row = $result->fetch_assoc()) {
                $n++;
            }
            ?>
            <?php
             } else {
             $n=0;
             }
             return $n;
            }
    
 public function fetchorder($id,$p_id) {
  $sql = "SELECT * FROM tbl_user WHERE id=$id";
  $sql2 = "SELECT * FROM tbl_info WHERE id=$p_id";


   $result = $this->conn->query($sql);
   $result2 = $this->conn->query($sql2);

   ?>
   

                        <?php
   $n = 1;
  if ($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phonenum'];
      ?>
       <main>            
<div class="table-data">
    

				<div class="todo">
					<div class="head">
						<h3>customer information</h3>
						<!--<i class='bx bx-plus' ></i>
						<i class='bx bx-filter' ></i>-->
					</div>
                    
                    <ul class="box-info">

                
				<li>
                    
					<span class="text">
						<h3>Name</h3>
						<p><?php echo $name;?></p>
					</span>
				</li>
                <li>
					<span class="text">
						<h3>Email</h3>
						<p><?php echo $email;?></p>
					</span>
				</li>
                <li>
					<span class="text">
						<h3>Phone number</h3>
						<p><?php echo $phone;?></p>
					</span>
				</li>
                	
				</div>
            </div>
</main>

      <?php
  }
  ?>

    
  <?php
   } 
   if ($result2->num_rows > 0) {
    while($row = $result2->fetch_assoc()) {
         $car_brand = $row['car_brand'];
         $car_model = $row['car_model'];
         $year = $row['year'];
         $service_date = $row['service_date'];
         $mile_age = $row['mile_age'];
         $oil_change = $row['oil_change'];
         $insurance = $row['insurance'];
         $bolo = $row['bolo'];
         $rd_wegen = $row['rd_wegen'];
         $Yemenged_Fend = $row['yemenged_fend'];





       ?>
        <main>            
 <div class="table-data">
     
 
         <div class="todo">
           <div class="head">
             <h3>car information</h3>
             <!--<i class='bx bx-plus' ></i>
             <i class='bx bx-filter' ></i>-->
           </div>
                     
                     <ul class="box-info">
 
                 
         <li>
                     
           <span class="text">
             <h3>Car brand</h3>
             <p><?php echo $car_brand;?></p>
           </span>
         </li>
         
                 <li>
           <span class="text">
             <h3>Car model</h3>
             <p><?php echo $car_model;?></p>
           </span>
         </li>
                 <li>
           <span class="text">
             <h3>Year</h3>
             <p><?php echo $year;?></p>
           </span>
         </li>


         <li>
           <span class="text">
             <h3>service date</h3>
             <p><?php echo $service_date;?></p>
           </span>
         </li>


         <li>
           <span class="text">
             <h3>mile age</h3>
             <p><?php echo $mile_age;?></p>
           </span>
         </li>

         <li>
           <span class="text">
             <h3>oil change</h3>
             <p><?php echo $oil_change;?></p>
           </span>
         </li>
         <li>
           <span class="text">
             <h3>insurance</h3>
             <p><?php echo $insurance;?></p>
           </span>
         </li>
         <li>
           <span class="text">
             <h3>bolo</h3>
             <p><?php echo $bolo;?></p>
           </span>
         </li>
         <li>
           <span class="text">
             <h3>3rd wegen</h3>
             <p><?php echo $rd_wegen;?></p>
           </span>
         </li>
         <li>
           <span class="text">
             <h3>Yemenged Fend</h3>
             <p><?php echo $Yemenged_Fend;?></p>
           </span>
         </li>
                   
         </div>
             </div>
 </main>
 
       <?php
   }
   ?>
 
     
   <?php
    }
   
   
   else {
   echo "No results found";
   }
  }


  public function search($search){
    $sql = "SELECT * FROM tbl_order WHERE name LIKE '%$search%' OR email LIKE '$$search$'";
    $result = $this->conn->query($sql);
    ?>
    <table>
         <thead>
 
           <tr>
             <th>sl no</th>

             <th scope="col">name</th>
                <th scope="col">email</th>
                <th scope="col">phone</th>
                <th scope="col">action</th>


             
           </tr>				
             </thead>
                         <tbody>
                         <?php
    $n = 1;
   if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

      $id = $row['id'];
       
        $name = $row['name'];
        $email = $row['email'];
        $phone=$row['phone'];
        $p_id = $row['p_id'];
     
       ?>
        <tr>
        <th scope="row"><?php echo $n++;?></th>
                <td><?php echo $name;?></td>
                <td><?php echo $email;?></td>
                <td><?php echo $phone;?></td>
                <td><a href="order_detail.php?id=<?php echo $id;?>&p_id=<?php echo $p_id;?>"><img src="img/eye_icon.png" alt=""></a></td>
            </tr>
       <?php
   }
   ?>
     </tbody>
     </table>
   <?php
    } else {
    echo "No results found";
    }
    
    

  }
  public function fetchpackage() {
    $sql = "SELECT * FROM tbl_package  ORDER BY id DESC";
     $result = $this->conn->query($sql);
     ?>
     <table>
          <thead>
  
            <tr>
              <th>sl no</th>

              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">status</th>
              <th scope="col">action</th>


              
            </tr>				
              </thead>
                          <tbody>
                          <?php
     $n = 1;
    if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {

      $id = $row['id'];
     
      $name = $row['name'];
      $description = $row['description'];
      $status=$row['status'];
      
        ?>
         <tr>
         <th scope="row"><?php echo $n++;?></th>
                 <td><?php echo $name;?></td>
                 <td><textarea readonly name="" id="" cols="30" rows="10"><?php echo $description;?></textarea></td>
                 <td><?php echo $status;?></td>
                 <td><a href="update_package.php?id=<?php echo $id; ?>"><img src="img/update.png" alt=""></a></td>
             </tr>
        <?php
    }
    ?>
      </tbody>
      </table>
    <?php
     } else {
     echo "No results found";
     }
    }

    
    public function fetchorderrecent() {
      // Prepare the SQL statement to fetch unpaid invoices
      $sql = "SELECT * FROM tbl_info ORDER BY id DESC LIMIT 3";
      $result = $this->conn->query($sql);
  
      // Initialize a counter for the row number
      $n = 1;
  
      // Check if there are results
      if ($result->num_rows > 0) {
          // Start the table
        
  
          // Loop through each unpaid invoice
          while ($row = $result->fetch_assoc()) {
              $carbrand = htmlspecialchars($row['car_brand']);
              $year = htmlspecialchars($row['year']);
              $userid = (int)$row['userid'];
              $id = (int)$row['id']; // Cast to int for security
  
              // Prepare a single query to fetch user details based on userid
              $sql2 = "SELECT * FROM tbl_user WHERE id = $userid"; // Assuming there's a users table
              $result2 = $this->conn->query($sql2);
  
              // Fetch user details if available
              if ($result2->num_rows > 0) {
                  $user = $result2->fetch_assoc();
                  $name = htmlspecialchars($user['name']);
                  $email = htmlspecialchars($user['email']);
                  
              } else {
                  // Default values if user not found
                  $firstname = ' ';
                  $lastname = ' ';
              }
  
              // Display the invoice details
              ?>
             <tr>
           <th scope="row"><?php echo $n++;?></th>
                   <td><?php echo $name;?></td>
                   <td><?php echo $email;?></td>
                   <td><?php echo $carbrand;?></td>
                   <td><?php echo $year;?></td>
                   <td><a href="order_detail.php?id=<?php echo $userid;?>&p_id=<?php echo $id;?>"><img src="img/eye_icon.png" alt=""></a></td>
               </tr>
            <?php
          }
  
          // Close the table
          
      } else {
          // No results found
          echo "No results found";
      }
  }


    public function fetchorderrecentz() {
      $sql = "SELECT * FROM tbl_info  ORDER BY id DESC LIMIT 3";
       $result = $this->conn->query($sql);
       ?>
       <table>
            <thead>
    
              <tr>
                <th>sl no</th>
  
                <th scope="col">name</th>
                <th scope="col">email</th>
                <th scope="col">phone</th>
                <th scope="col">action</th>
  
  
                
              </tr>				
                </thead>
                            <tbody>
                            <?php
       $n = 1;
      if ($result->num_rows > 0) {
       while($row = $result->fetch_assoc()) {
  
        $id = $row['id'];
       
        $name = $row['name'];
        $email = $row['email'];
        $phone=$row['phone'];
        $p_id = $row['p_id'];
        
          ?>
           <tr>
           <th scope="row"><?php echo $n++;?></th>
                   <td><?php echo $name;?></td>
                   <td><?php echo $email;?></td>
                   <td><?php echo $phone;?></td>
                   <td><a href="order_detail.php?id=<?php echo $id;?>&p_id=<?php echo $p_id;?>"><img src="img/eye_icon.png" alt=""></a></td>
               </tr>
          <?php
      }
      ?>
        </tbody>
        </table>
      <?php
       } else {
       echo "No results found";
       }
      }

      public function fetchallorder() {
        // Prepare the SQL statement to fetch unpaid invoices
        $sql = "SELECT * FROM tbl_info ORDER BY id DESC";
        $result = $this->conn->query($sql);
    
        // Initialize a counter for the row number
        $n = 1;
    
        // Check if there are results
        if ($result->num_rows > 0) {
            // Start the table
          
    
            // Loop through each unpaid invoice
            while ($row = $result->fetch_assoc()) {
                $carbrand = htmlspecialchars($row['car_brand']);
                $year = htmlspecialchars($row['year']);
                $userid = (int)$row['userid'];
                $id = (int)$row['id']; // Cast to int for security
    
                // Prepare a single query to fetch user details based on userid
                $sql2 = "SELECT * FROM tbl_user WHERE id = $userid"; // Assuming there's a users table
                $result2 = $this->conn->query($sql2);
    
                // Fetch user details if available
                if ($result2->num_rows > 0) {
                    $user = $result2->fetch_assoc();
                    $name = htmlspecialchars($user['name']);
                    $email = htmlspecialchars($user['email']);
                    
                } else {
                    // Default values if user not found
                    $firstname = ' ';
                    $lastname = ' ';
                }
    
                // Display the invoice details
                ?>
               <tr>
             <th scope="row"><?php echo $n++;?></th>
                     <td><?php echo $name;?></td>
                     <td><?php echo $email;?></td>
                     <td><?php echo $carbrand;?></td>
                     <td><?php echo $year;?></td>
                     <td><a href="order_detail.php?id=<?php echo $userid;?>&p_id=<?php echo $id;?>"><img src="img/eye_icon.png" alt=""></a></td>
                 </tr>
              <?php
            }
    
            // Close the table
            
        } else {
            // No results found
            echo "No results found";
        }
    }
  

    
 public function update_message($status,$id) {
  $sql = "UPDATE tbl_message SET status='$status' WHERE id='$id'";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
 return false;
  }
  }
  public function update_package($name,$price,$description,$status,$id) {
    $sql = "UPDATE tbl_package SET name='$name',price='$price',description='$description',status='$status' WHERE id='$id'";
   
    if ($this->conn->query($sql) === TRUE) {
    return true;
    } else {
   return false;
    }
    }
  public function update_article($status,$id) {
    $sql = "UPDATE tbl_blog SET status='$status' WHERE id='$id'";
   
    if ($this->conn->query($sql) === TRUE) {
    return true;
    } else {
   return false;
    }
    }
 
 public function delete_admin($rid) {
 $sql = "DELETE FROM tbl_admin WHERE id='$rid'";
 
if ($this->conn->query($sql) === TRUE) {
 return true;
 } else {
 return false;
 }
 }
 public function delete_worker($rid) {
  $sql = "DELETE FROM tbl_worker WHERE id='$rid'";
  
 if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
  return false;
  }
  }

 public function __destruct() {
 $this->conn->close();
 }
}