<?php 

require_once('libs/japanese.php');

//↓↓↓ dummy data
// $headers = array("No", "品名", "数", "担当者");
// $content = array();
// for ($i=1; $i <= 100; $i++) { 
//     $item = array($i, '品名'.$i, rand(0, 99), '担当者'.$i);
//     array_push($content, $item);
// }
//↑↑↑ dummy data

class PDF extends PDF_Japanese
{
    // $pdf = new PDF();
    function Header()
    {
        $this->SetFont('SJIS','',14);
       
        //set 
        $this->SetTextColor(0,0,0);

        // 表のタイトル
        $title = $_POST['title'];
        $this->Cell(0, 10,mb_convert_encoding($title, 'SJIS'),0,0,'L', false);
        
        //line break
        $this->Ln();

        //テーブルのヘッダー
        // $headers = array("No", "品名", "数", "担当者");

        //テーブルのヘッダー
        $this->Cell(0, 10, mb_convert_encoding($_POST['openDay'], 'SJIS'),0,0,'R', false);
        $this->Ln(50);
        // var_dump($_POST['image']);die();
        $image = $_POST['image'];
         // $this->Image('../../app/refer/images/topics/$image', 30, 0, 0, 0);
        $this->Ln(50);
        $this->Cell(0, 10, mb_convert_encoding($_POST['body'], 'SJIS'),0,0,'L', false);
        $this->Ln();

        $this->Cell(0, 10, mb_convert_encoding($_POST['linkTitle'], 'SJIS'),0,0,'L', false);
        
        //line break
        $this->Ln();
    }


    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
      
        //$this->SetTextColor(0,0,0);
 
        $this->Write(5,'Visit ');
        // Then put a blue underlined link
        $this->SetTextColor(0,0,255);
        $this->SetFont('','U');
        $this->Write(5,'www.fpdf.org','http://www.fpdf.org');

        // Page number
        $this->Cell(0,0,mb_convert_encoding('ページ ', 'SJIS').$this->PageNo().'',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddSJISFont();
$pdf->AddPage();

$pdf->SetFont('SJIS','',14);

//insert image
// $pdf->Image('printer.jpg', 30, 0, 0, 0);

//line break
$pdf->Ln();

//Fill data
// foreach ($content as $key1 => $item) {
    

//     $pdf->Cell(15, 8, mb_convert_encoding($item[0], 'SJIS'), 1, 0, 'C');
    
//     //set text color to red
//     $pdf->SetTextColor(255, 0, 0);
//     $pdf->Cell(80, 8, mb_convert_encoding($item[1], 'SJIS'), 1, 0, 'C');
    
//     //set the other to black
//     $pdf->SetTextColor(0,0,0);

//     $pdf->Cell(15, 8, mb_convert_encoding($item[2], 'SJIS'), 1, 0, 'C');
//     $pdf->Cell(75, 8, mb_convert_encoding($item[3], 'SJIS'), 1, 0, 'C');
//     $pdf->Ln();
// }

$pdf->Output('I','ファイル名.pdf',true);
$pdf->Close();



?>