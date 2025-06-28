<?php
if (!defined('INCLUDED')) {
    header("Location: ../index.php");
    exit();
}

function displayHomeAd($position) {
    try {
        global $con; // Use the existing database connection from db_con.php
        
        if (!$con) {
            // Fallback connection if global connection is not available
            include_once("partial-front/db_con.php");
            if (!$con) {
                throw new Exception("Database connection failed");
            }
        }

        $query = "SELECT id, title, description, image_name, target_url 
                 FROM tbl_ads 
                 WHERE position = ? 
                 AND status = 'active' 
                 AND (start_date IS NULL OR start_date <= CURRENT_DATE)
                 AND (end_date IS NULL OR end_date >= CURRENT_DATE)
                 ORDER BY RAND()
                 LIMIT 1";

        $stmt = $con->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $stmt->bind_param("s", $position);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $ad = $result->fetch_assoc();
            $output = '<div class="ad-container ad-' . htmlspecialchars($position) . '">';
            
            if (!empty($ad['target_url'])) {
                $output .= '<a href="track_ad_click.php?id=' . $ad['id'] . '" target="_blank">';
            }
            
            if (!empty($ad['image_name'])) {
                $image_path = 'assets/images/ads/' . $ad['image_name'];
                $output .= '<img src="' . $image_path . '" alt="' . htmlspecialchars($ad['title']) . '" 
                           onerror="this.parentElement.classList.add(\'img-placeholder\'); this.style.display=\'none\';">';
            }
            
            if (!empty($ad['target_url'])) {
                $output .= '</a>';
            }
            
            $output .= '</div>';
            return $output;
        }

        $stmt->close();
        return ''; // Return empty string if no ad is found

    } catch (Exception $e) {
        error_log("Error in displayHomeAd: " . $e->getMessage());
        return ''; // Return empty string on error
    }
}
?> 