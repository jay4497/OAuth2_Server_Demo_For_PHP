<?php
header('Content-Type:text/html;charset=utf-8');

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
$url = 'http://localhost/OauthServer/index.php?a=token';
$data = '';
if($action == 'refresh'){
    $data = 'grant_type=refresh_token&refresh_token='. $_GET['token'];
}else{
    $data = 'grant_type=authorization_code&code='. $_GET['code'];
}
$result = request($url, $data);
echo $result;
echo '<br>';
$result = json_decode($result);
$token = $result->message->access_token;
$refresh_token = $result->message->refresh_token;
?>
<p>
    <a href="index.php?a=verify&refresh=<?php echo $refresh_token;?>&access_token=<?php echo $token;?>">调用接口测试</a>
</p>
<p>
    <a href="index.php">返 回</a>
</p>
