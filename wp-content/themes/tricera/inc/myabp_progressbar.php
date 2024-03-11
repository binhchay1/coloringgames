<?php
/**
 * MyArcadePlugin Pro Theme Progress bar helps theme developers to create MyArcadePlugin Pro compatible theme backends.
 * @package MyArcadePlugin Pro Theme Progressbar
 * @author Onedin Ibrocevic - http://exells.com
 * @version 1.1
 */
?>
<script type="text/javascript">
  var counter = 1;
  
  function loadgame(wait_time) {
    var loadtext=document.getElementById('progressbarloadtext').style;
    var percentlimit = <?php echo get_option('tricera_progressbartextloadlimit'); ?>;
    var speedindex = <?php echo get_option('tricera_progressbarspeedindex'); ?>;
    var percentlimitstatus = "<?php echo get_option('tricera_progressbartextloadstatus'); ?>";
    speedindex = speedindex*2;
    if ( counter < wait_time) { 
      counter = counter + 1; 
      document.getElementById("progressbarloadbg").style.width = counter + "px"; 
      var percentage = Math.round( counter / wait_time * 100); 
      document.getElementById("progresstext").innerHTML = percentage+" %"; 
      window.setTimeout("loadgame('" + wait_time + "')", speedindex ); 
      if ( (percentage >= percentlimit) & (percentlimitstatus == "enable" ) ) {
        loadtext.display='block';
      }	
    } 
    else { 
      counter = 1;
      window.hide(); 
    }
  }

  function hide() {
    var showprogressbar=document.getElementById('showprogressbar').style;
    var loadtext=document.getElementById('progressbarloadtext').style;
    var game = document.getElementById('my_game').style;

    showprogressbar.display='none'; 
    loadtext.display='none';
    game.width = '100%';
    game.height = '100%';

    counter = 400;
  }
  
  jQuery(document).ready( function() {
    setTimeout('loadgame(400)', <?php echo get_option('tricera_progressbardelay'); ?>); 
  });
</script>