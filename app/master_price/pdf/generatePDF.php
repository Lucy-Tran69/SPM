<?php
include_once('database/db.inc');
require_once('libs/japanese.php');
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
$invalid = isset($_POST["searchInvalid"])?$_POST["searchInvalid"]:null;
$err_msg = "";
$conn  = getConnection();
$display=FALSE;
$user = $_SESSION["loginUserId"];
$userAccount = $_SESSION["loginAccount"];
$cust="";
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}
    $searchQuery = "";
    $searchCd = "";
    $searchMaker = "";
    $searchCustomer = "";
    $searchSupport = "";
    $printName = false;
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $maker = isset($_POST["selectedMaker"])?$_POST["selectedMaker"]:null;
        $customer = isset($_POST["selectedCustomer"])?$_POST["selectedCustomer"]:null;
        $cart = isset($_POST["searchCartridge"])?$_POST["searchCartridge"]:null;
        $support = isset($_POST["searchSupport"])?$_POST["searchSupport"]:null;
        $printName = isset($_POST["username"])?$_POST["username"]:null;
       // echo json_encode ($status);die();
        if(!empty($cart)){
            $zenkaku = mb_convert_kana($cart, "CKV");
            $hankaku = mb_convert_kana($cart,"hkV");
            $searchCd = " and (commodity.cd like '%$zenkaku%' or commodity.cd like '%$hankaku%') ";
        }
        if(!empty($maker)){
            $searchMaker = " and maker.no = ".$maker;
        }
        if(!empty($customer)){
            $cust = $customer;
            $searchCustomer = " and customer = ".$customer;
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
$result="";
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
                        WHERE a.seq = (select max(seq) from selling_price b WHERE (b.approvalday IS NOT NULL OR b.seq=0) ".$searchCustomer." )
                        AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 AND inventory_mark.hidden=0".$searchQuery."
                        GROUP BY a.customer,a.commodity,a.num
                        ORDER BY maker.name,print_type.no,code ASC");
    $result = execute($stmt,$conn);
}
else if($invalid!==null && !empty($searchCustomer))
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
                        WHERE a.seq = (select max(seq) from selling_price b WHERE (b.approvalday IS NOT NULL OR b.seq=0) ".$searchCustomer." )
                        AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 ".$searchQuery."
                        GROUP BY a.customer,a.commodity,a.num
                        ORDER BY maker.name,print_type.no,code ASC");
    $result = execute($stmt,$conn);
}


if($result==TRUE)
{
$result = $stmt->store_result();
$resultSet = $result;
} 

$oldstmt = $conn->prepare("SELECT  distinct a.seq as SEQ,
                    date(a.approvalday) as APDAY,
                    date_add(date(a.approvalday),interval 1 day) as APDAY1
                    from selling_price a
                    INNER JOIN commodity on a.commodity=commodity.no
                    INNER JOIN inventory on inventory.commodity=commodity.no
                    INNER JOIN maker on commodity.maker=maker.no
                    INNER JOIN inventory_mark on inventory.inventory_mark=inventory_mark.no
                    INNER JOIN print_type on print_type.no=commodity.print_type
                    INNER JOIN customer on a.customer=customer.no		
                    WHERE a.seq = (select max(seq) from selling_price b WHERE (b.approvalday IS NOT NULL OR b.seq=0) ".$searchCustomer." )
                    AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 ".$searchQuery."");

    $oldresult = execute($oldstmt,$conn);
    if($oldresult==TRUE)
    {
        $oldresult = $oldstmt->store_result();
        $oldresultSet = $oldresult;
    }


$name="";
$no="";
$tel="";
$cd="";
$address="";
$zip="";
$appDay="";
$appDay1="";
$seq="";
$fax="";
$customerStmt = $conn->prepare("select name,no,tel,cd,address,zip from customer where no=".$cust);
$customerResult = execute($customerStmt, $conn);
if ($customerResult == TRUE) {
    $customerResult = $customerStmt->store_result();
    $customerResultSet = $customerResult;
}


while($row = fetchAssocStatement($customerStmt))
{
    $name=$row["name"];
    $no=$row["no"];
    $tel=$row["tel"];
    $cd=$row["cd"];
    $address=$row["address"];
    $zip=$row["zip"];
}

while($row = fetchAssocStatement($oldstmt))
{
    $seq=$row["SEQ"];
    $appDay=$row["APDAY"];
    $appDay1=$row["APDAY1"];
}

$officeStmt = $conn->prepare("select office.name,
                                    office.no,
                                    office.tel,
                                    office.address,
                                    office.zip,
                                    office.fax from office 
                                    inner join users on office.no=users.office
                                    where users.no=".$user);
$officeResult = execute($officeStmt, $conn);
if ($officeResult == TRUE) {
    $officeResult = $officeStmt->store_result();
    $officeResultSet = $officeResult;
}

$oname="";
while($row = fetchAssocStatement($officeStmt))
{
    $oname=$row["name"];
    $tel=$row["tel"];
    $address=$row["address"];
    $zip=$row["zip"];
    $fax=$row["fax"];
}
class PDF extends PDF_Japanese
{
    var $tel;
    var $name;
    var $no;
    var $cd;
    var $widths;
    var $aligns;
    var $count;
    var $address;
    var $zip;
    var $seq;
    var $apday;
    var $apday1;
    var $fax;
    var $oname;
    var $printName;
    var $user;
    public function __construct($o,$n,$num,$telp,$code,$address,$zip,$seq,$ap,$ap1,$fax,$oname,$pname,$user) {
        parent::__construct($o);
        $this->name=$n;
        $this->no=$num;
        $this->tel = $telp;
        $this->cd=$code;
        $this->count=0;
        $this->address=$address;
        $this->zip=$zip;
        $this->seq=$seq;
        $this->apday=$ap;
        $this->apday1=$ap1;
        $this->fax=$fax;
        $this->oname=$oname;
        $this->printName=$pname;
        $this->user = $user;
    }
    function Header()
    {
       
        //set 
        $this->SetTextColor(0,0,0);

        // 表のタイトル
        if($this->count==0)
        {
            $this->SetFont('SJIS','B',18);
            $this->count=1;
            $top = "価格表";
            $this->Cell(0, 10,mb_convert_encoding($top, 'SJIS'),0,0,'C', false);
            $this->Ln();  
              
            $this->SetFont('SJIS','BU',13);
            $this->Cell(0, 6,mb_convert_encoding(trim($this->name)."　御中", 'SJIS'),0,0,'L', false);
            $this->SetFont('SJIS','',10 );
            $this->Cell(0, 6,mb_convert_encoding("発行日: ".trim($this->apday), 'SJIS'),0,0,'R', false);
            $this->Ln();
            
            $this->Cell(0, 6,mb_convert_encoding("適応日             : ".trim($this->apday1), 'SJIS'),0,0,'L', false);
            $this->SetFont('SJIS','',10);
            $this->SetX(131);
            $this->Cell(0, 6,mb_convert_encoding(trim($this->oname), 'SJIS'));
            $this->Ln();

            $this->Cell(0, 6,mb_convert_encoding("見積有効期限：次回発行までとなります", 'SJIS'),0,0,'L', false);
            $this->SetX(131);
            $this->Cell(0, 6,mb_convert_encoding("〒".trim($this->zip), 'SJIS'));
            $this->Ln();

            $this->SetX(131);
            $this->MultiCell(0,6,mb_convert_encoding(trim($this->address), 'SJIS'));

            $this->SetX(131);
            $this->Cell(0, 6,mb_convert_encoding("TEL : ".trim($this->tel)." FAX : ".trim($this->fax), 'SJIS'));
            $this->Ln();

            if($this->printName=='true')
            {
                $this->SetX(131);
                $this->Cell(0, 6,mb_convert_encoding("担当者： ".trim($this->user), 'SJIS'));
                $this->Ln();
            }

           
            // $this->SetX(123);
            // $this->Cell(0, 6,mb_convert_encoding("東京営業所", 'SJIS'),0,0);
            // $this->Ln();
            // $this->SetX(123);
            // $this->Cell(0, 6,mb_convert_encoding("〒136-0072", 'SJIS'),0,0);
            // $this->Ln();

            // $this->SetX(123);
            // $this->MultiCell(0, 6,mb_convert_encoding("東京都江東区大島2丁目31番8号　渡辺ビル2F", 'SJIS'),0);
            // $this->SetX(123);
            // $this->Cell(0,6,mb_convert_encoding("TEL : 03-3345-5651 FAX : 03-3345-5661", 'SJIS'),0,0);
            // $this->Ln();

            // $this->SetFont('SJIS','',10);
            // $this->Line(80,30,116,30);//承認　担当
            // $this->Text(85,28.5,mb_convert_encoding("承認",'SJIS'));
            // $this->Text(103,28.5,mb_convert_encoding("担当",'SJIS'));
            // $this->Rect(80,25,18,25);
            // $this->Rect(98,25,18,25);
            // $this->SetFont('SJIS','',40);
            // $this->SetTextColor(255,0,0);
            // $this->SetAlpha(0.5);
            // $this->SetDrawColor(255,0,0);
            // $this->SetLineWidth(0.75);
            // $this->Text(168,40,mb_convert_encoding("印",'SJIS'));
            // $this->Rect(165,25,20,20);
            // $this->SetTextColor(0,0,0);
            // $this->SetAlpha(1);
            // $this->SetDrawColor(0,0,0);
            // $this->SetLineWidth(0.2);
        }
        //line break
        $this->Ln();
        $this->SetFont('SJIS','B',8);
        //テーブルのヘッダー
        // $headers = array("No", "品名", "数", "担当者");
        $headers = array("メーカー名","弊社型番","ｸﾞﾘｰﾝ","参考価格","再生価格","再生回数","対応プリンター","備考");
        //テーブルのヘッダー
        // $this->Cell(15, 8, mb_convert_encoding("No", 'SJIS'), 1, 0, 'C');
        // $this->Cell(80, 8, mb_convert_encoding("品名", 'SJIS'), 1, 0, 'C');
        // $this->Cell(15, 8, mb_convert_encoding("数", 'SJIS'), 1, 0, 'C');
        // $this->Cell(75, 8, mb_convert_encoding("担当者", 'SJIS'), 1, 0, 'C');
        $this->SetWidths(array(22,22,11,15,15,14,48,47));
        $this->aligns=array('C','C','C','C','C','C','C','C');
        $this->Row($headers);
        // foreach($headers as $h)
        // {
        //     $this->Cell(24, 8, mb_convert_encoding($h, 'SJIS'), 1, 0, 'C');
            
        // }
        
        //line break
        // $this->Ln();
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

protected $extgstates = array();

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    function SetAlpha($alpha, $bm='Normal')
    {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms)
    {
        $n = count($this->extgstates)+1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc()
    {
        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
        parent::_enddoc();
    }

    function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++)
        {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_put(sprintf('/ca %.3F', $parms['ca']));
            $this->_put(sprintf('/CA %.3F', $parms['CA']));
            $this->_put('/BM '.$parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach($this->extgstates as $k=>$extgstate)
            $this->_put('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_put('>>');
    }

    function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }

}
$fmt = numfmt_create( 'ja_JP', NumberFormatter::CURRENCY );
$pdf = new PDF('P',$name,$no,$tel,$cd,$address,$zip,$seq,$appDay,$appDay1,$fax,$oname,$printName,$userAccount);
$pdf->AddSJISFont();
$pdf->AddPage();

//line break
// $pdf->Ln();
//insert image
// $pdf->Image('printer.jpg', 30, 0, 0, 50);
$pdf->SetFont('SJIS','',8);
//line break
// $pdf->Ln();
$pdf->SetWidths(array(22,22,11,15,15,14,48,47));
//Fill data
if($result==true)
{
    while($row = fetchAssocStatement($stmt)) {
        // foreach($row as $column)
        // {
        //     $pdf->MultiCell(22, 15, mb_convert_encoding($column, 'SJIS'), 1, 'C');
        // }
        // $pdf->Ln();
        $pdf->aligns=array('L','L','C','R','R','C','L','L');
        $pdf->Row(array($row["maker"],$row["code"],$row["green"],numfmt_format_currency($fmt,$row["original"], "JPY"),numfmt_format_currency($fmt,$row["sp"], "JPY"),$row["qty"],$row["support"],$row["note"]));
    }
}

$pdf->Output('I','価格表.pdf',true);
// readfile("filename.pdf");
$pdf->Close();
?>