<?php
  if (mail("toshiyasu.akazawa@gmail.com", "TEST MIL", "This is a test message.", "From: deepblue@bc.ca")){echo " send mail.";
  }else{
  echo "fail to send mail.";
  }
?>