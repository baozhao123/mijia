<?php
/**
 * 专栏模块微站定义
 *
 * @author xingkai
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
define('HCKJ_ZL',IA_ROOT.'/addons/hckj_zl/');
require HCKJ_ZL . 'class/qiniusdk/autoload.php';
use Qiniu\Auth;  
use Qiniu\Storage\UploadManager; 
set_time_limit(0);
//error_reporting (E_ALL);
class hckj_zlModuleSite extends WeModuleSite {
	//用户取消订阅模板消息推送
    public function Xxdy() {
        global $_GPC;
        global $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        //用户取消退订代码
        $gjz_first = pdo_fetchall("SELECT * FROM ".tablename('stat_msg_history')." WHERE uniacid = '{$uniacid}' ORDER BY id DESC"); //这里是先获取了所有的关键字再来进行排查。看服务器运行速度，OK的话就这么干了，不行就找其他方法。
        foreach($gjz_first as $k => $gjz) {  //既然这里是筛选了所有的数据了，导致用户只要取消退订过一次就一直取消退订了
            $msg = $gjz;
            $gjz = $gjz['message']; //此时得到的是一个被序列化的数组，serialize---序列化：serialize：就是将数组转为字符串，方便存储在数据库。
            $gjz = unserialize($gjz);  //这里使用unserialize来进行返序列化
            //关键字
            $gjz = $gjz['content'];
            //uid
            $uid = $msg['uniacid'];
            //发送关键字的用户openid
            $from_user = $msg['from_user'];
            if($gjz == '110') {
                $result = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka_vip')." WHERE uniacid = '{$uid}' AND openid = '{$from_user}'");
                $data_qx['status'] = 1;
                foreach($result as $k => $val) {
                    pdo_update('mijia_daka_vip', $data_qx, array('id' => $val['id']));
                }
            }
        }
    }

    //手机端任务列表页
    public function doMobileIndex() {
        global $_GPC;
        global $_W;
        load()->func('tpl');

        if (empty($_W['openid'])) {
            die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
        }
        
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $gs = $_W['account']['name'];
        $class_id=$_GPC['class_id'];
        $into = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka_class')."WHERE uniacid = '{$uniacid}'");

        $this -> Xxdy();

        mc_oauth_userinfo();  //此方法可以在分享给朋友，朋友圈后，让进来的用户授权登录
        //判断是否过期
        $time_vip = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_vip')." WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
        $pay_vip = pdo_fetch("SELECT * FROM ".tablename('mijia_list_pay')." WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' AND status = 1");
        if($time_vip['endtime'] < time()) {
            $result = pdo_query("DELETE FROM " . tablename('mijia_daka_vip') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $time_vip['id'], ':uniacid' => $uniacid));
        }
        if($pay_vip['endtime'] < time()) {
            $data['endtime'] = 0;
            $result_pay = pdo_update('mijia_list_pay', $data, array('id' => $pay_vip['id']));
        }
        /*  这里可以做成吸粉版
        $activity = pdo_fetch("SELECT * FROM ".tablename('hckj_edit_vote')." WHERE id = '{$id}'");
        if($activity['xf'] == 1) {
            $user = $_W['fans']['follow'];
            if($user != 1){
                message('请先关注',"http://mp.weixin.qq.com/s?__biz=MzI4MjAyODU5MA==&mid=2648886939&idx=1&sn=f28a1fa05be71a69a2f5f7b6528bfe71&chksm=f3b651dac4c1d8cc7820d289f23019aa5464f36123a8d3cbf5695b3ada5a17cdc62769abf701#rd", 'success');
            }
        }
        */

        if($_POST) {
            $keyword = $_GPC['keyword'];
        }
        if($keyword) {
            $info = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka')." WHERE uniacid = '{$uniacid}' AND name like '%{$keyword}%' ORDER BY sort DESC");
            if(empty($info)) {
                $info = 2;
            }
        }else{
            $class_id=$_GPC['class_id'];
            if($class_id){
                $info = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka')." WHERE uniacid = '{$uniacid}' AND class_id = '{$class_id}'  ORDER BY sort DESC");
            }else{
                $info = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka')." WHERE uniacid = '{$uniacid}' ORDER BY sort DESC");
            }
            
        }
        

        include $this->template('list_people');
    }

    //手机端人物详情页
    public function doMobileIndexList() {
        global $_GPC;
        global $_W;
        load()->func('tpl');

        if (empty($_W['openid'])) {
            die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
        }

        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $id = $_GPC['id'];

		//echo $id;exit;
		//$sun = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE id = '{$id}' AND uniacid = '{$uniacid}'");



        $op = $_GPC['op'];
        $more = $_GPC['more'];

        $info_list_msg = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE id = '{$id}' AND uniacid = '{$uniacid}'");
		//print_r($info_list_msg);exit;


		//var_dump($id);
        $dyxz = $info_list_msg['before_know'];
        $dyxz = explode('$$$',$dyxz);

		//赚栏简介
		$dyxzz = $info_list_msg['introduce'];
        $dyxzz = explode('$$$',$dyxzz);




        //看看进来的是不是老师
        $user_admin = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE openid = '{$openid}' AND id = '{$id}'");

        //分页输出
        // $pindex = max(intval($_GPC['page']),1);
        // $psize = 8;
        //$info_aduio = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE openid = '{$openid}' AND pid_id = '{$id}' ORDER BY id DESC  ");
        if($op=='minute'){
            $cellect = pdo_fetchall("SELECT * FROM ".tablename('mijia_list_pay')." WHERE pid_id = '{$id}' AND status = 1 AND uniacid = '{$uniacid}' ORDER BY id DESC  ");
            foreach($cellect as $key => $val){
                $info_cellect[] = pdo_get('mijia_daka_audio', array('id' => $cellect[$key]['pid']));
            }
        }
        if(!empty($more)){
		 $info_aduio = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE pid_id = '{$id}' ");
        }
        else{
         $info_aduio = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE pid_id = '{$id}' LIMIT 0 ,7 ");
        }
		$dakaid = $info_list_msg['id'];
		$list = pdo_fetch("SELECT * FROM".tablename("mijia_list_pays")."WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' AND pid_id = '{$dakaid}' ");
		$listd = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE uniacid = '{$uniacid}' AND id = '{$id}' ");
			 
		$bignumber = $listd['dakanumber'];

		$data = pdo_fetchAll("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' AND pid_id = '{$id}' ");
		$zongnumber = count($data);

		//重新
		//第一步：查mijia_daka_audio的视频
        include $this->template('list_people_info');
    }

    //手机端内容详情页
    public function doMobileIndexListInfo() {
        global $_GPC;
        global $_W;
        load()->func('tpl');
        if (empty($_W['openid'])) {
            die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
        }

        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $this -> Xxdy();
        //语音的ID主键
        $id = $_GPC['id'];//音频的自增id

        $info = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE id = '{$id}' AND uniacid = '{$uniacid}'");
		//print_r($info);
		$stu = $info['stu'];
		//判断音频是本地的还是七牛云上的
		if($stu == 0){
			//本地音频
			$beautiful = $info['music'];
		}else{
			//七牛音频
			//require_once './qiniusdk/autoload.php';
			//use Qiniu\Auth;
			//$accessKey = 'zwNtL9MeYlguEwQLlx5JkDvU50NYxVuRR1UizYCr';
			//$secretKey = 'ukisbjQTkFNsSJvsfmoMEpHAgZ7j16rrhHQ5Umcp';
			//$auth = new Auth($accessKey, $secretKey);
			//$baseUrl = 'http://oo8jka7n0.bkt.clouddn.com/1511503856.jpg';
			//$authUrl = $auth->privateDownloadUrl($baseUrl);
			//echo $authUrl;
			$uniacid = $_W['uniacid'];
			$set = pdo_fetch("SELECT * FROM".tablename("mijia_daka_set")."WHERE uniacid = '{$uniacid}' ");
			$accessKey = $set['accesskey'];
			$secretKey = $set['secretkey'];
			$link = $set['link'];
			$links = "http://".$link;
			$name = $info['music'];
			//$nameurl = $links."/".$name;
			//echo $nameurl;exit;
			//echo $links;exit;
			//oo8jka7n0.bkt.clouddn.com
			$auth = new Auth($accessKey, $secretKey);
			$baseUrl = $links."/".$name;
			$authUrl = $auth->privateDownloadUrl($baseUrl);
			$beautiful = $authUrl;
		}
        //在这里更新播放次数与评论次数
        if($info) {
            $data_updata['open_time']  = $info['open_time'] + 1;
            $discuss_time = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mijia_daka_discuss') . " WHERE uniacid = '{$uniacid}' AND pid_id = '{$id}' AND status = 1");
            $data_updata['discuss_time']  = $discuss_time;
            pdo_update('mijia_daka_audio',$data_updata,array('id' => $id));
        }

        $info_zl = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE openid = '{$info['openid']}' AND id = '{$info['pid_id']}'");


        //var_dump($info_zl);
        $pid_id = $info_zl['id'];
        $pid = $info['id'];
        $take = pdo_fetchall("SELECT * FROM ".tablename('mijia_list_pay')." WHERE status = 1 AND uniacid = '{$uniacid}' AND pid_id = '{$pid_id}' AND pid='{$pid}' AND openid = '{$openid}' ");


		$takes = pdo_fetchall("SELECT * FROM ".tablename('mijia_list_pays')." WHERE status = 1 AND uniacid = '{$uniacid}' AND pid_id = '{$pid_id}' AND openid = '{$openid}' ");

		//print_r($takes);

        //$info_vip = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_vip')." WHERE openid = '{$openid}' AND uniacid = '{$uniacid}' AND pid_id = '{$info_zl['id']}'");

		$info_vip = pdo_fetch("SELECT * FROM ".tablename('mijia_list_pay')." WHERE openid = '{$openid}' AND uniacid = '{$uniacid}' AND pid = '{$info['id']}'");


        if($info_vip) {
            $listen = 'yes';
        }
        else {
            $listen = 'no';
        }

        $discuss_show = pdo_fetchall(" SELECT * FROM ".tablename('mijia_daka_discuss')." WHERE uniacid = '{$uniacid}' AND pid_id = '{$id}' AND status = 1");

		$useropenid = $_W['openid'];

        include $this->template('new_class_information');
    }
	//手机端上传课程修改
	public function doMobileIndexAudios(){
		global $_W,$_GPC;
		if (empty($_W['openid'])) {
			die("<!DOCTYPE html>
				<html>
						<head>
							<meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
							<title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
						</head>
						<body>
						<div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
						</body>
					</html>");
			}

			$ops = array('display','edit','delete','add'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			//默认
			if($op == 'edit'){
				$pid = $_GPC['pid'];//音频表中的自增id
				$uniacid = $_W['uniacid'];
				$into = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE id = '{$pid}' AND uniacid = '{$uniacid}'");
				$class_ids = $into['pid_id'];
				$classdata = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE id = '{$class_ids}' AND uniacid = '{$uniacid}'");

				if(checksubmit('submit')){
					$id = $_GPC['pid'];
					$uniacid = $_W['uniacid'];
					$intos = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE id = '{$id}' AND uniacid = '{$uniacid}'");
					$class_id = $intos['pid_id'];
					$stu = $intos['stu'];
					if($stu == 0){
						//echo 0;die;
						//本地
						$data['title'] = $_GPC['title'];
						$data['beizhu'] = $_GPC['beizhu'];
						$data['info'] = $_GPC['info'];
						$data['info_money'] = $_GPC['info_money'];
						$beautmusic = $_FILES['music'];
						if($beautmusic['name']){
							//先删除原先的音频
							$music_url = dirname(__FILE__)."/template/mobile/mp3/";
							//echo $music_url;exit;
							$dele = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE id = '{$id}' AND uniacid = '{$uniacid}'");
							$music =  $music_url.$dele['music'];
							if(file_exists($music)){
								$delc = unlink($music);
								if($delc){
									$music_name = $_FILES['music']['name'];
									$fenge = explode('.',$music_name);
									//print_r($music_name);exit;
									$music_name = '.'.$fenge[1];
									$time = time();
									$music_url = MODULE_URL."template/mobile/mp3/";
									if(is_uploaded_file($_FILES['music']['tmp_name'])) {
										$result = move_uploaded_file($_FILES['music']['tmp_name'],'../addons/hckj_zl/template/mobile/mp3/'.$time.$music_name);
										$data['music'] = $time.$music_name;
									}
								}
							}
						}else{
							//没有修改音频
							$data['music'] = $intos['music'];
						}

						$result = pdo_update('mijia_daka_audio', $data, array('id' => $id));
						if (!empty($result)) {
						  message('修改成功', $this->createMobileUrl('IndexList', array('id' => $class_id), 'success'));
						} 

					}else{
						//七牛上
						//echo 11111;
						$data['title'] = $_GPC['title'];
						$data['beizhu'] = $_GPC['beizhu'];
						$data['info'] = $_GPC['info'];
						$data['info_money'] = $_GPC['info_money'];
						$beautmusic = $_FILES['music'];
						if($beautmusic['name']){
							//先删除七牛云上的音频
							//if($err){
								//删除过了，在上传。
								$list = pdo_fetch("SELECT * FROM".tablename("mijia_daka_set")."WHERE uniacid = '{$uniacid}' ");
								$accessKey = $list['accesskey'];
								$secretKey = $list['secretkey'];
								$auth = new Auth($accessKey, $secretKey);
								$bucket = $list['qnname'];
								$token = $auth->uploadToken($bucket);
								$uploadMgr = new UploadManager(); 
								$music = $_FILES['music'];
								$music_name = $_FILES['music']['name'];
								$fenge = explode('.',$music_name);
								$music_name = '.'.$fenge[1];
								$time = time();
								//$time.$music_name;
								$filePath = $_FILES['music']['tmp_name'];
								$key = $time.$music_name;
								list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
								//插入数据库
								$data['music'] = $key;
								$result = pdo_update('mijia_daka_audio', $data, array('id' => $id));
								if (!empty($result)) {
									$set = pdo_fetch("SELECT * FROM".tablename("mijia_daka_set")."WHERE uniacid = '{$uniacid}' ");
									$accessKey = $set['accesskey'];
									$secretKey = $set['secretkey'];
									$bucket = $set['qnname'];
									$key = $intos['music'];	
									$auth = new Auth($accessKey, $secretKey);
									$config = new \Qiniu\Config();
									$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
									$err = $bucketManager->delete($bucket, $key);
									message('修改成功', $this->createMobileUrl('IndexList', array('id' => $class_id), 'success'));
								}
							//}
						}else{
							//没有修改音频
							$data['music'] = $intos['music'];
							$result = pdo_update('mijia_daka_audio', $data, array('id' => $id));
							if (!empty($result)) {
							  message('修改成功', $this->createMobileUrl('IndexList', array('id' => $class_id), 'success'));
							}
						}
						//修改成功
						
					}
				}	
				//print_r($into);
				load()->func('tpl');
				include $this->template('audio_inputs');
			}
	}


	//手机端上传视频
	public function doMobileIndexAudio(){
		global $_GPC;
        global $_W;
		if (empty($_W['openid'])) {
            die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
        }


		$uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $id = $_GPC['id'];
		//echo $id;
        $pid = $_GPC['pid'];//mijia_daka_audio
        $tid = $_GPC['tid'];
		if($pid){
           $into = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE id = '{$pid}' AND openid = '{$openid}'");
			//print_r($into);
        }
		//塞数据进入数据库
		$info_main = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE id = '{$id}' AND openid = '{$openid}'");
		if(checksubmit('submit')) {
			/*$accessKey = 'zwNtL9MeYlguEwQLlx5JkDvU50NYxVuRR1UizYCr';  
			$secretKey = 'ukisbjQTkFNsSJvsfmoMEpHAgZ7j16rrhHQ5Umcp';  
			$auth = new Auth($accessKey, $secretKey);
			$bucket = 'zhibo';  
			$token = $auth->uploadToken($bucket);
			//print_r($token);exit;
			$uploadMgr = new UploadManager(); 
			$music = $_FILES['music'];
			$music_name = $_FILES['music']['name'];
			$fenge = explode('.',$music_name);
			$music_name = '.'.$fenge[1];
			$time = time();
			//$time.$music_name;
			$filePath = $_FILES['music']['tmp_name'];
			$key = $time.$music_name;
			list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
			echo "\n====> putFile result: \n";*/

			$uniacid = $_W['uniacid'];
			$list = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_set')." WHERE uniacid = '{$uniacid}'");
			$yess = $list['status'];
			//print_r($list);exit;


			if($yess == 0){//未启用
				$music = $_FILES['music'];
				$music_name = $_FILES['music']['name'];
				$fenge = explode('.',$music_name);
				$music_name = '.'.$fenge[1];
				$music_url = MODULE_URL."template/mobile/mp3/";
				$time = time();
				 if(is_uploaded_file($_FILES['music']['tmp_name'])) {
                $result = move_uploaded_file($_FILES['music']['tmp_name'],'../addons/hckj_zl/template/mobile/mp3/'.$time.$music_name);
              //  echo $_FILES['music']["error"].'1111111111'.$music_name;exit;
                $data['pid_id'] = $id;
                $data['music'] = $time.$music_name;
                $data['title'] = $_GPC['title'];
                $data['beizhu'] = $_GPC['beizhu'];
                $data['createtime'] = time();
                $data['openid'] = $openid;
                $data['uniacid'] = $uniacid;
                $data['info'] = $_GPC['info'];
				$data['info_money'] = $_GPC['info_money'];
				$data['stu'] = 0;//代表是存在本地的音频

				//var_dump($data);exit;
            }
           if(empty($_GPC['title']) || empty($_GPC['beizhu'])){
               message('名称或简介不得为空！', $this->createMobileUrl('IndexList', array('id' => $id), 'error'));
               //message('名称或简介不得为空！');
           }
            $result = pdo_insert('mijia_daka_audio',$data);
            $id_new = pdo_insertid();
            $info_xx = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
            $openid_user = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka_vip')." WHERE uniacid = '{$uniacid}' AND pid_id = '{$info_xx['id']}' AND status = 0;");

            $siteroot = $_W['siteroot'];
            $acid = $_W['acid'];
            $url = $siteroot.'app/index.php?i='.$uniacid.'&j='.$acid.'&c=entry&id='.$id_new.'&do=IndexListInfo&m=hckj_zl';
            if($result) {
                $templateid	    = "9H21gYTCGBI_SIDw5YPwvaGGpoebHI4qzHsCq2OwkJM";   // 课程更新通知的模板 写死了
				//$url = '';
                $data_xx = array(
                    'first' => array(
                        'value' => "您订阅的".$info_xx['title']."专栏更新了！",
                    ),
                    'keyword1' => array(
                        'value' => $data['title'],
                    ),
                    'keyword2' => array(
                        'value' => date('m月d日 H点m分',time()),
                    ),
                    'keyword3' => array(
                        'value' => $info_xx['name'],
                    ),
                    'remark' => array(
                        'value' => "请点击详情进入课程页面开始收听",
                    ),

                );
                foreach($openid_user as $k => $val) {
                    $this->sendtpl2($val['openid'], $url, $templateid, $data_xx);//发送给订阅者
                }
                message('新增成功', $this->createMobileUrl('IndexList', array('id' => $id), 'success'));
            }
////已启用//已启用//已启用//已启用//已启用//已启用//已启用//已启用//已启用
			}elseif($yess == 1){//已启用
			/*$accessKey = 'zwNtL9MeYlguEwQLlx5JkDvU50NYxVuRR1UizYCr';  
			$secretKey = 'ukisbjQTkFNsSJvsfmoMEpHAgZ7j16rrhHQ5Umcp';  
			$auth = new Auth($accessKey, $secretKey);
			//print_r($auth);die;
			$bucket = 'zhibo';  
			$token = $auth->uploadToken($bucket);
			//print_r($token);die;
			$uploadMgr = new UploadManager(); 
			$music = $_FILES['music'];
			$music_name = $_FILES['music']['name'];
			$fenge = explode('.',$music_name);
			$music_name = '.'.$fenge[1];
			$time = time();
			//$time.$music_name;
			$filePath = $_FILES['music']['tmp_name'];
			$key = $time.$music_name;
			//echo $key;exit;
			list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
			echo "\n====> putFile result: \n";*/
			
				//开始
				$accessKey = $list['accesskey'];
				$secretKey = $list['secretkey'];
				$auth = new Auth($accessKey, $secretKey);
				$bucket = $list['qnname'];
				$token = $auth->uploadToken($bucket);
				$uploadMgr = new UploadManager(); 
				$music = $_FILES['music'];
				$music_name = $_FILES['music']['name'];
				$fenge = explode('.',$music_name);
				$music_name = '.'.$fenge[1];
				$time = time();
				//$time.$music_name;
				$filePath = $_FILES['music']['tmp_name'];
				$key = $time.$music_name;
				list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
				//echo "\n====> putFile result: \n";  
				//插入数据库
				$data['pid_id'] = $id;
                $data['music'] = $key;
                $data['title'] = $_GPC['title'];
                $data['beizhu'] = $_GPC['beizhu'];
                $data['createtime'] = time();
                $data['openid'] = $openid;
                $data['uniacid'] = $uniacid;
                $data['info'] = $_GPC['info'];
				$data['info_money'] = $_GPC['info_money'];
				$data['stu'] = 1;//代表是存在七牛的音频
				//加约束
				if(empty($_GPC['title']) || empty($_GPC['beizhu'])){
					message('名称或简介不得为空！', $this->createMobileUrl('IndexList', array('id' => $id), 'error'));
				}
				//插入数据库
				$result = pdo_insert('mijia_daka_audio',$data);
				$id_new = pdo_insertid();
				$info_xx = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
				$openid_user = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka_vip')." WHERE uniacid = '{$uniacid}' AND pid_id = '{$info_xx['id']}' AND status = 0;");

				$siteroot = $_W['siteroot'];
				$acid = $_W['acid'];
				$url = $siteroot.'app/index.php?i='.$uniacid.'&j='.$acid.'&c=entry&id='.$id_new.'&do=IndexListInfo&m=hckj_zl';
				if($result) {
					$templateid	    = "9H21gYTCGBI_SIDw5YPwvaGGpoebHI4qzHsCq2OwkJM";   // 课程更新通知的模板 写死了
					//$url = '';
					$data_xx = array(
						'first' => array(
							'value' => "您订阅的".$info_xx['title']."专栏更新了！",
						),
						'keyword1' => array(
							'value' => $data['title'],
						),
						'keyword2' => array(
							'value' => date('m月d日 H点m分',time()),
						),
						'keyword3' => array(
							'value' => $info_xx['name'],
						),
						'remark' => array(
							'value' => "请点击详情进入课程页面开始收听",
						),

					);
					foreach($openid_user as $k => $val) {
						$this->sendtpl2($val['openid'], $url, $templateid, $data_xx);//发送给订阅者
					}
					message('新增成功', $this->createMobileUrl('IndexList', array('id' => $id), 'success'));
				}	
			}
        }


		 //删除语音
        $did = $_GPC['did'];
		//echo $did;exit;
        if($did){
            $music_url = dirname(__FILE__)."/template/mobile/mp3/";
			//echo $music_url;exit;
            $dele = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE id = '{$did}' AND uniacid = '{$uniacid}'");
            $music =  $music_url.$dele['music'];
            if(file_exists($music)){
                $delc = unlink($music);
                if($delc){
                    $result = pdo_query("DELETE FROM " . tablename('mijia_daka_audio') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $did, ':uniacid' => $uniacid));
                        if (!empty($result)) {
                            //message('');
							//message('c', $this->createMobileUrl('IndexList', array('id' => $id), 'success'));

							message("删除成功",$this->createMobileUrl("Index"),"success");
                        }

                    }
                }
            }


        load()->func('tpl');
		include $this->template('audio_input');
	}

    //手机端语音上传
    public function doMobileIndexAudioss() {
        global $_GPC;
        global $_W;
        load()->func('tpl');

        if (empty($_W['openid'])) {
            die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
        }

        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $id = $_GPC['id'];
        $pid = $_GPC['pid'];//mijia_daka_audio
        $tid = $_GPC['tid'];

        if($pid){
           $into = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE id = '{$pid}' AND openid = '{$openid}'");
			//print_r($into);
        }
       
       //更新数据
        //if($tid){
			//上传音乐
			$music = $_FILES['music'];
			//if($music){
				$music_name = $_FILES['music']['name'];
				$fenge = explode('.',$music_name);
			   // print_r($music_name);exit;
				$music_name = '.'.$fenge[1];
				$time = time();
				$music_url = MODULE_URL."template/mobile/mp3/";
				if(is_uploaded_file($_FILES['music']['tmp_name'])) {
					$result = move_uploaded_file($_FILES['music']['tmp_name'],'../addons/hckj_zl/template/mobile/mp3/'.$time.$music_name);
					$data['title'] = $_GPC['title'];
					$data['beizhu'] = $_GPC['beizhu'];
					$data['info'] = $_GPC['info'];
					$data['info_money'] = $_GPC['info_money'];
					$data['music'] = $time.$music_name;
				}else{
					$uniacid = $_W['uniacid'];
					$listmusic = pdo_fetch(" SELECT * FROM ".tablename('mijia_daka_audio')." WHERE uniacid = '{$uniacid}' AND id = '{$pid}' ");
					$data['title'] = $_GPC['title'];
					$data['beizhu'] = $_GPC['beizhu'];
					$data['info'] = $_GPC['info'];
					$data['info_money'] = $_GPC['info_money'];
					$data['music'] = $listmusic['music'];
				}





            $result = pdo_update('mijia_daka_audio', $data, array('id' => $tid));
            if (!empty($result)) {
               message('修改成功', $this->createMobileUrl('IndexList', array('id' => $id), 'success'));
            } 
        //}
		//var_dump($id);
        $info_main = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE id = '{$id}' AND openid = '{$openid}'");
		//var_dump($info_main);
        if(checksubmit('submit')) {
            $music = $_FILES['music'];
            $music_name = $_FILES['music']['name'];
            $fenge = explode('.',$music_name);
           // print_r($music_name);exit;
            $music_name = '.'.$fenge[1];
            $music_url = MODULE_URL."template/mobile/mp3/";
            $time = time();

            if(is_uploaded_file($_FILES['music']['tmp_name'])) {
                $result = move_uploaded_file($_FILES['music']['tmp_name'],'../addons/hckj_zl/template/mobile/mp3/'.$time.$music_name);
              //  echo $_FILES['music']["error"].'1111111111'.$music_name;exit;
                $data['pid_id'] = $id;
                $data['music'] = $time.$music_name;
                $data['title'] = $_GPC['title'];
                $data['beizhu'] = $_GPC['beizhu'];
                $data['createtime'] = time();
                $data['openid'] = $openid;
                $data['uniacid'] = $uniacid;
                $data['info'] = $_GPC['info'];
				$data['info_money'] = $_GPC['info_money'];
            }
           if(empty($_GPC['title']) || empty($_GPC['beizhu'])){
               message('名称或简介不得为空！', $this->createMobileUrl('IndexList', array('id' => $id), 'error'));
               //message('名称或简介不得为空！');
           }
            $result = pdo_insert('mijia_daka_audio',$data);
            $id_new = pdo_insertid();
            $info_xx = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
            $openid_user = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka_vip')." WHERE uniacid = '{$uniacid}' AND pid_id = '{$info_xx['id']}' AND status = 0;");

            $siteroot = $_W['siteroot'];
            $acid = $_W['acid'];
            $url = $siteroot.'app/index.php?i='.$uniacid.'&j='.$acid.'&c=entry&id='.$id_new.'&do=IndexListInfo&m=hckj_zl';

            if($result) {

                $templateid	    = "9H21gYTCGBI_SIDw5YPwvaGGpoebHI4qzHsCq2OwkJM";   // 课程更新通知的模板 写死了
             //   $url	    = '';
                $data_xx = array(
                    'first' => array(
                        'value' => "您订阅的".$info_xx['title']."专栏更新了！",
                    ),
                    'keyword1' => array(
                        'value' => $data['title'],
                    ),
                    'keyword2' => array(
                        'value' => date('m月d日 H点m分',time()),
                    ),
                    'keyword3' => array(
                        'value' => $info_xx['name'],
                    ),
                    'remark' => array(
                        'value' => "请点击详情进入课程页面开始收听",
                    ),

                );
                //这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！这里还要找晶晶在试试，看看能不能成功！
                foreach($openid_user as $k => $val) {
                    $this->sendtpl2($val['openid'], $url, $templateid, $data_xx);//发送给订阅者
                }



                message('新增成功', $this->createMobileUrl('IndexList', array('id' => $id), 'success'));
            }

        }
        //删除语音
        $did = $_GPC['did'];
        if($did){
            $music_url = dirname(__FILE__)."/template/mobile/mp3/";
            $dele = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_audio')." WHERE id = '{$did}' AND uniacid = '{$uniacid}'");
            $music =  $music_url.$dele['music'];
            if(file_exists($music)){
                $delc = unlink($music);
                if($delc){
                    $result = pdo_query("DELETE FROM " . tablename('mijia_daka_audio') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $did, ':uniacid' => $uniacid));
                        if (!empty($result)) {
                            message('删除成功');
                        }

                    }
                }
            }



        include $this->template('audio_input');
    }

    //PC端：人物专栏编辑
    public function doWebEdit()
    {
        global $_W;
        global $_GPC;
        load()->func('tpl');


        //判断是要输出哪个部分
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        $id = $_GPC['id'];    //若是编辑按钮转过来的这里会有参数id传过来
        $class_id = $_GPC['class_id'];
        $uniacid = $_W['uniacid'];

        $this -> Xxdy();

        if ($op == 'edit') {    //说明已经在编辑界面，肯定要编辑提交点什么东西了，先接收了再说看看是新插入还是更新
            if (checksubmit('submit')) {        //确认有没有提交
                $data['uniacid'] = $uniacid;
                $data['openid'] = $_GPC['openid_admin'];
                $data['title'] = $_GPC['title'];
                $data['small_title'] = $_GPC['small_title'];
                $data['name'] = $_GPC['name'];
                $data['beizhu'] = $_GPC['beizhu'];
                $data['money'] = $_GPC['money'];
                $data['pic_list'] = $_GPC['pic_list'];
                $data['pic_info'] = $_GPC['pic_info'];
                $data['introduce'] = $_GPC['introduce'];
                $data['pic_bg'] = $_GPC['pic_bg'];
                $data['before_know'] = $_GPC['before_know'];
                $data['sort'] = $_GPC['sort'];
                $data['tel'] = $_GPC['tel'];
                $data['class_id'] = $_GPC['class_id'];
                $data['moneycard'] = $_GPC['moneycard'];
                $data['dakanumber'] = $_GPC['dakanumber'];
                if (empty($id)) {    //属于新增
                    $data['createtime'] = time();
                    $res = pdo_insert('mijia_daka', $data);
                    $fanhui = pdo_insertid();

                    
                   

                    //生成微信二维码
                    $siteroot = $_W['siteroot'];
                    $uniacid = $_W['uniacid'];
                    $acid = $_W['acid'];
                    include IA_ROOT.'/framework/library/qrcode/phpqrcode.php';   //引用这个qr类库，这个是关键！
                    $url = $siteroot.'app/index.php?i='.$uniacid.'&j='.$acid.'&c=entry&id='.$fanhui.'&do=IndexList&m=hckj_zl'; //这是扫码之后的连接
                    $wxtimeinput = time();
                    $root_png = "../addons/hckj_zl/template/mobile/images/wxsaom".$wxtimeinput.".png";  //前台图片
                    QRcode::png($url,$root_png,1,2,2);  //122是调图片大小的

                    $data_2wm['ewm'] = $root_png;
                    pdo_update('mijia_daka', $data_2wm, array('id' => $fanhui));

                    if($res) {
                        message('新增成功', $this->doWebUrl('edit', array('op' => 'edit')), 'success');
                    }


                }
                else {    //属于更新
                    $data['endtime'] = time();
                    $result = pdo_update('mijia_daka', $data, array('id' => $id));
                    if (!empty($result)) {
                        message('修改成功', $this->createWebUrl('edit', array('op' => 'display')), 'success');
                    }
                }
            }
            else {    //没有提交说明是点“添加XX”或者是点“编辑”过来的，往这儿走，而且只有“编辑”过来的才有参数id才可以显现东西
                unset($info);    //销毁之前有的$info变量,以免干扰
                $info = pdo_get('mijia_daka', array('id' => $id));
                $class_name = pdo_fetchall("SELECT * FROM".tablename('mijia_daka_class')."WHERE uniacid='{$uniacid}'");
                if($class_name){
                    include $this->template('edit');exit;
                }
            }
        }
        elseif($op == 'class_info'){
            if (checksubmit('sub')) {        //确认有没有提交
                $data['class_name'] = $_GPC['class_name'];
                $data['uniacid'] = $uniacid;
                if (empty($class_id)) {    //属于新增
                    $res = pdo_insert('mijia_daka_class', $data);

                    if($res) {
                        message('新增成功', $this->createWebUrl('edit', array('op' => 'class')), 'success');
                    }

                }
                else {    //属于更新
                    $result = pdo_update('mijia_daka_class', $data, array('class_id' => $class_id));
                    if (!empty($result)) {
                        message('修改成功', $this->createWebUrl('edit', array('op' => 'class')), 'success');
                    }
                }
            }
            else {    //没有提交说明是点“添加XX”或者是点“编辑”过来的，往这儿走，而且只有“编辑”过来的才有参数id才可以显现东西
                unset($into);    //销毁之前有的$into变量,以免干扰
                $into = pdo_get('mijia_daka_class', array('class_id' => $class_id));

                include $this->template('edit');exit;
            }
        }


        
        //分页输出
        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;
        $edit_admin = pdo_fetchall(" SELECT * FROM " . tablename('mijia_daka') . " WHERE uniacid = '{$uniacid}' ORDER BY sort DESC,id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        foreach($edit_admin as $key=>$val){
            $edit_admin[$key]['class'] =  pdo_get('mijia_daka_class', array('class_id' => $edit_admin[$key]['class_id']));
        }    
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mijia_daka') . " WHERE uniacid = '{$uniacid}'");
        $pager = pagination($total, $pindex, $psize);

        $class = pdo_fetchall(" SELECT * FROM " . tablename('mijia_daka_class') . " WHERE uniacid = '{$uniacid}' ORDER BY class_id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $class_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mijia_daka_class') . " WHERE uniacid = '{$uniacid}'");
        $pag = pagination($class_total, $pindex, $psize);

        include $this->template('edit');
    }

    //PC端：人物专栏删除
    public function doWebEditdelete()
    {
        global $_W;
        global $_GPC;


        $del = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        load()->func('tpl');
        $req = pdo_fetchall(" SELECT * FROM " . tablename('mijia_daka_vip') . " WHERE pid_id = '{$del}' AND uniacid = '{$uniacid}'");
        if(!empty($req)){
            $res = pdo_query("DELETE FROM " . tablename('mijia_daka_vip') . " WHERE `pid_id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));
            $result = pdo_query("DELETE FROM " . tablename('mijia_daka') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));    
                if(!empty($result)&&!empty($res)){
                    message('删除成功', $this->createWebUrl('edit', array('op' => 'display'), 'success'));
            }
        }else{
            $result = pdo_query("DELETE FROM " . tablename('mijia_daka') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));
            if(!empty($result)){
                    message('删除成功', $this->createWebUrl('edit', array('op' => 'display'), 'success'));
                }
        }
        
    }

    //PC端：分类删除
    public function doWebDel()
    {
        global $_W;
        global $_GPC;


        $del = $_GPC['class_id'];
        $uniacid = $_W['uniacid'];
        load()->func('tpl');
        $res=pdo_get('mijia_daka', array('class_id' => $del));
        if($res){
                message('无法删除', $this->createWebUrl('edit', array('op' => 'class'), 'success'));
        }else{
            $result = pdo_query("DELETE FROM " . tablename('mijia_daka_class') . " WHERE `class_id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));
            if (!empty($result)) {
                message('删除成功', $this->createWebUrl('edit', array('op' => 'class'), 'success'));
            }
        }
    }

	//购买整章
	public function doMobileWxpayzheng(){
		global $_GPC;
        global $_W;
		$ops = array('display','add','open','pay'); 
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == 'display'){
			$id = $_GPC['id'];//大咖整章的id
			$money = $_GPC['money'];
			//echo $id;
			//echo $money;
			$openid = $_W['openid'];
			$uniacid = $_W['uniacid'];
			$people = pdo_fetch("SELECT * FROM".tablename('mijia_daka_vippeople')."WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' ");
			if($people){
				$endtime = $people['endtime'];
				$nowtime = time();
				if($nowtime>$endtime){//表示会员过期，不享受折扣
					$orderid = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
					$data['pid_id'] = $id;
					$data['openid'] = $openid;
					$data['uniacid'] = $uniacid;
					//$data['pid'] = $pid;
					$data['status'] = 0;//购买状态
					$data['createtime'] = time();
					$data['howmuch'] = $momey;
					$data['ordernumber'] = $orderid;
					$res = pdo_insert('mijia_list_pays',$data);
					if($res){
						message("去订单列表支付！",$this->createMobileUrl("Orderlists"),"success");
					}


				}else{//表示享受会员
					$agio = $people['agio'];
					$moneys = $agio*$money;
					$orderid = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
					$data['pid_id'] = $id;
					$data['openid'] = $openid;
					$data['uniacid'] = $uniacid;
					//$data['pid'] = $pid;
					$data['status'] = 0;//购买状态
					$data['createtime'] = time();
					$data['howmuch'] = $moneys;
					$data['ordernumber'] = $orderid;
					$res = pdo_insert('mijia_list_pays',$data);
					if($res){
						message("去订单列表支付！",$this->createMobileUrl("Orderlists"),"success");
					}
					

				}
			}else{//是直接价格
				$orderid = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
				$data['pid_id'] = $id;
				$data['openid'] = $openid;
				$data['uniacid'] = $uniacid;
				//$data['pid'] = $pid;
				$data['status'] = 0;//购买状态
				$data['createtime'] = time();
				$data['howmuch'] = $money;
				$data['ordernumber'] = $orderid;
				$res = pdo_insert('mijia_list_pays',$data);
				if($res){
					message("去订单列表支付！",$this->createMobileUrl("Orderlists"),"success");
				}

			}
		}
	}

	//单章节立即购买
	public function doMobileWxpays() {
        global $_GPC;
        global $_W;
		$ops = array('display','add','open','pay'); 
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		//默认
		if($op == 'display'){
			$howmuch = $_GPC['money'];//ims_mijia_daka_audio中的单价
			$pid_id = $_GPC['pid_id'];//是ims_mijia_daka中的自增id
			$openid = $_W['openid'];//谁购买的
			$uniacid = $_W['uniacid'];//模块号
			$pid = $_GPC['pid'];//是ims_mijia_daka_audio中的自增id
			$goods_title = $_GPC['goods_title'];//本章节的题目
			$vippeople = pdo_fetch("SELECT * FROM".tablename('mijia_daka_vippeople')."WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' ");
			if($vippeople){
				$endtime = $vippeople['endtime'];
				$nowtime = time();
				if($nowtime>$endtime){//表示会员过期,不享受折扣
					//echo $howmuch;
					$orderid = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
					$data['pid_id'] = $pid_id;
					$data['openid'] = $openid;
					$data['uniacid'] = $uniacid;
					$data['pid'] = $pid;
					$data['status'] = 0;//购买状态
					$data['createtime'] = time();
					$data['howmuch'] = $howmuch;
					$data['ordernumber'] = $orderid;
					$res = pdo_insert('mijia_list_pay',$data);
					if($res){
						message("去订单列表支付！",$this->createMobileUrl("Orderlist"),"success");
					}
				}else{//会员没有过期，享受折扣。
					$agio = $vippeople['agio'];
					$money = $agio*$howmuch;
					$orderid = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
					$data['pid_id'] = $pid_id;
					$data['openid'] = $openid;
					$data['uniacid'] = $uniacid;
					$data['pid'] = $pid;
					$data['status'] = 0;//购买状态
					$data['createtime'] = time();
					$data['howmuch'] = $money;
					$data['ordernumber'] = $orderid;
					$res = pdo_insert('mijia_list_pay',$data);
					if($res){
						message("去订单列表支付！",$this->createMobileUrl("Orderlist"),"success");
					}
				}

			}else{//表示此人不是会员，不可以享受折扣，按照原价
				//echo $howmuch;
				$orderid = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
				$data['pid_id'] = $pid_id;
				$data['openid'] = $openid;
				$data['uniacid'] = $uniacid;
				$data['pid'] = $pid;
				$data['status'] = 0;//购买状态
				$data['createtime'] = time();
				$data['howmuch'] = $howmuch;
				$data['ordernumber'] = $orderid;
				$res = pdo_insert('mijia_list_pay',$data);
				if($res){
					message("去订单列表支付！",$this->createMobileUrl("Orderlist"),"success");
				}

			}
		}
	}

	//手机端:用户充值订单显示
	public function doMobileChongorderinfo(){
		global $_W,$_GPC;
		$ops = array('display','edit','delete','shenhe'); // 只支持此 6 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "display"){
			$openid = $_W['openid'];
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 6; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']
			);

			//根据小区名查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`nickname` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_daka_chongorder').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);

			$sql = 'SELECT * FROM '.tablename('mijia_daka_chongorder')." {$where} AND paystute = 1 AND openid = '{$openid}' ORDER BY ctime desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$data = pdo_fetchall($sql, $params);
			//print_r($data);
			load()->func('tpl');
			include $this->template('pcchongorder');
		}

		//删除
		if($op == "delete"){
			$cid = $_GPC['cid'];
			//echo $cid;
			$res = pdo_delete('mijia_daka_chongorder',array('cid' => $_GPC['cid'],'uniacid' => $_W['uniacid']));
			if($res){
				echo 1;
			}
		}
	}

	//订阅详情面
	public function doMobileDingyue() {
        global $_GPC;
        global $_W;
		$ops = array('display','look','open','pay','delete'); 
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "display"){
			$openid = $_W['openid'];
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 6; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']
			);

			//根据小区名查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`nickname` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_list_pay').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);

			$sql = 'SELECT * FROM '.tablename('mijia_list_pay')." {$where} AND openid = '{$openid}' AND status = 1 ORDER BY createtime desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$data = pdo_fetchall($sql, $params);
			//print_r($data);

			if(empty($data)) {
				$nomeg = 1;
			}
			$uniacid = $_W['uniacid'];
			foreach ($data as $key => $value) {
				$power = $value['pid_id'];
				$data[$key]['roles'][]=pdo_fetch("SELECT * FROM".tablename('mijia_daka')."WHERE uniacid = '$uniacid' AND id = '{$power}'");
			}

			foreach ($data as $key => $value) {
				$power = $value['pid'];
				$data[$key]['roless'][]=pdo_fetch("SELECT * FROM".tablename('mijia_daka_audio')."WHERE uniacid = '$uniacid' AND id = '{$power}'");
			}

			//$take = pdo_fetchall("SELECT * FROM ".tablename('mijia_list_pay')." WHERE status = 1 AND uniacid = '{$uniacid}' AND pid_id = '{$pid_id}' AND pid='{$pid}' AND openid = '{$openid}' ");

			load()->func('tpl');
			include $this->template('dingyue');

		}

		//查看
		if($op == "look"){
			$pid = $_GPC['pid'];
			$uniacid = $_W['uniacid'];
			$list = pdo_fetch(" SELECT * FROM ".tablename('mijia_list_pay')." WHERE uniacid = '{$uniacid}' AND id = '{$pid}' ");
			$hid = $list['pid'];
			$sid = $list['pid_id'];
			$info = pdo_fetch(" SELECT * FROM ".tablename('mijia_daka_audio')." WHERE uniacid = '{$uniacid}' AND id = '{$hid}' ");
			$info_zl = pdo_fetch(" SELECT * FROM ".tablename('mijia_daka')." WHERE uniacid = '{$uniacid}' AND id = '{$sid}' ");
			//判断是七牛还是本地
			$stu = $info['stu'];
			if($stu == 0){
				$beautiful = $info['music'];
			}else{
				$uniacid = $_W['uniacid'];
				$set = pdo_fetch("SELECT * FROM".tablename("mijia_daka_set")."WHERE uniacid = '{$uniacid}' ");
				$accessKey = $set['accesskey'];
				$secretKey = $set['secretkey'];
				$link = $set['link'];
				$links = "http://".$link;
				$name = $info['music'];
				//$nameurl = $links."/".$name;
				//echo $nameurl;exit;
				//echo $links;exit;
				//oo8jka7n0.bkt.clouddn.com
				$auth = new Auth($accessKey, $secretKey);
				$baseUrl = $links."/".$name;
				$authUrl = $auth->privateDownloadUrl($baseUrl);
				$beautiful = $authUrl;
			}
			load()->func('tpl');
			include $this->template('dingyueclass');
		}
	}


	//支付订单列表====>整章
	public function doMobileOrderlists() {
        global $_GPC;
        global $_W;
		$ops = array('display','add','open','pay','delete'); 
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "display"){
			$openid = $_W['openid'];
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 6; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']
			);

			//根据小区名查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`nickname` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_list_pays').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);

			$sql = 'SELECT * FROM '.tablename('mijia_list_pays')." {$where} AND openid = '{$openid}' ORDER BY createtime desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$data = pdo_fetchall($sql, $params);
			//print_r($data);
			$uniacid = $_W['uniacid'];
			foreach ($data as $key => $value) {
				$power = $value['pid_id'];
				$data[$key]['roles'][]=pdo_fetch("SELECT * FROM".tablename('mijia_daka')."WHERE uniacid = '$uniacid' AND id = '{$power}'");
			}

			foreach ($data as $key => $value) {
				$power = $value['pid'];
				$data[$key]['roless'][]=pdo_fetch("SELECT * FROM".tablename('mijia_daka_audio')."WHERE uniacid = '$uniacid' AND id = '{$power}'");
			}

			load()->func('tpl');
			include $this->template('orderlists');

			
		}


		//删除
		if($op == "delete"){
			$cid = $_GPC['cid'];
			//echo $cid;exit;
			$res = pdo_delete('mijia_list_pays',array('id' => $_GPC['cid'],'uniacid' => $_W['uniacid']));
			if($res){
				echo 1;
			}
		}

		//支付===>直接从钱包扣掉
		if($op == "pay"){
			$cid = $_GPC['cid'];
			//echo $cid;
			$uniacid = $_W['uniacid'];
			$list = pdo_fetch("SELECT * FROM".tablename('mijia_list_pays')."WHERE uniacid = '$uniacid' AND id = '{$cid}'");
			$howmuch = $list['howmuch'];
			$pid_id = $list['pid_id'];
			$data = pdo_fetch("SELECT * FROM".tablename('mijia_daka')."WHERE uniacid = '$uniacid' AND id = '{$pid_id}'");
			$number = $data['number'];

			//查询钱包表
			$openid = $_W['openid'];
			$bag = pdo_fetch("SELECT * FROM".tablename('mijia_daka_bagmoney')."WHERE uniacid = '$uniacid' AND openid = '{$openid}'");
			$bagmoney = $bag['money'];//钱包里面的钱
			if($bagmoney<$howmuch){//钱包的钱不够
				echo 2;
				//message("余额不足，请先充值再支付！",$this->createMobileUrl("Chong"),"success");
			}else{
				$newmoney = $bagmoney - $howmuch;
				$lock['money'] = $newmoney;
				$res = pdo_update('mijia_daka_bagmoney', $lock, array('openid' => $openid,'uniacid' => $_W['uniacid']));
				if($res){
					//echo 1;
					$lockh['status'] = 1;
					$result = pdo_update('mijia_list_pays', $lockh, array('id' => $cid,'uniacid' => $_W['uniacid']));
					if($result){
						$nunbers = $number+1;
						$sunying['number'] = $nunbers;
						$results = pdo_update('mijia_daka', $sunying, array('id' => $pid_id,'uniacid' => $_W['uniacid']));
						if($results){
							echo 1;
						}
					}
				}
			}
		}

	}















	//支付订单列表====>单章
	public function doMobileOrderlist() {
        global $_GPC;
        global $_W;
		$ops = array('display','add','open','pay','delete'); 
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "display"){
			$openid = $_W['openid'];
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 6; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']
			);

			//根据小区名查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`nickname` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_list_pay').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);

			$sql = 'SELECT * FROM '.tablename('mijia_list_pay')." {$where} AND openid = '{$openid}' ORDER BY createtime desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$data = pdo_fetchall($sql, $params);
			//print_r($data);
			$uniacid = $_W['uniacid'];
			foreach ($data as $key => $value) {
				$power = $value['pid_id'];
				$data[$key]['roles'][]=pdo_fetch("SELECT * FROM".tablename('mijia_daka')."WHERE uniacid = '$uniacid' AND id = '{$power}'");
			}

			foreach ($data as $key => $value) {
				$power = $value['pid'];
				$data[$key]['roless'][]=pdo_fetch("SELECT * FROM".tablename('mijia_daka_audio')."WHERE uniacid = '$uniacid' AND id = '{$power}'");
			}

			load()->func('tpl');
			include $this->template('orderlist');

		}

		//删除
		if($op == "delete"){
			$cid = $_GPC['cid'];
			//echo $cid;
			$res = pdo_delete('mijia_list_pay',array('id' => $_GPC['cid'],'uniacid' => $_W['uniacid']));
			if($res){
				echo 1;
			}
		}

		//支付===>直接从钱包扣掉
		if($op == "pay"){
			$cid = $_GPC['cid'];
			//echo $cid;
			$uniacid = $_W['uniacid'];
			$list = pdo_fetch("SELECT * FROM".tablename('mijia_list_pay')."WHERE uniacid = '$uniacid' AND id = '{$cid}'");
			$howmuch = $list['howmuch'];
			$pid_id = $list['pid_id'];
			$data = pdo_fetch("SELECT * FROM".tablename('mijia_daka')."WHERE uniacid = '$uniacid' AND id = '{$pid_id}'");
			$number = $data['number'];

			//查询钱包表
			$openid = $_W['openid'];
			$bag = pdo_fetch("SELECT * FROM".tablename('mijia_daka_bagmoney')."WHERE uniacid = '$uniacid' AND openid = '{$openid}'");
			$bagmoney = $bag['money'];//钱包里面的钱
			if($bagmoney<$howmuch){//钱包的钱不够
				echo 2;
				//message("余额不足，请先充值再支付！",$this->createMobileUrl("Chong"),"success");
			}else{
				$newmoney = $bagmoney - $howmuch;
				$lock['money'] = $newmoney;
				$res = pdo_update('mijia_daka_bagmoney', $lock, array('openid' => $openid,'uniacid' => $_W['uniacid']));
				if($res){
					//echo 1;
					$lockh['status'] = 1;
					$result = pdo_update('mijia_list_pay', $lockh, array('id' => $cid,'uniacid' => $_W['uniacid']));
					if($result){
						$nunbers = $number+1;
						$sunying['number'] = $nunbers;
						$results = pdo_update('mijia_daka', $sunying, array('id' => $pid_id,'uniacid' => $_W['uniacid']));
						if($results){
							echo 1;
						}
					}
				}
			}
		}
	}

	

    //微信支付
    public function doMobileWxpay() {
        global $_GPC;
        global $_W;
        $howmuch = $_GPC['money'];//ims_mijia_daka_audio中的单价
        $pid_id = $_GPC['pid_id'];//是ims_mijia_daka中的自增id
        $openid = $_W['openid'];//谁购买的
        $uniacid = $_W['uniacid'];//模块号
        $danpian = $_GPC['danpian'];
        $pid = $_GPC['pid'];//是ims_mijia_daka_audio中的自增id
        if($danpian == 1) {
            $howmuch = 30;
        }
        $goods_title = $_GPC['goods_title'];//本章节的题目
        $time = time();
		$orderid = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $params = array(
            'tid' => $time,  //充值模块中的订单号，此号码用于业务模块中区分订单，交易的识别码，这个订单号是不显示的,只要 tid 不一样就可以无限买了
            'ordersn' => $orderid,//收银台中显示的订单号
            'user' => $openid,     //付款用户, 付款的用户名(选填项)
            'title' => $goods_title,          //收银台中显示的标题
            'fee' => $howmuch,
        );
        $data['pid_id'] = $pid_id;
        $data['openid'] = $openid;
        $data['uniacid'] = $uniacid;
        $data['pid'] = $pid;
        $data['status'] = 0;
        $data['createtime'] = time();
        $old_vip = pdo_fetch(" SELECT * FROM ".tablename('mijia_list_pay')." WHERE uniacid = '{$uniacid}' AND status = 1 AND openid = '{$openid}' ORDER BY endtime DESC");
        if($old_vip) {
            $data['endtime'] = $old_vip['endtime'];
        }
        $nopay = pdo_insert('mijia_list_pay',$data);
        if($nopay) {
            $payid = pdo_insertid();
            $params['tid'] = $payid.'///'.$howmuch.'///'.$time;
			//$params['tid'] = $orderid;
        }

        $this->pay($params);
    }

    //微信支付反馈,支付成功反馈界面
    public function payResult($params) {
        global $_W;
        global $_GPC;
        if ($params['result'] == 'success' && $params['from'] == 'notify') {   //notify为异步插入,按notify为准
            $infowxpay = explode('///',$params['tid']);
            $info = pdo_get('mijia_list_pay', array('id' => $infowxpay[0]));
			
			//购买会员卡的费用
			$a = $params['tid'];
			$viporder = pdo_get('mijia_daka_cardorder', array('cusorid' => $a));
			$chongorder = pdo_get('mijia_daka_chongorder', array('cusorid' => $a));
			if($viporder){
				$dat['paytype'] = $params['type'];
				$dat['uorderid'] = $params['tag']['transaction_id'];
				$dat['paystute'] = 1;
				$time = time();
				$dat['paytime'] = $time;
				$month = $viporder['month'];
				$onemonth = 60*60*24*30;
				$endtime = $onemonth*$month;//订单中几个月
				$endtimes = $time+$endtime;
				$dat['endtime'] = $endtimes;
				$span = pdo_update('mijia_daka_cardorder', $dat, array('cusorid' => $a));
				if($span){
					$uniacid = $viporder['uniacid'];
					$openid = $viporder['openid'];
					$vippeople = pdo_fetch(" SELECT * FROM ".tablename('mijia_daka_vippeople')." WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
					//print_r($vippeople);exit;
					if($vippeople){//更新折扣以及结束的时间
						$end = $vippeople['endtime'];
						$aa['agio'] = $viporder['agio'];//折扣
						$aa['month'] = $viporder['month'];//
						$aa['money'] = $viporder['money'];//
						$aa['endtime'] = $end+$endtime;
						//var_dump();
						$resdd = pdo_update('mijia_daka_vippeople', $aa, array('openid' => $openid,'uniacid' => $uniacid));
						//if($resdd){
							//message("更新成功！",$this->createMobileUrl("index"),"success");
						//}

					}else{//直接插入数据
						$sunying['agio'] = $viporder['agio'];//折扣
						$sunying['month'] = $viporder['month'];//
						$sunying['money'] = $viporder['money'];//
						$sunying['uniacid'] = $viporder['uniacid'];//
						$sunying['otime'] = time();
						$sunying['openid'] = $viporder['openid'];
						$sunying['endtime'] = $endtimes;
						$sunying['nickname'] = $viporder['nickname'];
						$resd = pdo_insert('mijia_daka_vippeople',$sunying);


					}
					
				}
			}
			//会员充值余额
			if($chongorder){
				$datas['paytype'] = $params['type'];
				$datas['uorderid'] = $params['tag']['transaction_id'];
				$datas['paystute'] = 1;
				$time = time();
				$datas['paytime'] = $time;
				$spans = pdo_update('mijia_daka_chongorder', $datas, array('cusorid' => $a));
				if($spans){//查询钱包表有没有这个人
					$openid = $chongorder['openid'];
					$uniacid = $chongorder['uniacid'];
					$chongpeople = pdo_fetch(" SELECT * FROM ".tablename('mijia_daka_bagmoney')." WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
					if($chongpeople){
						$money = $chongpeople['money'];//余额
						$chongmoney = $chongorder['money']+$chongorder['songmoney'];
						$moneysd = $money+$chongmoney;
						$lockd['money'] = $moneysd;//只要更新越就可以
						$resddd = pdo_update('mijia_daka_bagmoney', $lockd, array('openid' => $openid,'uniacid' => $uniacid));
					}else{
						$sunyings['uniacid'] = $chongorder['uniacid'];
						$sunyings['openid'] = $chongorder['openid'];
						$sunyings['nickname'] = $chongorder['nickname'];
						$sunyings['money'] = $chongorder['money'];
						$sunyings['ctime'] = time();
						$resddd = pdo_insert('mijia_daka_bagmoney',$sunyings);
					}
				}

			}




           if($info['endtime'] == 0) {
               $data['endtime'] = time()+1*12*30*24*60*60;
               $data['status'] = 1;
           }
            else {
                $data['endtime'] = $info['endtime']+1*12*30*24*60*60;
                $data['status'] = 1;
            }
            pdo_update('mijia_list_pay', $data, array('id' => $infowxpay[0]));

            $pid_id = $info['pid_id'];//购买的总章节的id
            $user_result = pdo_get('mijia_daka', array('id' => $pid_id));

            $vip_result = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_vip')." WHERE uniacid = '{$info['uniacid']}' AND openid = '{$info['openid']}' AND pid_id = '{$pid_id}'");
            if(empty($vip_result)) {
                $data_vip['pid_id'] = $pid_id;
                $data_vip['uniacid'] = $info['uniacid'];
                $data_vip['openid'] = $info['openid'];
                $data_vip['endtime'] = $data['endtime'];
                $data_vip['createtime'] = time();

				//昵称
				$openid = $_W['openid'];
				$arr = mc_oauth_userinfo($openid);
				$userinfo['nickname'] = $arr['nickname'];
				$name = $userinfo['nickname'];
				$data_vip['user_name'] = $name;
                pdo_insert('mijia_daka_vip',$data_vip);
            }
            else {
                $data_vip['endtime'] = $data['endtime'];
                pdo_update('mijia_daka_vip', $data_vip, array('id' => $vip_result['id']));
            }

            //计算订阅数
            $total['number'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mijia_daka_vip') . " WHERE uniacid = '{$info['uniacid']}' AND pid_id = '{$pid_id}'");
            if($total) {
                pdo_update('mijia_daka', $total, array('id' => $pid_id));
            }

            $templateid	    = "IAfHyLqGurSK8vWRl3pbCLIp177PUYh8wjnopKdbzk8";   // "订单支付成功" 的模板 写死了
            $url	    = '';
            $data = array(
                'first' => array(
                    'value' => "支付成功，我们已收到您的货款: )",
                ),
                'orderMoneySum' => array(
                    'value' => $infowxpay[1]."元",
                ),
                'orderProductName' => array(
                    'value' => $user_result['title'],
                ),
                'Remark' => array(
                    'value' => "如有问题请致电400-828-1878或直接在微信留言，小易将第一时间为您服务！",
                ),

            );
            $this->sendtpl($info['openid'], $url, $templateid, $data);//发送给用户通知

        }
        if (empty($params['result']) || $params['result'] != 'success') {
            //此处会处理一些支付失败的业务代码
        }
        //如果消息是用户直接返回（非通知），则提示一个付款成功
        if($params['from'] == 'return') {
            $infowxpay = explode('///',$params['tid']);
            if ($params['type'] == 'credit2') {
                message('已经成功支付', url('mobile/channel', array('name' => 'Index', 'weid' => $_W['weid'])));
            } else {
                message('已经成功支付',$this->createMobileUrl('Index','', 'success'));
            }
        }
    }

    //支付模板消息通知提醒
    public function sendtpl($openid,$url,$template_id,$content, $topcolor = "#FF0000"){
        global $_GPC,$_W;


        load()->classs('weixin.account');
        load()->func('communication');

        $key = $_W['account']['key'];
        $secret = $_W['account']['secret'];
        $url_openid = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$key."&secret=".$secret;
        $access_token = file_get_contents($url_openid);
        $access_token = json_decode($access_token,true); //加个ture可以将对象转化为数组
        $access_token = $access_token['access_token'];  // 这样获取到的 access_token 才真正的 access_token

        $data = array(
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'topcolor' => $topcolor,
            'data' => $content,
        );
        $json = json_encode($data);
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $ret = ihttp_post($url, $json);
        return $ret;
    }

    //新课程推送消息通知提醒
    public function sendtpl2($openid,$url,$template_id,$content, $topcolor = "#FF0000"){
        global $_GPC,$_W;


        load()->classs('weixin.account');
        load()->func('communication');

        $key = $_W['account']['key'];
        $secret = $_W['account']['secret'];
        $url_openid = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$key."&secret=".$secret;
        $access_token = file_get_contents($url_openid);
        $access_token = json_decode($access_token,true); //加个ture可以将对象转化为数组
        $access_token = $access_token['access_token'];  // 这样获取到的 access_token 才真正的 access_token

        $data = array(
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'topcolor' => $topcolor,
            'data' => $content,
        );
        $json = json_encode($data);
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $ret = ihttp_post($url, $json);
        return $ret;
    }

    //支付反馈
    public function doWebPayresult() {
        global $_W,$_GPC;
		$ops = array('display','add','delete'); // 只支持此 4 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "display"){
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 10; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']	
			);
			//根据名称查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`nickname` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_list_pay').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);
					
			$sql = 'SELECT * FROM '.tablename('mijia_list_pay').$where." AND status = 1 ORDER BY createtime desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$order = pdo_fetchall($sql, $params);
			
			$uniacid = $_W['uniacid'];
			foreach ($order as $key => $value) {
				$power = $value['pid'];
				$order[$key]['roles'][]=pdo_fetch("SELECT * FROM".tablename('mijia_daka_audio')."WHERE uniacid = '$uniacid' AND id = '{$power}'");
			}

			foreach ($order as $key => $value) {
				$power = $value['pid_id'];
				$order[$key]['roless'][]=pdo_fetch("SELECT * FROM".tablename('mijia_daka')."WHERE uniacid = '$uniacid' AND id = '{$power}'");
			}

			load()->func('tpl');
			include $this->template('result_pay');
		}	
    }




    //AJAX分页
    public function doMobilePager() {
        global $_GPC;
        global $_W;


        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $pid_id = $_GPC['pid_id'];
        $mypage2 = $_GPC['pagenumber'];
        //计算总页数然后配合前台进行判断
        $total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mijia_daka_audio')." WHERE uniacid = '{$uniacid}' AND pid_id = '{$pid_id}' ORDER BY id DESC");
        $allpage = $total/$mypage2;
        $allpage2 = ceil($total/$mypage2);
        $mypage = $mypage2 <= $allpage2?$mypage2:'nomsg';
        $pindex = $mypage;
        $psize = 4;
        if($mypage == 'nomsg') {
            $info_aduio = 2;
        }
        else{
            $info_aduio = pdo_fetchall(" SELECT * FROM " . tablename('mijia_daka_audio') . " WHERE uniacid = '{$uniacid}' AND pid_id = '{$pid_id}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $siteroot = $_W['siteroot'];
            $acid = $_W['acid'];
            foreach($info_aduio as $k => $val) {
                $info_aduio[$k]['time'] = date('m月d日',$val['createtime']);
                $info_aduio[$k]['url'] = $siteroot.'app/index.php?i='.$uniacid.'&j='.$acid.'&c=entry&id='.$val['id'].'&do=IndexListInfo&m=mijia_daka';
            }
        }
        echo json_encode($info_aduio);

    }

    //个人中心（用户/教师）
    public function doMobileCenter() {
        global $_W;
        global $_GPC;
        load()->func('tpl');

        if (empty($_W['openid'])) {
            die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
        }

        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $myname = mc_oauth_userinfo()['nickname'];
        $mypicture = mc_oauth_userinfo()['avatar'];
        //对应总订阅量
        $total = pdo_fetch('SELECT * FROM ' . tablename('mijia_daka') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
        $id_zl = $total['id'];
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mijia_list_pay') . " WHERE uniacid = '{$uniacid}' AND pid_id = '{$id_zl}' AND status = 1");
        //看看是不是老师
        $admin = pdo_fetch("SELECT * FROM ".tablename('mijia_daka'). " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
        //账户余额
        $money_admin_all = pdo_fetch("SELECT SUM(`money`) FROM " .tablename('mijia_list_pay')." a LEFT JOIN ".tablename('mijia_daka')." b ON a.pid_id = b.id "
            ." WHERE a.uniacid = '{$uniacid}' AND a.pid_id = '{$admin['id']}' AND a.status = 1");
        $money_admin_all = $money_admin_all['SUM(`money`)'];
        $money_admin_all = $money_admin_all * 1;  //yzg这里将来可以设置成税率；
        if($money_admin_all) {
            $money_after = pdo_fetch("SELECT SUM(`money`) FROM ".tablename('mijia_daka_money_tx')." WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' AND status = 1 ");
            $money_after = $money_after['SUM(`money`)'];
            if($money_after) {
                $money_admin_after = $money_admin_all - $money_after;
            }else{
                $money_admin_after = $money_admin_all - 0;
            }
        }else {
            $money_admin_after = 0;
        }
        //用户订阅课程总量
        
        //$center = pdo_fetchall("SELECT * FROM " . tablename('mijia_daka_vip') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");

		$center = pdo_fetchall("SELECT * FROM " . tablename('mijia_list_pay') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' AND status = 1 ");



            //foreach($center as $key=>$val){
               // $take[] =  pdo_get('mijia_daka', array('id' => $center[$key]['pid_id']));
            //}
        //$dinyue = count($center);

		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$bag = pdo_fetch("SELECT * FROM".tablename('mijia_daka_bagmoney')."WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' ");
		$bagmoney = $bag['money'];

		$chongorder = pdo_fetchAll("SELECT * FROM".tablename('mijia_daka_chongorder')."WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' AND paystute = 1");
		$ordernumber = count($chongorder);

		//购买多少章节
		$musicorder = pdo_fetchAll("SELECT * FROM".tablename('mijia_list_pay')."WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' AND status = 1 ");
		$ordernumbers = count($musicorder);
        include $this->template('personal_center');
    }

    //教师提现申请
    public function doMobileMoneytx() {
        global $_W;
        global $_GPC;
        load()->func('tpl');

        if (empty($_W['openid'])) {
            die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
        }

        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $info_zl = pdo_fetch("SELECT * FROM ".tablename('mijia_daka')." WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");

        if(checksubmit()) {
            $data['pid_id'] = $_GPC['id'];
            $data['money'] = $_GPC['money'];
            $data['info'] = $_GPC['info'];
            $data['createtime'] = time();
            $data['openid'] = $openid;
            $data['uniacid'] = $uniacid;
            $result = pdo_insert('mijia_daka_money_tx',$data);
            if($result) {
                message('申请提现成功，请耐心等待管理员确认！', $this->createMobileUrl('Center','', 'success'));
            }
        }

        include $this->template('money_tx');
    }

    //用户订阅详情
    public function doMobilePersonalInfo() {
        global $_W;
        global $_GPC;
		$ops = array('display','look','open','pay','delete'); 
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
       

        if (empty($_W['openid'])) {
            die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
        }

        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        //$edit_admin = pdo_fetchall("SELECT a.id,b.title , a.openid, b.money ,a.createtime,a.endtime,b.name,b.beizhu,b.small_title,b.number,b.pic_list,b.money FROM " .tablename('mijia_daka_vip')." a LEFT JOIN ".tablename('mijia_daka')." b ON a.pid_id = b.id "
            //." WHERE a.uniacid = '{$uniacid}' AND a.openid = '{$openid}'");
        //if(empty($edit_admin)) {
           // $nomeg = 1;
        //}

			if($op == "display"){
			$openid = $_W['openid'];
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 6; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']
			);

			//根据小区名查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`nickname` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_list_pay').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);

			$sql = 'SELECT * FROM '.tablename('mijia_list_pay')." {$where} AND openid = '{$openid}' AND status = 1 ORDER BY createtime desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$data = pdo_fetchall($sql, $params);
			//print_r($data);

			if(empty($data)) {
				$nomeg = 1;
			}


			$uniacid = $_W['uniacid'];
			foreach ($data as $key => $value) {
				$power = $value['pid_id'];
				$data[$key]['roles'][]=pdo_fetch("SELECT * FROM".tablename('mijia_daka')."WHERE uniacid = '$uniacid' AND id = '{$power}'");
			}

			foreach ($data as $key => $value) {
				$power = $value['pid'];
				$data[$key]['roless'][]=pdo_fetch("SELECT * FROM".tablename('mijia_daka_audio')."WHERE uniacid = '$uniacid' AND id = '{$power}'");
			}

			//$take = pdo_fetchall("SELECT * FROM ".tablename('mijia_list_pay')." WHERE status = 1 AND uniacid = '{$uniacid}' AND pid_id = '{$pid_id}' AND pid='{$pid}' AND openid = '{$openid}' ");

			load()->func('tpl');
			include $this->template('personal_center_info');
		}
    }

    //PC端 提现记录
    public function doWebTookMomey() {
        global $_W;
        global $_GPC;
        load()->func('tpl');


        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];

        $this -> Xxdy();

        $pid_id = $_GPC['pid_id'];
        if($pid_id) {
            $data['status'] = 1;
            $result_pay = pdo_update('mijia_daka_money_tx', $data, array('id' => $pid_id));
            if($result_pay) {
                message('已确认打款', $this->createWebUrl('TookMomey','', 'success'));
            }
        }

        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mijia_daka_money_tx') . " WHERE uniacid = '{$uniacid}'");
        $pager = pagination($total, $pindex, $psize);
        $edit_admin = pdo_fetchall("SELECT a.id,a.money , a.info, a.status ,a.createtime,b.name,b.tel, b.moneycard,b.pic_list FROM " .tablename('mijia_daka_money_tx')." a LEFT JOIN ".tablename('mijia_daka')." b ON a.pid_id = b.id "
            ." WHERE a.uniacid = '{$uniacid}' LIMIT ". ($pindex - 1) * $psize . ',' . $psize);


        include $this->template('record_tx');
    }

	//前端购买会员卡
	public function doMobileBuy_vip() {
        global $_W;
        global $_GPC;
		$openid = $_W['openid'];
		$arr = mc_oauth_userinfo($openid);
		//var_dump($arr);
		$userinfo['headimgurl'] = $arr['headimgurl'];
		$userinfo['nickname'] = $arr['nickname'];
		$image = $userinfo['headimgurl'];
		$name = $userinfo['nickname'];

		//查询vip折扣表
		$uniacid = $_W['uniacid'];
		$data = pdo_fetchAll("SELECT * FROM".tablename("mijia_daka_vipcard")."WHERE uniacid = '{$uniacid}' order by vtime asc");

		//查询vip表
		$openid = $_W['openid'];
		$vippeople = pdo_fetch("SELECT * FROM".tablename("mijia_daka_vippeople")."WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' ");
		$endtime = $vippeople['endtime'];
		$nowtime = time();


        load()->func('tpl');
		include $this->template('vipcard');
	}

	//购买会员卡
	public function doMobileBuyvip_order() {
        global $_W;
        global $_GPC;
		$ops = array('display','add','pay'); 
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "pay"){
			$vid = $_GPC['vid'];
			$uniacid = $_W['uniacid'];
			$openid = $_W['openid'];
			$list = pdo_fetch("SELECT * FROM".tablename('mijia_daka_vipcard')."WHERE uniacid = '{$uniacid}' AND vid = '{$vid}' ");
			//print_r($list);exit;
			$howmuch = $list['money'];
			//支付参数
			$orderid = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);//订单编号
			$time = time();
			$goods_title = '购买会员卡费用';
			//支付参数
			$params = array(
				'tid' => $time,  //充值模块中的订单号，此号码用于业务模块中区分订单，交易的识别码，这个订单号是不显示的,只要 tid 不一样就可以无限买了
				'ordersn' => $orderid,  //收银台中显示的订单号
				'user' => $openid,     //付款用户, 付款的用户名(选填项)
				'title' => $goods_title,          //收银台中显示的标题
				'fee' => $howmuch,
			);
			
			//塞进数据存入数据库
			$lock['agio'] = $list['agio'];//折扣
			$lock['month'] = $list['month'];//月份
			$lock['money'] = $howmuch;//费用
			$lock['uniacid'] = $uniacid;
			$lock['otime'] = time();
			$lock['openid'] = $openid;
			$lock['cusorid'] = $orderid;//订单编号
			$lock['vid'] = $vid;
			$arr = mc_oauth_userinfo($openid);
			$userinfo['nickname'] = $arr['nickname'];
			$name = $userinfo['nickname'];
			$lock['nickname'] = $name;
			//var_dump($lock);exit;
			$nopay = pdo_insert('mijia_daka_cardorder',$lock);
			if($nopay){
				$payid = pdo_insertid();
				$params['tid'] = $orderid;
			}
			$this->pay($params);
		}

	}




    //用户评论提交
    public function doMobileDiscuss() {
        global $_W;
        global $_GPC;
        load()->func('tpl');

        if (empty($_W['openid'])) {
            die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
        }

        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $pic_user = mc_oauth_userinfo()['avatar'];
        $name_user = mc_oauth_userinfo()['nickname'];
        $id = $_GPC['id'];
        if(checksubmit()) {
            $data['uniacid'] = $uniacid;
            $data['openid'] = $openid;
            $data['pic_user'] = $pic_user;
            $data['name_user'] = $name_user;
            $data['pid_id'] = $id;
            $data['discuss_info'] = $_GPC['discuss_info'];
            $data['createtime'] = time();
            $data['status'] = 0;
            $result = pdo_insert('mijia_daka_discuss',$data);
            if($result) {
                message('评论提交成功，请耐心等待管理员审核~', $this->createMobileUrl('IndexListInfo',array('id' => $id), 'success'));
            }
        }
    }

    //PC端 评论审核
    public function doWebDiscuss() {
        global $_W;
        global $_GPC;
        load()->func('tpl');
        
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];

        $this -> Xxdy();

        $pid_id = $_GPC['pid_id'];
        if($pid_id) {
            $data['status'] = 1;
            $result_discuss = pdo_update('mijia_daka_discuss', $data, array('id' => $pid_id));
            if($result_discuss) {
                message('审核通过!', $this->createWebUrl('Discuss','', 'success'));
            }
        }

        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mijia_daka_discuss') . " WHERE uniacid = '{$uniacid}'");
        $pager = pagination($total, $pindex, $psize);
        $edit_admin = pdo_fetchall("SELECT a.id,a.discuss_info , a.status, a.status ,a.createtime,b.title,a.openid FROM " .tablename('mijia_daka_discuss')." a LEFT JOIN ".tablename('mijia_daka_audio')." b ON a.pid_id = b.id "
            ." WHERE a.uniacid = '{$uniacid}' LIMIT ". ($pindex - 1) * $psize . ',' . $psize);


        include $this->template('discuss');
    }

//PC端 提成管理
    public function doWebCommission(){
        global $_W;
        global $_GPC;
        load()->func('tpl');


        //判断是要输出哪个部分
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        $id = $_GPC['id'];    //若是编辑按钮转过来的这里会有参数id传过来
        $partner = $_GPC['partner'];
        $uniacid = $_W['uniacid'];
        $this -> Xxdy();

        if ($op == 'edit') {    //说明已经在编辑界面，肯定要编辑提交点什么东西了，先接收了再说看看是新插入还是更新
            if (checksubmit('submit')) {        //确认有没有提交
                $data['uniacid'] = $uniacid;
                $data['partner'] = $_GPC['partner'];
                $data['mode'] = $_GPC['mode'];
                $data['income'] = $_GPC['income'];
                $data['pay'] = $_GPC['pay'];
                $data['notes'] = $_GPC['notes'];
                if (empty($id)) {    //属于新增
                    $res = pdo_insert('mijia_daka_commission', $data);
                    $fanhui = pdo_insertid();
                    if (!empty($res)) {
                        message('新增成功', $this->createWebUrl('commission', array('op' => 'display')), 'success');
                    }
                }
                else {    //属于更新
                    $result = pdo_update('mijia_daka_commission', $data, array('id' => $id));
                    if (!empty($result)) {
                        message('修改成功', $this->createWebUrl('commission', array('op' => 'display')), 'success');
                    }
                }
            }
            else {    //没有提交说明是点“添加XX”或者是点“编辑”过来的，往这儿走，而且只有“编辑”过来的才有参数id才可以显现东西
                unset($info);    //销毁之前有的$info变量,以免干扰
                $info = pdo_get('mijia_daka_commission', array('id' => $id));
            }
        }
        elseif($op == 'down'){
            if (checksubmit('sub')) {        //确认有没有提交
                $data['teacher'] = $_GPC['teacher'];
                $data['cent'] = $_GPC['cent'];
                $data['notes'] = $_GPC['notes'];
                $data['uniacid'] = $uniacid;
                if (empty($id)) {    //属于新增
                    $res = pdo_insert('mijia_daka_down', $data);

                    if($res) {
                        message('新增成功', $this->createWebUrl('commission', array('op' => 'class')), 'success');
                    }

                }
                else {    //属于更新
                    $result = pdo_update('mijia_daka_down', $data, array('id' => $id));
                    if (!empty($result)) {
                        message('修改成功', $this->createWebUrl('commission', array('op' => 'class')), 'success');
                    }
                }
            }
            else {    //没有提交说明是点“添加XX”或者是点“编辑”过来的，往这儿走，而且只有“编辑”过来的才有参数id才可以显现东西
                unset($into);    //销毁之前有的$into变量,以免干扰
                $into = pdo_get('mijia_daka_down', array('id' => $id));

                include $this->template('commission');exit;
            }
        }


        
        //分页输出
        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;
        $commission = pdo_fetchall(" SELECT * FROM " . tablename('mijia_daka_commission') . " WHERE uniacid = '{$uniacid}' ORDER BY id ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mijia_daka_commission') . " WHERE uniacid = '{$uniacid}'");
        $pager = pagination($total, $pindex, $psize);

        $class = pdo_fetchall(" SELECT * FROM " . tablename('mijia_daka_down') . " WHERE uniacid = '{$uniacid}' ORDER BY id ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $class_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mijia_daka_down') . " WHERE uniacid = '{$uniacid}'");
        $pag = pagination($class_total, $pindex, $psize);
        include $this->template('commission');
    }

    //PC端：提成删除
    public function doWebCommissiondelete()
    {
        global $_W;
        global $_GPC;


        $del = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        load()->func('tpl');

            $result = pdo_query("DELETE FROM " . tablename('mijia_daka_commission') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));    
                if(!empty($result)){
                    message('删除成功', $this->createWebUrl('commission', array('op' => 'display'), 'success'));
            }
        
    }

    //PC端：扣点删除
    public function doWebDowndelete()
    {
        global $_W;
        global $_GPC;


        $del = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        load()->func('tpl');
        
            $result = pdo_query("DELETE FROM " . tablename('mijia_daka_down ') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));
            if (!empty($result)) {
                message('删除成功', $this->createWebUrl('commission', array('op' => 'class'), 'success'));
            }
    }

    //支付反馈
   /* public function doWebMember() {
        global $_W;
        global $_GPC;
        load()->func('tpl');


        $uniacid = $_W['uniacid'];

        $this -> Xxdy();

        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mijia_daka') . " WHERE uniacid = '{$uniacid}'");
        $pager = pagination($total, $pindex, $psize);
        $edit_admin = pdo_fetchall(" SELECT * FROM " . tablename('mijia_daka_vip') . " WHERE uniacid = '{$uniacid}' ORDER BY id ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        include $this->template('member');
    }*/
	

	//会员卡设置
	public function doWebMember(){
        global $_W;
        global $_GPC;
		$ops = array('display', 'edit','delete','add'); // 只支持此 4 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		//默认
		if($op == "display"){
			$uniacid = $_W['uniacid'];
			$list = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka_vipcard')." WHERE uniacid = '{$uniacid}' order by vtime asc");
			//print_r($list);

			load()->func('tpl');
			include $this->template('Buycard');
		}
		//增加
		if($op == "add"){
			if(checksubmit('submit')){
				$lock['uniacid'] = $_W['uniacid'];
				$lock['vtime'] = time();
				$lock['agio'] = $_GPC['agio'];
				$lock['month'] = $_GPC['month'];
				$lock['money'] = $_GPC['money'];
				$lock['moneyone'] = $_GPC['moneyone'];
				$lock['moneyonetwo'] = $_GPC['moneyonetwo'];
				//var_dump($lock);exit;
				$res = pdo_insert('mijia_daka_vipcard',$lock);
				if($res){
					message("新增成功",$this->createWebUrl('Member'),"success");
				}
			}
		}
		//修改
		if($op == "edit"){
			if(checksubmit('submit')){
				$vid = $_GPC['vid'];
				//echo $vid;exit;
				$locks['month'] = $_GPC['month'];
				$locks['money'] = $_GPC['money'];
				$locks['agio'] = $_GPC['agio'];
				$locks['moneyone'] = $_GPC['moneyone'];
				$locks['moneyonetwo'] = $_GPC['moneyonetwo'];
				//var_dump($locks);exit;
				$ress = pdo_update('mijia_daka_vipcard', $locks, array('vid' => $vid,'uniacid' => $_W['uniacid']));
				if($ress){
					message("修改成功",$this->createWebUrl('Member'),"success");
				}
			}
			$vid = $_GPC['pid'];
			$uniacid = $_W['uniacid'];
			$data = pdo_fetch("SELECT * FROM ".tablename('mijia_daka_vipcard')." WHERE uniacid = '{$uniacid}' AND vid = '{$vid}' ");
			load()->func('tpl');
			include $this->template('Buycards');
		}
		//删除
		if($op == "delete"){
			$vid = $_GPC['pid'];
			$res = pdo_delete("mijia_daka_vipcard",array('vid' => $vid,'uniacid' => $_W['uniacid']));
			if($res){
				message("删除成功",$this->createWebUrl('Member'),"success");
			}
		}
	}


    //手机端任务列表页
    public function doMobileVipcard() {
        global $_GPC;
        global $_W;
        load()->func('tpl');

        if (empty($_W['openid'])) {
            die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
        }
        
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $gs = $_W['account']['name'];
        $this -> Xxdy();

        mc_oauth_userinfo();  //此方法可以在分享给朋友，朋友圈后，让进来的用户授权登录
        $myname = mc_oauth_userinfo()['nickname'];
        $LIST = pdo_fetchall("SELECT * FROM ".tablename('mijia_daka')." WHERE uniacid = '{$uniacid}' ORDER BY sort ASC");
        $old_vip = pdo_fetchall(" SELECT * FROM ".tablename('mijia_daka_vip')." WHERE uniacid = '{$uniacid}' AND status = 0 AND openid = '{$openid}' ORDER BY endtime DESC");
        // foreach ($old_vip as $k => $v) {
        // $listname[] = pdo_getcolumn('mijia_daka', array('id' => $v['pid_id']), 'title');
        // var_dump($v['pid_id']);

        // };
        $listname = pdo_fetchall("SELECT a.id,a.pid_id , a.status, a.endtime,b.title,a.openid FROM " .tablename('mijia_daka_vip')." a LEFT JOIN ".tablename('mijia_daka')." b ON a.pid_id = b.id "
            ." WHERE a.openid = '{$openid}'  ORDER BY a.id DESC");  
            // var_dump($_W['member']);
        include $this->template('vipcard_buy');
    }


    //微信支付
    public function doMobileVipwxpay() {
        global $_GPC;
        global $_W;
        //$howmuch = $_GPC['money'];
        $pid_id = $_GPC['pid_id'];
        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        $time = time();
		$sunying = pdo_fetch("SELECT * FROM".tablename('mijia_daka')."WHERE uniacid = '{$uniacid}' AND id = '{$pid_id}' ");
		$name = $sunying['title'];
		$howmuch = $sunying['money'];


        $params = array(
            'tid' => $time,  //充值模块中的订单号，此号码用于业务模块中区分订单，交易的识别码，这个订单号是不显示的,只要 tid 不一样就可以无限买了
            'ordersn' => date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8),  //收银台中显示的订单号
            'user' => 'dssads',     //付款用户, 付款的用户名(选填项)
            'title' => $name,          //收银台中显示的标题
            'fee' => $howmuch,
        );
        $data['user_name'] =  mc_oauth_userinfo()['nickname'];
        $data['pid_id'] = $pid_id;
        $data['openid'] = $openid;
        $data['uniacid'] = $uniacid;
        $data['status'] = 0;
        $data['createtime'] = time();
        $old_vip = pdo_fetch(" SELECT * FROM ".tablename('mijia_daka_vip')." WHERE uniacid = '{$uniacid}' AND status = 0 AND openid = '{$openid}' AND pid_id = '{$pid_id}' ORDER BY endtime DESC");
        if($old_vip) {
            $data['endtime'] = strtotime("+1month",$old_vip['endtime']);
            $nopay = pdo_update('mijia_daka_vip',$data, array('openid' => $openid));
            $payid = $old_vip['id'];
        }else{
            $data['endtime'] = strtotime('+1month');
            $nopay = pdo_insert('mijia_daka_vip',$data);
            $payid = pdo_insertid();
        }
        if($nopay) {

            $params['tid'] = $payid.'///'.$howmuch.'///'.$time;
        }

        $this->pay($params);
    }

	//pc端：购买会员卡的订单
	public function doWebOrder(){
		global $_W,$_GPC;
		$ops = array('display','add','delete'); // 只支持此 4 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "display"){
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 10; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']	
			);
			//根据名称查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`cusorid` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_daka_cardorder').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);
					
			$sql = 'SELECT * FROM '.tablename('mijia_daka_cardorder').$where." LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$order = pdo_fetchall($sql, $params);

			//print_r($order);

			load()->func('tpl');
			include $this->template('viporder');

		}

		//删除
		if($op == "delete"){
			$oid = $_GPC['oid'];
			//echo $oid;
			$res = pdo_delete('mijia_daka_cardorder',array('oid' => $_GPC['oid'],'uniacid' => $_W['uniacid']));
			if($res){
				message("删除成功",$this->createWebUrl("Order"),"success");
			}
		}
	}

	//显示购买会员卡的会员
	public function doWebPeople(){
		global $_W,$_GPC;
		$ops = array('display','add','delete'); // 只支持此 4 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "display"){
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 10; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']	
			);
			//根据名称查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`nickname` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_daka_vippeople').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);
					
			$sql = 'SELECT * FROM '.tablename('mijia_daka_vippeople').$where." LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$order = pdo_fetchall($sql, $params);

			//print_r($order);

			load()->func('tpl');
			include $this->template('vippeople');

		}

		//删除
		if($op == "delete"){
			$oid = $_GPC['oid'];
			//echo $oid;
			$res = pdo_delete('mijia_daka_cardorder',array('oid' => $_GPC['oid'],'uniacid' => $_W['uniacid']));
			if($res){
				message("删除成功",$this->createWebUrl("Order"),"success");
			}
		}
	}

	//充值余额选项设置
	public function doWebSet_bag(){
		global $_W,$_GPC;
		$ops = array('display','edit','delete','add'); // 只支持此 6 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		//默认
		if($op == "display"){
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 10; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']	
			);
			//根据名称查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`nickname` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_daka_setbag').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);
					
			$sql = 'SELECT * FROM '.tablename('mijia_daka_setbag').$where." order by sid asc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$order = pdo_fetchall($sql, $params);
			//print_r($order);
			load()->func('tpl');
			include $this->template('setbag');
		}
		//增加
		if($op == "add"){
			if(checksubmit('submit')){
				$lock['uniacid'] = $_W['uniacid'];
				$lock['money'] = $_GPC['money'];
				$lock['songmoney'] = $_GPC['songmoney'];
				$lock['ctime'] = time();
				$res = pdo_insert('mijia_daka_setbag',$lock);
				if($res){
					message("添加成功",$this->createWebUrl("Set_bag"),"success");
				}
			}
		}

		//修改
		if($op == "edit"){
			if(checksubmit('submit')){
				$sid = $_GPC['sid'];
				$lock['money'] = $_GPC['money'];
				$lock['songmoney'] = $_GPC['songmoney'];
				$res = pdo_update('mijia_daka_setbag', $lock, array('sid' => $sid,'uniacid' => $_W['uniacid']));
				if($res){
					message("修改成功",$this->createWebUrl("Set_bag"),"success");
				}
			}
			$uniacid = $_W['uniacid'];
			$sid = $_GPC['sid'];
			$data = pdo_fetch(" SELECT * FROM ".tablename('mijia_daka_setbag')." WHERE uniacid = '{$uniacid}' AND sid = '{$sid}' ");
			load()->func('tpl');
			include $this->template('setbags');
		}
		//删除
		if($op == "delete"){
			$sid = $_GPC['sid'];
			$res = pdo_delete('mijia_daka_setbag',array('sid' => $_GPC['sid'],'uniacid' => $_W['uniacid']));
			if($res){
				message("删除成功",$this->createWebUrl("Set_bag"),"success");
			}
		}
	}

	//手机端：充值余额
	public function doMobileChong(){
		global $_W,$_GPC;
		$ops = array('display','weipay'); // 只支持此 11 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		$uniacid = $_W['uniacid'];
		//默认
		if($op == "display"){
			$xuanze = pdo_fetchAll("SELECT * FROM".tablename('mijia_daka_setbag')."WHERE uniacid = '{$uniacid}' order by sid asc");
			$openid = $_W['openid'];
			$bag = pdo_fetch("SELECT * FROM".tablename('mijia_daka_bagmoney')."WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' ");


			load()->func('tpl');
			include $this->template('bagmoney');
		}
		//支付
		if($op == "weipay"){
			$openid = $_W['openid'];
			$sid = $_GPC['sid'];
			$list = pdo_fetch("SELECT * FROM".tablename('mijia_daka_setbag')."WHERE uniacid = '{$uniacid}' AND sid = '{$sid}' ");
			//参数
			$money = $list['money'];//充值的金额
			$songmoney = $list['songmoney'];//充值所送
			$time = time();
			$goods_title = '余额充值';
			$orderid = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
			$params = array(
				'tid' => $time,  //充值模块中的订单号，此号码用于业务模块中区分订单，交易的识别码，这个订单号是不显示的,只要 tid 不一样就可以无限买了
				'ordersn' => $orderid,  //收银台中显示的订单号
				'user' => $openid,     //付款用户, 付款的用户名(选填项)
				'title' => $goods_title,          //收银台中显示的标题
				'fee' => $money,
			);



			//塞数据进数组
			$lock['uniacid'] = $_W['uniacid'];
			$lock['openid'] = $_W['openid'];
			$arr = mc_oauth_userinfo($openid);
			$userinfo['nickname'] = $arr['nickname'];
			$name = $userinfo['nickname'];
			$lock['nickname'] = $name;
			$lock['ctime'] = time();
			$lock['money'] = $money;
			$lock['sid'] = $sid;
			$lock['cusorid'] = $orderid;
			$lock['songmoney'] = $songmoney;
			//塞数据进入数据库
			$res = pdo_insert('mijia_daka_chongorder',$lock);
			if($res){
				$payid = pdo_insertid();
				$params['tid'] = $orderid;
			}
			$this->pay($params);
		}
	}

	//pc端：会员钱包显示
	public function doWebBagmoney(){
		global $_W,$_GPC;
		$ops = array('display','edit','delete','shenhe'); // 只支持此 6 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "display"){
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 10; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']
			);

			//根据小区名查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`nickname` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_daka_bagmoney').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);

			$sql = 'SELECT * FROM '.tablename('mijia_daka_bagmoney')." {$where} ORDER BY ctime desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$data = pdo_fetchall($sql, $params);

			load()->func('tpl');
			include $this->template('pcbagmoney');
		}
	}

	//PC端：会员充值订单显示
	public function doWebChong_order(){
		global $_W,$_GPC;
		$ops = array('display','edit','delete','shenhe'); // 只支持此 6 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "display"){
			$pageindex = max(intval($_GPC['page']), 1); // 当前页码
			$pagesize = 10; // 设置分页大小
			$where = ' WHERE uniacid=:uniacid';
			$params = array(
				':uniacid'=>$_W['uniacid']
			);

			//根据小区名查询
			if (!empty($_GPC['keyword'])) {
				$where .= ' AND ( (`nickname` like :keyword))';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			// 处理 post 提交
			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_daka_chongorder').$where;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pageindex, $pagesize);

			$sql = 'SELECT * FROM '.tablename('mijia_daka_chongorder')." {$where} AND paystute = 1 ORDER BY ctime desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
			$data = pdo_fetchall($sql, $params);
			//print_r($data);

			load()->func('tpl');
			include $this->template('pcchongorder');
		}
	}

	//PC端：包月订单
	public function doWebMonth(){
		global $_W,$_GPC;
		$ops = array('display','edit','delete','add'); // 只支持此 4 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		$uniacid = $_W['uniacid'];
		if($op == "display"){
			$list = pdo_fetchAll("SELECT * FROM".tablename('mijia_daka_month')."WHERE uniacid = '{$uniacid}'");
			load()->func('tpl');
			include $this->template('month');
		}
		//增加
		if($op == "add"){
			if(checksubmit('submit')){
				$lock['uniacid'] = $_W['uniacid'];
				$lock['text'] = $_GPC['text'];
				$lock['ctime'] = time();
				$res = pdo_insert('mijia_daka_month',$lock);
				if($res){
					message("添加成功",$this->createWebUrl('Month'),"success");
				}
			}
		}

		//删除
		if($op == "delete"){
			$mid = $_GPC['mid'];
			$res = pdo_delete("mijia_daka_month",array('mid' => $_GPC['mid'],'uniacid' => $_W['uniacid']));
			if($res){
				message("删除成功",$this->createWebUrl('Month'),"success");
			}
		}

		//修改
		if($op == "edit"){
			if(checksubmit('submit')){
				$mmid = $_GPC['mmid'];
				$lock['text'] = $_GPC['text'];
				$res = pdo_update('mijia_daka_month', $lock, array('mid' => $mmid,'uniacid' => $_W['uniacid']));
				if($res){
					message("修改成功",$this->createWebUrl('Month'),"success");
				}
			}
			$mid = $_GPC['mid'];
			$list = pdo_fetch("SELECT * FROM".tablename('mijia_daka_month')."WHERE uniacid = '{$uniacid}' AND mid = '{$mid}' ");
			load()->func('tpl');
			include $this->template('months');
		}
	}

	//包月帮助
	public function doMobileMonth_help(){
		global $_W,$_GPC;
		$ops = array('display','edit','delete','shenhe'); // 只支持此 6 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op == "display"){
			$uniacid = $_W['uniacid'];
			$data = pdo_fetch("SELECT * FROM".tablename('mijia_daka_month')."WHERE uniacid = '{$uniacid}' ");
			load()->func('tpl');
			include $this->template('monthhelp');
		}
	}

	

	//上传视频
	public function doWebSet(){
		global $_W,$_GPC;
		$ops = array('display','delete','add','edit'); // 只支持此 4 种操作.
		$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';

		//默认显示
		if($op == "display"){
			$uniacid = $_W['uniacid'];
			$data = pdo_fetchAll(" SELECT * FROM ".tablename('mijia_daka_set')." WHERE uniacid = '{$uniacid}' ");

			load()->func('tpl');
			include $this->template('settings');
		}

		//增加
		if($op == "add"){
			if(checksubmit('submit')){
				$lock['uniacid'] = $_W['uniacid'];
				$lock['accesskey'] = $_GPC['accesskey'];
				$lock['secretkey'] = $_GPC['secretkey'];
				$lock['link'] = $_GPC['link'];
				$lock['qnname'] = $_GPC['qnname'];
				$lock['createtime'] = time();
				$lock['status'] = $_GPC['status'];
				//var_dump($lock);exit;
				$res = pdo_insert('mijia_daka_set',$lock);
				if($res){
					message("提交成功！",$this->createWebUrl("Set"),"success");
				}
			}
		}

		//修改
		if($op == "edit"){
			$sid = $_GPC['sid'];
			if(checksubmit('submit')){
				$lock['accesskey'] = $_GPC['accesskey'];
				$lock['secretkey'] = $_GPC['secretkey'];
				$lock['link'] = $_GPC['link'];
				$lock['qnname'] = $_GPC['qnname'];
				$lock['status'] = $_GPC['status'];
				//var_dump($lock);exit;
				$res = pdo_update('mijia_daka_set', $lock, array('sid' => $sid,'uniacid' => $_W['uniacid']));
				if($res){
					message("修改成功！",$this->createWebUrl("Set"),"success");
				}
			}
		}

		//删除
		if($op == "delete"){
			$res = pdo_delete("mijia_daka_set",array('sid' => $_GPC['sid'],'uniacid' => $_W['uniacid']));
			if($res){
				message("删除成功！",$this->createWebUrl("Set"),"success");
			}
		}
	}


	



















	







}







