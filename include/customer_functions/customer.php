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
        $name = isset($_POST["name"]) ? $_POST["name"] : '';
        $status = isset($_POST["status"]) ? $_POST["status"] : 0;
        $searchQuery = "";
        if(!empty($cd)){
            $searchCd = " customer.cd like '%$cd%' ";
        }
        if(!empty($name)){
            $searchName = " customer.name like '%$name%' ";
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

    $sel = mysqli_query($conn, "select count(*) as allcount from customer ".$searchQuery);

    $records = mysqli_fetch_assoc($sel);
    $totalRecordwithFilter = $records['allcount'];


    ## Fetch records
    $empQuery = "select customer.no, customer.cd, customer.name, customer.invalid, A.NumberCustomerRole5, B.NumberCustomerRole6 from customer
                                LEFT JOIN 
                                (SELECT customer, COUNT(users.no) AS NumberCustomerRole5 FROM users WHERE role=5 GROUP BY users.customer) A
                                on customer.no = A.customer
                                LEFT JOIN 
                                (SELECT customer, COUNT(users.no) AS NumberCustomerRole6 FROM users WHERE role=6 GROUP BY users.customer) B
                                on  customer.no = B.customer
                                ".$searchQuery." ORDER BY customer.name ASC LIMIT ".$row.",".$rowperpage;
    
    $empRecords = mysqli_query($conn, $empQuery);
    $data = array();

    while ($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array(
                "no" => $row['no'],
                "cd" => $row['cd'],
                "name" => $row['name'],
                "NumberCustomerRole5" => ($row['NumberCustomerRole5'] == '') ? 0 : $row['NumberCustomerRole5'],
                "NumberCustomerRole6" => ($row['NumberCustomerRole6'] == '') ? 0 : $row['NumberCustomerRole6'],
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
?>