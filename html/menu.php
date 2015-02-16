<?php

define("APPID", "wx03cccee44426ee51");
define("APPSECRET", "80f8942c040ff31e6f631038b85e7763");
define("REDIRECT_URL", "http://121.41.104.220/e-young/");
define("SCOPE", "snsapi_base");
define("JSONMENU", '{
        "button":[
         {
            "name":"E-young",
            "type":"view",
            "url":"http://121.41.104.220/e-young/" 
         },
         {
             "name":"我",
             "sub_button":[
              {
                 "type":"click",
                 "name":"查看数据",
                 "key":"viewdb"
              },
              {
                 "type":"view",
                 "name":"授权测试",
                 "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URL&response_type=code&scope=SCOPE&state=1#wechat_redirect"
              },
              {
                 "type":"click",
                 "name":"注册用户",
                 "key":"registerMember"
              }]
         }]
      }');

$weixin = new class_weixin(APPID, APPSECRET);

class class_weixin
{
    //构造函数，获取Access Token并注册menu
    public function __construct($appid = NULL, $appsecret = NULL)
    {
        //获取Access Token
        if($appid && $appsecret){
            $this->appid = $appid;
            $this->appsecret = $appsecret;
        }
        $url_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
        $res = $this->http_request($url_token);
        var_dump($res."<br>");
        $result = json_decode($res, true);
        $this->access_token = $result["access_token"];
        
        //注册menu
        if($this->access_token)
        {
          $this->menu_request($this->access_token);
        }
    }

    private function menu_request($access_token)
    {
      $url_menu = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
      $output = $this->http_request($url_menu,JSONMENU);
      var_dump($output."<br>");
    }

    //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    private function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}

?>