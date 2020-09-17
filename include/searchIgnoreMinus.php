<?php
//str1 - needle , str2 - haystack
function isMatch($str1,$str2)
{
    try
    {
        $chars_to_replace = array("ー","‐","－","―","￣");
        $str1 = str_replace($chars_to_replace,"",$str1);
        $str2 = str_replace($chars_to_replace,"",$str2); 

        if(stripos(mb_convert_kana($str2,"ask"),mb_convert_kana($str1,"ask"))===FALSE)
           {
                return 0;
           }
        else
           {
                return 1;
           }
                   
    }
    catch(Exception $e)
    {
        echo $e;
    }
}
?>