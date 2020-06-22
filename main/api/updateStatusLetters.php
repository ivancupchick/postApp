<?php
require 'database.php';

header('Access-Control-Allow-Origin: *');

// Get the posted data.
$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
  // Extract the data.
  $request = json_decode($postdata);

  // Validate.
  // if ((int)$request->id < 0 || trim($request->status) == '') {
  //   return http_response_code(400);
  // }

  // http_response_code($request->id);

  // Sanitize.
  // $id    =  || [];
  $status = mysqli_real_escape_string($con, trim($request->status));
  $letterHistory = $request->letterHistory;
  // echo $letterHistory;

  $ids = implode(",", $request->id);

  $sql = "UPDATE `letters` SET `status`='$status', `history`=CONCAT(`history`, '|$letterHistory') WHERE `id` in '($ids)'";

  // Update.

  $arr = array('message' => $ids); //etc

  header('HTTP/1.1 201 Created');
  echo json_encode($arr);


  // if(mysqli_query($con, $sql)) {
  //   http_response_code(204);
  // } else {
  //   return http_response_code(422);
  // }
}
