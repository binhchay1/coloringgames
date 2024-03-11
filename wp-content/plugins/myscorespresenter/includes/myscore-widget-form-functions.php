<?php
/**
 * Functions used to generate widget form fields
 */

/**
 * Order parameters in widgets
 *
 * @param array $args
 */
function myscore_form_dropdown_order( $args = array() ) {

  $defaults = array(
    'field_title' => '',
    'field_id' => '',
    'field_name' => '',
    'value' => '',
  );

  $r = wp_parse_args( $args, $defaults );
  extract($r);

  ?>
  <p>
  <label for="<?php echo $field_id; ?>"><?php _e( 'Order', 'myscorespresenter' ); ?></label><br />
  <select name="<?php echo $field_name; ?>" id="<?php echo $field_id; ?>" class="widefat">
    <option value="ASC" <?php selected( $value, 'ASC' ); ?>><?php _e( "Ascending", 'myscorespresenter' ) ?></option>
    <option value="DESC" <?php selected( $value, 'DESC' ); ?>><?php _e( "Descending", 'myscorespresenter' ) ?></option>
  </select>
  </p>
  <?php
}

/**
 * Numeric field
 *
 * @param array $args
 */
function myscore_form_number( $args = array() ) {

  $defaults = array(
    'field_title' => '',
    'field_id'    => '',
    'field_name'  => '',
    'value'       => 0,
  );

  $r = wp_parse_args( $args, $defaults );
  extract($r);

  ?>
  <p>
  <label for="<?php echo $field_id; ?>"><?php echo $field_title; ?></label>
  <input id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" type="number" class="widefat" value="<?php echo intval( $value ); ?>" />
  </p>
  <?php
}

/**
 * Text field
 *
 * @param array $args
 */
function myscore_form_text( $args = array() ) {

  $defaults = array(
    'field_title' => '',
    'field_id' => '',
    'field_name' => '',
    'value' => '',
  );

  $r = wp_parse_args( $args, $defaults );
  extract($r);

  ?>
  <p>
  <label for="<?php echo $field_id; ?>"><?php echo $field_title; ?></label>
  <input id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" class="widefat" type="text" value="<?php echo $value; ?>" />
  </p>
  <?php
}

/**
 * Text area
 *
 * @param array $args
 */
function myscore_form_textarea( $args = array() ) {

  $defaults = array(
    'field_title' => '',
    'field_id' => '',
    'field_name' => '',
    'value' => '',
  );

  $r = wp_parse_args( $args, $defaults );
  extract($r);

  ?>
  <p>
  <label for="<?php echo $field_id; ?>"><?php echo $field_title; ?></label>
  <textarea id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" class="widefat"><?php echo $value; ?></textarea>
  </p>
  <?php
}

/**
 * Select
 *
 * @param array $args
 */
function myscore_form_select( $args = array() ) {

  $defaults = array(
    'field_title' => '',
    'field_id' => '',
    'field_name' => '',
    'options' => array(),
    'selection' => '',
  );

  $r = wp_parse_args( $args, $defaults );
  extract($r);
  ?>
  <p>
  <label for="<?php echo $field_id; ?>"><?php echo $field_title; ?></label><br />
  <select name="<?php echo $field_name; ?>" class="widefat">
    <?php foreach ( $options as $id => $name ) : ?>
    <option value="<?php echo $id;?>" <?php selected( $selection, $id ) ?>><?php echo $name; ?></option>
    <?php endforeach; ?>
    </select>
  </p>
  <?php
}
