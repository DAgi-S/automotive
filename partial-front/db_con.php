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

 public function validate($data){
  $data = trim($data);
  $data = htmlspecialchars($data);
  return $data;
}

 public function insert_users($name,$phonenum,$password,$email,$carbrand,$new_img_name) {
  // Prepare the SQL statement with placeholders
  $stmt = $this->conn->prepare("INSERT INTO tbl_user (name, phonenum, password, car_brand, email,new_img_name) VALUES (?, ?, ?, ?, ?,?)");

  // Check if the statement was prepared successfully
  if ($stmt === false) {
      return false; // or handle the error as needed
  }


  // Bind parameters to the statement
  $stmt->bind_param("ssssss",$name,$phonenum,$password,$carbrand,$email,$new_img_name);

  // Execute the statement
  if ($stmt->execute()) {
      return true; // Success
  } else {
      // Optionally log the error or handle it
      return false; // Failure
  }

  // Close the statement
  $stmt->close();
}

public function insert_message($help,$name,$email,$subject,$question,$status,$userid) {
  $sql = "INSERT INTO tbl_message (help,name,email,subject,question,status,userid) VALUES ('$help','$name','$email','$subject','$question','$status','$userid')";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
  return false;
  }
 }



 public function insert_info($userid, $car_brand, $year, $car_model, $service_date, $mile_age, $oil_change, $insurance, $bolo, $rd_wegen, $yemenged_fend, $img_name1, $img_name2, $img_name3, $plate_number, $trailer_number) {
    $stmt = $this->conn->prepare("INSERT INTO tbl_info (userid, car_brand, car_year, car_model, service_date, mile_age, oil_change, insurance, bolo, rd_wegen, yemenged_fend, img_name1, img_name2, img_name3, plate_number, trailer_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        return false;
    }

    $stmt->bind_param("isssssssssssssss", $userid, $car_brand, $year, $car_model, $service_date, $mile_age, $oil_change, $insurance, $bolo, $rd_wegen, $yemenged_fend, $img_name1, $img_name2, $img_name3, $plate_number, $trailer_number);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}



 public function insert_ulmail($choice,$alternative_email,$userid) {
  $sql = "INSERT INTO tbl_ulmail(choice,alternative_email,userid) VALUES ('$choice','$alternative_email','$userid')";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
  return false;
  }
 }


 public function insert_note($name,$email,$subject,$author,$date,$note,$userid) {
  $sql = "INSERT INTO tbl_note (name,email,subject,author,date,note,userid) VALUES ('$name','$email','$subject','$author','$date','$note','$userid')";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
  return false;
  }
 }


 public function fetchpackage() {
  $sql = "SELECT * FROM tbl_package";
   $result = $this->conn->query($sql);
   ?>
   <?php
   $n = 1;
  if ($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {

            $ssl_status = $row['ssl_status'];
            $free_domain = $row['free_domain'];
            $storage = $row['storage'];
            $support = $row['support'];
            $website_template = $row['website_template'];
            $name = $row['name'];
            $price = $row['price'];
            $id = $row['id'];

            if(isset($_GET['message'])){
              $dn = $_GET['message'];

            }

      ?>
       <div class="item">
                    <div class="single-hosting-price">
                        <h2><?php echo $name?></h2>
                        <div class="pricing-amount">
                            <span class="currency">ETB </span>
                            <span class="price">
                            <?php echo $price?>
                                  </span>
                            <span class="subscription">
                                      /yearly
                                  </span>
                        </div>
                            <ul>
                            <li><strong class="pt"><?php echo $ssl_status?></strong></li>
                            <li><strong class="pt"><?php echo $free_domain?></strong></li>
                            <li><strong class="pt"><?php echo $storage?></strong></li>
                            <li><strong class="pt"><?php echo $support?></strong></li>
                            <li><strong class="pt"><?php echo $website_template?></strong></li>
                            
                            </ul>
                        <a class="green-btn" href="check_order.php?id=<?php echo $id; ?>&domain=<?php echo $dn;?>">Select Plan</a>
                    </div>
                </div>
      
      <?php
  }
  ?>
      

  <?php
   } else {
   echo "No results found";
   }
  }

  public function fetcharticle() {
    $sql = "SELECT * FROM tbl_blog WHERE status = 'featured' ORDER BY id DESC";
    $result = $this->conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        ?>
        <div class="blog-card">
          <div class="img-container">
            <?php if (!empty($row['image_url'])) : ?>
              <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                   alt="<?php echo htmlspecialchars($row['title']); ?>" 
                   onerror="this.src='assets/images/gallery/default-blog.jpg'"/>
            <?php else: ?>
              <img src="assets/images/gallery/default-blog.jpg" alt="Default Image"/>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <h5 class="card-title">
              <?php echo htmlspecialchars($row['title']); ?>
            </h5>
            <p class="card-text">
              <?php echo htmlspecialchars($row['s_article']); ?>
            </p>
            <div class="blog-meta">
              <div><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['writer']); ?></div>
              <div><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($row['date'])); ?></div>
            </div>
            <div class="text-end mt-auto">
              <a href="blog.php?blogid=<?php echo $row['id']; ?>" class="read-more-btn">
                Read More <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
        <?php
      }
    } else {
      echo '<div style="grid-column: 1/-1;"><div class="alert alert-info">No blogs available.</div></div>';
    }
  }



    public function fetchinfo($id) {
      $sql = "SELECT * FROM tbl_info WHERE userid= '$id' ";
       $result = $this->conn->query($sql);
       ?>
       
    
    
       <?php
       
      if ($result->num_rows > 0) {
       while($row = $result->fetch_assoc()) {
    
    
          ?>
     <div class="tab-content" id="course-tab-btn">
                  <div class="tab-pane fade show active" id="course-content" role="tabpanel" tabindex="0">
                    

                  <div class="mentor-course-tab-wrap mt-16">
                      <div class="shop-now2-sec">
                        <div class="checkout-screen-top">
                          <div class="checkout-first">
                            <img src="assets/img/<?php echo $row['img_name1'];?>" height="100px"alt="social-media-img" />
                          </div>
                          <div class="checkout-second">
                            <div>
                              <h1 class="check-txt1">
                                <b>Fighter Girl Character in </b>
                              </h1>
                            </div>
                            <div>
                              <h1 class="check-txt1">
                                Fighter Girl Character in Blender
                              </h1>
                            </div>
                            <div class="checkout-second-wrap mt-12">
                              <div class="checkout-design" style="background: goldenrod">
                                <p>A-12345</p>
                              </div>
                              <div class="checkout-bookmark">
                                <p>&#x1F6E2; 53,000</p>
                              </div>
                            </div>

                            <div class="checkout-second-fourth" style="margin-top: 10px">
                              <div class="bookmark-rating">
                                <div class="bookmark-star">
                                  <img src="assets/svg/yellow-star.svg" alt="star-img" />
                                </div>
                                <div class="bookmark-star">
                                  <img src="assets/svg/yellow-star.svg" alt="star-img" />
                                </div>
                                <div class="bookmark-star">
                                  <img src="assets/svg/yellow-star.svg" alt="star-img" />
                                </div>
                                <div class="bookmark-star">
                                  <img src="assets/svg/yellow-star.svg" alt="star-img" />
                                </div>
                                <div class="bookmark-star">
                                  <img src="assets/svg/light-yellow-star.svg" alt="star-img" />
                                </div>
                              </div>
                              <div>
                                <h4><?php echo $row['car_model'];?></h4>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    </div>
                  </div>
                </div>
          <?php
      }
      ?>
    
    
      <?php
       } else {
       echo "No results found";
       }
      }
 

      public function fetchprofile($id) {
        $sql = "SELECT * FROM tbl_user WHERE id= '$id' ";
         $result = $this->conn->query($sql);
         ?>
         
      
      
         <?php
         
        if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
      
      
            ?>
       <div class="single-mentor-sec-wrap mt-32">
            <div class="single-mentor-first-wrap">
              <div class="mentor-img-sec">
                
                <img src="assets/img/<?php echo $row['new_img_name']; ?>" height="100px" alt="client-img" />
              </div>
              <div class="single-mentor-details">
                <h3><?php echo $row['name'];?></h3>
                <h4 class="mt-12"><?php echo $row['email'];?></h4>
                <p class="mt-12"><?php echo $row['phonenum'];?></p>
                <p style="padding-top: 10px">Registerd: <i><?php echo $row['created_at'];?></i></p>
                <p>
      <a href="edit_profile.php" style="display: inline-block; padding: 10px 20px; background-color: blue; color: white; text-align: center; text-decoration: none; border-radius: 5px;">Update</a>
  </p>            </div>
            </div>
            <?php
        }
        ?>
      
      
        <?php
         } else {
         echo "No results found";
         }
        }


  public function CountMessage($id) {
    $sql = "SELECT * FROM tbl_message WHERE status='answered' AND userid='$id'";
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


 public function fetchNote($id) {
  $sql = "SELECT * FROM tbl_note WHERE userid='$id'";
   $result = $this->conn->query($sql);
   ?>
   <?php
   $n = 1;
  if ($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
            $subject = $row['subject'];
            $author = $row['author'];
            $date = $row['date'];

           

      ?>
       <tr>
       <th scope="row"><?php echo $n++;?></th>
               <td><?php echo $subject?></td>
               <td><?php echo $author;?></td>
               <td><?php echo $date;?></td>
              

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







 public function insert_worker($full_name,$username,$password,$position) {
  $sql = "INSERT INTO tbl_worker (full_name,username,password,position) VALUES ('$full_name','$username','$password','$position')";
 
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

  public function fetchmessage($id) {
    $sql = "SELECT * FROM tbl_message WHERE userid=$id ORDER BY id DESC";
     $result = $this->conn->query($sql);
     ?>
    
                          <tbody>
  
  
     <?php
     $n = 1;
    if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
              $id = $row['id'];
              $type = $row['help'];
              $subject = $row['subject'];
              $issued_date = $row['issued_date'];
              $question = $row['question'];
              $status = $row['status'];

  
        ?>
         <tr>
         <th scope="row"><?php echo $n++;?></th>
                 <td><?php echo $type;?></td>
                 <td><?php echo $subject;?></td>
                 <td><?php echo $question;?></td>
                 <td><?php echo $issued_date;?></td>
                 <td><?php echo $status;?></td>


                 <td><a href="update-message.php?id=<?php echo $id;?>"><img src="img/update.png" alt=""></a></td>
  
                 
             </tr>
        
        <?php
    }
    ?>
       
    <?php
     } else {
     echo "No results found";
     }
    }



    public function fetchmessageans($id) {
      $sql = "SELECT * FROM tbl_message WHERE userid=$id AND status='answered' ORDER BY id DESC";
       $result = $this->conn->query($sql);
       ?>
      
                            <tbody>
    
    
       <?php
       $n = 1;
      if ($result->num_rows > 0) {
       while($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $issued_date = $row['issued_date'];
                $question = $row['question'];
                $answer = $row['answer'];
  
    
          ?>
           <tr>
           <th scope="row"><?php echo $n++;?></th>
                   <td><?php echo $question;?></td>
                   <td><?php echo $answer;?></td>
                   <td><?php echo $issued_date;?></td>


  
  
                   <td><a href="update-message.php?id=<?php echo $id;?>"><img src="img/update.png" alt=""></a></td>
    
                   
               </tr>
          
          <?php
      }
      ?>
         
      <?php
       } else {
       echo "No results found";
       }
      }


    public function CountHosting($id) {
        $sql = "SELECT * FROM tbl_hosting WHERE userid='$id'";
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


        public function fetchPackages($id) {
          $sql = "SELECT * FROM tbl_hosting WHERE userid='$id'";
           $result = $this->conn->query($sql);
           ?>
           <?php
           $n = 1;
          if ($result->num_rows > 0) {
           while($row = $result->fetch_assoc()) {
                    $duedate = $row['duedate'];
                    $billingcycle = $row['billing_cycle'];
                    $status = $row['status'];
                   
        
              ?>
               <tr>
               <th scope="row"><?php echo $n++;?></th>
                       <td><?php echo $duedate?></td>
                       <td><?php echo $billingcycle;?></td>
                       <td><?php echo $status;?></td>
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

        public function CountDomainName($id) {
            $sql = "SELECT * FROM tbl_domain WHERE userid='$id'";
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


            public function fetchDomainName($id) {
              $sql = "SELECT * FROM tbl_domain WHERE userid='$id'";
               $result = $this->conn->query($sql);
               ?>
               <?php
               $n = 1;
              if ($result->num_rows > 0) {
               while($row = $result->fetch_assoc()) {
                        $domain = $row['domain'];
                        $expiredate = $row['expiredate'];
                        $price = $row['price'];
                        $status = $row['status'];

                       
            
                  ?>
                   <tr>
                   <th scope="row"><?php echo $n++;?></th>
                           <td><?php echo $domain?></td>
                           <td><?php echo $expiredate;?></td>
                           <td><?php echo $price;?></td>
                           <td><?php echo $status;?></td>
                          
                           <td><button class="btn btn-success btn-sm">Edit</button></td>
            
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



            public function CountUnpaidInvoice($id) {
              $sql = "SELECT * FROM unpaid_invoice WHERE userid='$id' ";
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


              public function fetchUnpaidInvoice($id) {
                $sql = "SELECT * FROM unpaid_invoice WHERE userid='$id'";
                 $result = $this->conn->query($sql);
                 ?>
                 <?php
                 $n = 1;
                if ($result->num_rows > 0) {
                 while($row = $result->fetch_assoc()) {
                          $description = $row['description'];
                          $due_date = $row['due_date'];
                          $amount = $row['amount'];
                          $total = $row['total'];
                          $status = $row['status'];

                         
              
                    ?>
                     <tr>
                     <th scope="row"><?php echo $n++;?></th>
                             <td><a href=""><?php echo $description?></a></td>
                             <td><?php echo $due_date;?></td>
                             <td><?php echo $amount;?></td>
                             <td><?php echo $total;?></td>
                             <td><?php echo $status;?></td>

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


public function insert_unpaidinvoice($userid,$description,$duedate,$amount,$total,$status) {
  $sql = "INSERT INTO unpaid_invoice (userid,description,due_date,amount,total,status) VALUES ('$userid','$description','$duedate','$amount','$total','$status')";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
  return false;
  }
 }


    
 
  public function search($search){
    $sql = "SELECT * FROM tbl_order WHERE s_name LIKE '%$search%' OR r_name LIKE '$$search$'";
    $result = $this->conn->query($sql);
    ?>
    <table>
         <thead>
 
           <tr>
             <th>sl no</th>

             <th scope="col">kind</th>
             <th scope="col">sender name</th>
             <th scope="col">sender phone</th>
             <th scope="col">action</th>


             
           </tr>				
             </thead>
                         <tbody>
                         <?php
    $n = 1;
   if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

     $id = $row['id'];
    
     $kind = $row['item_kind'];
     $s_name = $row['s_name'];
     $s_phone=$row['s_phone'];
     
       ?>
        <tr>
        <th scope="row"><?php echo $n++;?></th>
                <td><?php echo $kind;?></td>
                <td><?php echo $s_name;?></td>
                <td><?php echo $s_phone;?></td>
                <td><a href="order_detail.php?id=<?php echo $id; ?>"><img src="img/eye_icon.png" alt=""></a></td>
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
    $sql = "SELECT * FROM tbl_order  ORDER BY id DESC LIMIT 3";
     $result = $this->conn->query($sql);
     ?>
     <table>
          <thead>
  
            <tr>
              <th>sl no</th>

              <th scope="col">kind</th>
              <th scope="col">sender name</th>
              <th scope="col">sender phone</th>
              <th scope="col">action</th>


              
            </tr>				
              </thead>
                          <tbody>
                          <?php
     $n = 1;
    if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {

      $id = $row['id'];
     
      $kind = $row['item_kind'];
      $s_name = $row['s_name'];
      $s_phone=$row['s_phone'];
      
        ?>
         <tr>
         <th scope="row"><?php echo $n++;?></th>
                 <td><?php echo $kind;?></td>
                 <td><?php echo $s_name;?></td>
                 <td><?php echo $s_phone;?></td>
                 <td><a href="order_detail.php?id=<?php echo $id; ?>"><img src="img/eye_icon.png" alt=""></a></td>
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
        $sql = "SELECT * FROM tbl_order ORDER BY id DESC";
         $result = $this->conn->query($sql);
         ?>
         <table>
              <thead>
      
                <tr>
                  <th>sl no</th>
    
                  <th scope="col">kind</th>
                  <th scope="col">sender name</th>
                  <th scope="col">sender phone</th>
                  <th scope="col">action</th>
    
    
                  
                </tr>				
                  </thead>
                              <tbody>
                              <?php
         $n = 1;
        if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()) {
    
          $id = $row['id'];
         
          $kind = $row['item_kind'];
          $s_name = $row['s_name'];
          $s_phone=$row['s_phone'];
          
            ?>
             <tr>
             <th scope="row"><?php echo $n++;?></th>
                     <td><?php echo $kind;?></td>
                     <td><?php echo $s_name;?></td>
                     <td><?php echo $s_phone;?></td>
                     <td><a href="order_detail.php?id=<?php echo $id; ?>"><img src="img/eye_icon.png" alt=""></a></td>
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

   
  
 
 
 public function update_message($status,$id) {
  $sql = "UPDATE tbl_message SET status='$status' WHERE id='$id'";
 
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




  public function update_user($firstname, $lastname, $phonenum, $email, $address, $state, $country, $telegram_id, $id) {
    // Prepare the SQL statement
    $sql = "UPDATE tbl_users SET 
                firstname = ?, 
                lastname = ?, 
                phonenum = ?, 
                email = ?, 
                address = ?, 
                state = ?, 
                country = ?, 
                telegram_id = ? 
            WHERE password = ?";

    // Initialize a prepared statement
    $stmt = $this->conn->prepare($sql);
    
    // Check if the statement was prepared successfully
    if ($stmt === false) {
        return "Error preparing statement: " . $this->conn->error;
    }

    // Bind parameters to the prepared statement
    $stmt->bind_param("ssssssssi", $firstname, $lastname, $phonenum, $email, $address, $state, $country, $telegram_id, $id);

    // Execute the statement
    if ($stmt->execute()) {
        return true;
    } else {
        return "Error executing statement: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

public function update_password($confirm_password,$id) {
  $sql = "UPDATE tbl_users SET password='$confirm_password' WHERE password='$id'";
 
  if ($this->conn->query($sql) === TRUE) {
  return true;
  } else {
 return false;
  }
  }




  public function update_info($car_brand,$car_year,$car_model,$service_date,$mile_age,$oil_change,$insurance,$bolo,$rd_wegen,$yemenged_fend,$id) {
    $sql = "UPDATE tbl_info SET 
                car_brand = ?, 
                car_year = ?, 
                car_model = ?, 
                service_date = ?, 
                mile_age = ?, 
                oil_change = ?, 
                insurance = ?, 
                bolo = ?,
                rd_wegen = ?,
                yemenged_fend = ? 
            WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    if ($stmt === false) {
        return "Error preparing statement: " . $this->conn->error;
    }
    $stmt->bind_param("ssssssssssi",$car_brand,$car_year,$car_model,$service_date,$mile_age,$oil_change,$insurance,$bolo,$rd_wegen,$yemenged_fend,$id);
    if ($stmt->execute()) {
        return true;
    } else {
        return "Error executing statement: " . $stmt->error;
    }
    $stmt->close();
}




public function udpate_user($name, $phonenum, $email, $car_brand, $new_img_name, $id) {
  // Prepare the SQL statement with proper column names
  $sql = "UPDATE tbl_user SET 
              name = ?, 
              phonenum = ?, 
              email = ?, 
              car_brand = ?, 
              new_img_name = ?
          WHERE id = ?";

  // Initialize a prepared statement
  $stmt = $this->conn->prepare($sql);
  
  // Check if the statement was prepared successfully
  if ($stmt === false) {
      return false;
  }

  // Bind parameters to the prepared statement
  $stmt->bind_param("sssssi", $name, $phonenum, $email, $car_brand, $new_img_name, $id);

  // Execute the statement
  $success = $stmt->execute();
  
  // Close the statement
  $stmt->close();
  
  return $success;
}



  

 public function __destruct() {
 $this->conn->close();
 }

 public function get_user_cars($user_id) {
    $stmt = $this->conn->prepare("
        SELECT i.*, cb.brand_name, cm.model_name 
        FROM tbl_info i
        LEFT JOIN car_brands cb ON i.car_brand = cb.id
        LEFT JOIN car_models cm ON i.car_model = cm.id
        WHERE i.userid = ?
        ORDER BY i.id DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cars = [];
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
    return $cars;
}

public function get_car_by_id($car_id, $user_id) {
    $stmt = $this->conn->prepare("
        SELECT i.*, cb.brand_name, cm.model_name 
        FROM tbl_info i
        LEFT JOIN car_brands cb ON i.car_brand = cb.id
        LEFT JOIN car_models cm ON i.car_model = cm.id
        WHERE i.id = ? AND i.userid = ?
    ");
    $stmt->bind_param("ii", $car_id, $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

public function update_car($car_id, $user_id, $car_brand, $car_year, $car_model, $service_date, $mile_age, $oil_change, $insurance, $bolo, $rd_wegen, $yemenged_fend, $image1 = null, $image2 = null, $image3 = null) {
    $sql = "UPDATE tbl_info SET 
            car_brand = ?, 
            car_year = ?,
            car_model = ?,
            service_date = ?,
            mile_age = ?,
            oil_change = ?,
            insurance = ?,
            bolo = ?,
            rd_wegen = ?,
            yemenged_fend = ?";
    if ($image1) $sql .= ", image1 = ?";
    if ($image2) $sql .= ", image2 = ?";
    if ($image3) $sql .= ", image3 = ?";
    $sql .= " WHERE id = ? AND userid = ?";
    $stmt = $this->conn->prepare($sql);
    $types = "iissiiissss";
    $params = [$car_brand, $car_year, $car_model, $service_date, $mile_age, $oil_change, $insurance, $bolo, $rd_wegen, $yemenged_fend];
    if ($image1) { $types .= "s"; $params[] = $image1; }
    if ($image2) { $types .= "s"; $params[] = $image2; }
    if ($image3) { $types .= "s"; $params[] = $image3; }
    $types .= "ii";
    $params[] = $car_id;
    $params[] = $user_id;
    $stmt->bind_param($types, ...$params);
    return $stmt->execute();
}

public function delete_car($car_id, $user_id) {
    // First get the image filenames
    $stmt = $this->conn->prepare("SELECT image1, image2, image3 FROM tbl_info WHERE id = ? AND userid = ?");
    $stmt->bind_param("ii", $car_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result) {
        // Delete the image files
        foreach (['image1', 'image2', 'image3'] as $image) {
            if (!empty($result[$image])) {
                $file_path = $_SERVER['DOCUMENT_ROOT'] . '/Automotive/assets/img/' . $result[$image];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        
        // Delete the database record
        $stmt = $this->conn->prepare("DELETE FROM tbl_info WHERE id = ? AND userid = ?");
        $stmt->bind_param("ii", $car_id, $user_id);
        return $stmt->execute();
    }
    return false;
}

// New function to get user appointments
public function get_user_appointments($user_id) {
    $sql = "SELECT a.*, s.service_name, s.price, s.duration 
            FROM tbl_appointments a 
            JOIN tbl_services s ON a.service_id = s.service_id 
            WHERE a.user_id = ? 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    $stmt = $this->conn->prepare($sql);
    
    if ($stmt === false) {
        return false;
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $appointments = array();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
    }
    
    $stmt->close();
    return $appointments;
}

// New function to get available services
public function get_available_services() {
    $sql = "SELECT * FROM tbl_services WHERE status = 'active' ORDER BY service_name";
    
    $result = $this->conn->query($sql);
    
    $services = array();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }
    
    return $services;
}

// Function to cancel an appointment
public function cancel_appointment($appointment_id, $user_id) {
    $sql = "UPDATE tbl_appointments SET status = 'cancelled' WHERE appointment_id = ? AND user_id = ? AND status = 'pending'";
    
    $stmt = $this->conn->prepare($sql);
    
    if ($stmt === false) {
        return false;
    }
    
    $stmt->bind_param("ii", $appointment_id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

public function fetchAllArticles() {
    $sql = "SELECT * FROM tbl_blog WHERE status IN ('featured', 'published') ORDER BY date DESC";
    return $this->conn->query($sql);
}

public function query($sql) {
    return $this->conn->query($sql);
}

// Method to get user data as array
public function get_user_by_id($user_id) {
    $stmt = $this->conn->prepare("SELECT * FROM tbl_user WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $stmt->close();
    return $user_data;
}

public function getConn() {
    return $this->conn;
}

public function update_car_fields($car_id, $user_id, $fields) {
    // $fields is an associative array: ['column' => value, ...]
    $set = [];
    $params = [];
    $types = '';

    foreach ($fields as $col => $val) {
        $set[] = "$col = ?";
        $params[] = $val;
        // Determine type
        if (is_int($val)) $types .= 'i';
        elseif (is_double($val)) $types .= 'd';
        else $types .= 's';
    }
    $sql = "UPDATE tbl_info SET " . implode(', ', $set) . " WHERE id = ? AND userid = ?";
    $params[] = $car_id;
    $params[] = $user_id;
    $types .= 'ii';

    $stmt = $this->conn->prepare($sql);
    if ($stmt === false) return false;

    $stmt->bind_param($types, ...$params);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
}