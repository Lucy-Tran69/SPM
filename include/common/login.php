<?php
// if (session_status() == PHP_SESSION_NONE) 
// {
//     session_start();
// }
header("content-type: text/html; charset=UTF-8"); 
include "database/db.inc";
include_once "const/system.inc";
include_once "common/session.php"; 
$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}

$stmt = $conn->prepare("select users.no as uID,
                        users.account,
                        users.password,
                        users.effective_st_day,
                        users.effective_en_day,
                        users.name,
                        users.role,
                        COALESCE(customer.invalid,0) as cInvalid 
                        FROM users left join customer on users.customer=customer.no 
                        WHERE account=? AND users.invalid=0");

try
{
    if($_SERVER["REQUEST_METHOD"]=="POST")
    {
    if(isset($_POST["username"]) && isset($_POST["password"]))
    {
        if(!empty($_POST["username"]) && !empty($_POST["password"]))
        {
           if($stmt->bind_param('s',$_POST["username"])==TRUE)
           {
                $result = execute($stmt,$conn);
           }
           else
           {
                throw new Exception($stmt->error);
           }
           
           if($result==TRUE)
           {
            //    $stmt->bind_result($result);
               $stmt->store_result();
           }
           $passwd = "";
           $st_date;
           $en_date;
           $curr_date = date('Y-m-d');
           if($stmt->num_rows()>0)
           {
               while($row = fetchAssocStatement($stmt))
               {
                    if($row["account"]==$_POST["username"])
                    {
                        $passwd = $row["password"];
                        $st_date = date('Y-m-d',strtotime($row['effective_st_day']));
                        $en_date = date('Y-m-d',strtotime($row['effective_en_day']));
                        if( password_verify($_POST["password"],$passwd)==TRUE && $row["cInvalid"]==0)
                        {
                            if($st_date<=$curr_date && ($en_date>$curr_date || $en_date == '1970-01-01'))
                            {
                                $_SESSION["loginAccount"] = $row["name"];
                                $_SESSION["loginUserId"] = $row["uID"];
                                $_SESSION["loginRole"]=$row["role"];
                                $_SESSION[AUTH]=$row["uID"];
                                setMenu($row['uID'],$conn);
                                // header("Location: ../app/login/home.html");
                            }
                            else
                            {
                                //echo "Account is disabled";
                                $err_msg = "アカウントが無効になっています";
                            }
                        }
                        else
                        {
                            //$err_msg = "Username or password is incorrect";
                            $err_msg = "ユーザー名かパスワードが間違っています";
                        }
                    }
               }
           }
           else
           {
                //$err_msg = "Account does not exist or is disabled";
                $err_msg = "アカウントが存在しないか無効になっています";
           }
        }
        else
        {
            //$err_msg = "Username and password cannot be blank";
            $err_msg = "ユーザー名とパスワードを空白にすることはできません";
        }
    }
    else
    {
        //$err_msg = "Something went wrong. Please try again";
        $err_msg = "問題が発生しました。もう一度お試しください";
    }

    echo json_encode(array(
        "message"=>$err_msg
    ));


    }
}
catch(ErrorException $ex)
{
    echo $ex->getMessage();
}
finally
{
    $stmt->free_result();
    freeConnection($conn);
}

function setMenu($userid, $conn)
{
    $role_list = array();
    $keys = array();
    $values = array();
    $roleStmt = $conn->prepare("SELECT menu.name,menu.url from menu INNER JOIN role_menu ON menu.no=role_menu.menu
                                                                    INNER JOIN role ON role.no=role_menu.role
                                                                    INNER JOIN users ON users.role=role.no
                                                                    WHERE users.no=? ORDER BY menu.sort_order");
    if (isset($_POST["username"]) && !empty($_POST["username"])) 
    {
        if ($roleStmt->bind_param('i', $userid) == TRUE) 
        {
            $result = execute($roleStmt, $conn);
        } else 
        {
            throw new Exception($roleStmt->error);
        }

        if ($result == TRUE) 
        {
            $roleStmt->store_result();
        }

        // if ($result->num_rows > 0) 
        // {
            while ($row = fetchAssocStatement($roleStmt)) 
            {
                $keys[] = $row['name'];
                $values[] = $row['url'];
            }

            $role_list = array_combine($keys, $values);
        // }

        $_SESSION['roles'] = $role_list;
        $roleStmt->free_result();
    }
}
?>

