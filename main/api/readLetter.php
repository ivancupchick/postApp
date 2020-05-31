<?php
/**
 * Returns the list of policies.
 */
require 'database.php';

$letters = [];
$sql = "SELECT * FROM letters;";

if($result = mysqli_query($con,$sql))
{
  $i = 0;
  while($row = mysqli_fetch_assoc($result))
  {
    $letters[$i]['hash'] = $row['hash'];
    $letters[$i]['status'] = $row['status'];

    $letters[$i]['isMejdunarond'] = $row['isMejdunarond'];

    $letters[$i]['receiverAddress']['komu']['name'] = $row['r_ko_n'];
    $letters[$i]['receiverAddress']['komu']['surname'] = $row['r_ko_s'];
    $letters[$i]['receiverAddress']['komu']['otchestvo'] = $row['r_ko_o'];

    $letters[$i]['receiverAddress']['kuda']['streetType'] = $row['r_ku_st'];
    $letters[$i]['receiverAddress']['kuda']['streetName'] = $row['r_ku_sn'];
    $letters[$i]['receiverAddress']['kuda']['numberOfHouse'] = $row['r_ku_noh'];
    $letters[$i]['receiverAddress']['kuda']['numberOfKorpus'] = $row['r_ku_nok'];
    $letters[$i]['receiverAddress']['kuda']['numberOfFlat'] = $row['r_ku_nof'];

    $letters[$i]['receiverAddress']['index'] = $row['r_index'];

    $letters[$i]['receiverAddress']['nasPunktName']['oblast'] = $row['r_npn_o'];
    $letters[$i]['receiverAddress']['nasPunktName']['region'] = $row['r_npn_r'];
    $letters[$i]['receiverAddress']['nasPunktName']['townName'] = $row['r_npn_tn'];
    $letters[$i]['receiverAddress']['nasPunktName']['typeOfTown'] = $row['r_npn_tot'];

    $letters[$i]['otpravitelAddress']['otKogo']['name'] = $row['o_ok_n'];
    $letters[$i]['otpravitelAddress']['otKogo']['surname'] = $row['o_ok_s'];
    $letters[$i]['otpravitelAddress']['otKogo']['otchestvo'] = $row['o_ok_o'];

    $letters[$i]['otpravitelAddress']['adress']['oblast'] = $row['o_a_o'];
    $letters[$i]['otpravitelAddress']['adress']['region'] = $row['o_a_r'];
    $letters[$i]['otpravitelAddress']['adress']['townName'] = $row['o_a_tn'];
    $letters[$i]['otpravitelAddress']['adress']['typeOfTown'] = $row['o_a_tot'];

    $letters[$i]['otpravitelAddress']['adress']['streetType'] = $row['o_a_st'];
    $letters[$i]['otpravitelAddress']['adress']['streetName'] = $row['o_a_sn'];
    $letters[$i]['otpravitelAddress']['adress']['numberOfHouse'] = $row['o_a_noh'];
    $letters[$i]['otpravitelAddress']['adress']['numberOfKorpus'] = $row['o_a_nok'];
    $letters[$i]['otpravitelAddress']['adress']['numberOfFlat'] = $row['o_a_nof'];

    $letters[$i]['dateAndTimeOfStartWay'] = $row['dateAndTimeOfStartWay'];
    // $letter['hash'] = $row['hash'];
    // $letter['status'] = $row['status'];

    // $letter['isMejdunarond'] = $row['isMejdunarond'];
    // $letter['receiverAddress']['komu']['name'] = $row['r_ko_n'];
    // $letter['receiverAddress']['komu']['surname'] = $row['r_ko_s'];
    // $letter['receiverAddress']['komu']['otchestvo'] = $row['r_ko_o'];

    // $letter['receiverAddress']['adress']['streetType'] = $row['r_a_st'];
    // $letter['receiverAddress']['adress']['streetName'] = $row['r_a_sn'];
    // $letter['receiverAddress']['adress']['numberOfHouse'] = $row['r_a_noh'];
    // $letter['receiverAddress']['adress']['numberOfKorpus'] = $row['r_a_nok'];
    // $letter['receiverAddress']['adress']['numberOfFlat'] = $row['r_a_nof'];

    // $letter['receiverAddress']['adress']['index'] = $row['r_a_index'];
    // // $letter['receiverAddress']['adress']['index'] = $row['r_index'];

    // $letter['receiverAddress']['adress']['oblast'] = $row['r_a_o'];
    // $letter['receiverAddress']['adress']['region'] = $row['r_a_r'];
    // $letter['receiverAddress']['adress']['townName'] = $row['r_a_tn'];
    // $letter['receiverAddress']['adress']['typeOfTown'] = $row['r_a_tot'];
    // $letter['receiverAddress']['adress']['country'] = $row['r_a_c'];

    $i++;
  }

  echo json_encode($letters);
}
else
{
  http_response_code(404);
}
