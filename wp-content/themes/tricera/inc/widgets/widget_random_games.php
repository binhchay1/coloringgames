<?php

/*
 * Shows random games scroller
 *
 */

if ( !class_exists('WP_Widget_MABP_Random_Games') ) {
  class WP_Widget_MABP_Random_Games extends WP_Widget {

    // Constructor
    function WP_Widget_MABP_Random_Games() {

      $widget_ops   = array('description' => 'Shows a random game scroller.');

      $this->WP_Widget('MABP_Random_Games', 'MyArcade Game Scroller', $widget_ops);
    }

    // Display Widget
    function widget($args, $instance) {
      extract($args);

      $title = apply_filters('widget_title', esc_attr($instance['title']));
      $limit = intval($instance['limit']);
      $category = isset($instance['category']) ? intval($instance['category']) : false;

      global $post, $wpdb;

      echo $before_widget;

      if($title) {
        echo $before_title . $title . $after_title;
      }

      // <-- START --> HERE COMES THE OUTPUT
      if ( !$category ) {$category = ''; $comma = ''; } else { $comma = ','; }
      $blogcat = get_cat_ID( get_option('fungames_blog_category'));
      if ( !empty($blogcat) ) $exclude = '&cat='.$category.$comma.'-'.$blogcat; else $exclude = $category;

      $games = new WP_Query("showposts=".$limit.'&orderby=rand'.$exclude);

      if ( !empty($games) ) {
        ?>
        <div id="postlist">
          <ul class="spy">
        <?php
        while( $games->have_posts() ) : $games->the_post();
          ?>
          <li>
            <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
              <img src="<?php echo get_post_meta($post->ID, 'mabp_thumbnail_url', true); ?>" height="80" width="80" alt="<?php the_title_attribute(); ?>" />
              <span><?php myarcade_title(25); ?></span>
            </a>

            <div class="fcats"><?php the_category(', '); ?> </div>
            <div class="auth">
              <?php fungames_get_excerpt(100); ?>
            </div>
          </li>
          <?php
        endwhile;
        ?>
        </ul>
        </div>
        <div class="clear"></div>
        <?php
      }

      // <-- END --> HERE COMES THE OUTPUT

      echo $after_widget;
    }

    // Update Widget
    function update($new_instance, $old_instance) {

      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['limit'] = intval($new_instance['limit']);
      $instance['category'] = intval($new_instance['category']);

      return $instance;
    }

    // Display Widget Control Form
    function form($instance) {

      $instance = wp_parse_args((array) $instance, array('title' => 'Featured Games', 'limit' => 12, 'wcategory' => 0));

      $title = esc_attr($instance['title']);
      $limit = intval($instance['limit']);

      if ( isset($instance['category']) )
        $category = intval($instance['category']);
      else
        $category = false;

      $slidercategs_obj = get_categories('hide_empty=0');
      $slidercategs = array();
      $slidercategs[0] = 'All';
      foreach ($slidercategs_obj as $categ) {
        $slidercategs[$categ->cat_ID] = $categ->cat_name;
      }

      ?>

      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>">
          Title
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </label>
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('limit'); ?>">
          Limit
          <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>" />
        </label>
      </p>

      <p>
        <label for="wcategory">
          Category<br />
          <select name="<?php echo $this->get_field_name('category'); ?>">
            <?php foreach ($slidercategs as $id => $name) { ?>
            <option value="<?php echo $id;?>" <?php if ( $category == $id) { echo 'selected="selected"'; } ?>><?php echo $name; ?></option>
            <?php } ?>
          </select>
        </label>
      </p>

      <?php
    }
  }
}
?>