<?php
namespace Candy;

use OAuth2\Storage\Pdo;
use OAuth2\Server;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\RefreshToken;
use OAuth2\Request;
use OAuth2\Response;

class OauthServer
{
    protected $server;

    private $message = [
        'status' => 0,
        'message' => 'success'
    ];

    public function __construct($db_dsn, $db_user, $db_pass)
    {
        $storage = new Pdo(array('dsn' => $db_dsn, 'username' => $db_user, 'password' => $db_pass));
        $server_config = array(
            'access_lifetime'                   => 86400,
            'refresh_token_lifetime'            => 604800
        );
        $refresh_config = array(
            'always_issue_new_refresh_token'    => true
        );
        $this->server = new Server($storage, $server_config);
        $this->server->addGrantType(new ClientCredentials($storage));
        $this->server->addGrantType(new AuthorizationCode($storage));
        $this->server->addGrantType(new RefreshToken($storage, $refresh_config));
    }

    public function auth_code()
    {
        $request = Request::createFromGlobals();
        $response = new Response();
        $user_id = @$_GET['uid']? : null;

        // validate the authorize request
        if (!$this->server->validateAuthorizeRequest($request, $response)) {
            return false;
        }

        // print the authorization code if the user has authorized your client
        $is_authorized = true;
        $this->server->handleAuthorizeRequest($request, $response, $is_authorized, $user_id);
        return $response->getHttpHeader('Location');
    }

    public function send_token()
    {
        $result = $this->server->handleTokenRequest(Request::createFromGlobals())->getResponseBody();
        return $this->trans_result($result);
    }

    public function verify()
    {
        if (!$this->server->verifyResourceRequest(Request::createFromGlobals())) {
            $result = $this->server->getResponse()->getResponseBody();
            return $this->trans_result($result);
        }
        return true;
    }

    protected function trans_result($message)
    {
        $result = json_decode($message);
        if(empty($result)){
            $this->message['status'] = 1;
            $this->message['message'] = 'Unknown error';
        }else {
            if (property_exists($result, 'error')) {
                $this->message['status'] = 1;
                $this->message['message'] = $result->error_description;
            }
            if (property_exists($result, 'success')) {
                $this->message['message'] = $result->message;
            }
            if (property_exists($result, 'access_token')) {
                $this->message['message'] = $result;
            }
        }
        return json_encode($this->message);
    }
}
