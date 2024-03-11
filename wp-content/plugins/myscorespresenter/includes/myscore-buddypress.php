<?php
/**
 * MyScoresPresenter's BuddyPress integration -- integrates into members and activity components.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( class_exists( 'BP_Component' ) ) :
  class MyScore_BuddyPress_Component extends BP_Component {

    public function __construct() {
      parent::start(
        'scores',
        __('Scores', 'myscorespresenter'),
        BP_PLUGIN_DIR
      );
    }

    public function setup_globals( $args = '' ) {
      parent::setup_globals( array(
          'has_directory' => true,
          'root_slug'     => 'scores',
          'slug'          => 'scores'
        )
      );
    }

    public function setup_actions() {
      add_action( 'bp_init', array( $this, 'init_components'), 7 );
      parent::setup_actions();
    }

    public function init_components() {
      if ( ! bp_is_active( 'activity') )
        return;
    }

    public function setup_nav( $main_nav = '', $sub_nav = '' ) {

      // Stop if there is no user displayed or logged in
      if ( !is_user_logged_in() && !bp_displayed_user_id() )
        return;

      $main_nav = array(
        'default_subnav_slug' => 'all',
        'item_css_id'         => $this->id,
        'name'                => __('Scores', 'myscorespresenter'),
        'position'            => 100,
        'screen_function'     => 'myscore_bp_members_my_scores',
        'slug'                => $this->slug
      );

      // Determinate user to use for the link
      if ( bp_displayed_user_domain() )
        $user_domain = bp_displayed_user_domain ();
      elseif ( bp_loggedin_user_domain() )
        $user_domain = bp_loggedin_user_domain();
      else
        return;

      $sub_nav = array(
        'item_css_id'   => "{$this->id}-all",
        'name'          => __("My Scores", 'myscorespresenter'),
        'parent_slug'   => $this->slug,
        'parent_url'    => trailingslashit( $user_domain . $this->slug ),
        'position'      => 20,
        'screen_function' => 'myscore_bp_members_my_scores',
        'slug'          => 'all'
      );

      parent::setup_nav( $main_nav, $sub_nav );
    }

    public function setup_title() {
      global $bp;

      if (  bp_is_activity_component() ) {
        if ( bp_is_my_profile() ) {
          $bp->bp_options_title = __( 'My Scores', 'myscorespresenter');
        }
      }

      parent::setup_title();
    }
  }

  function myscore_bp_members_my_scores() {
    add_action( 'bp_template_content', 'myscore_bp_members_my_scores_content' );
    bp_core_load_template( apply_filters( 'myscore_bp_members_my_scores', 'members/single/plugins' ) );
  }

  function myscore_bp_members_my_scores_content() {
    $template = WP_PLUGIN_DIR . '/myscorespresenter/template/bp-my-scores.php';
    if ( file_exists( $template ) )
      include( $template );
  }
endif;
