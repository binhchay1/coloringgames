	</div>
	</div>

	<div class="footer">
	  <div class="footer_wrapper">

	    <?php
      wp_nav_menu(array('container' => false, 'menu_id' => 'nav', 'theme_location' => 'footer'));
      ?>

	    <div style="display: flex; justify-content: center; margin-top: 20px;">
	      <img src="http://localhost/coloringgames/wp-content/uploads/2024/03/color.png">
	    </div>

	    <p class="footer_link">
	      <?php
        $copyright = tricera_get_option('footer_copyright');
        if (empty($copyright)) {
          $copyright = sprintf(__('Proudly powered by %sMyArcadePlugin%s', 'tricera'), '<a target="_blank" href="http://myarcadeplugin.com/" title="WordPress Arcade" itemprop="url">', '</a>');
        }

        echo $copyright;
        ?>
	    </p>
	  </div>
	</div>

	<?php wp_footer(); ?>
	</body>

	</html>