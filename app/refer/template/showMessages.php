<?php
    include_once "common/session.php";
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
                $msg->success($title.'トピックスの編集に成功しました。');
            }
            else{
                $msg->success($title.'トピックスの追加に成功しました。');
            }
        } else {
           if(!empty($id)){
            $msg->error($title.'トピックスの編集に失敗しました。');
        }
            else{
                $msg->error($title.'トピックスの追加に失敗しました。');
            }
        }
        
        $msg->display();
    }