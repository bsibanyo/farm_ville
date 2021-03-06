<?php
  function cleanInput($input) {
    $search = array(
      '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
      '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
      '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
      '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );

    $output = preg_replace($search, '', $input);
    return $output;
  }
  
  function sanitizeData($input) {
    /*
      if (is_array($input)) {
          foreach($input as $var=>$val) {
              $output[$var] = sanitize($val);
          }
      }
      else {
          if (get_magic_quotes_gpc()) {
              $input = stripslashes($input);
          }
          $input  = cleanInput($input);
          $output = mysql_real_escape_string($input);
      }
    */
    
    return  filter_var($input, FILTER_SANITIZE_STRING);
  }

  //$bad_string = "Hi! <script src='http://www.evilsite.com/bad_script.js'></script> It's a good day!";
  //$good_string = sanitize($bad_string);
  // $good_string returns "Hi! It\'s a good day!"

  // Also use for getting POST/GET variables
  //$_POST = sanitize($_POST);
  //$_GET  = sanitize($_GET);
?>