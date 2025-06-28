<?php include("partial-fronts/header-sidebar.php");?>
<?php include("db_conn.php");?>
<script src="script.js"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Article - Nati Automotive</title>
    <!-- Add SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    <?php
     session_start();
     if(isset($_SESSION['user_name']) AND isset($_SESSION['id'])){
    ?>
    <!-- Success/Error Message Display -->
    <?php if(isset($_GET['error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo htmlspecialchars($_GET['error']); ?>',
                confirmButtonColor: '#3C91E6'
            });
        </script>
    <?php endif; ?>

    <?php if(isset($_GET['success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo htmlspecialchars($_GET['success']); ?>',
                confirmButtonColor: '#3C91E6'
            });
        </script>
    <?php endif; ?>

    <main>            
        <div class="head-title">
            <div class="left">
                <h1>Add Article</h1>
            </div>
        </div>

        <!-- Keep existing form code unchanged -->
        <?php echo isset($form_error) ? "<div style='color: red; margin-bottom: 10px;'>{$form_error}</div>" : ""; ?>
        <form action="" method="post" enctype="multipart/form-data">
        <ul class="box-info2">
            <li>
                <i class='bx bx-info-circle'></i>
                <span class="text" style="width: 100%;">
                    <h4>Title:</h4>
                    <input type="text" name="title" required style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
                </span>
            </li>
            <li>
                <i class='bx bxs-user'></i>
                <span class="text" style="width: 100%;">
                    <h4>Writer:</h4>
                    <input type="text" name="writer" value="<?php echo $_SESSION['user_name']; ?>" required style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;">
                </span>
            </li>
            <li>
                <i class='bx bx-text' style="align-self: start; padding-top: 0.3em;"></i>
                <span class="text" style="width: 100%;">
                    <h4>Short Description:</h4>
                    <textarea name="s_article" required rows="3" style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;"></textarea>
                </span>
            </li>
            <li>
                <i class='bx bx-file' style="align-self: start; padding-top: 0.3em;"></i>
                <span class="text" style="width: 100%;">
                    <h4>Main Content:</h4>
                    <textarea name="content" required rows="10" style="width: 100%;flex-grow: 1; border: 1px solid lightgray;border-radius:0.5em;padding:0.6em 0.7em;margin-top: 0.5em;"></textarea>
                </span>
            </li>
            <li>
                <i class='bx bx-image'></i>
                <span class="text" style="width: 100%;">
                    <h4>Featured Image:</h4>
                    <input type="file" name="my_image" accept="image/*" required style="margin-top: 0.5em;">
                    <small style="color: #666;">Recommended size: 800x400px, Max size: 2MB</small>
                </span>
            </li>
            <li>
                <i class='bx bx-check-circle'></i>
                <span class="text" style="width: 100%; align-items: center;">
                    <h4 style="margin-bottom: 0.8em;">Status:</h4>
                    <input type="radio" name="status" value="featured" required> <span style="font-weight: 500;margin-right: 4em;">Featured</span>
                    <input type="radio" name="status" value="none"> <span style="font-weight: 500;">None</span>
                </span>
            </li>
            <li style="text-align: center; justify-content: center;">
                <input name="submit" type="submit" value="PUBLISH ARTICLE" style="background-color: #3C91E6; border: none; padding: 0.5em 2em; border-radius: 0.7em; font-size: 1em; color: white; font-weight: 500; cursor: pointer; text-align:center;">
            </li>
        </ul>
        </form>
    </main>
    
    <?php
    if(isset($_POST['submit']) && isset($_FILES['my_image'])) {
        try {
            // Validate required fields
            if(empty($_POST['title']) || empty($_POST['writer']) || empty($_POST['s_article']) || 
               empty($_POST['content']) || empty($_POST['status'])) {
                throw new Exception("All fields are required!");
            }

            $title = htmlspecialchars($_POST['title']);
            $writer = htmlspecialchars($_POST['writer']);
            $s_article = htmlspecialchars($_POST['s_article']);
            $content = htmlspecialchars($_POST['content']);
            $status = htmlspecialchars($_POST['status']);
            
            $img_name = $_FILES['my_image']['name'];
            $img_size = $_FILES['my_image']['size'];
            $tmp_name = $_FILES['my_image']['tmp_name'];
            $error = $_FILES['my_image']['error'];

            if($error !== 0) {
                throw new Exception("Error uploading file. Please try again.");
            }

            if($img_size > 2097152) {
                throw new Exception("File is too large! Maximum size is 2MB.");
            }

            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $allowed_exs = array("jpg", "jpeg", "png", "webp");

            if(!in_array($img_ex_lc, $allowed_exs)) {
                throw new Exception("Invalid file type! Only JPG, JPEG, PNG & WEBP files are allowed.");
            }

            $new_img_name = uniqid("ARTICLE-", true).'.'.$img_ex_lc;
            $img_upload_path = '../assets/img/'.$new_img_name;
            
            // Create directory if it doesn't exist
            if (!file_exists('../assets/img/')) {
                if(!mkdir('../assets/img/', 0777, true)) {
                    throw new Exception("Failed to create image directory.");
                }
            }
            
            if(!move_uploaded_file($tmp_name, $img_upload_path)) {
                throw new Exception("Failed to upload image. Please try again.");
            }

            $db = new DB_con();
            if(!$db->insert_article($new_img_name, $title, $s_article, $content, $writer, date('Y-m-d H:i:s'), $status)) {
                // If insert fails, remove the uploaded image
                @unlink($img_upload_path);
                throw new Exception("Failed to publish article. Please try again.");
            }

            // Success - redirect with success message
            header("location: article.php?success=Article published successfully!");
            exit();

        } catch (Exception $e) {
            // Error handling - redirect with error message
            header("location: add-article.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
    } else {
        header("location: index.php?error=login first");
    }
    ?>

    <!-- Add error handling for form validation -->
    <script>
    document.querySelector('form').addEventListener('submit', function(e) {
        let requiredFields = this.querySelectorAll('[required]');
        let empty = false;
        
        requiredFields.forEach(field => {
            if (!field.value) {
                empty = true;
                field.style.borderColor = 'red';
            } else {
                field.style.borderColor = 'lightgray';
            }
        });

        if (empty) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please fill in all required fields',
                confirmButtonColor: '#3C91E6'
            });
        }
    });
    </script>
</body>
</html>