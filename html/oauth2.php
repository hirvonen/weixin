<?php
header("Content-Type: text/html;charset=utf-8");

define("APPID", "wx03cccee44426ee51");
define("APPSECRET", "80f8942c040ff31e6f631038b85e7763");
define("TOKEN_GET_URL", "https://api.weixin.qq.com/sns/oauth2/access_token?");
define("REFR_TOKEN_URL", "https://api.weixin.qq.com/sns/oauth2/refresh_token?");
define("USRINFO_GET_URL", "https://api.weixin.qq.com/sns/userinfo?");
define("GRANT_TYPE_AUTH_CODE", "authorization_code");
define("GRANT_TYPE_REFR_TOKEN", "refresh_token");

$weixin = new class_weixin_usrinfo();

class class_weixin_usrinfo
{
    private $access_token;
    private $usrinfo_openid;
    private $usrinfo_nickname;
    private $usrinfo_sex;
    private $usrinfo_language;
    private $usrinfo_province;
    private $usrinfo_city;
    private $usrinfo_country;
    private $usrinfo_headimgurl;
    private $usrinfo_privilege;

    //构造函数，获取Access Token并注册menu
    public function __construct()
    {
        $url_token = TOKEN_GET_URL.
            "appid=".APPID.
            "&secret=".APPSECRET.
            "&code=".$_GET['code'].
            "&grant_type=".GRANT_TYPE_AUTH_CODE;
        $res = $this->http_request($url_token);
        //var_dump($res . "<br>");
        $result = json_decode($res, true);
        $this->access_token = $result["access_token"];
        $this->usrinfo_openid = $result["openid"];

        //注册menu
        if (($this->access_token) && ($this->usrinfo_openid)) {
            $this->userinfo_request();
            $this->print_usrinfo();
        }
    }

    //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    private function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    private function userinfo_request()
    {
        $url_usrinfo = USRINFO_GET_URL .
            "access_token=".$this->access_token.
            "&openid=".$this->usrinfo_openid;
        $output = $this->http_request($url_usrinfo);
        //var_dump($output . "<br>");
        $result = json_decode($output, true);
        $this->usrinfo_nickname = $result["nickname"];
        $this->usrinfo_sex = $result["sex"];
        $this->usrinfo_language = $result["language"];
        $this->usrinfo_city = $result["city"];
        $this->usrinfo_province = $result["province"];
        $this->usrinfo_country = $result["country"];
        $this->usrinfo_headimgurl = $result["headimgurl"];
        $this->usrinfo_privilege = $result["privilege"];
    }

    private function print_usrinfo()
    {
        echo "用户昵称： " . $this->usrinfo_nickname . "<br>";
        echo "openid： " . $this->usrinfo_openid . "<br>";
        echo "性别： " . $this->usrinfo_sex . "<br>";
        echo "国家： " . $this->usrinfo_country . "<br>";
        echo "省份： " . $this->usrinfo_province . "<br>";
        echo "城市： " . $this->usrinfo_city . "<br>";
        echo "头像： " . $this->usrinfo_headimgurl . "<br>";
        echo "特权信息 " . $this->usrinfo_privilege . "<br>";
    }
}

?>