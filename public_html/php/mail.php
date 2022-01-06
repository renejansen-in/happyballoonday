<?php
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];
$subject = $_POST['subject'];
header('Content-Type: application/json');
if ($name === ''){
  print json_encode(array('message' => 'Ik wil wel graag je naam weten', 'code' => 0));
  exit();
}
if ($email === ''){
  print json_encode(array('message' => 'Zonder e-mail adres kan ik je niet vinden', 'code' => 0));
  exit();
} else {
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
  print json_encode(array('message' => 'Volgens mij is je email adres verkeerd geschreven', 'code' => 0));
  exit();
  }
}
if ($subject === ''){
  print json_encode(array('message' => 'Over welk onderwerp gaat het?', 'code' => 0));
  exit();
}
if ($message === ''){
  print json_encode(array('message' => 'Beschrijf alvast waar ik je mee kan helpen', 'code' => 0));
  exit();
}
$content="From: $name \nEmail: $email \nMessage: $message";
$recipient = "info@happyballoonday.nl";
$mailheader = "From: $email \r\n";
mail($recipient, $subject, $content, $mailheader) or die("Error!");
print json_encode(array('message' => 'Bedankt voor je berichtje, ik neem z.s.m. contact met je op!', 'code' => 1));
exit();
?>