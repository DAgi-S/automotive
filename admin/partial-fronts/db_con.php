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
 public function insert_article($new_img_name, $title, $s_article, $content, $writer, $date, $status) {
    $sql = "INSERT INTO tbl_blog (image_url, title, s_article, content, writer, status) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ssssss", $new_img_name, $title, $s_article, $content, $writer, $status);
    
    return $stmt->execute();
 }
 public function insert_package($name,$price,$description,$status,$new_img_name) {
  $sql = "INSERT INTO tbl_package (name,price,description,status,image_url) VALUES ('$name','$price','$description','$status','$new_img_name')";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
  return false;
  }
 }
 public function insert_worker($full_name, $username, $password, $position, $new_img_name) {
  // Validate inputs
  $full_name = $this->validate($full_name);
  $username = $this->validate($username);
  
  // Hash password
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  
  // Use prepared statement to prevent SQL injection
  $sql = "INSERT INTO tbl_worker (full_name, username, password, position, image_url) VALUES (?, ?, ?, ?, ?)";
  $stmt = $this->conn->prepare($sql);
  $stmt->bind_param("sssss", $full_name, $username, $hashed_password, $position, $new_img_name);
  
  return $stmt->execute();
 }
 public function fetchworker() {
  $sql = "SELECT * FROM tbl_worker";
   $result = $this->conn->query($sql);
   ?>
   <table  class="tbl-full">
     
   <thead>
   <tr>
  <th scope="col"></th>
      <th scope="col">Full Name</th>
      <th scope="col">Username</th>
      <th scope="col">Position</th>
      <th scope="col">Action</th>
      <!--<th scope="col">operations</th>-->


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
       <td scope="row"><?php echo $n++;?></td>
               <td><?php echo $name;?></td>
               <td><?php echo $username;?></td>
               <td><?php echo $position;?></td>
               <td style="padding-left: 0.7em;"><a href="delete-worker.php?id=<?php echo $id;?>"><i class="bx bxs-trash" alt="" style="color: red;"></i></a></td>

           </tr>
      
      <?php
  }
  ?>
      </tbody> 
      </table>

  <?php
       } else {
        ?>
       <tr>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
       </tr>
       </tbody> 
          </table>
       <?php
       }
      }
 public function fetchadmin() {
  $sql = "SELECT * FROM tbl_admin";
   $result = $this->conn->query($sql);
   ?>
   <table  class="tbl-full">
     
   <thead>
  <tr>
      <th scope="col"></th>
      <th scope="col">Full Name</th>
      <th scope="col">Username</th>
      <th scope="col">Position</th>
      <th scope="col">Action</th>
      <!--<th scope="col">operations</th>-->


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
       <td scope="row"><?php echo $n++;?></td>
               <td><?php echo $name;?></td>
               <td><?php echo $username;?></td>
               <td><?php echo $position;?></td>
               <?php
               if($position==="admin"){
                ?>
                <td style="padding-left: 0.7em;"><a href="delete-admin.php?id=<?php echo $id;?>"><i class="bx bxs-trash" alt="" style="color: red;"></i></a></td>

                <?php
               }
               else if($position==="worker"){
                ?>
                <td style="padding-left: 0.7em;"><a href="delete-admin.php?id=<?php echo $id;?>"><i class="bx bxs-trash" alt="" style="color: red;"></i></div></td>
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
        ?>
       <tr>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
       </tr>
       </tbody> 
          </table>

       <?php
       }
      }

  public function fetchmessage() {
    $sql = "SELECT * FROM tbl_message ORDER BY id DESC";
     $result = $this->conn->query($sql);
     ?>
     <table>
          <thead>
  
            <tr>
              <th></th>

              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">Subject</th>
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
              $status = $row['status'];

  
        ?>
         <tr>
         <td scope="row"><?php echo $n++;?></td>
                 <td><?php echo $name;?></td>
                 <td><?php echo $email;?></td>
                 <td><?php echo $subject;?></td>
                 <td><?php echo $status;?></td>


                 <td><a href="update-message.php?id=<?php echo $id;?>"><i class="fas fa-eye"></i</a></td>
  
                 
             </tr>
        
        <?php
    }
    ?>
        </tbody> 
        </table>
  
    <?php
       } else {
        ?>
       <tr>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
       </tr>
       </tbody> 
          </table>
       <?php
       }
      }

    public function fetcharticle() {
      $sql = "SELECT * FROM tbl_blog ORDER BY id DESC";
       $result = $this->conn->query($sql);
       ?>
       <table>
            <thead>
    
              <tr>
                <th> </th>
  
                <th scope="col">Title</th>
                <th scope="col">Writer</th>
                <th scope="col">Date</th>
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
                $title = $row['title'];
                $writer = $row['writer'];
                $date = $row['date'];
                $status = $row['status'];
  
    
          ?>
           <tr>
           <td scope="row"><?php echo $n++;?></td>
                   <td><?php echo $title;?></td>
                   <td><?php echo $writer;?></td>
                   <td><?php echo $date;?></td>
                   <td><?php echo $status;?></td>

  
  
                   <td><a href="update-article.php?id=<?php echo $id;?>"><i class="bx bxs-edit"></i></a></td>
    
                   
               </tr>
          
          <?php
      }
      ?>
          
    
      <?php
       } else {
        ?>
       <tr>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
       </tr>
       </tbody> 
          </table>
       <?php
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
        $sql = "SELECT * FROM tbl_user WHERE id=?";
        $sql2 = "SELECT * FROM tbl_info WHERE id=?";

        // Prepare and execute first query
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Prepare and execute second query
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bind_param("i", $p_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $name = htmlspecialchars($row['name']);
                $email = htmlspecialchars($row['email']);
                $phone = htmlspecialchars($row['phonenum']);
                ?>
                <div class="container-fluid px-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">Customer Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Name</h5>
                                        <p><?php echo $name; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Email</h5>
                                        <p><?php echo $email; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Phone number</h5>
                                        <p><?php echo $phone; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }

        if ($result2->num_rows > 0) {
            while($row = $result2->fetch_assoc()) {
                $car_brand = htmlspecialchars($row['car_brand']);
                $car_model = htmlspecialchars($row['car_model']);
                $car_year = isset($row['car_year']) ? htmlspecialchars($row['car_year']) : 'N/A';
                $service_date = htmlspecialchars($row['service_date']);
                $mile_age = htmlspecialchars($row['mile_age']);
                $oil_change = htmlspecialchars($row['oil_change']);
                $insurance = htmlspecialchars($row['insurance']);
                $bolo = htmlspecialchars($row['bolo']);
                $rd_wegen = htmlspecialchars($row['rd_wegen']);
                $Yemenged_Fend = htmlspecialchars($row['yemenged_fend']);
                $image1 = isset($row['image1']) ? htmlspecialchars($row['image1']) : '';
                $image2 = isset($row['image2']) ? htmlspecialchars($row['image2']) : '';
                $image3 = isset($row['image3']) ? htmlspecialchars($row['image3']) : '';
                ?>
                <div class="container-fluid px-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Car Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Car Brand</h5>
                                        <p><?php echo $car_brand; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Car Model</h5>
                                        <p><?php echo $car_model; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Year</h5>
                                        <p><?php echo $car_year; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Service Date</h5>
                                        <p><?php echo $service_date; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Mileage</h5>
                                        <p><?php echo $mile_age; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Oil Change</h5>
                                        <p><?php echo $oil_change; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Insurance</h5>
                                        <p><?php echo $insurance; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Bolo</h5>
                                        <p><?php echo $bolo; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>3rd Wegen</h5>
                                        <p><?php echo $rd_wegen; ?></p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-item">
                                        <h5>Yemenged Fend</h5>
                                        <p><?php echo $Yemenged_Fend; ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Car Images Gallery -->
                            <div class="car-images-section mt-4">
                                <h5 class="mb-3">Car Images</h5>
                                <div class="car-images-gallery">
                                    <?php if (!empty($image1)): ?>
                                        <div class="car-image-container">
                                            <img src="assets/img/<?php echo $image1; ?>" alt="Car Image 1" class="car-image">
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($image2)): ?>
                                        <div class="car-image-container">
                                            <img src="assets/img/<?php echo $image2; ?>" alt="Car Image 2" class="car-image">
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($image3)): ?>
                                        <div class="car-image-container">
                                            <img src="assets/img/<?php echo $image3; ?>" alt="Car Image 3" class="car-image">
                                        </div>
                                    <?php endif; ?>
                                    <?php if (empty($image1) && empty($image2) && empty($image3)): ?>
                                        <div class="alert alert-info">No car images available</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="alert alert-info">No results found</div>';
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
    $sql = "SELECT * FROM tbl_package ORDER BY id DESC";
     $result = $this->conn->query($sql);
     ?>
     <table>
          <thead>
  
            <tr>
              <th></th>

              <th scope="col">Name</th>
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
      $status=$row['status'];
      
        ?>
         <tr>
         <td scope="row"><?php echo $n++;?></td>
                 <td><?php echo $name;?></td>
                 <td><?php echo $status;?></td>
                 <td><a href="update_package.php?id=<?php echo $id; ?>"><i class="bx bxs-edit"></i></a></td>
             </tr>
        <?php
    }
    ?>
    <?php
        } else {
          ?>
         <tr>
          <td>--</td>
          <td>--</td>
          <td>--</td>
          <td>--</td>
         </tr>
         </tbody> 
            </table>
         <?php
         }
        }

    
    public function fetchorderrecent() {
      // Prepare the SQL statement to fetch unpaid invoices
      $sql = "SELECT * FROM tbl_info ORDER BY id DESC LIMIT 3";
      $result = $this->conn->query($sql);
      ?>

<table>
            <thead>
    
              <tr>
                <th> </th>
  
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Car Brand</th>
                <th scope="col">Year</th>
                <th></th>
                
              </tr>				
                </thead>
                            <tbody>

    <?php
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
  
              // Initialize user details with default values
              $name = 'Unknown User'; // Default name
              $email = 'No Email'; // Default email
  
              // Prepare a single query to fetch user details based on userid
              $sql2 = "SELECT * FROM tbl_user WHERE id = $userid"; // Assuming there's a users table
              $result2 = $this->conn->query($sql2);
  
              // Fetch user details if available
              if ($result2->num_rows > 0) {
                  $user = $result2->fetch_assoc();
                  $name = htmlspecialchars($user['name']);
                  $email = htmlspecialchars($user['email']);
              }
  
              // Display the invoice details
              ?>
              <tr>
                  <td scope="row"><?php echo $n++; ?></td>
                  <td><?php echo $name; ?></td>
                  <td><?php echo $email; ?></td>
                  <td><?php echo $carbrand; ?></td>
                  <td><?php echo $year; ?></td>
                  <td><a href="order_detail.php?id=<?php echo $userid; ?>&p_id=<?php echo $id; ?>"><i class="fas fa-eye"></i></a></td>
              </tr>
              <?php
          }
  
          // Close the table
        } else {
          ?>
         <tr>
          <td>--</td>
          <td>--</td>
          <td>--</td>
          <td>--</td>
          <td>--</td>
          <td>--</td>
         </tr>
         </tbody> 
            </table>
         <?php
         }
        }


    public function fetchorderrecentz() {
      $sql = "SELECT * FROM tbl_info  ORDER BY id DESC LIMIT 3";
       $result = $this->conn->query($sql);
       ?>
       <table>
            <thead>
    
              <tr>
                <th></th>
  
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
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
        $phone=$row['phone'];
        $p_id = $row['p_id'];
        
          ?>
           <tr>
           <td scope="row"><?php echo $n++;?></td>
                   <td><?php echo $name;?></td>
                   <td><?php echo $email;?></td>
                   <td><?php echo $phone;?></td>
                   <td><a href="order_detail.php?id=<?php echo $id;?>&p_id=<?php echo $p_id;?>"><i class="bx bxs-eye"></i></a></td>
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
        // Prepare the SQL statement to fetch orders with brand names
        $sql = "SELECT i.*, b.brand_name 
                FROM tbl_info i 
                LEFT JOIN car_brands b ON i.car_brand = b.id 
                ORDER BY i.id DESC";
        $result = $this->conn->query($sql);
    
        // Initialize a counter for the row number
        ?>
        <table class="tbl-full">
            <thead>
                <tr>
                    <th> </th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Car Brand</th>
                    <th scope="col">Year</th>
                    <th></th>
                </tr>				
            </thead>
            <tbody>
            <?php 
            $n = 1; 

            // Check if there are results
            if ($result && $result->num_rows > 0) {
                // Loop through each order
                while ($row = $result->fetch_assoc()) {
                    // Use brand_name if available, otherwise use car_brand directly
                    $carbrand = !empty($row['brand_name']) ? htmlspecialchars($row['brand_name']) : 
                               (!empty($row['car_brand']) ? htmlspecialchars($row['car_brand']) : 'N/A');
                    
                    $year = !empty($row['car_year']) ? htmlspecialchars($row['car_year']) : 'N/A';
                    $userid = $row['userid'];
                    $id = $row['id'];

                    // Prepare a single query to fetch user details based on userid
                    $sql2 = "SELECT * FROM tbl_user WHERE id = ?";
                    $stmt = $this->conn->prepare($sql2);
                    $stmt->bind_param("i", $userid);
                    $stmt->execute();
                    $result2 = $stmt->get_result();

                    // Fetch user details if available
                    if ($result2->num_rows > 0) {
                        $user = $result2->fetch_assoc();
                        $name = htmlspecialchars($user['name']);
                        $email = htmlspecialchars($user['email']);
                    } else {
                        $name = 'N/A';
                        $email = 'N/A';
                    }

                    // Display the order details
                    ?>
                    <tr>
                        <td scope="row"><?php echo $n++;?></td>
                        <td><?php echo $name;?></td>
                        <td><?php echo $email;?></td>
                        <td><?php echo $carbrand;?></td>
                        <td><?php echo $year;?></td>
                        <td><a href="order_detail.php?id=<?php echo $userid;?>&p_id=<?php echo $id;?>"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td>--</td>
                    <td>--</td>
                    <td>--</td>
                    <td>--</td>
                    <td>--</td>
                    <td>--</td>
                </tr>
                <?php
            }
            ?>
            </tbody> 
        </table>
        <?php
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

