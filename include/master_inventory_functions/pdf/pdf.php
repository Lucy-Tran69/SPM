<?php 
// header("Content-type:application/pdf");
// header("Content-Disposition:attachment;filename='filename.pdf'");
include_once('database/db.inc');
require_once('libs/japanese.php');
$invalid = isset($_POST["pdfInvalid"])?$_POST["pdfInvalid"]:null;
$inventory= isset($_POST["pdfInventory"])?$_POST["pdfInventory"]:null;
$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}
$searchQuery = "";
$searchCd = "";
$searchMaker = "";

    $maker = isset($_POST["selectedMaker"])?$_POST["selectedMaker"]:null;
    $code = isset($_POST["searchCartridge"])?$_POST["searchCartridge"]:null;
   // echo json_encode ($status);die();
    if(!empty($code)){
        $searchCd = " and commodity.name like '%$code%' ";
    }
    if(!empty($maker)){
        $searchMaker = " and maker.no = ".$maker;
    }
   
    if (!empty($searchCd) || !empty($searchMaker)) {
        if (!empty($searchCd))
        {
            $searchQuery = $searchQuery.$searchCd ;
        }
        if (!empty($searchMaker))
        {
            $searchQuery = $searchQuery.$searchMaker;
        }
    }
$stmt;
if($inventory==null)
{
if($invalid==null)
{
    $stmt = $conn->prepare("select maker.name as mName,
                    commodity.name as cName,
                    inventory_mark.display as display 
                    from maker 
                    LEFT JOIN commodity on maker.no=commodity.maker
                    inner JOIN inventory on inventory.commodity=commodity.no
                    LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                    where commodity.name IS NOT NULL AND maker.invalid=0 ".$searchQuery." order by commodity.cd ASC");
}
else
{
    $stmt = $conn->prepare("select maker.name as mName,
                    commodity.name as cName,
                    inventory_mark.display as display 
                    from maker 
                    LEFT JOIN commodity on maker.no=commodity.maker
                    inner JOIN inventory on inventory.commodity=commodity.no
                    LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                    where commodity.name IS NOT NULL AND maker.invalid=0 ".$searchQuery. " AND inventory.inventory_mark<>4 order by commodity.cd ASC");
}
}
else
{   
if($invalid==null)
{
    $stmt = $conn->prepare("select maker.name as mName,
                    commodity.name as cName,
                    inventory_mark.display as display 
                    from maker 
                    LEFT JOIN commodity on maker.no=commodity.maker
                    inner JOIN inventory on inventory.commodity=commodity.no
                    LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                    where commodity.name IS NOT NULL AND maker.invalid=0 ".$searchQuery." order by commodity.cd ASC");
}
else
{
    $stmt = $conn->prepare("select maker.name as mName,
                    commodity.name as cName,
                    inventory_mark.display as display  
                    from maker 
                    LEFT JOIN commodity on maker.no=commodity.maker
                    inner JOIN inventory on inventory.commodity=commodity.no
                    LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                    where commodity.name IS NOT NULL AND maker.invalid=0 ".$searchQuery." AND inventory.inventory_mark<>4 order by commodity.cd ASC");
}
}
// $stmt = $conn->prepare("select maker.name as mName,
// commodity.name as cName,
// inventory_mark.display as display 
// from maker 
//                         LEFT JOIN commodity on maker.no=commodity.maker
//                         LEFT JOIN inventory on inventory.commodity=commodity.no
//                         LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
//                         where commodity.name IS NOT NULL order by commodity.no ASC");
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
$id = $_POST["UserId"];
$officeStmt = $conn->prepare("select office,customer from users where no=?");
$officeStmt->bind_param('i',$id);
$officeResult = execute($officeStmt, $conn);
if ($officeResult == TRUE) {
    $officeResult = $officeStmt->get_result();
    $officeResultSet = $officeResult;
}
$office;
$customer;
$tel;
$name="";
$fax="";
if ($officeResultSet->num_rows > 0) 
{
    mysqli_data_seek($officeResultSet, 0);
    while ($row = $officeResultSet->fetch_assoc()) 
    {
        $office = $row["office"];
        $customer = $row["customer"];
    }

    if (!empty($office)) 
    {
        $Stmt = $conn->prepare("select tel,title as NAME,fax from office where no=".$office);
        $Result = execute($Stmt, $conn);
        if ($Result == TRUE) 
        {
            $Result = $Stmt->get_result();
            $ResultSet = $Result;
        }
    }
    else if(!empty($customer))
    {
        $Stmt = $conn->prepare("select tel,name as NAME,fax from customer where no=".$customer);
        $Result = execute($Stmt, $conn);
        if ($Result == TRUE) 
        {
            $Result = $Stmt->get_result();
            $ResultSet = $Result;
        }
    }

    while($row = $ResultSet->fetch_assoc())
    {
        $tel = $row["tel"];
        $name = $row["NAME"];
        $fax = empty($row["fax"])?" -":$row["fax"];
    }
}
class PDF extends PDF_Japanese
{
    public $tel;
    public $name;
    var $count;
    public $fax;
    public function __construct($custom,$name,$fax) {
        parent::__construct();
        $this->tel = $custom;
        $this->name = $name;
        $this->fax=$fax;
        $this->count=0;
    }
    function Header()
    {

        $this->SetFont('SJIS','B',14);
       
        //set 
        $this->SetTextColor(0,0,0);

        // 表のタイトル
        if($this->count==0)
        {
            $this->count=1;
            $top = "在庫表";
            $this->SetFont('SJIS','B',20);
            $this->Cell(0, 10,mb_convert_encoding($top, 'SJIS'),0,0,'C', false);
            $this->Ln();
            $this->SetFont('SJIS','',12);
            $this->Cell(0, 10,mb_convert_encoding(trim($this->name), 'SJIS'),0,0,'R', false);
            $this->Ln();
            $this->Cell(0, 10,mb_convert_encoding("TEL :".$this->tel." FAX :".$this->fax, 'SJIS'),0,0,'R', false);
            $this->SetFont('SJIS','',12);
            //line break
            $this->Ln();
            $title = "在庫状況表示：　○/在庫あり　△/在庫少量　リターン/リターン再生対応";
            $this->Cell(0, 10,mb_convert_encoding($title, 'SJIS'),0,0,'C', false);
            
            //line break
            $this->Ln();
        }
        $this->SetFont('SJIS','B',14);
        //テーブルのヘッダー
        // $headers = array("No", "品名", "数", "担当者");
        $headers = array("メーカー","完成品在庫","在庫状況");
        //テーブルのヘッダー
        // $this->Cell(15, 8, mb_convert_encoding("No", 'SJIS'), 1, 0, 'C');
        // $this->Cell(80, 8, mb_convert_encoding("品名", 'SJIS'), 1, 0, 'C');
        // $this->Cell(15, 8, mb_convert_encoding("数", 'SJIS'), 1, 0, 'C');
        // $this->Cell(75, 8, mb_convert_encoding("担当者", 'SJIS'), 1, 0, 'C');
        $this->Cell(33, 8, mb_convert_encoding($headers[0], 'SJIS'), 1, 0, 'C');
        $this->Cell(130, 8, mb_convert_encoding($headers[1], 'SJIS'), 1, 0, 'C');
        $this->Cell(32, 8, mb_convert_encoding($headers[2], 'SJIS'), 1, 0, 'C');
        
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

$pdf = new PDF($tel,$name,$fax);
$pdf->AddSJISFont();
$pdf->AddPage();

$pdf->SetFont('SJIS','',14);

//insert image
// $pdf->Image('printer.jpg', 30, 0, 0, 50);

//line break
// $pdf->Ln();

//Fill data
foreach ($result as $row) {
    $i=0;
    foreach($row as $column)
    {
        $w;
        if($i==0)
        {
            $pdf->Cell(33, 8, mb_convert_encoding($column, 'SJIS'), 1, 0, 'L');
        }
        else if($i==1)
        {
            $pdf->Cell(130, 8, mb_convert_encoding($column, 'SJIS'), 1, 0, 'L');
        }
        else
        {
            $pdf->Cell(32, 8,mb_convert_encoding($column, 'SJIS'), 1, 0, 'C');
        }
        $i++;
    }
    $pdf->Ln();
}

$pdf->Output();
// readfile("filename.pdf");
$pdf->Close();

?>