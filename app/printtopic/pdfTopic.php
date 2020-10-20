<?php 
include_once('pdf/japanese.php');
require_once('getTopicDetail.php');

$imgPath = "";
if (!empty($data["image"])) {
    $imgPath = "../../app/refer/images/topics/".$data["image"];
}

$mm = 0.2645833333;

class PDF extends PDF_Japanese
{
    private $data;

    public function setData($data)
    {
        $this->data = $data;
    }

    function Header()
    {
        $this->SetFont('SJIS','',14);
       
        $this->SetTextColor(0,0,0);

        $this->SetFont('SJIS','',7);
        $this->Cell(0, 0, mb_convert_encoding("公開日：". $this->data["open_day"], 'SJIS'), 0, 0, 'R', false);
        $this->Ln(3);

        $this->SetFont('SJIS','',14);
        $this->Ln();
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-10);
      
        //$this->SetTextColor(0,0,0);
 
        // Then put a blue underlined link
        // $this->SetTextColor(0,0,255);
        // $this->SetFont('','U');

        // Page number
        $this->SetFont('SJIS','',7);
        $this->Cell(0,0,mb_convert_encoding('ページ ', 'SJIS').$this->PageNo()."/{nb}".'',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->setData($data);

$pdf->AddSJISFont();
$pdf->AddPage("P","A4");

$pdf->SetFont('SJIS','',14);

//title
$pdf->MultiCell(0, 7, mb_convert_encoding( $data['title'], 'SJIS'),0,'C', false);
$pdf->Ln();

//image
$imgPath = "";
if (!empty($data["image"])) {
    $imgPath = "../refer/images/topics/".$data["image"];
}

//insert image
if (file_exists($imgPath)) {
    list($width, $height) =  getimagesize($imgPath);

    $r = $height/$width;

    //convert size to milimet
    $width  = $width  * $mm;
    $height = $height * $mm;

    //set max height is 100 mili
    if ($height > 100) {
        $height = 100;
    }
    
    $pageWidth = $pdf->GetPageWidth() - 20;

    //set max width is page width
    if ($width > $pageWidth) {
        $width = $pageWidth;
    }

    if ($height/$width > $r) {
        $height = $width * $r;
    } elseif ($height/$width < $r) {
        $width = $height / $r;
    }

    $pdf->Image($imgPath, null, null, $width, $height);
}

//line break
$pdf->Ln();

//topic content
$pdf->SetFont('SJIS','',11);
$pdf->MultiCell(0, 7, mb_convert_encoding( $data['body'], 'SJIS'),0,'L', false);
$pdf->Ln();

//link
if (!empty($data["link_title"])) {
    $pdf->MultiCell(0, 7, mb_convert_encoding( $data['link_title'], 'SJIS'),0,'L', false);
}
if (!empty($data["link_url"])) {
    $pdf->MultiCell(0, 7, mb_convert_encoding( $data['link_url'], 'SJIS'),0,'L', false);
    $pdf->Ln();        
}


$pdf->Output('I','Topic.pdf',true);
$pdf->Close();
?>