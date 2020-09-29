<!-- <?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
    try
    {
        if(isset($_SESSION["loginAccount"]))
        {
            $_SESSION = array();
        }
        else
        {
            echo "<script>
                alert('Session not found');
                window.location.href='../../app/';
                </script>";
        }
    }
    catch(Exception $ex)
    {

    }
    finally
    {
        session_destroy();
        header("Location: ../../app/");
    }
?> -->