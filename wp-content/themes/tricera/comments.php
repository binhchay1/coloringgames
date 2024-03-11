<?php // Do not delete these lines
  if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die (__("Please do not load this page directly. Thanks!", "tricera"));

  if (function_exists('post_password_required')) {
    if ( post_password_required() ) {
      echo '<p class="nocomments">'.__("This post is password protected. Enter the password to view comments.", "tricera").'</p>';
      return;
    }
  }
  else {
    if (!empty($post->post_password)) { // if there's a password
      if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
        ?>
          <p class="nocomments"><?php _e("This post is password protected. Enter the password to view comments.", "tricera"); ?></p>
        <?php

        return;
      }
    }

    /* This variable is for alternating comment background */
    $oddcomment = 'class="alt" ';
  }

  if (function_exists('wp_list_comments')) :
    //WP 2.7 Comment Loop
    if ( have_comments('type=comment') ) : ?>
      <h3 id="comments"><?php comments_number(__("No Comments", "tricera"), __("One Comment", "tricera"), __("% Comments", "tricera"));?></h3>
      <ol class="commentlist">
        <?php wp_list_comments('type=comment'); ?>
      </ol>

      <h3 id="pingbacks"><?php _e("TrackBacks / PingBacks", "tricera"); ?></h3>
      <ol class="trackback">
        <?php wp_list_comments('type=pings'); ?>
      </ol>

      <div class="navigation">
        <div class="alignleft"><?php previous_comments_link() ?></div>
        <div class="alignright"><?php next_comments_link() ?></div>
      </div>
    <?php else : // this is displayed if there are no comments so far ?>
      <?php if ('open' == $post->comment_status) :
        // If comments are open, but there are no comments.
      else : ?>
        <!-- <p class="nocomments"><?php _e("Comments are closed.", "tricera"); ?></p> -->
      <?php endif;
    endif;
  else:
    //WP 2.6 and older Comment Loop
    // Do not delete these lines
    global $tech_comments;
    global $tech_trackbacks;
    split_comments( $comments );
    /* This variable is for alternating comment background */
    $oddcomment = 'alt';
    ?>

    <!-- You can start editing here. -->
    <!-- This comment template seperates comments from ping/trackbacks.  If this is not the desired output change the code below that looks like this.
    <?php // foreach ( $tech_comments as $comment ) : ?>
    to this
    <?php // foreach ( $comments as $comment ) : ?> Delete the //
    Then delete everything below that is between the TRACKBACK tags. -->
    <?php if ($comments) : ?>
      <h3 id="comments"><?php comments_number(__("No comments filed", "tricera"), __("One comment", "tricera"), __("% comments", "tricera" ));?><?php _e("to", "tricera"); ?> &#8220;<?php the_title(); ?>&#8221;</h3>
      <ol class="commentlist">
        <?php foreach ( $tech_comments as $comment ) : ?>
          <li class="<?php echo $oddcomment; ?> <?php if ($comment->user_id == '1') echo 'author'; ?>" id="comment-<?php comment_ID() ?>">
            <?php _e("Comment by", "tricera"); ?> <em><?php comment_author_link() ?></em>:
            <?php if ($comment->comment_approved == '0') : ?>
              <em><?php _e("Your comment is awaiting moderation.", "tricera"); ?></em>
            <?php endif; ?>
            <br />
            <small class="commentmetadata">
              <a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date('l, F jS Y') ?> at <?php comment_time() ?></a>
              <?php edit_comment_link(__("Edit", "tricera"),'',''); ?>
            </small>

            <?php comment_text() ?>
          </li>

          <?php /* Changes every other comment to a different class */
            if ('alt' == $oddcomment) $oddcomment = '';
            else $oddcomment = 'alt';
          ?>
        <?php endforeach; /* end for each comment */ ?>
      </ol>

      <!-- TRACKBACK -->
      <?php if ( count( $tech_trackbacks ) > 0 ) { ?>
        <h3 id="pingbacks"><?php _e("TrackBacks / PingBacks", "tricera"); ?></h3>
        <ol class="trackback">
          <?php foreach ( $tech_trackbacks as $comment ) : ?>
            <?php if ( $comment->comment_type == 'trackback') {
              $tbtype = 'tb';
            } else {
              $tbtype = 'pb';
            } ?>
            <li class="<?php echo $tbtype; ?>" id="comment-<?php comment_ID() ?>">
              <?php if ( $tbtype == 'tb') { ?>
                <?php _e("Trackback", "tricera"); ?>
              <?php } else { ?>
                <?php _e("Pingback", "tricera"); ?>
              <?php } ?>
              <br />
              <em><?php comment_author_link() ?></em>
              <?php if ($comment->comment_approved == '0') : ?>
                <em><?php _e("Your comment is awaiting moderation.", "tricera"); ?></em>
              <?php endif; ?>
            </li>
          <?php endforeach; /* end for each comment */ ?>
        </ol>
      <?php } ?>
      <!-- TRACKBACK -->
      <?php else : // this is displayed if there are no comments so far ?>
        <?php if ('open' == $post->comment_status) : ?>
          <!-- If comments are open, but there are no comments. -->
        <?php else : // comments are closed ?>
          <!-- If comments are closed. -->
          <!-- <p class="nocomments"><?php _e("Comments are closed.", "tricera"); ?></p> -->
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; // 2.6 and older Comment Loop end ?>

          <?php comment_form(); ?>
