<?php
define("TOKEN", "phptestjbf");

$wechatObj = new wechatCallbackapiTest();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
} 

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"]; 
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msgtype_rcv = trim($postObj->MsgType);
            switch($msgtype_rcv){
                case "text":
                    $this->handleText($postObj);
                    break;
                case "event":
                    $this->handleEvent($postObj);
                    break;
                default:
                    break;
            }
        }else{
            echo "";
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"]; 
         $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr ); 
         if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    //Common Funcs
    private function getTpl($postObj)
    {
        $msgType = trim($postObj->MsgType);
        switch ($msgType) {
            case 'text':
                $replyTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
                break;
            case 'event':
                $replyTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
                break;
            default:
                break;
        }
        return $replyTpl;
    }

    //Text Handle Funcs
    private function handleText($postObj)
    {
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $time = time();
        $textTpl = $this->getTpl($postObj);
        $contentStr = $this->getReplyText($postObj);
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
        echo $resultStr;
    }
    private function getReplyText($postObj)
    {
        $keyword = trim($postObj->Content);
        if($keyword == "women"){
            $contentStr = "E-young【产后上门护理】\n".'<a href="http://sekikou.oicp.net:48446/wordpress/"> 我们的站点</a>';
        }else{
            $contentStr = "test OK: ".$keyword;
        }
        return $contentStr;
    }

    //Event Handle Funcs
    private function handleEvent($postObj)
    {
        $event = trim($postObj->Event);
        switch ($event) {
            case 'CLICK':
                $this->handleEvent_Click($postObj);
                break;
            case 'subscribe':
                $this->handleEvent_Subscribe($postObj);
            default:
                # code...
                break;
        }
    }
    private function handleEvent_Click($postObj)
    {
        $eventKey = trim($postObj->EventKey);
        switch ($eventKey) {
            case 'e_young_intro':
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $time = time();
                $textTpl = $this->getTpl($postObj);
                $contentStr = "E-young是一家专门做产后调理的公司blablabla";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
                echo $resultStr;
                break;
            default:
                # code...
                break;
        }
    }
    private function handleEvent_Subscribe($postObj)
    {
        $
    }
}
?>