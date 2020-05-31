<?php
require 'database.php';

// Get the posted data.
$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);


    // Validate.
    if (
        trim($request->hash) === '' ||
        trim($request->receiverAddress->komu->name) === '' ||
        trim($request->receiverAddress->komu->surname) === '' ||
        trim($request->receiverAddress->komu->otchestvo) === '' // ||
        // trim() === ''
    ) {
        return http_response_code(400);
    }

    // Sanitize.
    $hash = mysqli_real_escape_string($con, trim($request->hash));
    $status = mysqli_real_escape_string($con, trim($request->status));
    $isMejdunarond = mysqli_real_escape_string($con, trim($request->isMejdunarond));

    $r_ko_n = mysqli_real_escape_string($con, trim($request->receiverAddress->komu->name));
    $r_ko_s = mysqli_real_escape_string($con, trim($request->receiverAddress->komu->surname));
    $r_ko_o = mysqli_real_escape_string($con, trim($request->receiverAddress->komu->otchestvo));

    $r_a_st = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->streetType));
    $r_a_sn = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->streetName));
    $r_a_noh = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->numberOfHouse));
    $r_a_nok = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->numberOfKorpus));
    $r_a_nof = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->numberOfFlat));

    $r_a_o = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->oblast));
    $r_a_r = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->region));
    $r_a_tn = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->townName));
    $r_a_tot = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->typeOfTown));
    $r_a_c = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->country));

    $r_a_index = mysqli_real_escape_string($con, trim($request->receiverAddress->adress->index));

    $o_ok_n = mysqli_real_escape_string($con, trim($request->otpravitelAddress->otKogo->name));
    $o_ok_s = mysqli_real_escape_string($con, trim($request->otpravitelAddress->otKogo->surname));
    $o_ok_o = mysqli_real_escape_string($con, trim($request->otpravitelAddress->otKogo->otchestvo));

    $o_a_o = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->oblast));
    $o_a_r = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->region));
    $o_a_tn = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->townName));
    $o_a_tot = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->typeOfTown));

    $o_a_c = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->country));
    $o_a_index = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->index));

    $o_a_st = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->streetType));
    $o_a_sn = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->streetName));
    $o_a_noh = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->numberOfHouse));
    $o_a_nok = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->numberOfKorpus));
    $o_a_nof = mysqli_real_escape_string($con, trim($request->otpravitelAddress->adress->numberOfFlat));

    $lt = mysqli_real_escape_string($con, trim($request->letterType));
    $sm = mysqli_real_escape_string($con, trim($request->specMarks));
    $lwav = mysqli_real_escape_string($con, trim($request->letterWithAnnouncedValue));
    $lwp = mysqli_real_escape_string($con, trim($request->letterWithPrice));
    $dateAndTimeOfStartWay = mysqli_real_escape_string($con, (int)$request->dateAndTimeOfStartWay);
    $history = mysqli_real_escape_string($con, (int)$request->history);

    // Create.
    $sql = "INSERT INTO `letters`(`id`,
    `hash`,`status`,`isMejdunarond`,`r_ko_n`,`r_ko_s`,`r_ko_o`,`r_a_st`,`r_a_sn`,`r_a_noh`,`r_a_nok`,`r_a_nof`,`r_a_o`,`r_a_r`,`r_a_tn`,`r_a_tot`,`r_a_c`,`r_a_index`,`o_ok_n`,`o_ok_s`,`o_ok_o`,`o_a_o`,`o_a_r`,`o_a_tn`,`o_a_tot`,`o_a_c`,`o_a_index`,`o_a_st`,`o_a_sn`,`o_a_noh`,`o_a_nok`,`o_a_nof`,`lt`,`sm`,`lwav`,`lwp`,`dateAndTimeOfStartWay`,`history`
    ) VALUES (null,
    '{$hash}','{$status}','{$isMejdunarond}','{$r_ko_n}','{$r_ko_s}','{$r_ko_o}','{$r_a_st}','{$r_a_sn}','{$r_a_noh}','{$r_a_nok}','{$r_a_nof}','{$r_a_o}','{$r_a_r}','{$r_a_tn}','{$r_a_tot}','{$r_a_c}','{$r_a_index}','{$o_ok_n}','{$o_ok_s}','{$o_ok_o}','{$o_a_o}','{$o_a_r}','{$o_a_tn}','{$o_a_tot}','{$o_a_c}','{$o_a_index}','{$o_a_st}','{$o_a_sn}','{$o_a_noh}','{$o_a_nok}','{$o_a_nof}','{$lt}','{$sm}','{$lwav}','{$lwp}','{$dateAndTimeOfStartWay}','{$history}'
    )";

    /*

    create table letters( id int not null auto_increment,
    hash varchar(200),
    status varchar(200),
    isMejdunarond varchar(200),
    r_ko_n varchar(200),
    r_ko_s varchar(200),
    r_ko_o varchar(200),
    r_a_st varchar(200),
    r_a_sn varchar(200),
    r_a_noh varchar(200),
    r_a_nok varchar(200),
    r_a_nof varchar(200),
    r_a_o varchar(200),
    r_a_r varchar(200),
    r_a_tn varchar(200),
    r_a_tot varchar(200),
    r_a_c varchar(200),
    r_a_index varchar(200),
    o_ok_n varchar(200),
    o_ok_s varchar(200),
    o_ok_o varchar(200),
    o_a_o varchar(200),
    o_a_r varchar(200),
    o_a_tn varchar(200),
    o_a_tot varchar(200),
    o_a_c varchar(200),
    o_a_index varchar(200),
    o_a_st varchar(200),
    o_a_sn varchar(200),
    o_a_noh varchar(200),
    o_a_nok varchar(200),
    o_a_nof varchar(200),
    lt varchar(200),
    sm varchar(200),
    lwav varchar(200),
    lwp varchar(200),
    dateAndTimeOfStartWay float,
    primary key(id))
    */

    if (mysqli_query($con, $sql)) {
        // http_response_code(201);

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $id = mysqli_insert_id($con);

        // $hash; 8, 6, 4, 2, 3, 5, 9, 7;

        $newHash = str_pad($id, 8, "0", STR_PAD_LEFT);

        $first = (int)$newHash[0] * 8;
        $second = (int)$newHash[1] * 6;
        $third = (int)$newHash[2] * 4;
        $fourth = (int)$newHash[3] * 2;
        $fifth = (int)$newHash[4] * 3;
        $sixth = (int)$newHash[5] * 5;
        $seventh = (int)$newHash[6] * 9;
        $eighth = (int)$newHash[7] * 7;
        $sum = $first + $second + $third + $fourth + $fifth + $sixth + $seventh + $eighth;

        $promSum = 11 - ($sum % 11);

        $contrl = 0;

        if ($promSum >= 1 && $promSum <= 9) {
          $contrl = $promSum;
        } else if ($promSum == 10) {
          $contrl = 0;
        } else if ($promSum == 11) {
          $contrl = 5;
        }

        $newNewHash = $hash . $newHash . $contrl . "BY";

        $sql = "UPDATE `letters` SET `hash`='$newNewHash' WHERE `id` = '{$id}' LIMIT 1";

        if (mysqli_query($con, $sql)) {
          http_response_code(201);

          $letter["url"] = $actual_link . "/../downloadPDF.php?id=" . $id . "&hash=" . $newNewHash;
          $letter["hash"] = $newNewHash;
          echo json_encode($letter);
        } else {
          return http_response_code(422);
        }



    } else {
        http_response_code(422);
    }
}
/*
 export interface SimpleLeterInterface {
  id: number;
  hash: string;
  status: LetterStatus;
  isMejdunarond: string;

  receiverAddress: {
    komu:  {
      name: string;
      surname: string;
      otchestvo: string;
    };
    adress: {
      streetType?: StreetType; // enum
      streetName?: string;
      numberOfHouse: string;
      numberOfKorpus?: string;
      numberOfFlat?: string;
      oblast: string; // область
      region?: string; // район
      townName: string; // город (название населеного пункта)
      typeOfTown: TypeOfTown;
      country: string;
      index: string;
    }

  };

  otpravitelAddress: {
    otKogo:  {
      name: string;
      surname: string;
      otchestvo: string;
    },
    adress: {
      streetType?: StreetType; // enum
      streetName?: string;
      numberOfHouse: string;
      numberOfKorpus?: string;
      numberOfFlat?: string;
      oblast: string; // область
      region?: string; // район
      townName: string; // город (название населеного пункта)
      typeOfTown: TypeOfTown;
      country: string;
      index: string;
    }
  };

   typeOfLetter: TypeOfLetter; // вид отправления
   letterType?: LetterType; // if typeOfLetter === TypeOfLetter.pismo
  specMarks: string; // string[].join(',') // if typeOfLetter === LetterType.zakaz
   letterWithAnnouncedValue: 'true' | 'false';
   letterWithPrice: 'true' | 'false';

  dateAndTimeOfStartWay: number;
}
*/
