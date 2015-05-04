<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Registration</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>    
  <?php
	foreach($_POST as $key => $value)
	{
		$post[$key] = htmlspecialchars($value);
		print $key.":".$value;
	}
	
	$name = $post['name'];
	$birth = $post['birth'];
	$nation = $post['nation'];
	$passportNo = $post['passportNo'];
	$passportExpire = $post['passportExpire'];
	$email = $post['email'];
	$phone = $post['phone'];
	$passwd = $post['passwd'];
	$passwd2 = $post['passwd2'];
	$flag = true;
	
	//check input data
	if($name == '')
	{
		print 'Input your Name <br/>';
		$flag = false;
	}else{
		print 'Name: '. $name;
		print '<br/>';
	}
	if(!preg_match('/^\w+$/', $nation))
	{
		print 'Nationality: Invalid Value<br/>';
		$flag = false;
	}else{
		print 'Nationality: '. $nation;
		print '<br/>';
	}
	if(!preg_match('/^\w+$/', $passportNo))
	{
		print 'Passport Number: Invalid Value<br/>';
		$flag = false;
	}else{
		print 'Passport Number: '. $passportNo;
		print '<br/>';
	}
	if(!preg_match('/^[\w\-\.]+\@[\w\-\.]+\.([a-z]+)$/', $email))
	{
		print 'E-mail: Invalid E-mail Address<br/>';
		$flag = false;
	}else{
		print 'E-mail: '. $email;
		print '<br/>';
	}
	if(!preg_match('/^\w+$/', $passwd))
	{
		print 'Password: Password is not available<br/>';
		$flag = false;
	}else{
		if($passwd == $passwd2){
			print 'Password is OK';
			print '<br/>';
		}else{
			print 'Two passwrods are different<br/>';
			$flag = false;
		}
	}
	//pass data to next page
	if($flag)
	{
		print '<form method="post" action="form_done.php">';
		print '<input type="hidden" name="name" value="'.$name.'">';
		print '<input type="hidden" name="birth" value="'.$birth.'">';
		print '<input type="hidden" name="nation" value="'.$nation.'">';
		print '<input type="hidden" name="passportNo" value="'.$passportNo.'">';
		print '<input type="hidden" name="passportExpire" value="'.$passportExpire.'">';
		print '<input type="hidden" name="email" value="'.$email.'">';
		print '<input type="hidden" name="phone" value="'.$phone.'">';
		print '<input type="hidden" name="passwd" value="'.$passwd.'">';
		print '<input type="hidden" name="passwd2" value="'.$passwd2.'">';
		print '<input type="button" onclick="history.back()" value="Return">';
		print '<input type="submit" value="Create">';
		print '</form>';
	}else{
		print '<form>';
		print '<input type="button" onclick="history.back()" value="Return">';
		print '</form>';
	}	
  ?>
  </body>
</html>