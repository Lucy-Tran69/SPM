<?php 

include_once('database/db.inc');
require_once('libs/japanese.php');


$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}

$stmt = $conn->prepare("select maker.name as mName,
                        commodity.name as cName,
                        inventory_mark.display as display 
                        from maker 
                        LEFT JOIN commodity on maker.no=commodity.maker
                        LEFT JOIN inventory on inventory.commodity=commodity.no
                        LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                        where commodity.name IS NOT NULL order by commodity.no ASC");
$result = execute($stmt,$conn);
if($result==TRUE)
{
    $result = $stmt->get_result();
    $resultSet = $result;
}


//↓↓↓ dummy data
// // $headers = array("No", "品名", "数", "担当者");
// $content = array();
// for ($i=1; $i <= 100; $i++) { 
//     $item = array($i, '品名'.$i, rand(0, 99), '担当者'.$i);
//     array_push($content, $item);
// }
//↑↑↑ dummy data

class PDF extends PDF_Japanese
{
    function Header()
    {
        $this->SetFont('SJIS','B',14);
       
        //set 
        $this->SetTextColor(0,0,0);

        // 表のタイトル
        $title = "在庫状況表示：　○/在庫あり　△/在庫少量　リターン/リターン再生対応";
        $this->Cell(0, 10,mb_convert_encoding($title, 'SJIS'),0,0,'C', false);
        
        //line break
        $this->Ln();

        //テーブルのヘッダー
        // $headers = array("No", "品名", "数", "担当者");
        $headers = array("メーカー","完成品在庫","在庫状況");
        //テーブルのヘッダー
        // $this->Cell(15, 8, mb_convert_encoding("No", 'SJIS'), 1, 0, 'C');
        // $this->Cell(80, 8, mb_convert_encoding("品名", 'SJIS'), 1, 0, 'C');
        // $this->Cell(15, 8, mb_convert_encoding("数", 'SJIS'), 1, 0, 'C');
        // $this->Cell(75, 8, mb_convert_encoding("担当者", 'SJIS'), 1, 0, 'C');
        foreach($headers as $h)
        {
            $this->Cell(65, 8, mb_convert_encoding($h, 'SJIS'), 1, 0, 'C');
        }
        
        //line break
        $this->Ln();
    }


    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
      
        //$this->SetTextColor(0,0,0);
 
        // $this->Write(5,'Visit ');
        // // Then put a blue underlined link
        // $this->SetTextColor(0,0,255);
        // $this->SetFont('','U');
        // $this->Write(5,'www.fpdf.org','http://www.fpdf.org');

        // Page number
        $this->Cell(0,0,mb_convert_encoding('ページ ', 'SJIS').$this->PageNo().'',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddSJISFont();
$pdf->AddPage();

$pdf->SetFont('SJIS','',14);

//insert image
// $pdf->Image('printer.jpg', 30, 0, 0, 50);

//line break
// $pdf->Ln();

//Fill data
foreach ($result as $row) {
    foreach($row as $column)
    {
        $pdf->Cell(65, 8, mb_convert_encoding($column, 'SJIS'), 1, 0, 'C');
    }
    $pdf->Ln();
}

$pdf->Output();
$pdf->Close();


?>