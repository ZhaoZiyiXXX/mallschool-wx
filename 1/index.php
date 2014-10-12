<?php
/**
 * 微信公众平台 PHP SDK 示例文件
 *
 * @author NetPuter <netputer@gmail.com>
 */

  require('src/Wechat.php');

  /**
   * 微信公众平台演示类
   */
  class MyWechat extends Wechat {

    /**
     * 用户关注时触发，回复「欢迎关注」
     *
     * @return void
     */
    protected function onSubscribe() {
        $url="http://api.jige.olege.com/wuser";
        $data = array();
        $data['openid'] = $this->getOpenid();
        $data['type'] = "new";
        $data['nickname'] = $this->getUserInfo();
        //$this->Log($data['openid'] . ' : ' . $data['type'] . ' : ' . $data['nickname']);
        $this->curl_post($url, $data);
        $this->responseText("欢迎【" . $this->getUserInfo() . "】关注喵校园");
    }
	

    /**
     * 用户取消关注时触发
     *
     * @return void
     */
    protected function onUnsubscribe() {
      // 「悄悄的我走了，正如我悄悄的来；我挥一挥衣袖，不带走一片云彩。」
    }
 
    private function hasEnoughInfo() {
        $url="http://api.jige.olege.com/wuser?openid=" . $this->getOpenid();
        $html = file_get_contents($url);
		$obj = json_decode($html);
        if (empty($obj->{'data'}->{'college'}) || empty($obj->{'data'}->{'campus'})) {
            $url='<a href="http://1.mallschoolwx.sinaapp.com/pages/userinfo.php?openid=' . $this->getOpenid() . '">请先点击补全校区信息</a>';
            $this->responseText($url);
            return false;
        }
        if (empty($obj->{'data'}->{'tel'})) {
            $url='<a href="http://1.mallschoolwx.sinaapp.com/pages/userinfo.php?openid=' . $this->getOpenid() . '">请先点击补全联系信息</a>';
            $this->responseText($url);
            return false;
        }
        return true;
    }
    
    /**
     * 用户扫码推送，且弹出“消息接受中”事件
     *
     * @return void
     */
    protected function onScancodeWaitMsg() {
        if ( !$this->hasEnoughInfo() ) {
            return;
        }
        $request = $this->getRequest();
        $scancodeinfo = (array)$request['scancodeinfo'];
        $scancodeinfo = array_change_key_case($scancodeinfo, CASE_LOWER);
		$tmparray = explode(",",$scancodeinfo['scanresult']);
        //sae_debug( $this->getOpenid() );
		if("EAN_13"==$tmparray[0]){
			$url='http://api.jige.olege.com/book?type=ISBN&q='.$tmparray[1];
			$html = file_get_contents($url);
			$obj = json_decode($html);
			if('{"result":0,"data":{"result":0}}' == $html){
				$this->responseText('ISBN:'.$tmparray[1].'获取图书详情失败，需要用户手工录入数据');
			}
			else{
                $tmpresponse = '<a href="http://1.mallschoolwx.sinaapp.com/pages/sellbook.php?isbn=' . $tmparray[1] . 
                    '&name='   . $obj->{"data"}->{"name"} . 
                    '&author=' . $obj->{"data"}->{"author"} . 
                    '&press='  . $obj->{"data"}->{"press"} .
                    '&price='  . $obj->{"data"}->{"fixedPrice"} . 
                    '&openid=' . $this->getOpenid() . 
                    '&bookid=' . $obj->{"data"}->{"id"} . 
                    '">点击出售</a>';
				$this->responseText($tmpresponse);
			}
		}
		else
			$this->responseText('扫码事件:' . $scancodeinfo['scanresult']);
    }
      
      /**
       * 用户点击菜单事件
       *
       * @return void
       */
      protected function onClick($event_key) {
          if ($event_key == 'USER_INFO') {
              $url='http://api.jige.olege.com/wuser?openid=' . $this->getOpenid();
              $html = file_get_contents($url);
              if('{"result":0,"data":{"result":0}}' == $html){
                  $this->responseText("抱歉，获取个人信息异常，请稍后重试。");
                  return;
              }else{
                  $obj = json_decode($html);
                  $college = $obj->{"data"}->{"college"};
                  $campus = $obj->{"data"}->{"campus"};
                  $nickname = $obj->{"data"}->{"nickname"};
                  $tel = $obj->{"data"}->{"tel"};
                  $this->responseText("个人信息：\n" . 
                                      "【昵称】：" . $obj->{"data"}->{"nickname"} . "\n" . 
                                      "【学校】：" . $obj->{"data"}->{"college"} . "\n" . 
                                      "【校区】：" . $obj->{"data"}->{"campus"} . "\n" . 
                                      "【电话】：" . $obj->{"data"}->{"tel"} . "\n" .
                                      '<a href="http://1.mallschoolwx.sinaapp.com/pages/userinfo.php?openid=' . $this->getOpenid() . '">点击修改个人信息</a>');
              }
          } else if ($event_key == 'MY_BOOK_INFO') {
              $url='http://api.jige.olege.com/wsells?openid=' . $this->getOpenid();
              $html = file_get_contents($url);
              $obj = json_decode($html);
              if($obj->{"result"} == "1000"){
                  $this->responseText("抱歉，获取个人信息异常，请稍后重试。");
                  return;
              }else{
                  if (!isset($obj->{"data"}[0])) {
                      $this->responseText("您还没有出售信息");
                      return;
                  }
                  $index = 0;
                  $book_info = "";
                  while (isset($obj->{"data"}[$index])) {
                      $book_info = $book_info . "《" . $obj->{"data"}[$index]->{"name"} . "》\n";
                      $index++;
                      if ($index > 2) {
                          $book_info = $book_info . "...\n";
                      }
                  }
                  
                  $this->responseText("您当前的在售图书信息：\n" . 
                                      $book_info .
                                      '<a href="http://1.mallschoolwx.sinaapp.com/pages/mysellinfo.php?openid=' . $this->getOpenid() . '">点击查看详细信息</a>');
                  //$this->responseText($obj->{"data"}[0]->{"name"});
              }
          }
      }
    
    /**
     * 收到文本消息时触发，回复收到的文本消息内容
     *
     * @return void
     */
    protected function onText() {
        $content = $this->getRequest('content');
        
        // 测试接口。格式: test:<arg1>:<arg2>...
        $array = explode(':', $content);
        if ( $array[0] == 'test' ) {
            $this->runTestCommand($array);
            return;
        }
        
        $this->responseText('收到了文字消息：' . $this->getRequest('content'));
    }

    /**
     * 收到图片消息时触发，回复由收到的图片组成的图文消息
     *
     * @return void
     */
    protected function onImage() {
      $items = array(
        new NewsResponseItem('标题一', '描述一', $this->getRequest('picurl'), $this->getRequest('picurl')),
        new NewsResponseItem('标题二', '描述二', $this->getRequest('picurl'), $this->getRequest('picurl')),
      );

      $this->responseNews($items);
    }

    /**
     * 收到地理位置消息时触发，回复收到的地理位置
     *
     * @return void
     */
    protected function onLocation() {
      $num = 1 / 0;
      // 故意触发错误，用于演示调试功能

      $this->responseText('收到了位置消息：' . $this->getRequest('location_x') . ',' . $this->getRequest('location_y'));
    }

    /**
     * 收到链接消息时触发，回复收到的链接地址
     *
     * @return void
     */
    protected function onLink() {
      $this->responseText('收到了链接：' . $this->getRequest('url'));
    }

    /**
     * 收到未知类型消息时触发，回复收到的消息类型
     *
     * @return void
     */
    protected function onUnknown() {
      $this->responseText('收到了未知类型消息：' . $this->getRequest('msgtype'));
    }
      
      private function runKvdbTest($command) {
          $kvdb = new SaeKV();
          $kvdb->init();
          if ( empty($command[2]) ) {
              $this->responseText('请输入正确的命令格式');
              return;
          }
          switch ( $command[2] ) {
              case 'set':
                  if ( empty($command[3]) || empty($command[4]) ) {
                      $this->responseText('请输入正确的命令格式');
                      return;
                  }
                  if ( $kvdb->set( $command[3], $command[4] ) ) {
                      $this->responseText('KVDB Set 成功');
                  } else {
                      $this->responseText('KVDB Set 失败：' . $kvdb->errmsg());
                  }
                  break;
              case 'get':
                  if ( empty($command[3]) ) {
                      $this->responseText('请输入正确的命令格式');
                      return;
                  }
                  if ( $ret = $kvdb->get( $command[3] ) ) {
                      $this->responseText('KVDB Get 成功：' . $ret);
                  } else {
                      $this->responseText('KVDB Get 失败：' . $kvdb->errmsg());
                  }
                  break;
              default:
                  $this->responseText('不支持此KVDB命令');
                  break;
          }
      }
      
      private function runDefaultTest($command) {
          // 测试获取access token接口
          //$this->responseText( 'access_token:' . $this->getAccessToken() );
          
          // 测试图文消息接口
          
          $items = array(
              new NewsResponseItem('标题一', '描述一', "http://1.mallschoolwx.sinaapp.com/source/smalllogo.png", "http://1.mallschoolwx.sinaapp.com/pages/mysellinfo.php"),
              //new NewsResponseItem('标题二', '描述二', "http://1.mallschoolwx.sinaapp.com/source/smalllogo.png", $this->getRequest('picurl')),
          );
          $this->responseNews($items);
          
          
          // 测试客服消息接口
          //$this->responseText( $this->postCustomMsg('Hello') );
          //$this->postCustomMsg('World');
          
          // HTML内容回复测试
          //$this->responseText( "\ue415" );

      }
      
      private function runTestCommand($command) {
          if ( empty($command[1]) ) {
              $this->runDefaultTest( $command );
              return;
          }
              
          switch ( $command[1] ) {
              case 'kvdb':
                  $this->runKvdbTest( $command );
                  break;
              default:
                  $this->responseText('不支持此测试命令');
                  break;
          }
      }
  }

  $wechat = new MyWechat('weixin', TRUE);
  $wechat->run();
