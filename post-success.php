<?php
//if ($_POST['secret_key'] != $_SESSION['form_xx'])
/*  $_SESSION['form_xx'] = '';
  echo "hi";
  print_r($_SESSION);
  print_r($_POST);
//  header("location: bulletin-board.php");

if (isset($_GET['change']){
  $success = "Name changed successfully";
  $success = "Name changed!";
}
else {
  header('location: https://apple.com');
  $success = "Post successful";
}
 */
// print <<<EOF

if (isset($_GET['change'])){
  $given = $_GET['change'];
  if ($given == 'user') {
    $changed = 'Username changed!';
  }
  elseif ($given == 'badcaptcha') {
    $changed = 'You failed the captcha!';
  }
  elseif ($given == 'nocaptcha') {
    $changed = 'You forgot to enter the captcha!';
  }
  elseif ($given == 'success') {
    $changed = 'Post successful!';
  }
}
else {
  $changed = 'Joe\'s Bulletin Board';
}

?>

<html>
  <title><?php echo $changed?> - jbussard.com</title>
<link rel='stylesheet' type='text/css' href='css/bulletin.css'/>
<link rel='shortcut icon' href='./favicon.ico' type='image/x-icon'/>
<body>
<div style='display:flex; flex-direction:column; justify-content:center;align-items:center;height:300px'>
<h1 style='color:black'><?php echo $changed?> <br>Click to return to Bulletin Board.</h1>
<br><br>
<form action='bulletin-board.php'>
<input type='submit' value='Continue'>
</form>
</div>
<script src='rgb-anywhere.js'>partyMode()</script>
</body>
</html>
