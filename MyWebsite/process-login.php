<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
try {
    require ('mysqli_connect.php');
	    $email = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL);	
	if  ((empty($email)) || (!filter_var($email, FILTER_VALIDATE_EMAIL))) {
		$errors[] = 'Bạn chưa nhập email';
		$errors[] = 'Hoặc email đã nhập sai';
	}
	    $password = filter_var( $_POST['password'], FILTER_SANITIZE_STRING);	
	if (empty($password)) {
		$errors[] = 'Bạn chưa nhập mật khẩu';
	}
   if (empty($errors)) { 
    $query = "SELECT userid, password, first_name, user_level FROM users WHERE email=?";
    $q = mysqli_stmt_init($dbcon);
    mysqli_stmt_prepare($q, $query);
    mysqli_stmt_bind_param($q, "s", $email); 
    mysqli_stmt_execute($q);
    $result = mysqli_stmt_get_result($q);
    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    if (mysqli_num_rows($result) == 1) {
      if (password_verify($password, $row[1])) {
        session_start();								
        $_SESSION['user_level'] = (int) $row[3];
        $url = ($_SESSION['user_level'] === 1) ? 'admin-page.php' : 'members-page.php'; 
        header('Location: ' . $url); 
      }
      else {
        $errors[] = 'Email hoặc Mật khẩu của bạn không khớp với dữ liệu hiện có';
        $errors[] = 'Để đăng nhập, nhấn vào nút Đăng ký';
        $errors[] = 'trên thanh menu';
      }
    }
    else{
      $errors[] = 'Email hoặc Mật khẩu của bạn không khớp với dữ liệu hiện có';
      $errors[] = 'Để đăng nhập, nhấn vào nút Đăng ký';
      $errors[] = 'trên thanh menu';
    }
  } 
  if (!empty($errors)) {                     
		$errorstring = "Lỗi! <br> Các lỗi sau đã xảy ra:<br>";
		foreach ($errors as $msg) {
			$errorstring .= " - $msg <br>\n";
		}
		$errorstring .= "Xin thử lại sau<br>";
		echo "<p class=' text-center col-sm-2' style='color:red'>$errorstring</p>";
	}// End of if (empty($errors)) IF.
  mysqli_stmt_free_result($q);
  mysqli_stmt_close($q);
}
  catch(Exception $e) // We finally handle any problems here   
  {
    print "Hệ thống bận xin thử lại sau";
  }
  catch(Error $e)
  {
    print "Hệ thống bận xin thử lại sau";
  }
}?>