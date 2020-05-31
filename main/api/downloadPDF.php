<?php
require 'database.php';
require_once("../fpdf/fpdf.php");
// require_once("");

require '../vendor/autoload.php';

$letter = [];

$id = ($_GET['id'] !== null && (int)$_GET['id'] > -1) ? mysqli_real_escape_string($con, (int)$_GET['id']) : false;
global $hash;
$hash = $_GET['hash'] !== null ? $_GET['hash'] : '';

if ($id === false) {
    return http_response_code(400);
}

$sql = "SELECT * FROM letters WHERE `id` = $id LIMIT 1";

//$sql = "SELECT * FROM letters WHERE `id` = '{$id}' LIMIT 1";
//временно указал id равным 1 для теста, позже его придётся передавать
//$sql = "SELECT id, number, amount FROM policies";

function writeOnPdf(FPDF $pdf, string $text, int $x, int $y) {
    $pdf->SetXY($x, $y);
    $pdf->Write(0, iconv('utf-8', 'windows-1251', $text));
}

$line = "";
$j = 0;

function ceateBlock(FPDF $pdf, array $array, int $x, int $y, int $lines, $row) {
    $stepBottom = 7;
    $lengthOfTextLine = 140;

    // $pdf->GetStringWidth("d");

    $jFinish = count($array);

    global $j;
    $j = 0;

    for ($i=0; $i <= $lines && $j <= $jFinish; $i++) {
        global $line;
        $line = "";

        global $j;
        for ($jj=$j; $jj <= $jFinish; $jj++) {
            $newLine = $line;

            if (
                ($array[$jj] == "обл. " && $jj == 2) ||
                ($array[$jj] == "р-н " && $jj == 3) ||
                ($array[$jj] == $row['r_a_st'] && $jj == 6) ||
                ($array[$jj] == "к. " && $jj == 8) ||
                ($array[$jj] == "кв. " && $jj == 9)
            ) {
                $j++;
                continue;
            }

            $newLine = $newLine . " " . $array[$jj];

            if ($jj == 0) {
                global $j;
                $j++;
                global $line;
                $line = $newLine;
                break;
            }

            if ($pdf->GetStringWidth($newLine) > $lengthOfTextLine) {
                break;
            } else {
                global $j;
                $j++;
                global $line;
                $line = $newLine;
                continue;
            }
        }

        global $line;
        writeOnPdf($pdf, $line, $x, $y + ($stepBottom * $i));
    }
}


if ($result = mysqli_query($con, $sql)) {


    // начало PDF

    $pdf = new FPDF('L', 'mm', 'A4');

    $pdf->AddPage();

//$pdf=new FPDF();
//// устанавливаем заголовок документа
    $pdf->SetTitle("Скачать PDF письма", true);

//// создаем страницу
//$pdf->AddPage('L');
//$pdf->SetDisplayMode(real,'default');

// добавляем шрифт ариал
    $pdf->AddFont('Arial', '', 'arial.php');
// устанавливаем шрифт Ариал
    $pdf->SetFont('Arial');
// устанавливаем цвет шрифта
//$pdf->SetTextColor(250,60,100);
// устанавливаем размер шрифта
    $pdf->SetFontSize(17);

// добавляем текст

$i = 0;
    while ($row = mysqli_fetch_assoc($result)) {
//        $letter['hash'] = $row['hash'];
//        $letter['status'] = $row['status'];

//        $letter['isMejdunarond'] = $row['isMejdunarond'];


        $arrayForInsertingDataRightBotton = array(
            $row['r_ko_s'],
            $row['r_ko_n'] . " " . $row['r_ko_o'],

            "обл. " . $row['r_a_o'], // please check if this exist
            "р-н " . $row['r_a_r'], // please check if this exist

            $row['r_a_index'],

            $row['r_a_tot'] . " " . $row['r_a_tn'],

            $row['r_a_st'] . " " . $row['r_a_sn'], // please check if this exist

            "д. " . $row['r_a_noh'],
            "к. " . $row['r_a_nok'], // please check if this exist
            "кв. " . $row['r_a_nof'], // please check if this exist

            // $row['r_a_c'] // check if this exist, oby for mejdunarodn
        );

        ceateBlock($pdf, $arrayForInsertingDataRightBotton, 140, 77, 8, $row);
        //нижний правый угол
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


        $arrayForInsertingDataLeftTop = array(
            $row['o_ok_s'],
            $row['o_ok_n'] . " " . $row['o_ok_o'],

            "обл. " . $row['o_a_o'], // please check if this exist
            "р-н " . $row['o_a_r'], // please check if this exist

            $row['o_a_index'], // may not need ?

            $row['o_a_tot'] . " " . $row['o_a_tn'],

            $row['o_a_st'] . " " . $row['o_a_sn'], // please check if this exist

            "д. " . $row['o_a_noh'],
            "к. " . $row['o_a_nok'], // please check if this exist
            "кв. " . $row['o_a_nof'], // please check if this exist

            // $row['r_a_c'] // check if this exist, oby for mejdunarodn
        );

        ceateBlock($pdf, $arrayForInsertingDataLeftTop, 20, 22, 8, $row);

        //вержний левый угол
        // $letter['otpravitelAddress']['otKogo']['name'] = $row['o_ok_n'];
        // $letter['otpravitelAddress']['otKogo']['surname'] = $row['o_ok_s'];
        // $letter['otpravitelAddress']['otKogo']['otchestvo'] = $row['o_ok_o'];

        // $letter['otpravitelAddress']['adress']['oblast'] = $row['o_a_o'];
        // $letter['otpravitelAddress']['adress']['region'] = $row['o_a_r'];
        // $letter['otpravitelAddress']['adress']['townName'] = $row['o_a_tn'];
        // $letter['otpravitelAddress']['adress']['typeOfTown'] = $row['o_a_tot'];

        // $letter['otpravitelAddress']['adress']['streetType'] = $row['o_a_st'];
        // $letter['otpravitelAddress']['adress']['streetName'] = $row['o_a_sn'];
        // $letter['otpravitelAddress']['adress']['numberOfHouse'] = $row['o_a_noh'];
        // $letter['otpravitelAddress']['adress']['numberOfKorpus'] = $row['o_a_nok'];
        // $letter['otpravitelAddress']['adress']['numberOfFlat'] = $row['o_a_nof'];
//
//        $letter['dateAndTimeOfStartWay'] = $row['dateAndTimeOfStartWay'];

        $i++;
    }

    //1
    // writeOnPdf($pdf, $letter['receiverAddress']['komu']['surname'], 140, 77);

    // //2
    // writeOnPdf($pdf, $letter['receiverAddress']['komu']['name'], 140, 84);

    // writeOnPdf($pdf, $letter['receiverAddress']['komu']['otchestvo'], 175, 84);

    // //3
    // writeOnPdf($pdf, $letter['receiverAddress']['nasPunktName']['oblast'], 140, 91);

    // writeOnPdf($pdf, "обл.", 205, 91);

    // //4
    // writeOnPdf($pdf, $letter['receiverAddress']['nasPunktName']['region'], 140, 98);

    // writeOnPdf($pdf, "р-н", 205, 98);

    // //5 индекс
    // writeOnPdf($pdf, $letter['receiverAddress']['index'], 140, 105);

    // writeOnPdf($pdf, $letter['receiverAddress']['nasPunktName']['typeOfTown'], 171, 105);

    // writeOnPdf($pdf, $letter['receiverAddress']['nasPunktName']['townName'], 180, 105);

    // //6
    // writeOnPdf($pdf, $letter['receiverAddress']['kuda']['streetType'], 140, 112);

    // writeOnPdf($pdf, $letter['receiverAddress']['kuda']['streetName'], 150, 112);

    // writeOnPdf($pdf, $letter['receiverAddress']['kuda']['numberOfHouse'], 185, 112);

    // writeOnPdf($pdf, $letter['receiverAddress']['kuda']['numberOfKorpus'], 295, 112);

    // writeOnPdf($pdf, $letter['receiverAddress']['kuda']['numberOfFlat'], 205, 112);


//    $pdf->Ln(4);                    //Break

    //рамка
    $pdf->Line(10, 10, 230, 10);  //Set the line
    $pdf->Line(230, 10, 230, 120);  //Set the line
    $pdf->Line(230, 120, 10, 120);  //Set the line
    $pdf->Line(10, 120, 10, 10);  //Set the line

    //верхий левый угол - линии
    $pdf->Line(20, 22 + 3, 90, 22 + 3);
    $pdf->Line(20, 29 + 3, 90, 29 + 3);
    $pdf->Line(20, 36 + 3, 90, 36 + 3);
    $pdf->Line(20, 43 + 3, 90, 43 + 3);
    $pdf->Line(20, 50 + 3, 90, 50 + 3);

    //правый нижний угол - линии

    $pdf->Line(140, 77 + 3, 220, 77 + 3);
    $pdf->Line(140, 84 + 3, 220, 84 + 3);
    $pdf->Line(140, 91 + 3, 220, 91 + 3);
    $pdf->Line(140, 98 + 3, 220, 98 + 3);
    $pdf->Line(140, 105 + 3, 220, 105 + 3);
    $pdf->Line(140, 112 + 3, 220, 112 + 3);




    //вержний левый угол

    //1
    // writeOnPdf($pdf, $letter['otpravitelAddress']['otKogo']['surname'], 20, 22);

    // //2
    // writeOnPdf($pdf, $letter['otpravitelAddress']['otKogo']['name'], 20, 29);

    // writeOnPdf($pdf, $letter['otpravitelAddress']['otKogo']['otchestvo'], 55, 29);

    // //3
    // writeOnPdf($pdf, $letter['otpravitelAddress']['adress']['oblast'], 20, 36);

    // writeOnPdf($pdf, "обл.", 80, 36);

    // //4
    // writeOnPdf($pdf, $letter['otpravitelAddress']['adress']['region'], 20, 43);

    // writeOnPdf($pdf, $letter['otpravitelAddress']['adress']['typeOfTown'], 45, 43);

    // writeOnPdf($pdf, $letter['otpravitelAddress']['adress']['townName'], 55, 43);

    // //5
    // writeOnPdf($pdf, $letter['otpravitelAddress']['adress']['streetType'], 20, 50);

    // writeOnPdf($pdf, $letter['otpravitelAddress']['adress']['streetName'], 30, 50);

    // writeOnPdf($pdf, $letter['otpravitelAddress']['adress']['numberOfHouse'], 65, 50);

    // writeOnPdf($pdf, $letter['otpravitelAddress']['adress']['numberOfKorpus'], 75, 50);

    // writeOnPdf($pdf, $letter['otpravitelAddress']['adress']['numberOfFlat'], 85, 50);

//индекс отправителя забыли в бд

    $value = $id;
    $pdf->Image('http://chart.apis.google.com/chart?cht=qr&chs=350x350&chl=' . $value . '.png', 20, 65, 45);

    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    $file = $generator->getBarcode($hash, $generator::TYPE_CODE_128);
    $baseFile = "data:image/png;base64," . base64_encode($file);

    $img = str_replace('data:image/png;base64,', '', $baseFile);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    file_put_contents('/tmp/image.png', $data);

    $pdf->Image('/tmp/image.png', 75, 70, 50);


    // конец PDF


    $pdf->Output('letter.pdf', 'I');
} else {
    http_response_code(404);
}

//$dateAndTimeOfStartWay = mysqli_real_escape_string($con, (int)$request->dateAndTimeOfStartWay);
// подключаем шрифты
//define('FPDF_FONTPATH',"fpdf/font/");
// подключаем библиотеку
//require('fpdf/fpdf.php');
//require_once( "../fpdf/fpdf.php" );
//// создаем PDF документ
//
//$pdf = new FPDF( 'L', 'mm', 'A4' );
//
//$pdf->AddPage();
//
////$pdf=new FPDF();
////// устанавливаем заголовок документа
//$pdf->SetTitle("Скачать PDF письма", true);
//
////// создаем страницу
////$pdf->AddPage('L');
////$pdf->SetDisplayMode(real,'default');
//
//// добавляем шрифт ариал
//$pdf->AddFont('Arial','','arial.php');
//// устанавливаем шрифт Ариал
//$pdf->SetFont('Arial');
//// устанавливаем цвет шрифта
////$pdf->SetTextColor(250,60,100);
//// устанавливаем размер шрифта
//$pdf->SetFontSize(6);
//
//// добавляем текст
//$pdf->SetXY(140,75);
//writeOnPdf($pdf, $r_ko_n));
//
//$pdf->SetXY(140,82);
//$pdf->Write(0,iconv('utf-8', 'windows-1251',$r_ko_s));
//
//$pdf->SetXY(175,82);
//$pdf->Write(0,iconv('utf-8', 'windows-1251',$r_ko_o));
//
//
//
//
//// выводим документа в браузере
//$pdf->Output('letter.pdf','I');
//


//require_once( "../fpdf/fpdf.php" );
//
//// Начало конфигурации
//
//$textColour = array( 0, 0, 0 );
//$headerColour = array( 100, 100, 100 );
//$tableHeaderTopTextColour = array( 255, 255, 255 );
//$tableHeaderTopFillColour = array( 125, 152, 179 );
//$tableHeaderTopProductTextColour = array( 0, 0, 0 );
//$tableHeaderTopProductFillColour = array( 143, 173, 204 );
//$tableHeaderLeftTextColour = array( 99, 42, 57 );
//$tableHeaderLeftFillColour = array( 184, 207, 229 );
//$tableBorderColour = array( 50, 50, 50 );
//$tableRowFillColour = array( 213, 170, 170 );
//$reportName = "привет";
//$reportNameYPos = 160;
//$logoFile = "widget-company-logo.png";
//$logoXPos = 50;
//$logoYPos = 108;
//$logoWidth = 110;
//$columnLabels = array( "Q1", "Q2", "Q3", "Q4" );
//$rowLabels = array( "SupaWidget", "WonderWidget", "MegaWidget", "HyperWidget" );
//$chartXPos = 20;
//$chartYPos = 250;
//$chartWidth = 160;
//$chartHeight = 80;
//$chartXLabel = "Product";
//$chartYLabel = "2009 Sales";
//$chartYStep = 20000;
//
//$chartColours = array(
//                  array( 255, 100, 100 ),
//                  array( 100, 255, 100 ),
//                  array( 100, 100, 255 ),
//                  array( 255, 255, 100 ),
//                );
//
//$data = array(
//          array( 9940, 10100, 9490, 11730 ),
//          array( 19310, 21140, 20560, 22590 ),
//          array( 25110, 26260, 25210, 28370 ),
//          array( 27650, 24550, 30040, 31980 ),
//        );
//
//// Конец конфигурации
//
//
////  Создаем титульную страницу
//
//
//$pdf = new FPDF( 'P', 'mm', 'A4' );
//
//
//
//$pdf->SetTextColor( $textColour[0], $textColour[1], $textColour[2] );
//
//
//$pdf->AddPage();
//
//
//
//// Название отчета
////$pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
////$pdf->SetFont('DejaVu','B',12);
////$pdf->SetFont( 'Arial', 'B', 24 );
//// добавляем шрифт ариал
//$pdf->AddFont('Arial','','arial.php');
//// устанавливаем шрифт Ариал
//$pdf->SetFont('Arial');
//
//
//$pdf->Ln( $reportNameYPos );
//
//
////$pdf->Cell( 0, 15, $reportName, 0, 0, 'C' );
//
//// добавляем текст
//$pdf->SetXY(10,10);
//$pdf->Write(0,iconv('utf-8', 'windows-1251',$reportName));
//
//
//
//
///***
//  Выводим PDF
//***/
//
//
//
//$pdf->Output( "report.pdf", "I" );

?>
