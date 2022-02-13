<?php

/**************************************************
*         bulletin-board.php
*
* This is what generates the bulletin board itself
* as well as lets the users submit their new posts
***************************************************/

$dbhost = 'REPLACE WITH YOUR HOST';  // TODO
$dbuser = 'REPLACE WITH YOUR USER';  // TODO
$dbpass = 'REPLACE WITH YOUR PASSWORD'; // TODO
$dbname = 'REPLACE WITH YOUR DBNAME'; // TODO
$thing = 'in die() put mysqli error';

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) OR DIE();
$reminder = '';

// builds a query
function query($query){
  global $conn;
  $result = mysqli_query($conn, $query);
  return $result;
}

// gets a single record from a query
function getSingle($query) {
  global $conn;
  $result = query($query);
  $row = mysqli_fetch_row($result);
  return $row[0];
}

// user wants to change his or her username (per ip)
if ($_POST['name']) {
  global $conn;
  $newname = mysqli_real_escape_string($conn, $_REQUEST['name']);
  $ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
  $uid = getSingle("select uid from users where ip = '$ip'");
  $newname = htmlspecialchars($newname);
  $newname = substr($newname, 0, 20);
  if (!$uid) {
    query("insert into users(ip) values ('$ip')");
    $uid = getSingle("select uid from users where ip = '$ip'");
  }
  if ($uid != $newname) {
    query("update users set name='$newname' where ip = '$ip'");
  }
  if ($uid == 745) {
    die("Vandalism detected");
  }
  if (!($_POST['tweet'])) {
    header('location: post-success.php?change=user');
  }
}

// user is posting a tweet
if($_POST['tweet']) {
  if ((!isset($_POST['h-captcha-response']) || empty($_POST['h-captcha-response']))){
    header('location: post-success.php?change=nocaptcha');
    exit();
  }
  else {
    $data = array(
      'secret' => "YOUR HCAPTCHA SECRET KEY",  // TODO
      'response' => $_POST['h-captcha-response']
    );
    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($verify);
    $responseData = json_decode($response);
    if($responseData->success || $VIP == true) {
      global $conn;
      $tweet = mysqli_real_escape_string($conn, $_REQUEST['tweet']);
      $tweet = htmlspecialchars($tweet);
      $tweet = substr($tweet, 0, 300);
      $ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
      $uid = getSingle("select uid from users where ip = '$ip'");
      if (!$uid) {
        query("insert into users(ip) values ('$ip')");
        $uid = getSingle("select uid from users where ip = '$ip'");
      }
      $uname = getSingle("select name from users where ip = '$ip'");
      if (!$uname) {
        $uname = $uid;
      }
      $date = Date("Y-m-d H:i:s");
      query("insert into tweets(uid, post, date) values ($uid, '$tweet', '$date')");
      header("location: post-success.php?change=success");
    }
    else {
      header('location: post-success.php?change=badcaptcha');
      exit();
    }
  }
}

print <<<EOF
<html>
<head>
  <title>Bulletin Board</title>
  <link rel="stylesheet" type="text/css" href='css/bulletin.css'/>
  <link rel="shortcut icon" href='./favicon.ico' type="image/x-icon"/>
  <script src='https://www.hCaptcha.com/1/api.js' async defer></script>
</head>
<header>
<p style='background:black;color:white; text-align:center; width:200px'><a href='index.php' style='color:white'>Back to main page..........</a></p>
</header>
<body>
  <h1>Bulletin Board</h1>
  <h3 style='padding:2px;background:yellow;color:black'>Thank you for checking out the Bulletin Board I created. It's made in a LAMP stack, using MariaDB for the database.  Thanks to <a href='https://www.youtube.com/watch?v=1YXqXPWjmKk'>this tutorial</a>, which helped me start the project. - Joe</h3>

<form action='' method='post'>
  <div id='form-container'>
      <textarea id='name-input' placeholder="Username (optional)" maxlength='20' name=name></textarea>
      <textarea id='post-input' placeholder="Enter your post here" name=tweet maxlength='300'></textarea>
      <div class="h-captcha" data-sitekey="1850a563-ebce-46a7-85ca-b620155722d8"></div>
      <input type=submit value="Post">
  </div>
</form>

<br>
<br>
EOF;

// building the table of posts
$result = query("select * from tweets order by date desc limit 20");
print "<table style='table-layout:fixed; max-width:100%; min-width:550px; width:100%;'>";
while ($row = mysqli_fetch_assoc($result)) {
  $uid = $row['uid'];

  $post = htmlspecialchars($row['post']);

  $post = str_replace("&amp;", "&", $post);
  $post = substr($post, 0, 300);
  $number = $row['tid'];
  $uname = htmlspecialchars(getSingle("select name from users where uid = '$uid'"));
  $date = $row['date'];
  //$local_date = gettype($date);
  $local_date = date_create_from_format("Y-m-d H:i:s", $date);
  date_timezone_set($local_date, timezone_open("America/Los_Angeles"));
  $local_date_str = date_format(/*local*/$local_date, 'l \a\t g:ia');
  $test_date = Date("Y-m-d H:i:s");
  $test_date = $local_date_str;
  print <<<EOF
<tr>
<td align='right' style='width:200px; padding-right:0.5em'><strong>$uname</strong> says:</td>
<td style="overflow-wrap: break-word; background-color:white; min-width:100px; max-width: 400px;border-radius:8px;padding:6px;"><em>$post</em></td>
<td style='width:160px'>&nbsp$test_date</td>
<td style='width:90px' align="center"><p><span class='postnumber'>Post #$number</span></p></td>
</tr>
EOF;
}
print  <<<EOF
</table>
<p style='text-align:center'>Note: the Bulletin Board is unmoderated.</p>
</body>
</html>
EOF;
