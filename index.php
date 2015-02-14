<?php
define("TOKEN", "phptestjbf");
define("AppID", "wxfad891501f5e751d");
define("EncodingAESKey", "xIb7PhJVeqgQvWaE774mCt7uwQgifSD6v99BAVNhlEH");

require (dirname(__FILE__).'/'.'encrypt/wxBizMsgCrypt.php');
require (dirname(__FILE__).'/'.'msgHandle/msgHandle.php');

$wechatObj = new wechatCallback();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechatCallback
{
    /**
     *验证签名
     */
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array(TOKEN, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    /**
     *响应消息:处理msg的主函数
     */
    public function responseMsg()
    {
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $msg_signature = $_GET['msg_signature'];
        $encrypt_type = (isset($_GET['encrypt_type']) && ($_GET['encrypt_type']=='aes')) ? 'aes':'raw';

        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $pc = new WXBizMsgCrypt(TOKEN, EncodingAESKey, AppID);

        if(!empty($postStr)){
            //解密
            if ($encrypt_type == 'aes'){
                $this->logger(" D \r\n".$postStr);
                $decryptMsg = "";  //解密后的明文存储用
                $errCode = $pc->DecryptMsg($msg_signature, $timestamp, $nonce, $postStr, $decryptMsg);
                if($errCode == ErrorCode::$OK) {
                    $postStr = $decryptMsg;
                }
                else{
                    $this->logger(" R \r\n".$errCode);
                    return;
                }
            }
            $this->logger(" R \r\n".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            //消息类型分离
            $msgHdl = new msgHandle($postObj);
            $result = 0;
            switch ($RX_TYPE)
            {
                case "event":
                    $hdl_result = $msgHdl->receiveEvent();
                    break;
                case "text":
                    $hdl_result = $msgHdl->receiveText();
                    break;
                default:
                    $hdl_result = '';
                    break;
            }
            $this->logger(" R \r\n".$result);
            //加密
            if ($encrypt_type == 'aes'){
                $encryptMsg = ''; //加密后的密文
                $pc->encryptMsg($hdl_result, $timestamp, $nonce, $encryptMsg);
                $result = $encryptMsg;
                $this->logger(" E \r\n".$result);
            }
            echo $result;
        }
    }

    //日志记录
    public function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            $this->set_display_errors(false);
            sae_debug($log_content);
            $this->set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 500000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('Y-m-d H:i:s').$log_content."\r\n", FILE_APPEND);
        }
    }
    private function set_display_errors($displayErrors)
    {
        if($displayErrors == true) {
            ini_set('display_errors', 1);
        }
        else{
            ini_set('display_errors', 0);
        }
    }
}
?>