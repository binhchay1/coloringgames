<div id="search">
  <form method="get" id="searchform" action="<?php echo home_url(); ?>" >
    <input id="s" type="text" name="s" value="<?php echo esc_html( get_search_query() ); ?>" />
    <input id="searchsubmit" type="submit" value="<?php _e('GO', 'tricera'); ?>" />
  </form>
</div>