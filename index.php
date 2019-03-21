<?php
date_default_timezone_set('Asia/Shanghai');
require 'vendor/autoload.php';

$server = new Candy\OauthServer('mysql:dbname=db_auth2;host=localhost', 'root', 'root');
$action = @$_GET['a'];
if($action == 'token') {
    echo $server->send_token();
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>服务端 Server</title>
    <link href="https://cdn.bootcss.com/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container" style="margin-top: 45px;">
    <p>
        <a class="btn btn-primary" href="index.php?a=code&client_id=testclient&response_type=code&state=xyz&uid=321">授 权</a>
    </p>
    <?php
    if($action == 'verify') {
        $result = $server->verify();
        if ($result === true) {
            echo '成功 OK ！';
        } else {
            echo $result;
        }
        echo '<!--<p><a href="client.php?a=refresh&token=' . @$_GET['refresh'] . '">更新 token</a></p>-->';
        echo '<p><a href="index.php">返 回</a></p> ';
        exit('</div></body></html>');
    }
    if($action == 'code') {
        $result = $server->auth_code();
        if ($result === false || empty($result)) {
            exit('fail </div></body></html>');
        }
        echo $result;
        echo '<p><a href="' . $result . '">换取 access_token</a>';
        exit('</div></body></html>');
        //header('Location: '. $result);
    }
    ?>
</div>
</body>
</html>
