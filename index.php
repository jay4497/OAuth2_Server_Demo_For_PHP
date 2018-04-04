<?php
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
require 'vendor/autoload.php';

$server = new Candy\OauthServer('mysql:dbname=your_database_name;host=your_database_host', 'your_database_user', 'your_database_password');

$action = @$_GET['a'];
if($action == 'token') {
    echo $server->send_token();
    exit;
}
if($action == 'verify'){
    $result = $server->verify();
    if($result === true){
        echo '成功 OK ！';
    }else{
        echo $result;
    }
    echo '<p><a href="client.php?a=refresh&token='. @$_GET['refresh'].'">更新 token</a></p>';
    echo '<p><a href="index.php">返 回</a></p> ';
    exit;
}
if($action == 'code'){
    $result = $server->auth_code();
    if($result === false || empty($result)){
        exit('fail');
    }
    header('Location: '. $result);
}
?>
<p>
    <a href="index.php?a=code&client_id=testclient&response_type=code&state=xyz&uid=321">授 权</a>
</p>