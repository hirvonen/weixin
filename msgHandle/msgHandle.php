<?php
define("DBHOST", "121.41.104.220");
define("DBUSER", "root");
define("DBPASS", "Cccc1111");
define("DBNAME", "eyoungdb");

class msgHandle
{
    private $postObj;
    private $db_conn;
    private $send_fromUsername;
    private $send_toUsername;
    private $send_time;
    private $send_textTpl;

    /**
     * 构造函数
     * @param $postObj
     */
    public function msgHandle($postObj)
    {
        $this->postObj = $postObj;
        $this->send_fromUsername = $this->postObj->ToUserName;
        $this->send_toUsername = $this->postObj->FromUserName;
        $this->send_time = time();
        $this->send_textTpl = $this->getTpl($this->postObj);
    }

    /**
     * Event受信处理函数
     * @return int
     */
    public function receiveEvent()
    {
        $event = trim($this->postObj->Event);

        switch ($event) {
            case 'CLICK':   //点击按钮事件处理
                $result = $this->receiveEvent_Click();
                break;
            case 'subscribe':   //用户关注事件处理
                $result = $this->receiveEvent_Subscribe();
                break;
            default:
                $result = '';
                break;
        }
        return $result;
    }

    /**
     * Text受信处理函数
     * @return int
     */
    public function receiveText()
    {
        $contentStr = $this->getReplyText($this->postObj);
        $result = $this->sentText($contentStr);
        return $result;
    }


    //内部函数
    /**
     * 点击按钮事件处理函数
     */
    private function receiveEvent_Click()
    {
        $this->connectDB();
        $this->selectDB();
        $eventKey = trim($this->postObj->EventKey);
        switch ($eventKey) {
            case 'myOrder'://我的订单
                /*$sql = "SHOW TABLES FROM eyoungdb ";
                $retval = mysql_query($sql, $this->db_conn);

                $strtemp = '';
                while($row = mysql_fetch_row($retval)){
                    //echo "<tr><td>$row[0]</td></tr>";
                    $strtemp = $strtemp."$row[0]"."\n";
                }
                //    $row = mysql_fetch_row($retval);
                $contentStr = $strtemp;*/
                $contentStr = "暂时无法查看订单，程序员玩儿命施工中，敬请期待。";
                break;
            case 'myInfo'://我的信息
                $contentStr = "暂时无法查看信息，程序员玩儿命施工中，敬请期待。";
                break;
            case 'memberCharge'://会员充值
                $contentStr = "暂时无法充值，程序员玩儿命施工中，敬请期待。";
                break;
            default:
                $contentStr = "哎呦出错啦！请联系我们！021-XXXXXXXX";
                break;
        }
        $result = $this->sentText($contentStr);
        $this->disconnectDB();
        return $result;
    }

    /**
     * 关注事件处理函数
     * @return int
     */
    private function receiveEvent_Subscribe()
    {
        $contentStr = "欢迎关注Eyoung！我们将为您提供最完美的产后恢复上门美疗服务！";
        //$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
        $result = $this->sentText($contentStr);
        return $result;
    }

    private function connectDB()
    {
        $this->db_conn = mysql_connect(DBHOST, DBUSER, DBPASS);
    }

    private function disconnectDB()
    {
        mysql_close($this->db_conn);
    }

    private function selectDB()
    {
        if( $this->db_conn ) {
            mysql_select_db( DBNAME );
        }
    }

    private function getTpl()
    {
        $msgType = trim($this->postObj->MsgType);
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

    private function sentText($contentStr)
    {
        $resultStr = sprintf($this->send_textTpl,
            $this->send_toUsername,
            $this->send_fromUsername,
            $this->send_time,
            "text",
            $contentStr);
        return $resultStr;
    }

    private function getReplyText()
    {
        $keyword = trim($this->postObj->Content);
        if($keyword == "women"){
            $contentStr = "E-young【产后恢复美疗】\n".'<a href="http://121.41.104.220/e-young/"> 我们的站点</a>';
        }else{
            $contentStr = "程序员玩儿命施工中，敬请期待。 ".$keyword;
        }
        return $contentStr;
    }
}
?>