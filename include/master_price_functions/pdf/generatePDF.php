<?php
include_once('database/db.inc');
require_once('libs/japanese.php');

$invalid = isset($_POST["searchInvalid"])?$_POST["searchInvalid"]:null;
$err_msg = "";
$conn  = getConnection();
$display=FALSE;
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}
    $searchQuery = "";
    $searchCd = "";
    $searchMaker = "";
    $searchCustomer = "";
    $searchSupport = "";
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $maker = isset($_POST["selectedMaker"])?$_POST["selectedMaker"]:null;
        $customer = isset($_POST["selectedCustomer"])?$_POST["selectedCustomer"]:null;
        $cart = isset($_POST["searchCartridge"])?$_POST["searchCartridge"]:null;
        $support = isset($_POST["searchSupport"])?$_POST["searchSupport"]:null;
       // echo json_encode ($status);die();
        if(!empty($cart)){
            $searchCd = " and commodity.cd like '%$cart%' ";
        }
        if(!empty($maker)){
            $searchMaker = " and maker.no = ".$maker;
        }
        if(!empty($customer)){
            $searchCustomer = " and customer.no = ".$customer;
        }
        if(!empty($support)){
            $searchSupport = " and commodity.printer_support like '%$support%'";
        }
       
        if (!empty($searchCd) || !empty($searchMaker) || !empty($searchCustomer) || !empty($searchSupport)) {
            $display=TRUE;
            if (!empty($searchCd))
            {
                $searchQuery = $searchQuery.$searchCd ;
            }
            if (!empty($searchMaker))
            {
                $searchQuery = $searchQuery.$searchMaker;
            }
            if (!empty($searchCustomer))
            {
                $searchQuery = $searchQuery.$searchCustomer;
            }
            if (!empty($searchSupport))
            {
                $searchQuery = $searchQuery.$searchSupport;
            }
        }
    }
$stmt;
$result;
if($invalid==null && !empty($searchCustomer))
{
    $stmt = $conn->prepare("SELECT  distinct maker.name as maker,
    commodity.name as code,
    IF(commodity.green=1,'●','') as green,
    '' as blank,
    commodity.price as original,
    a.price as sp,
    commodity.num as qty,
    commodity.printer_support as support,
    commodity.note
    from selling_price a
    INNER JOIN commodity on a.commodity=commodity.no
    INNER JOIN inventory on inventory.commodity=commodity.no
    INNER JOIN maker on commodity.maker=maker.no
    INNER JOIN inventory_mark on inventory.inventory_mark=inventory_mark.no
    INNER JOIN print_type on print_type.no=commodity.print_type
    INNER JOIN customer on a.customer=customer.no		
    WHERE a.seq = (select max(seq) from selling_price WHERE approvalday IS NOT NULL".$searchCustomer.")
    AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 AND inventory_mark.hidden=0 ".$searchQuery."
    GROUP BY a.customer,a.commodity,a.num
    ORDER BY code ASC");
    $result = execute($stmt,$conn);
}
else if(!empty($invalid) && !empty($searchCustomer))
{
    $stmt = $conn->prepare("SELECT  distinct maker.name as maker,
    commodity.name as code,
    IF(commodity.green=1,'●','') as green,
    '' as blank,
    commodity.price as original,
    a.price as sp,
    commodity.num as qty,
    commodity.printer_support as support,
    commodity.note
    from selling_price a
    INNER JOIN commodity on a.commodity=commodity.no
    INNER JOIN inventory on inventory.commodity=commodity.no
    INNER JOIN maker on commodity.maker=maker.no
    INNER JOIN inventory_mark on inventory.inventory_mark=inventory_mark.no
    INNER JOIN print_type on print_type.no=commodity.print_type
    INNER JOIN customer on a.customer=customer.no		
    WHERE a.seq = (select max(seq) from selling_price WHERE ".$searchCustomer." AND approvalday IS NOT NULL)
    AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 AND inventory_mark.hidden=0 ".$searchQuery."
    GROUP BY a.customer,a.commodity,a.num
    ORDER BY code ASC");
    $result = execute($stmt,$conn);
}


if($result==TRUE)
{
$result = $stmt->get_result();
$resultSet = $result;
} 

class PDF extends PDF_Japanese
{
    public $tel;
    var $widths;
    var $aligns;
    public function __construct($o,$custom) {
        parent::__construct($o);
        $this->tel = $custom;
    }
    function Header()
    {

        $this->SetFont('SJIS','',14);
       
        //set 
        $this->SetTextColor(0,0,0);

        // 表のタイトル
        $top = "在庫表";
        $this->Cell(0, 10,mb_convert_encoding($top, 'SJIS'),0,0,'C', false);
        $this->Ln();
        $this->Cell(0, 10,mb_convert_encoding("エヌシーアイ販売(株)　CS業務担当", 'SJIS'),0,0,'R', false);
        $this->Ln();
        $this->Cell(0, 10,mb_convert_encoding("TEL :".$this->tel." FAX :".$this->tel, 'SJIS'),0,0,'R', false);
        
        //line break
        $this->Ln();
        $title = "在庫状況表示：　○/在庫あり　△/在庫少量　リターン/リターン再生対応";
        $this->Cell(0, 10,mb_convert_encoding($title, 'SJIS'),0,0,'C', false);
        
        //line break
        $this->Ln();
        $this->SetFont('SJIS','B',8);
        //テーブルのヘッダー
        // $headers = array("No", "品名", "数", "担当者");
        $headers = array("メーカー名","弊社型番","グリーン購入法","-","参考価格","再生価格","再生回数","対応プリンター","備考");
        //テーブルのヘッダー
        // $this->Cell(15, 8, mb_convert_encoding("No", 'SJIS'), 1, 0, 'C');
        // $this->Cell(80, 8, mb_convert_encoding("品名", 'SJIS'), 1, 0, 'C');
        // $this->Cell(15, 8, mb_convert_encoding("数", 'SJIS'), 1, 0, 'C');
        // $this->Cell(75, 8, mb_convert_encoding("担当者", 'SJIS'), 1, 0, 'C');
        foreach($headers as $h)
        {
            $this->Cell(22, 8, mb_convert_encoding($h, 'SJIS'), 1, 0, 'C');
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
    function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,mb_convert_encoding($data[$i],'SJIS'),0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=mb_convert_encoding($s[$i],'SJIS');
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}

}

$pdf = new PDF('P',1234567);
$pdf->AddSJISFont();
$pdf->AddPage();

$pdf->SetFont('SJIS','',8);

//insert image
// $pdf->Image('printer.jpg', 30, 0, 0, 50);

//line break
// $pdf->Ln();
$pdf->SetWidths(array(22,22,22,22,22,22,22,22,22));
//Fill data
foreach ($result as $row) {
    // foreach($row as $column)
    // {
    //     $pdf->MultiCell(22, 15, mb_convert_encoding($column, 'SJIS'), 1, 'C');
    // }
    // $pdf->Ln();
    $pdf->Row(array($row["maker"],$row["code"],$row["green"],$row["blank"],$row["original"],$row["sp"],$row["qty"],$row["support"],$row["note"]));
}

$pdf->Output();
// readfile("filename.pdf");
$pdf->Close();
?>