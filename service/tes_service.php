<?php 
define('_VALID_ACCESS',TRUE);
include "../middle/conn.php";
include "../middle/functions.php";
?>
<html>
<?php
echo date("Y-m-d H:i:s");
?>
<script type="text/javascript">
  setTimeout(function(){ 
    window.location = "tes_service.php";
  }, 1000);              
</script>
</html>