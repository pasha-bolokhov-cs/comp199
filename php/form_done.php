<!-- author: Toshi  This code is just sample -->
<div>
  <?php
	//include ('/home/student/cst400/comp199/comp199db.php');
	require_once '../../../comp199-www/mysqli_auth.php';
	
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
	
	$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
      if ($mysqli->connect_error) {
              $error = 'Connect Error (' . $mysqli->connect_errno . ') '
                       . $mysqli->connect_error;
              return FALSE;
      }
	/*
	$LinkID = mysqli_connect(_HOST, _USER, _PASS);
	if(!$LinkID)
	{
		die('Could not connect: '.mysqli_error($LinkID));
	}
	mysqli_select_db($LinkID, 'c199grp08');
	*/
	$query = "INSERT INTO customers (name, birth, nationality, passportNo, passportExp, email, phone, password) 
	                 VALUE('".$name."', STR_TO_DATE('".$birth."','%d/%m/%Y'),'".$nation."','".$passportNo."',STR_TO_DATE('".$passportExpire."', '%d/%m/%Y'),'".$email."','".$phone."','".$passwd."')";
	
	$stmt = mysqli_query($mysqli, $query);
	if(!$stmt){die('fauLt'.mysql_error);}
	
	//$LinkID = null;	
	$mysqli = null;
  ?>
</div>