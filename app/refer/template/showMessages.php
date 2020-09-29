<?php
    include_once "FlashMessages.php";
    $msg = new \Plasticbrain\FlashMessages\FlashMessages();

    $urlCurrent = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $query = parse_url($urlCurrent, PHP_URL_QUERY);
    parse_str($query, $params);

    if(!empty($params)) {
        $status = $params['status'];
        $title = $params['title'];
        $id = $params['id'];
        if($status === 'success') {
            if(!empty($id)){
                $msg->success('Edited '.$title.' Topic successfully');
            }
            else{
                $msg->success('Added '.$title.' Topic successfully');
            }
        } else {
           if(!empty($id)){
            $msg->error('Edit '.$title.' Topic failed');
        }
            else{
                $msg->error('Add '.$title.' Topic failed');
            }
        }
        
        $msg->display();
    }