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
  if ((int)$request->id < 0 || trim($request->status) == '') {
    return http_response_code(400);
  }

  // Sanitize.
  $id    = mysqli_real_escape_string($con, (int)$request->id);
  $status = mysqli_real_escape_string($con, trim($request->status));
  $letterHistory = $request->letterHistory;
  // echo $letterHistory;

  // Update.
  $sql = "UPDATE `letters` SET `status`='$status', `history`=CONCAT(`history`, '|$letterHistory') WHERE `id` = '{$id}' LIMIT 1";

  if(mysqli_query($con, $sql)) {
    http_response_code(204);
  } else {
    return http_response_code(422);
  }
}
