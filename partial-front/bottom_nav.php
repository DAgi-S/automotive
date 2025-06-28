<?php
// Get the current page name to highlight the active tab
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Tabbar start -->
<div id="bottom-navigation">
  <div class="container">
    <div class="home-navigation-menu">
      <div class="bottom-panel nagivation-menu-wrap">
        <ul class="bootom-tabbar">
          <li class="<?php echo ($current_page == 'home.php') ? 'active' : ''; ?>">
            <a href="home.php" class="<?php echo ($current_page == 'home.php') ? 'active' : ''; ?>">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_202_7251" style="mask-type: alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
                  <rect width="24" height="24" fill="white" />
                </mask>
                <g mask="url(#mask0_202_7251)">
                  <path d="M8.12602 14C8.57006 15.7252 10.1362 17 12 17C13.8638 17 15.4299 15.7252 15.874 14M11.0177 2.764L4.23539 8.03912C3.78202 8.39175 3.55534 8.56806 3.39203 8.78886C3.24737 8.98444 3.1396 9.20478 3.07403 9.43905C3 9.70352 3 9.9907 3 10.5651V17.8C3 18.9201 3 19.4801 3.21799 19.908C3.40973 20.2843 3.71569 20.5903 4.09202 20.782C4.51984 21 5.07989 21 6.2 21H17.8C18.9201 21 19.4802 21 19.908 20.782C20.2843 20.5903 20.5903 20.2843 20.782 19.908C21 19.4801 21 18.9201 21 17.8V10.5651C21 9.9907 21 9.70352 20.926 9.43905C20.8604 9.20478 20.7526 8.98444 20.608 8.78886C20.4447 8.56806 20.218 8.39175 19.7646 8.03913L12.9823 2.764C12.631 2.49075 12.4553 2.35412 12.2613 2.3016C12.0902 2.25526 11.9098 2.25526 11.7387 2.3016C11.5447 2.35412 11.369 2.49075 11.0177 2.764Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </g>
              </svg>
            </a>
            <div class="orange-boder <?php echo ($current_page == 'home.php') ? 'active' : ''; ?>"></div>
          </li>
          <li class="<?php echo ($current_page == 'location.php') ? 'active' : ''; ?>">
            <a href="location.php" class="<?php echo ($current_page == 'location.php') ? 'active' : ''; ?>">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>
            <div class="orange-border <?php echo ($current_page == 'location.php') ? 'active' : ''; ?>"></div>
          </li>
          <li class="<?php echo ($current_page == 'service.php') ? 'active' : ''; ?>">
            <a href="service.php" class="<?php echo ($current_page == 'service.php') ? 'active' : ''; ?>">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 10L5 5H19L21 10M3 10H21M3 10L5 15H19L21 10M5 15V18.5M19 15V18.5M7 21C7.82843 21 8.5 20.3284 8.5 19.5C8.5 18.6716 7.82843 18 7 18C6.17157 18 5.5 18.6716 5.5 19.5C5.5 20.3284 6.17157 21 7 21ZM17 21C17.8284 21 18.5 20.3284 18.5 19.5C18.5 18.6716 17.8284 18 17 18C16.1716 18 15.5 18.6716 15.5 19.5C15.5 20.3284 16.1716 21 17 21Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>
            <div class="orange-border <?php echo ($current_page == 'service.php') ? 'active' : ''; ?>"></div>
          </li>
          <li class="<?php echo ($current_page == 'ecommerce/pages/products.php') ? 'active' : ''; ?>">
            <a href="ecommerce/pages/products.php" class="<?php echo ($current_page == 'ecommerce/pages/products.php') ? 'active' : ''; ?>">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 6H4M6 6H20L18 16H8M6 6L8 16M8 16L10 21M8 16H18M10 21C10.8284 21 11.5 20.3284 11.5 19.5C11.5 18.6716 10.8284 18 10 18C9.17157 18 8.5 18.6716 8.5 19.5C8.5 20.3284 9.17157 21 10 21ZM18 21C18.8284 21 19.5 20.3284 19.5 19.5C19.5 18.6716 18.8284 18 18 18C17.1716 18 16.5 18.6716 16.5 19.5C16.5 20.3284 17.1716 21 18 21Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>
            <div class="orange-border <?php echo ($current_page == 'info.php') ? 'active' : ''; ?>"></div>
          </li>
          <li class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <a href="profile.php" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
              <svg class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_202_1984" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
                  <rect width="24" height="24" fill="white" />
                </mask>
                <g mask="url(#mask0_202_1984)">
                  <path d="M20 21C20 19.6044 20 18.9067 19.8278 18.3389C19.44 17.0605 18.4395 16.06 17.1611 15.6722C16.5933 15.5 15.8956 15.5 14.5 15.5H9.5C8.10444 15.5 7.40665 15.5 6.83886 15.6722C5.56045 16.06 4.56004 17.0605 4.17224 18.3389C4 18.9067 4 19.6044 4 21M16.5 7.5C16.5 9.98528 14.4853 12 12 12C9.51472 12 7.5 9.98528 7.5 7.5C7.5 5.01472 9.51472 3 12 3C14.4853 3 16.5 5.01472 16.5 7.5Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </g>
              </svg>
            </a>
            <div class="orange-boder <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>"></div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- Tabbar end --> 