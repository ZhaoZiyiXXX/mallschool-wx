<?php
/**
 * 微信公众平台 PHP SDK
 *
 * @author NetPuter <netputer@gmail.com>
 */

/**
   * 微信公众平台处理类
   */
class Wechat {
    
    /**
     * 调试模式，将错误通过文本消息回复显示
     *
     * @var boolean
     */
    private $debug;
    
    /**
     * 以数组的形式保存微信服务器每次发来的请求
     *
     * @var array
     */
    private $request;
    
    public $kvdb;
    
    /*正式帐号
      private $appid = 'wxbe694e2d2a3e8bf7';
      private $secret = '6698077cc0e529f5015bf8f8fe9f7f9d';
      private $root_url = 'http://1.mallschoolwx.sinaapp.com/';
	  */
    
    /*测试帐号*/
    private $appid = 'wx93e6d64b31eba742';
    private $secret = 'fa594b11c7652cda706c700eff625828';
    public $root_url = 'http://2.mallschoolwx.sinaapp.com/';
    
    public function Log($content){
        $mysql = new SaeMysql();
        $sql = "INSERT  INTO `log` ( `id`, `time`, `content`) VALUES (null ,'" . time() . "' ,'".$content."') ";
        $mysql->runSql($sql);
        $mysql->closeDb();
    }
    public function curl_post($url, $data) {
        $info = array ();
        $header = "Content-type: text/xml";
        // 初始化会话。
        $curl = curl_init ();
        // 需要获取的URL地址，也可以在curl_init()函数中设置。
        curl_setopt ( $curl, CURLOPT_URL, $url );
        // 启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
        curl_setopt ( $curl, CURLOPT_POST, 1 );
        // 全部数据使用HTTP协议中的"POST"操作来发送。
        curl_setopt ( $curl, CURLOPT_POSTFIELDS, $data );
        // 设置cURL允许执行的最长秒数。
        curl_setopt ( $curl, CURLOPT_TIMEOUT, 5 );
        // 启用时会将头文件的信息作为数据流输出。
        curl_setopt ( $curl, CURLOPT_HEADER, $header );
        // 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
        // 执行会话
        $info = curl_exec ( $curl );
        if (curl_errno ( $curl )) {
            echo 'Errno' . curl_error ( $curl );
            sae_debug( 'Errno' . curl_error ( $curl ));
        }
        // 关闭会话
        curl_close ( $curl );
        // 返回数据
        return $info;
    }
    
    public function curl_post_ssl($url, $data) {
        $info = array ();
        $curl = curl_init ();
        
        curl_setopt ( $curl, CURLOPT_URL, $url );
        curl_setopt ( $curl, CURLOPT_POST, 1 );
        curl_setopt ( $curl, CURLOPT_POSTFIELDS, $data );
        curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 );
        curl_setopt ( $curl, CURLOPT_HEADER, 0 );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
        
        $info = curl_exec ( $curl );
        if (curl_errno ( $curl )) {
            echo 'Errno' . curl_error ( $curl );
        }
        
        curl_close ( $curl );
        return $info;
    }
    
    //自定义菜单中获取access_token
    private function get_access_token(){
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret;
        $json=$this->http_request_json($url);//这个地方不能用file_get_contents
        $data=json_decode($json,true);
        if($data['access_token']){
            return $data['access_token'];
        }else{
            return "获取access_token错误";
        }		
    }
    // 客服消息属于高级接口，目前不支持
    public function postCustomMsg( $msg ) {
        $url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $this->getAccessToken();
        $data = array();
        //$content = array();
        //$content['content'] = $msg;
        $data['touser'] = $this->getOpenid();
        $data['msgtype'] = "text";
        $data['text[content]'] = $msg;
        
        return $this->curl_post_ssl( $url, $data );
    }
    //因为url是https 所有请求不能用file_get_contents,用curl请求json 数据
    public function http_request_json($url){  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;  
    }
    
    private function isAccessTokenTimeout($access_token_time)
    {
        if ( empty($access_token_time) || time() - $access_token_time > 7200 ) {
            $this->kvdb->set('access_token_time', time());
            return true;
        } else {
            return false;
        }
    }
    
    public function getAccessToken()
    {
        $access_token = $this->kvdb->get(  'access_token' );
        $access_token_time = $this->kvdb->get('access_token_time');
        if ( empty($access_token) || $this->isAccessTokenTimeout( $this->kvdb->get('access_token_time') ) ) {
            $this->kvdb->set(  'access_token', $this->get_access_token() );
        }
        $access_token = $this->kvdb->get(  'access_token' );
        return $access_token;
    }
    
    public function getOpenid()
    {
        return $this->getRequest('fromusername');
    }
    
    public function getUserInfo()
    {
        /*
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $this->getAccessToken() . "&openid=".$this->getOpenid() . "&lang=zh_CN";
		$json=$this->http_request_json($url);//这个地方不能用file_get_contents
		$data=json_decode($json,true);
        $ret;
		if(!empty($data['nickname'])){
			return $data['nickname'];
		}else{
            //$this->responseText($json);
			return "匿名";
		}	
        */
          return "匿名";
          
      }
    
    /**
     * 初始化，判断此次请求是否为验证请求，并以数组形式保存
     *
     * @param string $token 验证信息
     * @param boolean $debug 调试模式，默认为关闭
     */
    public function __construct($token, $debug = FALSE) {
        if ($this->isValid() && $this->validateSignature($token)) {
            exit($_GET['echostr']);
        }
        
        // 设置错误处理函数，将错误通过文本消息回复显示
        $this->debug = $debug;
        set_error_handler(array(&$this, 'errorHandler'));
        
        $xml = (array) simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);
        
        // 将数组键名转换为小写，提高健壮性，减少因大小写不同而出现的问题
        $this->request = array_change_key_case($xml, CASE_LOWER);
        // 初始化KVDB
        $this->kvdb = new SaeKV();
        $this->kvdb->init();
        if ( !$this->kvdb->get('access_token_time') ) {
            $this->kvdb->add('access_token_time', '');
        }
        if ( !$this->kvdb->get('access_token') ) {
            $this->kvdb->add('access_token', '');
        }
    }
    
    /**
     * 判断此次请求是否为验证请求
     *
     * @return boolean
     */
    private function isValid() {
        return isset($_GET['echostr']);
    }
    
    /**
     * 判断验证请求的签名信息是否正确
     *
     * @param  string $token 验证信息
     * @return boolean
     */
    private function validateSignature($token) {
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        
        $signatureArray = array($token, $timestamp, $nonce);
        sort($signatureArray);
        
        return sha1(implode($signatureArray)) == $signature;
    }
    
    /**
     * 获取本次请求中的参数，不区分大小
     *
     * @param  string $param 参数名，默认为无参
     * @return mixed
     */
    protected function getRequest($param = FALSE) {
        if ($param === FALSE) {
            return $this->request;
        }
        
        $param = strtolower($param);
        
        if (isset($this->request[$param])) {
            return $this->request[$param];
        }
        
        return NULL;
    }
    
    
    
    /**
     * 用户关注时触发，用于子类重写
     *
     * @return void
     */
    protected function onSubscribe() {}
    
    /**
     * 用户取消关注时触发，用于子类重写
     *
     * @return void
     */
    protected function onUnsubscribe() {}
    
    /**
     * 用户扫码推送，且弹出“消息接受中”事件
     *
     * @return void
     */
    protected function onScancodeWaitMsg() {}
    
    /**
     * 用户点击菜单事件
     *
     * @return void
     */
    protected function onClick($event_key) {}
    
    /**
     * 收到文本消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onText() {}
    
    /**
     * 收到图片消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onImage() {}
    
    /**
     * 收到地理位置消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onLocation() {}
    
    /**
     * 收到链接消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onLink() {}
    
    /**
     * 收到未知类型消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onUnknown() {}
    
    /**
     * 回复文本消息
     *
     * @param  string  $content  消息内容
     * @param  integer $funcFlag 默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    protected function responseText($content, $funcFlag = 0) {
        exit(new TextResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $content, $funcFlag));
    }
    
    /**
     * 回复音乐消息
     *
     * @param  string  $title       音乐标题
     * @param  string  $description 音乐描述
     * @param  string  $musicUrl    音乐链接
     * @param  string  $hqMusicUrl  高质量音乐链接，Wi-Fi 环境下优先使用
     * @param  integer $funcFlag    默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    protected function responseMusic($title, $description, $musicUrl, $hqMusicUrl, $funcFlag = 0) {
        exit(new MusicResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $title, $description, $musicUrl, $hqMusicUrl, $funcFlag));
    }
    
    /**
     * 回复图文消息
     * @param  array   $items    由单条图文消息类型 NewsResponseItem() 组成的数组
     * @param  integer $funcFlag 默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    protected function responseNews($items, $funcFlag = 0) {
        exit(new NewsResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $items, $funcFlag));
    }
    
    /**
     * 分析消息类型，并分发给对应的函数
     *
     * @return void
     */
    public function run() {
        switch ($this->getRequest('msgtype')) {
            
            case 'event':
            switch ($this->getRequest('event')) {
                
                case 'subscribe':
                $this->onSubscribe();
                break;
                
                case 'unsubscribe':
                $this->onUnsubscribe();
                break;
                
                case 'scancode_waitmsg':
                $this->onScancodeWaitMsg();
                break;
                
                case 'CLICK':
                sae_debug("onClick Event");
                $this->onClick($this->getRequest('EventKey'));
                break;
                
            }
            
            break;
            
            case 'text':
            $this->onText();
            break;
            
            case 'image':
            $this->onImage();
            break;
            
            case 'location':
            $this->onLocation();
            break;
            
            case 'link':
            $this->onLink();
            break;
            
            default:
            $this->onUnknown();
            break;
            
        }
    }
    
    /**
     * 自定义的错误处理函数，将 PHP 错误通过文本消息回复显示
     * @param  int $level   错误代码
     * @param  string $msg  错误内容
     * @param  string $file 产生错误的文件
     * @param  int $line    产生错误的行数
     * @return void
     */
    protected function errorHandler($level, $msg, $file, $line) {
        if ( ! $this->debug) {
            return;
        }
        
        $error_type = array(
            // E_ERROR             => 'Error',
            E_WARNING           => 'Warning',
            // E_PARSE             => 'Parse Error',
            E_NOTICE            => 'Notice',
            // E_CORE_ERROR        => 'Core Error',
            // E_CORE_WARNING      => 'Core Warning',
            // E_COMPILE_ERROR     => 'Compile Error',
            // E_COMPILE_WARNING   => 'Compile Warning',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_STRICT            => 'Strict',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'User Deprecated',
        );
        
        $template = <<<ERR
PHP 报错啦！

%s: %s
File: %s
Line: %s
ERR;
          
          $this->responseText(sprintf($template,
                                      $error_type[$level],
                                      $msg,
                                      $file,
                                      $line
                                     ));
      }
    
}

/**
   * 用于回复的基本消息类型
   */
abstract class WechatResponse {
    
    protected $toUserName;
    protected $fromUserName;
    protected $funcFlag;
    
    public function __construct($toUserName, $fromUserName, $funcFlag) {
        $this->toUserName = $toUserName;
        $this->fromUserName = $fromUserName;
        $this->funcFlag = $funcFlag;
    }
    
    abstract public function __toString();
    
}

/**
   * 用于回复的文本消息类型
   */
class TextResponse extends WechatResponse {
    
    protected $content;
    
    protected $template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA[%s]]></Content>
  <FuncFlag>%s<FuncFlag>
</xml>
XML;
    
    public function __construct($toUserName, $fromUserName, $content, $funcFlag = 0) {
        parent::__construct($toUserName, $fromUserName, $funcFlag);
        $this->content = $content;
    }
    
    public function __toString() {
        return sprintf($this->template,
                       $this->toUserName,
                       $this->fromUserName,
                       time(),
                       $this->content,
                       $this->funcFlag
                      );
    }
    
}

/**
   * 用于回复的音乐消息类型
   */
class MusicResponse extends WechatResponse {
    
    protected $title;
    protected $description;
    protected $musicUrl;
    protected $hqMusicUrl;
    
    protected $template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[music]]></MsgType>
  <Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
  </Music>
  <FuncFlag>%s<FuncFlag>
</xml>
XML;
    
    public function __construct($toUserName, $fromUserName, $title, $description, $musicUrl, $hqMusicUrl, $funcFlag) {
        parent::__construct($toUserName, $fromUserName, $funcFlag);
        $this->title = $title;
        $this->description = $description;
        $this->musicUrl = $musicUrl;
        $this->hqMusicUrl = $hqMusicUrl;
    }
    
    public function __toString() {
        return sprintf($this->template,
                       $this->toUserName,
                       $this->fromUserName,
                       time(),
                       $this->title,
                       $this->description,
                       $this->musicUrl,
                       $this->hqMusicUrl,
                       $this->funcFlag
                      );
    }
    
}

/**
   * 用于回复的图文消息类型
   */
class NewsResponse extends WechatResponse {
    
    protected $items = array();
    
    protected $template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[news]]></MsgType>
  <ArticleCount>%s</ArticleCount>
  <Articles>
    %s
  </Articles>
  <FuncFlag>%s<FuncFlag>
</xml>'
XML;
    
    public function __construct($toUserName, $fromUserName, $items, $funcFlag) {
        parent::__construct($toUserName, $fromUserName, $funcFlag);
        $this->items = $items;
    }
    
    public function __toString() {
        return sprintf($this->template,
                       $this->toUserName,
                       $this->fromUserName,
                       time(),
                       count($this->items),
                       implode($this->items),
                       $this->funcFlag
                      );
    }
    
}

/**
   * 单条图文消息类型
   */
class NewsResponseItem {
    
    protected $title;
    protected $description;
    protected $picUrl;
    protected $url;
    
    protected $template = <<<XML
<item>
  <Title><![CDATA[%s]]></Title>
  <Description><![CDATA[%s]]></Description>
  <PicUrl><![CDATA[%s]]></PicUrl>
  <Url><![CDATA[%s]]></Url>
</item>
XML;
    
    public function __construct($title, $description, $picUrl, $url) {
        $this->title = $title;
        $this->description = $description;
        $this->picUrl = $picUrl;
        $this->url = $url;
    }
    
    public function __toString() {
        return sprintf($this->template,
                       $this->title,
                       $this->description,
                       $this->picUrl,
                       $this->url
                      );
    }
    
}
