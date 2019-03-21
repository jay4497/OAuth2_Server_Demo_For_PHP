<?php
function request($url, $data)
{
    $res = curl_init($url);
    curl_setopt($res, CURLOPT_POST, 1);
    curl_setopt($res, CURLOPT_POSTFIELDS, $data);
    curl_setopt($res, CURLOPT_USERPWD, 'testclient:testpass');
    curl_setopt($res, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($res);
    return $result;
}

$action = @$_GET['a'];
// 根据实际情况改成带域名的地址
$url = 'index.php?a=token';
$data = '';
if($action == 'refresh'){
    $data = 'grant_type=refresh_token&refresh_token='. $_GET['token'];
}else{
    $data = 'grant_type=authorization_code&code='. $_GET['code'];
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>客户端 Client</title>
    <link href="https://cdn.bootcss.com/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container" style="margin-top: 45px;">
    <?php
    $result = request($url, $data);
    echo '<code>'. $result. '</code>';
    echo '<br>';
    $result = json_decode($result);
    $token = $result->message->access_token;
    $refresh_token = $result->message->refresh_token;
    ?>
    <p>
        <a class="btn btn-primary" href="index.php?a=verify&refresh=<?php echo $refresh_token;?>&access_token=<?php echo $token;?>">调用接口测试</a>
    </p>
    <p>
        <a href="index.php" class="btn btn-default">返 回</a>
    </p>
</div>
</body>
</html>
