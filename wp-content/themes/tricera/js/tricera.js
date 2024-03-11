this.tooltip=function(){this.xOffset=-10;this.yOffset=20;jQuery(".tooltip").unbind().hover(function(e){this.t=this.title;this.title="";this.top=e.pageY+yOffset;this.left=e.pageX+xOffset;jQuery("body").append('<p id="tooltip">'+this.t+"</p>");jQuery("p#tooltip").css("top",this.top+"px").css("left",this.left+"px").fadeIn("slow")},function(){this.title=this.t;jQuery("p#tooltip").fadeOut("slow").remove()}).mousemove(function(e){this.top=e.pageY+yOffset;this.left=e.pageX+xOffset;jQuery("p#tooltip").css("top",this.top+"px").css("left",
this.left+"px")})};jQuery(document).ready(function($){tooltip()});this.footip=function(){this.xOffset=-10;this.yOffset=20;jQuery(".footip").unbind().hover(function(e){this.t=this.title;this.title="";this.top=e.pageY+yOffset;this.left=e.pageX+xOffset;jQuery("body").append('<p id="tooltip2">'+this.t+"</p>");jQuery("p#tooltip2").css("top",this.top+"px").css("left",this.left+"px").fadeIn("slow")},function(){this.title=this.t;("p#tooltip2").fadeOut("slow").remove()}).mousemove(function(e){this.top=e.pageY+yOffset;this.left=e.pageX+xOffset;jQuery("p#tooltip2").css("top",this.top+"px").css("left",this.left+"px")})};jQuery(document).ready(function($){footip()});

jQuery(document).ready(function($){

  $("#dropcat").css("display", "none");

  var cat_offset = jQuery('#show_cat').offset();
  $('#dropcat').css( {
    'left': cat_offset.left - 10,
    'top' : cat_offset.top + 35
  });

  $('#logincube').hide();
  $('#favCUBE').hide();

  $('#show_cat').click(function() {
    $('#dropcat').fadeToggle("medium", "linear");
    return false;
  });

  $("#dropcat").mouseleave(function(){
    $(this).fadeOut("medium", "linear");
  });

  $('#show_login').click(function() {
    $('#logincube').fadeToggle("medium", "linear");
    return false;
  });

  $("#logincube").mouseleave(function(){
    $(this).fadeOut("medium", "linear");
  });
});

/** Game resizing */
function myarcadeDomReady(e){if("function"==typeof e)return"interactive"===document.readyState||"complete"===document.readyState?e():void document.addEventListener("DOMContentLoaded",e,!1)}var myarcade=myarcade||{};myarcade.intrinsicRatioGames={init:function(){this.makeFit(),window.addEventListener("resize",function(){this.makeFit()}.bind(this))},makeFit:function(){element_list=document.querySelectorAll("#playframe"),element_list.length||(element_list=document.querySelectorAll("#myarcade_game iframe")),element_list.forEach(function(e){var t,i,a,n=e.parentNode,d=window.innerHeight-50,r=n.offsetWidth;e.dataset.origwidth||(e.setAttribute("data-origwidth",e.width),e.setAttribute("data-origheight",e.height)),t=e.dataset.origwidth/e.dataset.origheight,parseInt(e.dataset.origheight)>parseInt(e.dataset.origwidth)?(i=d,a=d*t,a>r&&(i=r/t,a=r)):(a=r,i=a/t,i>d&&(i=d,a=i*t)),e.style.width=a+"px",e.style.height=i+"px"})}},myarcadeDomReady(function(){myarcade.intrinsicRatioGames.init()});