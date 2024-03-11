jQuery(document).ready(function($){
  // Lights On / Off
  $("#turnoff").css("visibility", "visible");
  $("#turnoff").css("height", $(document).height()).hide();
  $(".interruptor").click(function() {
    $("#turnoff").toggle();
    if ( $("#turnoff").is(":hidden"))
      $(this).removeClass("luz_apagada");
    else
      $(this).addClass("luz_apagada");
         
    return false;
  });
});