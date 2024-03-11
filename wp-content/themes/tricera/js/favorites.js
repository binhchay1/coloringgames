jQuery(document).ready( function($) {
  $('.favoritos').on('click', function() {
    dhis = $(this);
    fungames_wpfp_do_js( dhis, 1 );
    return false;
  });
});

function fungames_wpfp_do_js( dhis, doAjax ) {
  // Remove button and display loading image
  jQuery(".favoritos").hide();
  jQuery(".fungames-fav-img").show();
  url = document.location.href.split('#')[0];
  params = dhis.parent().attr('href').replace('?', '') + '&ajax=1';

  // What should we do?
  image_src = jQuery(".favoritos").attr('src');
  link_src  = dhis.parent().attr('href');
  if (params.search(/=add&/i) >= 0) {
    image_src = image_src.replace("add.png", "remove.png");
    link_src  = link_src.replace("=add&", "=remove&");
  } else {
    image_src = image_src.replace("remove.png", "add.png");
    link_src  = link_src.replace("=remove&", "=add&");
  }
  jQuery(".favoritos").attr('src', image_src);
  dhis.parent().attr('href', link_src);

  if ( doAjax ) {
    jQuery.get(url, params, function(data) {
      jQuery(".fungames-fav-img").hide();
      jQuery(".favoritos").show();
    });
  }
}