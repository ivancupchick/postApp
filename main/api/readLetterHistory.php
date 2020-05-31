<?php
/**
 * Returns the list of policies.
 */
require 'database.php';

$hash = $_GET['hash'] !== null ? $_GET['hash'] : '';

$letters = [];
$sql = "SELECT * FROM letters WHERE `hash` = '{$hash}' LIMIT 1;";

if($result = mysqli_query($con,$sql))
{
  $i = 0;
  while($row = mysqli_fetch_assoc($result))
  {
    $letters[$i]['history'] = $row['history'];

    $i++;
  }

  echo json_encode($letters);
}
else
{
  http_response_code(404);
}
