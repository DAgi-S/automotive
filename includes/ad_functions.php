<?php
/**
 * Advertisement Display Functions
 * Helper functions for displaying ads on the website with analytics tracking
 */

if (!defined('INCLUDED')) {
    die('Direct access not permitted');
}

// Define the root path if not already defined
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . '/includes/db.php';

/**
 * Display advertisement by position
 * @param PDO $conn Database connection
 * @param string $position Ad position (home_top, sidebar, etc.)
 * @param int $limit Maximum number of ads to display
 * @return string HTML output
 */
function displayAd($conn, $position, $limit = 1) {
    try {
        $stmt = $conn->prepare("
            SELECT id, title, description, image_name, target_url, priority 
            FROM tbl_ads 
            WHERE position = ? 
                AND status = 'active' 
                AND start_date <= CURDATE() 
                AND end_date >= CURDATE()
                AND (max_impressions = 0 OR impression_count < max_impressions)
            ORDER BY priority DESC, RAND() 
            LIMIT " . (int)$limit
        );
        $stmt->execute([$position]);
        $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($ads)) {
            return '';
        }
        
        $output = '';
        foreach ($ads as $ad) {
            // Track impression
            trackAdImpression($conn, $ad['id']);
            
            // Generate ad HTML based on position
            $output .= generateAdHTML($ad, $position);
        }
        
        return $output;
        
    } catch (PDOException $e) {
        error_log("Error displaying ads: " . $e->getMessage());
        return '';
    }
}

/**
 * Generate HTML for an advertisement
 * @param array $ad Ad data
 * @param string $position Ad position
 * @return string HTML output
 */
function generateAdHTML($ad, $position) {
    $adId = $ad['id'];
    $title = htmlspecialchars($ad['title']);
    $description = htmlspecialchars($ad['description']);
    $imageUrl = $ad['image_name'] ? 'uploads/ads/' . htmlspecialchars($ad['image_name']) : '';
    $targetUrl = htmlspecialchars($ad['target_url']);
    
    // Add click tracking to URL
    $clickUrl = $targetUrl ? "javascript:trackAdClick($adId, '$targetUrl')" : '#';
    
    switch ($position) {
        case 'home_top':
        case 'home_middle':
        case 'home_bottom':
            return generateBannerAd($ad, $clickUrl, $imageUrl, $title, $description);
            
        case 'sidebar':
            return generateSidebarAd($ad, $clickUrl, $imageUrl, $title, $description);
            
        case 'small_banner':
            return generateSmallBannerAd($ad, $clickUrl, $imageUrl, $title);
            
        case 'mini_square':
            return generateSquareAd($ad, $clickUrl, $imageUrl, $title);
            
        default:
            return generateDefaultAd($ad, $clickUrl, $imageUrl, $title, $description);
    }
}

/**
 * Generate banner advertisement HTML
 */
function generateBannerAd($ad, $clickUrl, $imageUrl, $title, $description) {
    return "
    <div class='advertisement-banner mb-4' data-ad-id='{$ad['id']}'>
        <div class='ad-container' style='position: relative; overflow: hidden; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
            " . ($imageUrl ? "
            <a href='$clickUrl' class='ad-link'>
                <img src='$imageUrl' alt='$title' class='ad-image' style='width: 100%; height: 200px; object-fit: cover;'>
                <div class='ad-overlay' style='position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.7)); color: white; padding: 1rem;'>
                    <h5 class='ad-title mb-1'>$title</h5>
                    " . ($description ? "<p class='ad-description mb-0 small'>$description</p>" : "") . "
                </div>
            </a>
            " : "
            <div class='ad-text-only' style='padding: 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center;'>
                <h4 class='ad-title'>$title</h4>
                " . ($description ? "<p class='ad-description'>$description</p>" : "") . "
                " . ($clickUrl !== '#' ? "<a href='$clickUrl' class='btn btn-light btn-sm'>Learn More</a>" : "") . "
            </div>
            ") . "
            <small class='ad-label' style='position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.5); color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;'>Ad</small>
        </div>
    </div>";
}

/**
 * Generate sidebar advertisement HTML
 */
function generateSidebarAd($ad, $clickUrl, $imageUrl, $title, $description) {
    return "
    <div class='advertisement-sidebar mb-3' data-ad-id='{$ad['id']}'>
        <div class='card ad-card' style='border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
            " . ($imageUrl ? "
            <a href='$clickUrl' class='ad-link'>
                <img src='$imageUrl' alt='$title' class='card-img-top ad-image' style='height: 150px; object-fit: cover;'>
            </a>
            " : "") . "
            <div class='card-body p-3'>
                <h6 class='card-title ad-title'>" . ($clickUrl !== '#' ? "<a href='$clickUrl' class='text-decoration-none'>$title</a>" : $title) . "</h6>
                " . ($description ? "<p class='card-text ad-description small text-muted'>$description</p>" : "") . "
                " . ($clickUrl !== '#' ? "<a href='$clickUrl' class='btn btn-primary btn-sm'>View Details</a>" : "") . "
                <small class='ad-label text-muted' style='font-size: 10px;'>Advertisement</small>
            </div>
        </div>
    </div>";
}

/**
 * Generate small banner advertisement HTML
 */
function generateSmallBannerAd($ad, $clickUrl, $imageUrl, $title) {
    return "
    <div class='advertisement-small-banner mb-3' data-ad-id='{$ad['id']}'>
        <div class='ad-container' style='position: relative; height: 90px; overflow: hidden; border-radius: 0.25rem; border: 1px solid #dee2e6;'>
            " . ($imageUrl ? "
            <a href='$clickUrl' class='ad-link'>
                <img src='$imageUrl' alt='$title' class='ad-image' style='width: 100%; height: 100%; object-fit: cover;'>
            </a>
            " : "
            <div class='ad-text-only d-flex align-items-center justify-content-center h-100' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'>
                <span class='ad-title'>$title</span>
            </div>
            ") . "
            <small class='ad-label' style='position: absolute; top: 2px; right: 2px; background: rgba(0,0,0,0.5); color: white; padding: 1px 4px; border-radius: 2px; font-size: 9px;'>Ad</small>
        </div>
    </div>";
}

/**
 * Generate square advertisement HTML
 */
function generateSquareAd($ad, $clickUrl, $imageUrl, $title) {
    return "
    <div class='advertisement-square mb-3' data-ad-id='{$ad['id']}'>
        <div class='ad-container' style='position: relative; width: 100%; aspect-ratio: 1; overflow: hidden; border-radius: 0.25rem; border: 1px solid #dee2e6;'>
            " . ($imageUrl ? "
            <a href='$clickUrl' class='ad-link'>
                <img src='$imageUrl' alt='$title' class='ad-image' style='width: 100%; height: 100%; object-fit: cover;'>
            </a>
            " : "
            <div class='ad-text-only d-flex align-items-center justify-content-center h-100 text-center p-2' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'>
                <span class='ad-title small'>$title</span>
            </div>
            ") . "
            <small class='ad-label' style='position: absolute; top: 2px; right: 2px; background: rgba(0,0,0,0.5); color: white; padding: 1px 4px; border-radius: 2px; font-size: 9px;'>Ad</small>
        </div>
    </div>";
}

/**
 * Generate default advertisement HTML
 */
function generateDefaultAd($ad, $clickUrl, $imageUrl, $title, $description) {
    return "
    <div class='advertisement-default mb-3' data-ad-id='{$ad['id']}'>
        <div class='card ad-card'>
            " . ($imageUrl ? "
            <a href='$clickUrl' class='ad-link'>
                <img src='$imageUrl' alt='$title' class='card-img-top ad-image' style='height: 200px; object-fit: cover;'>
            </a>
            " : "") . "
            <div class='card-body'>
                <h5 class='card-title ad-title'>" . ($clickUrl !== '#' ? "<a href='$clickUrl' class='text-decoration-none'>$title</a>" : $title) . "</h5>
                " . ($description ? "<p class='card-text ad-description'>$description</p>" : "") . "
                " . ($clickUrl !== '#' ? "<a href='$clickUrl' class='btn btn-primary'>Learn More</a>" : "") . "
                <small class='text-muted'>Advertisement</small>
            </div>
        </div>
    </div>";
}

/**
 * Track advertisement impression
 * @param PDO $conn Database connection
 * @param int $adId Advertisement ID
 */
function trackAdImpression($conn, $adId) {
    try {
        $stmt = $conn->prepare("UPDATE tbl_ads SET impression_count = impression_count + 1 WHERE id = ?");
        $stmt->execute([$adId]);
    } catch (PDOException $e) {
        error_log("Error tracking ad impression: " . $e->getMessage());
    }
}

/**
 * Track advertisement click
 * @param PDO $conn Database connection
 * @param int $adId Advertisement ID
 */
function trackAdClick($conn, $adId) {
    try {
        $stmt = $conn->prepare("UPDATE tbl_ads SET click_count = click_count + 1 WHERE id = ?");
        $stmt->execute([$adId]);
        
        // Return success response for AJAX
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log("Error tracking ad click: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

/**
 * Get advertisement statistics
 * @param PDO $conn Database connection
 * @param int $adId Advertisement ID (optional)
 * @return array Statistics
 */
function getAdStatistics($conn, $adId = null) {
    try {
        if ($adId) {
            $stmt = $conn->prepare("
                SELECT 
                    title,
                    impression_count,
                    click_count,
                    CASE 
                        WHEN impression_count > 0 THEN ROUND((click_count / impression_count) * 100, 2)
                        ELSE 0 
                    END as ctr
                FROM tbl_ads 
                WHERE id = ?
            ");
            $stmt->execute([$adId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->query("
                SELECT 
                    COUNT(*) as total_ads,
                    SUM(impression_count) as total_impressions,
                    SUM(click_count) as total_clicks,
                    CASE 
                        WHEN SUM(impression_count) > 0 THEN ROUND((SUM(click_count) / SUM(impression_count)) * 100, 2)
                        ELSE 0 
                    END as overall_ctr
                FROM tbl_ads
            ");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        error_log("Error getting ad statistics: " . $e->getMessage());
        return [];
    }
}

/**
 * Generate ad click tracking JavaScript
 * @return string JavaScript code
 */
function getAdTrackingScript() {
    return "
    <script>
    function trackAdClick(adId, targetUrl) {
        // Track the click via AJAX
        fetch('includes/track_ad_click.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ad_id: adId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to target URL
                if (targetUrl && targetUrl !== '#') {
                    window.open(targetUrl, '_blank');
                }
            }
        })
        .catch(error => {
            console.error('Error tracking ad click:', error);
            // Still redirect even if tracking fails
            if (targetUrl && targetUrl !== '#') {
                window.open(targetUrl, '_blank');
            }
        });
    }
    </script>";
}

function getActiveAds($position) {
    $conn = getConnection();
    
    try {
        if (!$conn) {
            throw new PDOException("Database connection not available");
        }

        $stmt = $conn->prepare("
            SELECT * FROM tbl_ads 
            WHERE position = :position 
            AND status IN ('active', 'scheduled')
            AND start_date <= CURRENT_DATE 
            AND end_date >= CURRENT_DATE
            ORDER BY RAND()
            LIMIT 1
        ");
        $stmt->execute(['position' => $position]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching ad: " . $e->getMessage());
        return null;
    }
}

function incrementAdClicks($adId) {
    $conn = getConnection();
    
    try {
        if (!$conn) {
            throw new PDOException("Database connection not available");
        }

        $stmt = $conn->prepare("
            UPDATE tbl_ads 
            SET click_count = click_count + 1 
            WHERE id = ?
        ");
        $stmt->execute([$adId]);
        return true;
    } catch(PDOException $e) {
        error_log("Error incrementing ad clicks: " . $e->getMessage());
        return false;
    }
} 