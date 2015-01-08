<?php
namespace mobile\controllers;

use Yii;
use common\services\WeixinService;
use yii\helpers\Url;

/**
 * WeixinAPIController 微信所有API接口
 */
class WeixinapiController extends BaseController
{
    //微信号： gh_498a3007710d
    public $token = 'f990514e095c8528';//token
    public $appid = 'wx9a8fce4b97312d3f';
    public $secret = '6862c4f8bc7a2d8895c0c447c0cd97fe';
    public $openid = '';  //oapFxt3yuKe192BLewsLHG0h5-hE
    public $token_err = array(40001,41001);
    public static $APPID = 'wx9a8fce4b97312d3f';
    public static $SECRET = '6862c4f8bc7a2d8895c0c447c0cd97fe';

    public $debug = false;//是否debug的状态标示，方便我们在调试的时候记录一些中间数据
    public $setFlag = false;



    /** *************************  方法入口 START  *********************** */

    /**
     * 设置微信菜单方法入口
     */
    public function actionSetmenu(){
        $result = $this->setMenu();
        var_dump($result);
    }


    /**
     * 接收自定义菜单事件
     */
    public function actionIndex(){
        //签名效验     忽删 第一次效验用 。。。
        //$this->valid();

        //回复数据方法
        $this->responseMsg();
    }


    /** *************************  方法入口 END  *********************** */


    /** *************************  消息验证 模块 start ***********************  */

    protected $weixinService;

    public function __construct($id, $module, WeixinService $weixinService, $config = []){
        $this->weixinService = $weixinService;
        parent::__construct($id, $module, $config);
    }


    public static function getIndexUrl(){
        $url = Url::toRoute(['site/index'],true);
        return $url;
        //return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".self::$APPID."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    }

    /**
     * 相关回复
     */
    public function responseMsg(){
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];//返回回复数据
        if ($this->debug) {
            $this->write_log ($postStr);
        }
        if (!empty($postStr)){
            $postObj = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $MsgType = strtoupper($postObj['MsgType']);//消息类型
            //$this->openid = $postObj['FromUserName'];
            if($MsgType=='EVENT'){
                $MsgEvent = strtoupper($postObj['Event']);//获取事件类型ll
                if ($MsgEvent=='CLICK'){
                    //点击事件
                    $EventKey = $postObj['EventKey'];//菜单的自定义的key值，可以根据此值判断用户点击了什么内容，从而推送不同信息
                    switch($EventKey){
                        case "USER_INFO" :  //账户信息
                            //要返回相关内容
                            $msg = $this->weixinService->accountInformation($postObj['FromUserName']);
                            $this->makeText($postObj,$msg);
                            break;
                        case "SALE_LOG" :   //交易记录
                            //要返回相关内容
                            $msg = $this->weixinService->accountTrades($postObj['FromUserName']);
                            $this->makeText($postObj,$msg);
                            break;
                        case "ASSETS_INFO" :  //持有资产
                            //要返回相关内容
                            $msg = $this->weixinService->accountHold($postObj['FromUserName']);
                            $this->makeText($postObj,$msg);
                            break;
                        case "ACTIVITY" :  //活动中心
                            $result = $this->weixinService->pageListactivitybyNews($postObj['FromUserName']);
                            $this->makeNews($postObj,$result);
                            break;
                        case "LOGIN_OUT" : // 登录或者退出
                            $result = var_export(Yii::$app->user->identity, true);
                            $this->makeNews($postObj,$result);
                            break;
                        default:
                            break;
                    }

                }else if ($MsgEvent=='SUBSCRIBE'){  //subscribe
                    //订阅事件
                }
            }else if (!empty($postObj['keyword'])){   //回复用户输入事件

            }
        }else{
            echo '没有任何消息传递';
            $this->write_log('responseMsg function no message ... ');
        }
    }



    //回复文本消息
    public function makeText($msg = array() , $text = '暂无内容'){
        $CreateTime = time ();
        $FuncFlag = $this->setFlag ? 1 : 0;
        $textTpl = "<xml>
            <ToUserName><![CDATA[{$msg['FromUserName']}]]></ToUserName>
            <FromUserName><![CDATA[{$msg['ToUserName']}]]></FromUserName>
            <CreateTime>{$CreateTime}</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>%s</FuncFlag>
            </xml>";
        echo sprintf ($textTpl, $text, $FuncFlag);
        exit;
    }
    //根据数组参数回复图文消息
    public function makeNews($msg,$newsData = array()){
        $CreateTime = time ();
        $FuncFlag = $this->setFlag ? 1 : 0;
        $newTplHeader = "<xml>
            <ToUserName><![CDATA[{$msg['FromUserName']}]]></ToUserName>
            <FromUserName><![CDATA[{$msg['ToUserName']}]]></FromUserName>
            <CreateTime>{$CreateTime}</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <ArticleCount><![CDATA[%s]]></ArticleCount><Articles>";
        $newTplItem = "<item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
            </item>";
        $newTplFoot = "</Articles>
            <FuncFlag>%s</FuncFlag>
            </xml>";
        $Content = '';
        $itemsCount = count ($newsData['items']);
        $itemsCount = $itemsCount < 10 ? $itemsCount : 10;//微信公众平台图文回复的消息一次最多10条
        if ($itemsCount) {
            foreach ($newsData['items'] as $key => $item) {
                if ($key <= 9) {
                    $Content .= sprintf ($newTplItem, $item['title'], $item['description'], $item['picurl'], $item['url']);
                }
            }
        }
        $header = sprintf ($newTplHeader, $newsData['content'], $itemsCount);
        $footer = sprintf ($newTplFoot, $FuncFlag);
        echo $header . $Content . $footer;
        exit;
    }

    public function valid()
    {
        if ($this->checkSignature ()) {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                echo $_GET['echostr'];
                exit;
            }
        } else {
            $this->write_log  ('认证失败');
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $tmpArr = array($this->token, $timestamp, $nonce);
        sort ($tmpArr);
        $tmpStr = implode ($tmpArr);
        $tmpStr = sha1 ($tmpStr);


        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    //这里是你记录调试信息的地方  请自行完善   以便中间调试<br>　　
    private function write_log($log) {
        Yii::error($log);
    }

    /** *************************   消息验证 模块 end *************************   */



    /**
     * 设置公众号菜单
     * "url"  : "http://192.168.1.214/frontend/web/app-login/login?openid='.$this->openid.'"   做个备份而已 DO NOT MIND
     * "url"  : "http://42.96.204.114/koudai/frontend/web/app-login/index?openid='.$this->openid.'"
     * @return mixed
     */
    public function setMenu(){
        $url = self::getIndexUrl();

        $xjson = '{
            "button":[
                {
                   "type" : "view",
                   "name" : "精品推荐",
                   "url"  : "'.$url.'"
                },
               {
                   "type":"click",
                   "name":"活动中心",
                   "key":"ACTIVITY"
               },
               {
                   "name":"我的口袋",
                   "sub_button":[
                        {
                           "type":"click",
                           "name":"账户信息",
                           "key":"USER_INFO"
                        },
                        {
                            "type": "click",
                            "name": "交易记录",
                            "key": "SALE_LOG"
                        },
                        {
                            "type": "click",
                            "name": "持有资产",
                            "key": "ASSETS_INFO"
                        },
                        {
                            "type": "click",
                            "name": "登录/退出",
                            "key": "LOGIN_OUT"
                        }
                    ]
               }
        ]}';
        $access_token = $this->get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        return self::postData($url,$xjson);

    }


    /**
     * 封装的获取微信用户信息方法
     * @return bool|mixed
     */
    public function get_bll_userinfo(){
        $access_token = !empty($_SESSION['access_token']) ? $_SESSION['access_token'] : '';
        if (empty($access_token)){
            $access_token = $this->get_access_token();
        }
        return $this->get_user_info($access_token,$this->openid);
    }

    /**
     * 保存SESSION值
     * @param $key
     * @param $val
     */
    public function setsession($key,$val){
        if (isset($key) && isset($val)){
            $lifeTime = 7200;
            session_set_cookie_params($lifeTime);
            $_SESSION[$key] = $val;
        }

    }

    /**
     * 获取授权token
     *
     * @param string $code 通过get_authorize_url获取到的code
     */
    public static function get_code_token($code = ''){
        if (empty($code)){
            return false;
        }
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::$APPID."&secret=".self::$SECRET."&code={$code}&grant_type=authorization_code";
        $token_data = self::postData($token_url);
        return json_decode($token_data,TRUE);
    }

    /**
     * 根据OPENID获取微信的用户信息
     * @param $access_token
     * @param $openid
     */
    public function get_user_info($access_token,$openid){
        $params = array('access_token'=>$access_token,'openid'=>$openid,'lang'=>'zh_CN');
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?'.http_build_query($params);
        $json_data = self::postData($url);
        $result = json_decode($json_data,true);
        if (isset($result['subscribe']) && $result['subscribe'] == 1){   //获取成功
            return $result;
        }

        if (in_array($result['errcode'] , $this->token_err)){
            $access_token = $this->get_access_token();
            self::get_user_info($access_token,$this->openid);
        }

        return false;
    }

    /**
     * 获取ACCESS_TOKEN接口
     * @param string $appid
     * @param string $secret
     * @return mixed
     */
    public function get_access_token(){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->secret}";
        $json_data = self::postData($url);
        $result = json_decode($json_data,true);
        if (isset($result['access_token'])){
            $this->setsession('access_token',$result['access_token']);
            return $result['access_token'];
        }
        return false;
    }


    /**
     * 验证授权
     * @param string $access_token
     * @param string $open_id
     */
    public function check_access_token($access_token = '', $open_id = ''){
        if($access_token && $open_id){
            $url = "https://api.weixin.qq.com/sns/auth?access_token={$access_token}&openid={$open_id}&lang=zh_CN";
            $result = self::postData($url);
            if($result[0] == 200){
                return json_decode($result[1], TRUE);
            }
        }
        return FALSE;
    }

    /**
     * CURL获取
     * @param $url
     * @return mixed
     */
    public static function postData($url , $data = ''){
        $timeout = 1000;
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在


        if (!empty($data)){
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)'); // 模拟用户使用的浏览器
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包x
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        }
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); // 设置超时限制防止死循环

        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }

}