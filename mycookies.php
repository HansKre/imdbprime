<?php 
$userParam = isset($_GET["user"]) ? $_GET["user"] : "";
if ($userParam == "") {
  echo "Please provide a user by calling 'mycookies.php?user=name'<br>Aborting. Missing user parameter!";
  return;
}
if (isset($_COOKIE["userNameCookie"]) && $_COOKIE["userNameCookie"] == $userParam) {
  $userNameCookie = $_COOKIE["userNameCookie"];
  echo "Welcome back $userNameCookie";
} else {
  setcookie("userNameCookie",$userParam,time()+(3600*24));
  echo "Saved user name to cookie: $userParam";
}
?>