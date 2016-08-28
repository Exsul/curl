<?php namespace Exsul\curl;

require_once('bridge.php');

class request
{
  private $proxy;
  private $cookie;
  public $ch;
  private $refresh;

  public function __construct()
  {
    $this->refresh = true;
    $this->IfNeedRefresh();
  }

  private function IfNeedRefresh()
  {
    if (!$this->refresh)
      return;
    $this->refresh = true;
    $this->ch = new bridge();
  }

  public function SetProxy($ip, $port = null, $login = null, $password = null)
  {
    if (is_null($port))
    {
      $parts = explode("@", $ip);
      if (count($parts) > 1)
        list($login, $password) = explode(":", array_shift($parts));
      list($ip, $port) = explode(":", array_shift($parts));
    }

    $this->proxy =
    [
      'ip' => $ip,
      'port' => $port,
      'login' => $login,
      'password' => $password,
    ];
  }

  public function SetCookies($cookies)
  {
    $this->cookie = $cookies;
  }

  public function GetCookies()
  {
    return $this->cookie;
  }

  private function MakeCookieFile()
  {
    $name = tempnam(sys_get_temp_dir(), "EXSULCURL_COOKIE_");
    file_put_contents($name, $this->cookie);
    return $name;
  }

  private function FreeCookieFile($name)
  {
    $this->cookie = file_get_contents($name);
    unlink($name);
    return $this->cookie;
  }

  public function execute($endpoint, $post = null)
  {
    $this->IfNeedRefresh();

    if ($post)
      $this->ch->SendPost($post);

    if ($this->proxy)
      $this->ch->UseProxy($this->proxy);

    $cookie_container = $this->MakeCookieFile();

    $this->ch->UseCookieFile($cookie_container);
    //$this->ch->SendHeaders(config::HEADERS);

    $response = $this->ch->Execute(config::API_URL.$endpoint);

    $this->FreeCookieFile($cookie_container);

    return $response;
  }

  public function __invoke($endpoint, $post = null)
  {
    return $this->execute($endpoint, $post);
  }
}
