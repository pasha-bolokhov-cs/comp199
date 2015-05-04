<div>
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
</div>