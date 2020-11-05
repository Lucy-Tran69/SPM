<?php
    include_once "common/session.php";
    include_once "database/db.inc";

    $err_msg = "";
    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    ## Read value
    $draw = isset($_POST['draw']) ? $_POST['draw'] : 1;
    $row = isset($_POST['start']) ? $_POST['start'] : 0;
    $rowperpage = isset($_POST['length']) ? $_POST['length'] : 10; // Rows display per page

    ## Search 
    $searchQuery = "";
    $searchCd = "";
    $searchName = "";
    $searchStatus = "";
    $status = 0;
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $cd = isset($_POST["cd"]) ? $_POST["cd"] : '';
        $cd = removeWhitespaceAtBeginAndEndOfString($cd);

        $name = isset($_POST["name"]) ? $_POST["name"] : '';
        $name =  removeWhitespaceAtBeginAndEndOfString($_POST["name"]);
        $name1 = mb_convert_kana($name, "KVC");
        $name2 = mb_convert_kana($name, "kVC");
        $name3 = mb_convert_kana($name1, "kVC");

        $status = isset($_POST["status"]) ? $_POST["status"] : 0;
        $types = "";
        $countsss =[];
        if(!empty($cd)){
            $searchCd = " customer.cd like ? ";
            $types .= "s";
            array_push($countsss,"%{$cd}%");
        }
        if(!empty($name)){
            $searchName = " (customer.name like ? OR customer.name like ? OR customer.name like ? OR customer.name like ? ) ";
            $types .= "ssss";
            array_push($countsss,"%{$name}%");
            array_push($countsss,"%{$name1}%");
            array_push($countsss,"%{$name2}%");
            array_push($countsss,"%{$name3}%");
        }
        if($status == 0) {
            $searchStatus = " customer.invalid = 0 ";
        }
       
        if (!empty($searchCd) || !empty($searchName) || !empty($searchStatus)) {
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
            if (!empty($searchStatus))
            {
                if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
                $searchQuery = $searchQuery.$searchStatus;
            }
            $searchQuery = "where ".$searchQuery;
        }
    }

    $sel = "select count(*) as allcount from customer ".$searchQuery;
    $stmSel = $conn->prepare($sel);
    if (!empty($cd) && !empty($name)) {
        $stmSel->bind_param($types, $countsss[0], $countsss[1], $countsss[2], $countsss[3], $countsss[4]);
    } else if (!empty($cd)) {
        $stmSel->bind_param($types, $countsss[0]);
    } else if (!empty($name)){
        $stmSel->bind_param($types, $countsss[0], $countsss[1], $countsss[2], $countsss[3]);
    }
    $stmSel->execute();
    $stmSel->store_result();
    $records = fetchAssocStatement($stmSel);
    $totalRecordwithFilter = isset($records) ? $records['allcount'] : 0;

    ## Fetch records
    $empQuery = "select customer.no, customer.cd, customer.name, customer.invalid, A.NumberCustomerRole5, B.NumberCustomerRole6, C.name AS DisplayLimit from customer
                                LEFT JOIN 
                                (SELECT customer, COUNT(users.no) AS NumberCustomerRole5 FROM users WHERE role=5 GROUP BY users.customer) A
                                on customer.no = A.customer
                                LEFT JOIN 
                                (SELECT customer, COUNT(users.no) AS NumberCustomerRole6 FROM users WHERE role=6 GROUP BY users.customer) B
                                on  customer.no = B.customer
                                LEFT JOIN displaylimit C on C.no = customer.displaylimit
                                ".$searchQuery." ORDER BY customer.name ASC, customer.cd LIMIT ".$row.",".$rowperpage;
     $stmSel = $conn->prepare($empQuery);
    if (!empty($cd) && !empty($name)) {
        $stmSel->bind_param($types, $countsss[0], $countsss[1], $countsss[2], $countsss[3], $countsss[4]);
    } else if (!empty($cd)) {
        $stmSel->bind_param($types, $countsss[0]);
    } else if (!empty($name)){
        $stmSel->bind_param($types, $countsss[0], $countsss[1], $countsss[2], $countsss[3]);
    }
    $stmSel->execute();
    $stmSel->store_result();
    $data = array();

    while ($row = fetchAssocStatement($stmSel)) {
        $data[] = array(
                "no" => $row['no'],
                "cd" => $row['cd'],
                "name" => $row['name'],
                "NumberCustomerRole5" => ($row['NumberCustomerRole5'] == '') ? 0 : $row['NumberCustomerRole5'],
                "NumberCustomerRole6" => ($row['NumberCustomerRole6'] == '') ? 0 : $row['NumberCustomerRole6'],
                "DisplayLimit" => $row['DisplayLimit'],
                "invalid" => ($row['invalid'] == 0) ? '有効' : '無効',
            );
    }
    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    );

    echo json_encode($response);
    $conn->close();

    function removeWhitespaceAtBeginAndEndOfString($data) {
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        $data = mb_ereg_replace("^[\n\r\s\t　]+", '', $data);
        $data = mb_ereg_replace("[\n\r\s\t　]+$", '', $data);
        $data = trim($data);
        $data = str_replace('_', '\\_', $data);
        $data = str_replace('%', '\\%', $data);
        return $data;
    }

    function escapeSpecialChar($str){

    }
?>