<!-- author: Toshi  This code is just sample -->

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
	}
	
	$name = $post['name'];
	$birth = $post['birth'];
	$nation = $post['nation'];
	$passportNo = $post['passportNo'];
	$passportExpire = $post['passportExpire'];
	$email = $post['email'];
	$phone = $post['phone'];
	$passwd = $post['passwd'];
	$passwd = md5($passwd);
	
	$format = '%d%m%Y';
	
	print 'Complete to create the account<br/>';
	
	$LinkID = mysqli_connect("localhost", "c199grp08", "traveller");
	if(!$LinkID)
	{
		die('Could not connect: '.mysqli_error($LinkID));
	}
	mysqli_select_db($LinkID, 'c199grp08');
	/*
	$query = "INSERT INTO customers (name, birth, nationality, passportNo, passportExp, email, phone, password)
                     VALUE('Jun', STR_TO_DATE('12011971', '%d%m%Y'), %nation, $passportNo, STR_TO_DATE('09092016', '%d%m%Y'), 'test@dt.com', '1332033',$passwd)";	
    */
	$query = "INSERT INTO customers (name, birth, nationality, passportNo, passportExp, email, phone, password) 
	                 VALUE('".$name."', STR_TO_DATE('".$birth."','%d/%m/%Y'),'".$nation."','".$passportNo."',STR_TO_DATE('".$passportExpire."', '%d/%m/%Y'),'".$email."','".$phone."','".$passwd."')";
	
	$stmt = mysqli_query($LinkID, $query);
	if(!$stmt){die('fauLt'.mysql_error);}
	
	$LinkID = null;	
	
  ?>
  </body>
</html>