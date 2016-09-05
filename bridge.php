<?php namespace Exsul\curl;

class bridge
{
  public $url;
  public $__curl;
  public $useragent;
  public $return_header;

  public $result;

  public function __construct($url = null)
  {
    $this->url = $url;
    $this->__curl = curl_init();

    $this->InitDefaultParams();
  }

  private function InitDefaultParams()
  {
    curl_setopt($this->__curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->__curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($this->__curl, CURLOPT_VERBOSE, false);
    curl_setopt($this->__curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($this->__curl, CURLOPT_HEADER, true);
  }

  public function SendHeaders($headers)
  {
    curl_setopt($this->__curl, CURLOPT_HTTPHEADER, $headers);
  }

  public function UseCookieFile($filename)
  {
    curl_setopt($this->__curl, CURLOPT_COOKIEFILE, $filename);
    curl_setopt($this->__curl, CURLOPT_COOKIEJAR, $filename);
  }

  public function UseProxy($ip, $port = null, $login = null, $password = null)
  {
    if (is_array($ip))
      return $this->UseProxy($ip['ip'], $ip['port'], $ip['login'], $ip['password']);

    $string = "";

    if (!is_null($login))
      $string .= "$login:$password@";

    $string .= "$ip:$port";

    curl_setopt($this->__curl, CURLOPT_PROXY, $string);
  }

  public function SendPost($postdata = null)
  {
    if (is_array($postdata))
      $postdata = http_build_query($postdata);

    curl_setopt($this->__curl, CURLOPT_POST, !is_null($postdata));
    curl_setopt($this->__curl, CURLOPT_POSTFIELDS, $postdata);
  }

  public function Execute($url = null)
  {
    if (!is_null($url))
      $this->url = $url;

    curl_setopt($this->__curl, CURLOPT_URL, $this->url);
    $response = curl_exec($this->__curl);

    $this->result = [];

    $header_length = curl_getinfo($this->__curl, CURLINFO_HEADER_SIZE);
    $this->result['header'] = substr($response, 0, $header_length);
    $this->result['body'] = substr($response, $header_length);
    $this->result['code'] = curl_getinfo($this->__curl, CURLINFO_HTTP_CODE);

    curl_close($this->__curl);

    return $this->result['body'];
  }

  public function GetHeader()
  {
    return $this->result['header'];
  }

  public function GetHTTPCode()
  {
    return $this->result['code'];
  }


}
