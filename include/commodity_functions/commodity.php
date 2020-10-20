<?php
include_once "database/db.inc";
$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}
    $searchQuery = "";
    $searchCd = "";
    $searchName = "";
    $searchMaker = "";
    $searchType = 0;
    $searchInvalid = 0;

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $invalid = isset($_POST["searchInvalid"])?$_POST["searchInvalid"]:null;
        $type = isset($_POST["selectedType"])?$_POST["selectedType"]:null;
        $maker = isset($_POST["selectedMaker"])?$_POST["selectedMaker"]:null;
        $code = isset($_POST["searchCode"])?$_POST["searchCode"]:null;
        $name = isset($_POST["searchName"])?$_POST["searchName"]:null;
       // echo json_encode ($status);die();
        $searchQuery = "";
        if(!empty($code)){
            $searchCd = " commodity.cd like '%$code%' ";
        }
        if(!empty($name)){
            $searchName = " commodity.name like '%$name%' ";
        }
        if($type == 0) {
            $searchType = "";
        }
        else if($type == 1) {
            $searchType = " commodity.print_type = 1 ";
        }
        else  if($type == 2) {
            $searchType = " commodity.print_type = 2 ";
        }
        if(!empty($maker)){
            $searchMaker = " commodity.maker = ".$maker;
        }
         if($invalid == 0) {
            $searchInvalid = " commodity.invalid = 0 ";
        }
        
       
        if (!empty($searchCd) || !empty($searchName) || !empty($searchMaker) || !empty($searchType) || !empty($searchInvalid)) {
            if (!empty($searchCd))
            {
                if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
                $searchQuery = $searchQuery.$searchCd ;
            }
            if (!empty($searchName))
            {
                if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
                $searchQuery = $searchQuery.$searchName;
            }
            if (!empty($searchMaker))
            {
                if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
                $searchQuery = $searchQuery.$searchMaker;
            }
            if (!empty($searchType))
            {
                if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
                $searchQuery = $searchQuery.$searchType;
            }
            if (!empty($searchInvalid))
            {
                if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
                $searchQuery = $searchQuery.$searchInvalid;
            }
            $searchQuery = "where ".$searchQuery;
        }
    }

// $typeQuery="";
// if($invalid===null)
// {
//     if($selectedType==1)
//     {
//         $typeQuery=" and commodity.print_type=1";
//     }
//     elseif($selectedType==2)
//     {
//         $typeQuery=" and commodity.print_type=2";
//     }
//     $stmt = $conn->prepare("select  commodity.no,
//                                 commodity.invalid as invalid,
//                                 commodity.cd as code,
//                                 commodity.name as name,
//                                 maker.name as maker,
//                                 commodity.memo as memo,
//                                 commodity.print_type as type
//                                 from commodity 
//                                 left join maker on commodity.maker=maker.no where commodity.invalid=0".$typeQuery);
// }
// else
// {
//     if($selectedType==1)
//     {
//         $typeQuery=" where commodity.print_type=1";
//     }
//     elseif($selectedType==2)
//     {
//         $typeQuery=" where commodity.print_type=2";
//     }
//     $stmt = $conn->prepare("select  commodity.no,
//                                 commodity.invalid as invalid,
//                                 commodity.cd as code,
//                                 commodity.name as name,
//                                 maker.name as maker,
//                                 commodity.memo as memo,
//                                 commodity.print_type as type
//                                 from commodity 
//                                 left join maker on commodity.maker=maker.no".$typeQuery);
// }
$stmt = $conn->prepare("select  commodity.no,
                        commodity.invalid as invalid,
                        commodity.cd as code,
                        commodity.name as name,
                        commodity.note as note,
                        maker.name as maker,
                        commodity.memo as memo,
                        commodity.print_type as type
                        from commodity 
                        left join maker on commodity.maker=maker.no " . $searchQuery);

$result = execute($stmt, $conn);
if ($result == TRUE) {
    $result = $stmt->get_result();
    $resultSet = $result;
}

$makerStmt = $conn->prepare("select no,name from maker where invalid=0");
$makerResult = execute($makerStmt,$conn);
if($makerResult==TRUE)
{
    $makerResult=$makerStmt->get_result();
    $makerResultSet = $makerResult;
}

?>?