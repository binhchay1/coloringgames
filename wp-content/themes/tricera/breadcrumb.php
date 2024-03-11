<?php if (class_exists('breadcrumb_navigation_xt')) : ?>
  <div class="breadcrumb">
     <?php _e("You Are Here: ", "tricera"); ?>
      <?php
      $mybreadcrumb = new breadcrumb_navigation_xt;
      $mybreadcrumb->display();
      ?>
  </div>
<?php endif; ?>