<!-- Include this function at the start of every page. $target_url is the url that is being accessed. -->
<!-- Returns true if the user can access the page, if user cannot access page it goes back to previous page and shows alert. -->
<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
include_once "../../include/database/db.inc";

function check_view_permission($target_url)
{
    $conn  = getConnection();
    if($conn->connect_error)
    {
        die("Failed to connect to database. ".$conn->connect_error);
    }   
    $menuStmt = $conn->prepare("select no from menu where url like ?");
    $menuStmt->bind_param('s',$target_url);
    $menuResult = execute($menuStmt,$conn);
    if($menuResult==TRUE)
    {
        $menuSet = $menuStmt->get_result();
    }

    if($menuSet->num_rows>0)
    {
        while($row=$menuSet->fetch_assoc())
        {
            $menu_no=$row["no"];
        }
    }

    $roleStmt = $conn->prepare("select role,menu from role_menu order by role");
    execute($roleStmt,$conn);
    $roles = $roleStmt->get_result();
    $map = array();
    if($roles->num_rows>0)
    {
        while($row=$roles->fetch_assoc())
        {
            $map[$row["role"]][$row["menu"]]=1;
        }
    }  

    if(isset($_SESSION["loginUserId"]) && isset($_SESSION["loginRole"]) && $menu_no!=null)
    {
        if($map[$_SESSION["loginRole"]][$menu_no]==1)
            return true;
        else
        {
            // die("No permission");
            echo "<script>alert(\"No Permission\");history.go(-1);</script>";
        }
    }

    freeConnection($conn);
}
?>