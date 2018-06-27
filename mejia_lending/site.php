<?php
/**
 * mejia_lending模块微站定义
 *
 * @author mejia
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Mejia_lendingModuleSite extends WeModuleSite {

	public function doMobileIndex() {
		//这个操作被定义用来呈现 功能封面
			global $_W,$_GPC;
			$ops = array('display','add','ade','adf','mmt'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
				load()->func('tpl');
			$data = pdo_fetch("SELECT forcegz FROM".tablename('mijia_lending_product')." WHERE `uniacid`=:uniacid",array(':uniacid' => $_W['uniacid']));

			if($data['forcegz'] == 1){

				if ($_W['fans']['follow']== 0) {
					$mc=$_W['uniacid'];
					$urls='../attachment/qrcode_'.$_W['uniacid'].'.jpg';
					load()->func('tpl');
					include $this->template('guanzhu');

				}else{

					$dataa = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_templetlist')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));

					$datab = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_templetlist1')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));

					$datac = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_templetlist2')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));


					$data = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_gong')." WHERE `uniacid`=:uniacid",array(':uniacid' => $_W['uniacid']));

					$datad = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_order')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));


					include $this->template('index');
				}

			}else{


					$dataa = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_templetlist')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));

					$datab = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_templetlist1')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));

					$datac = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_templetlist2')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));

				$data = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_gong')." WHERE `uniacid`=:uniacid",array(':uniacid' => $_W['uniacid']));

				$datad = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_order')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));

					include $this->template('index');
			}
		}
			

			if($op=='add'){

				$gid=$_GPC['gid'];


					$dataa = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_templetlist')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));

					$datab = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_templetlist1')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));

					$datac = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_templetlist2')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));

				$data = pdo_fetch("SELECT * FROM".tablename('mijia_lending_gong')." WHERE `uniacid`=:uniacid AND `gid`=:gid",array(':uniacid' => $_W['uniacid'],':gid'=>$gid));

					include $this->template('update');
			}
			if($op=='ade'){

				$datas = pdo_fetchall("SELECT * FROM".tablename('mijia_lending_order')." WHERE `uniacid`=:uniacid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':status'=>1));

				// var_dump($data);exit;

				include $this->template('choice');
			}
			if($op=='mmt'){

				include $this->template('about');
			}
		
				
	}

	//登 录
	public function doMobileLogin(){
				global $_W,$_GPC;
				$ops = array('display','add','edits','msm','cny','adds','ddmy','ddmc','mem'); 
				$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';

				if($op=='display'){
		
					include $this->template('login');
		
				}
					
				if($op=='edits'){
					// var_dump($_GPC['name']);exit;
					if(checksubmit()){ //验证是否是表单提交
						$code = $_GPC['code'];  //接受code
						$hash = md5($code . $_W['config']['setting']['authkey']); //微擎验证码加密方式
						if($_GPC['__code'] != $hash) { //对比
							message('你输入的验证码不正确, 请重新输入.');die;
	
						}
					}
					$name=$_GPC['name'];
					$pass=$_GPC['pass'];

					$data=pdo_fetch('select * from'.tablename('mijia_lending_admin'). 'where'." `uniacid`=:uniacid and `pass`=:pass and `name`=:name",array(':uniacid'=>$_W['uniacid'],':pass'=>$pass,':name'=>$name));
					if($data){
						$phone=$data['phone'];
						isetcookie ('name', "$name", 3600);
						isetcookie ('phone', "$phone", 3600);

						message("登录成功！",$this->createMobileUrl('Index'),"success");

					}else{

						message("登录失败！用户名或者密码不正确！");die;
					}
				
				
			}

				if($op=='msm'){
					include $this->template('verification');
				}

				if($op=='ddmy'){
					$code=$_GPC['code'];
					$codey=$_GPC['codey'];

					if($code !== $codey){

						message('你输入的验证码不正确, 请重新获取验证码！');die;
					}

					include $this->template('mima');

				}

				if($op=='ddmc'){

						$pass=$_GPC['pass'];
						$password1=$_GPC['pwd2'];

						if(empty($pass)){

							message('密码为空请重新填写!');die;
						}

						if($pass !== $password1){

							message('两次密码不一致！');die;

						}

						$phone=$_GPC['phone'];

						if(empty($phone)){

							message('验证码时间过期，请重新获取！');die;
						}

						

						$data=pdo_fetch('select * from'.tablename('mijia_lending_admin').' WHERE '. "`uniacid`=:uniacid AND `phone`=:phone",array(':uniacid'=>$_W['uniacid'],':phone'=>$phone));

						if($data){
							$res=pdo_update('mijia_lending_admin',array('pass'=>$pass),array('phone'=>$phone));

							if($res){

							message("修改成功",$this->createMobileUrl('Login'),"success");
							}

						}else{

							message("账号不存在，请去注册！",$this->createMobileUrl('Zhuce'),"error");
						}


						

				}
				if($op=='mem'){
					$phone=$_GPC['phone'];

					$da=pdo_fetch('select * from'.tablename('mijia_lending_duanxin').' WHERE '. "`uniacid`=:uniacid",array(':uniacid'=>$_W['uniacid']));
					if($da){

						message('请配置短信的APPKEY');

					}
					$keys=$da['appkey'];
					$ids=$da['tplid'];

					$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
		            $code = rand(0001,9999);//验证码生成格式，请生成4-8位，数字或字母随机组合

		            $smsConf = array(
		                "key"       => "$keys", //您申请的APPKEY
		                "mobile"    => "$phone", //接受短信的用户手机号码
		                "tpl_id"    => "$ids", //您申请的短信模板ID，根据实际情况修改
		                "tpl_value" => "#code#=".$code."&#company#=聚合数据"//您设置的模板变量，根据实际情况修改
		            );

		            isetcookie ('code', "$code", 360);
		            isetcookie ('phone', "$phone", 360);
		            $content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信
		            if($content){
		            	$result = json_decode($content,true);
		                $error_code = $result['error_code'];

		                if($error_code == 0){
		                	// 短信ID：".$result['result']['sid']
		                	echo 1;

		                }else{

		                	echo 0;

		                	
		                }
				}
			}



	}

	// 注册
	public function doMobileZhuce(){
			global $_W,$_GPC;
			$ops = array('display','add','edit'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){

				include $this->template('zhuce');
			}

			if($op=='add'){

				$phone=$_GPC['phone'];

				$data=pdo_fetch('select * from'.tablename('mijia_lending_admin').' WHERE '. "`uniacid`=:uniacid AND `phone`=:phone",array(':uniacid'=>$_W['uniacid'],':phone'=>$phone));

				if($data){

					echo 2;die;
				}

				$da=pdo_fetch('select * from'.tablename('mijia_lending_duanxin').' WHERE '. "`uniacid`=:uniacid",array(':uniacid'=>$_W['uniacid']));

				// isetcookie ('phone', "$phone", 360);
				if($da){

						$keys=$da['appkey'];
						$ids=$da['tplid'];



					$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
		            $code = rand(0001,9999);//验证码生成格式，请生成4-8位，数字或字母随机组合

		            $smsConf = array(
		                "key"       => "$keys", //您申请的APPKEY
		                "mobile"    => "$phone", //接受短信的用户手机号码
		                "tpl_id"    => "$ids", //您申请的短信模板ID，根据实际情况修改
		                "tpl_value" => "#code#=".$code."&#company#=聚合数据"//您设置的模板变量，根据实际情况修改
		            );

		            isetcookie ('code', "$code", 360);
		            $content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信
		            if($content){
		            	$result = json_decode($content,true);
		                $error_code = $result['error_code'];

		                if($error_code == 0){
		                	// 短信ID：".$result['result']['sid']
		                	echo 1;

		                }else{

		                	echo 0;

		                	
		                }

					}
					

				}else{

					message('请配置短信的APPKEY');


				}
			

   			}
   			if($op=='edit'){
   				$code=$_GPC['code'];
   				$codes=$_GPC['codes'];
   				$name=$_GPC['name'];


   				$user=$_W['openid'];

   				if(empty($user)){
   					$status=0;
   				}else{

   					$status=1;
   				}

   				$data=pdo_fetch('select name from'.tablename('mijia_lending_admin').' WHERE '. "`uniacid`=:uniacid AND `name`=:name",array(':uniacid'=>$_W['uniacid'],':name'=>$name));

   				if($data){
   					message('你的用户名已经存在！请从新输入！');die;
   				}

   				if($code !== $codes){

   					message('你输入的验证码不正确, 请重新获取验证码！');
   				}

   				// if(preg_match('/^[a-zA-Z\x{4e00}-\x{9fa5}]{6,20}$/u',$name)) {



						
						$data['name']=$_GPC['name'];
				        $data['pass']=$_GPC['password'];
				        $data['phone']=$_GPC['phone'];
				        $data['atime']=$_W['timestamp'];
				        $data['status']=$status;
				        $data['uniacid']=$_W['uniacid'];
				        $data['openid']=$_W['openid'];
		   


		        		$info=pdo_insert('mijia_lending_admin',$data);
		        	if($info){
				        	message("注册成功！",$this->createMobileUrl('Login'),"success");
				        }
				// }else{

				// 	    	message("用户名由2-16位数字或字母、汉字、下划线组成!");die;
					       
				// 	    }
		        
		        


		          

   			}

	}
	public function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost ){
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }else{
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }

	// 管理者注册
	public function doMobileAdmins() {
			global $_W,$_GPC;
			$ops = array('display'); 
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

				if ($_W['fans']['follow']==0) {
					$urls='../attachment/qrcode_'.$_W['uniacid'].'.jpg';
					load()->func('tpl');

					include $this->template("guanzhu");	
				}else{
					//获取个人信息头像，昵称
					$openid = $_W['openid'];
					$arr = mc_oauth_userinfo($openid);
					$userinfo['headimgurl'] = $arr['headimgurl'];
					$userinfo['nickname'] = $arr['nickname'];
					$image = $userinfo['headimgurl'];
					$name = $userinfo['nickname'];

					$lock = pdo_fetch("SELECT * FROM".tablename('mijia_lending_member')." WHERE `uniacid`=:uniacid and `openid`=:openid",array(':uniacid' => $_W['uniacid'],':openid'=>$openid));

					if($lock){
						message("申请失败！你已经申请！");
					}else{

						$data['openid']=$openid;
						$data['name']=$name;
						$data['uniacid']=$_W['uniacid'];
						$data['atime']=$_W['timestamp'];
						$data['status']=0;
						$data['tatus']=0;

						$info=pdo_insert('mijia_lending_member',$data);
						if($info){

					 	message("注册成功！等待审核！",$this->createMobileUrl('Index'),"success");
					 }


					}

					
					
				}
		
	}

		//个人中心
	public function doMobilePersonal(){
			global $_W,$_GPC;
			$ops = array('display','mma','mmb','mmc','mmd','mme'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}

			if($op=='display'){
				
					$name=$_GPC['name'];

					$lock = pdo_fetch("SELECT * FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `name`=:name",array(':uniacid' => $_W['uniacid'],':name'=>$name));


					include $this->template('member');
				

			}
			
			if($op=='mma'){
				if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}

				$names=$_GPC['name'];
				// var_dump($name);exit;

				$lock = pdo_fetch("SELECT * FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `name`=:name",array(':uniacid' => $_W['uniacid'],':name'=>$names));

					if(checksubmit()){ //验证是否是表单提交
					if(empty($_GPC['pass'])){

						message("老密码不能为空");die;
						
					}
					if(empty($_GPC['passd'])){

						message("新密码不能为空");die;
						
					}
					$aid=$_GPC['aid'];
					$pass=$_GPC['pass'];
					$passd=$_GPC['passd'];
					$locks = pdo_fetch("SELECT * FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `aid`=:aid",array(':uniacid' => $_W['uniacid'],':aid'=>$aid));


					if($pass !== $locks['pass']){

						message("原始密码不正确！请从新输入.");die;
						
					}

					if(preg_match('/^[a-zA-Z\d_]{4,8}$/',$passd)) {
						
						$res=pdo_update('mijia_lending_admin',array('pass'=>$_GPC['passd']),array('aid'=>$_GPC['aid']));

						if($res){

									setcookie("name");
									message("修改成功",$this->createMobileUrl('Login'),"success");die;
							}
					    }else{

					    	message("密码由首字母4-8位数字组成!");die;
					       
					    }
					


					}

				include $this->template('xiugai');

			}
			// 完善资料
			if($op=='mmb'){
				if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}
				$name=$_GPC['name'];
				$phone=$_GPC['phone'];
				$lock = pdo_fetch("SELECT * FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `name`=:name",array(':uniacid' => $_W['uniacid'],':name'=>$name));

				if(checksubmit()){

					if(empty($_GPC['site'])){

						message("年龄不能为空");die;
						
					}
					if(empty($_GPC['hao'])){

						message("爱好不能为空");die;
						
					}
					if(empty($_GPC['address'])){

						message("地址不能为空");die;
						
					}


						$data['site']=$_GPC['site'];
						$data['hao']=$_GPC['hao'];
						$data['address']=$_GPC['address'];
						$res=pdo_update('mijia_lending_admin',$data,array('aid'=>$_GPC['aid']));

						if($res){
							message("资料修改成功!",$this->createMobileUrl('Personal',array('op'=>'mmc')),"success");die;
						}else{
							message("资料修改失败!");die;
						}

					
					
					}

					

				



			include $this->template('infor');

			}

			if($op=='mmc'){
				if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}
				$name=$_GPC['name'];
				$phone=$_GPC['phone'];
				$lock=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `name`=:name",array(':uniacid' => $_W['uniacid'],':name'=>$name));

				include $this->template('infor1');
			}
			if($op=='mmd'){
					


				if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}
				$name=$_GPC['name'];
				$phone=$_GPC['phone'];
				$mmt=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `name`=:name",array(':uniacid' => $_W['uniacid'],':name'=>$name));

				$datas=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admins')." WHERE `uniacid`=:uniacid and `phone`=:phone and `status`=:status",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':status'=>1));

				if($datas){
					message("你的信息已经审核通过，可以取申请额度！",$this->createMobileUrl('Edu'),"success");
				}

				if(checksubmit()){
					load()->func('tpl');
					load()->func('file'); //调用上传函数

					if ((($_FILES["pic1"]["type"] == "image/gif")
					|| ($_FILES["pic1"]["type"] == "image/jpeg")
					|| ($_FILES["pic1"]["type"] == "image/png")))
					  {
					  if ($_FILES["pic1"]["error"] > 0)
					    {
					     message("身份证正面图片不能为空");die;
					    }
					  else
					    {  
					    $dir_url='../attachment/qrcode_'.$_W['uniacid']."/"; //上传路径
						mkdirs($dir_url); //创建目录
					    }
					  }
					else
					  {
					   message("上传的文件类型不符合图片类型！");die;
					  }

				if ((($_FILES["pic2"]["type"] == "image/gif")
					|| ($_FILES["pic2"]["type"] == "image/jpeg")
					|| ($_FILES["pic2"]["type"] == "image/png")))
					  {
					  if ($_FILES["pic2"]["error"] > 0)
					    {
					     message("身份证反面图片不能为空");die;
					    }
					  else
					    {  
					    $dir_url='../attachment/qrcode_'.$_W['uniacid']."/"; //上传路径
						mkdirs($dir_url); //创建目录 
					    }
					  }
					else
					  {
					   message("上传的文件类型不符合图片类型！");die;
					  }

				if ((($_FILES["file0"]["type"] == "image/gif")
					|| ($_FILES["file0"]["type"] == "image/jpeg")
					|| ($_FILES["file0"]["type"] == "image/png")))
					  {
					  if ($_FILES["file0"]["error"] > 0)
					    {
					     message("头像图片不能为空");die;
					    }
					  else
					    {
					     // "Upload: " . $_FILES["file0"]["name"] . "<br />";
					     // "Type: " . $_FILES["pic1"]["type"] . "<br />";
					     
					    $dir_url='../attachment/qrcode_'.$_W['uniacid']."/"; //上传路径
						mkdirs($dir_url); //创建目录
						   
					    }
					  }
					else
					  {
					   message("上传的文件类型不符合图片类型！");die;
					  }

					if(empty($_GPC['phonede'])){

						message("手机服务密码不能为空");die;
						
					}
					if(empty($_GPC['yinhang'])){

						message("银行卡号不能为空");die;
						
					}
					if(empty($_GPC['zhifu'])){

						message("支付宝账号不能为空");die;
						
					}
					if(empty($_GPC['user'])){

						message("真实姓名不能为空");die;
						
					}
					if(empty($_GPC['shen'])){

						message("身份证账号不能为空");die;
						
					}
					if(empty($_GPC['user'])){

						message("真实姓名不能为空");die;
						
					}
					

						$data['aid']=$_GPC['aid'];
						// $data['name']=$_GPC['name'];
						$data['user']=$_GPC['user'];
						$data['phone']=$_GPC['phone'];
						$data['phonede']=$_GPC['phonede'];
						$data['yinhang']=$_GPC['yinhang'];
						$data['zhifu']=$_GPC['zhifu'];
						$data['qq']=$dir_url;
						$data['pic1']=$_FILES["pic1"]["name"];
						$data['pic2']=$_FILES["pic2"]["name"];
						$data['pic']=$_FILES["file0"]["name"];
						$data['shen']=$_GPC['shen'];
						$data['shtime']=$_W['timestamp'];
						$data['status']=0;
						$data['uniacid']=$_W['uniacid'];

						
						$info=pdo_insert('mijia_lending_admins',$data);
							if($info){
						move_uploaded_file($_FILES["pic2"]["tmp_name"],$dir_url.$_FILES["pic2"]["name"]); 
						move_uploaded_file($_FILES["file0"]["tmp_name"],$dir_url.$_FILES["file0"]["name"]);

						move_uploaded_file($_FILES["pic1"]["tmp_name"],$dir_url.$_FILES["pic1"]["name"]);





						message("申请成功！等待评估！",$this->createMobileUrl('Index'),"success");

						}else{

							unlink($dir_url.$_FILES["file0"]["name"]);
							unlink($dir_url.$_FILES["pic1"]["name"]);
							unlink($dir_url.$_FILES["pic2"]["name"]);

							 message("申请失败!请联系客服！");die;
						}
		
				}

				$mmts=pdo_fetchall("SELECT * FROM".tablename('mijia_lending_zige')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$phone));


				if($mmts){

					include $this->template('renzheng');

				}else{

					message("还没有购买资格！请去购买资格！",$this->createMobileUrl('Goumai'),"success");die;

				}



				
			}
			if($op=='mme'){
				$name=$_GPC['name'];
				$phone=$_GPC['phone'];
				
			$sql = "SELECT * FROM ".tablename('mijia_lending_zige')."as z ,".tablename('mijia_lending_order')."as o WHERE z.oid=o.oid AND z.`uniacid`=:uniacid AND z.`name`=:name AND z.`phone`=:phone";

			// var_dump($sql);
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		            ':name'    =>$name,
		            ':phone'   =>$phone
		        ));

				include $this->template('choicewo');
				
			}
			

	}
	// 资格
	public function  doMobileZige(){
			global $_W,$_GPC;	
			$ops = array('display',); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
				if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}
				$oid=$_GPC['oid'];
				$name=$_GPC['name'];
				$phone=$_GPC['phone'];

				$mmt=pdo_fetch("SELECT * FROM".tablename('mijia_lending_zige')." WHERE `uniacid`=:uniacid and `phone`=:phone and `oid`=:oid and `statusz`=:statusz",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':oid'=>$oid,':statusz'=>1));

				// var_dump($mmt);exit;

				if($mmt){
					

// 这个地方还要判断客户是否已经核实完资料了。
					
					$mmts=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admins')." WHERE `uniacid`=:uniacid and `phone`=:phone and `status`=:status",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':status'=>1));

						if($mmts){

							message("资料已经审核完毕！请去申请额度",$this->createMobileUrl('Personal'),"success");die;

						}else{

						message("请去核实资料！或者查看进度！",$this->createMobileUrl('Personal'),"success");die;
						}

					


				}else{


					$oid=$_GPC['oid'];
					isetcookie ('oid', "$oid");
					message("还没有购买资格！请去购买资格！",$this->createMobileUrl('Goumai'),"success");die;
				}


			}
	}

	// 购买资格
	public function  doMobileGoumai(){
			global $_W,$_GPC;	
			$ops = array('display','adfs'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
				// var_dump($oid);exit;
				if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}
				if(empty($_GPC['oid'])){

					message("信息已过时，请重新选择！",$this->createMobileUrl('Zige'),"error");die;
				}
				$oid=$_GPC['oid'];
				$data = pdo_fetch("SELECT * FROM".tablename('mijia_lending_order')." WHERE `uniacid`=:uniacid AND `oid`=:oid",array(':uniacid' => $_W['uniacid'],':oid'=>$_GPC['oid']));

				include $this->template('confirm');
			}
			if($op =='adfs'){
				// var_dump($_GPC['oid']);die;
				// var_dump($_GPC['oname']);exit;
				$datas = pdo_fetch("SELECT * FROM".tablename('mijia_lending_order')." WHERE `uniacid`=:uniacid AND `oid`=:oid",array(':uniacid' => $_W['uniacid'],':oid'=>$_GPC['oid']));

				include $this->template('choicecon');
			}
	}

	//支付操作
	public function doMobilePays(){
			global $_W,$_GPC;
				$oid=$_GPC['oid'];
				$fill=$_GPC['fill'];
				$oname=$_GPC['oname'];
					$params = array(
						'tid' => "MJ".date('ymd',time()).rand(100000000,999999999),
						'ordersn' => "MJ".date('ymd',time()).rand(100000000,999999999),
						'title' =>"$oname"."资格",         
						'fee' => "$fill",      
						'user' => $_W['member']['uid'],    
			
						);
		        $phone=$_GPC['phone'];
		        $data = pdo_fetch("SELECT * FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid AND `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$phone));
		        $mmt['sid']=$data['sid'];
		        $mmt['name']=$data['name'];
		        $mmt['phone']=$phone;
		        $mmt['ztime']=$_W['timestamp'];
		        $mmt['uniacid']=$_W['uniacid'];
		        $mmt['oid']=$oid;
		        $mmt['aid']=$data['aid'];
		        $mmt['statusz']=0;
		        $mmt['tid']=$params['tid'];   
		        $mmt['fees']=$params['fee'];
		        $mmt['userid']=$params['user'];
		        pdo_insert('mijia_lending_zige',$mmt);

				$this->pay($params);
	}

	// 验证支付
	public function payResult($params) {
			global $_W,$_GPC;
		    //一些业务代码
		    if($params['type'] == 'wechat'){
					$paytype = '微信支付';
			}else if($params['type'] == 'credit'){
					$paytype = '余额支付';
			}else{
					$paytype = '支付宝支付';
				
			}
   	 
      
    
    //根据参数params中的result来判断支付是否成功
    // var_dump($params);exit;
    if ($params['result'] == 'success' && $params['from'] == 'notify') {

            $datas['type']=$paytype;
	        $datas['hstatus']=1;
	        $mmt = pdo_update('mijia_lending_huankuan', $datas, array(
	                        'tid' => $params['tid'],
	                        'uniacid' => $params['uniacid']
	                    ));

	        $sqls = "SELECT * FROM ".tablename('mijia_lending_huankuan')."as e ,".tablename('mijia_lending_edu')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid and e.`tid`=:tid";
				
			$rows = pdo_fetch($sqls,array(
		            ':uniacid' =>$params['uniacid'],
		            ':tid'     =>$params['tid'],
		        ));

          	 $sst= pdo_update('mijia_lending_edu', array('tatus'=>1), array(
                        'edid' => $rows['edid'],
                        'uniacid' => $params['uniacid']
                    ));

          	 	 // 判断客户是否关注，关注就发微信提示，要不就发短信提醒
				
				$mmts=pdo_fetch("SELECT openid FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$order['phone']));

				if($mmts){

					//放款成功通知
						$data2=array(
							'name'=>array(
							'value'=> '还款额度：'.$params['fee'].'时间：'.date("Y-m-d",$rows['hutime']),
							'color'=>'#777777'
								) ,
							'remark'=>array(
							'value'=> '订单号：'.$params['tid'],
							'color'=>'#777777'
									) ,
								
							);

					$account_api = WeAccount::create();
					$account_api->sendTplNotice($mmts['openid'],'Nhzk15a5ihBQ2mGLQofND3Q7OpIzlat0LCEwbyk0uCs', $data2, $url ='', $topcolor = '#FF683F');


					

				}

			$sql = "SELECT * FROM " . tablename('mijia_lending_zige') . " WHERE `tid`=:tid AND `uniacid`=:uniacid";
            $order = pdo_fetch($sql, array(
                ':tid' => $params['tid'],
                ':uniacid' => $params['uniacid']
            ));

        if(($order['statusz'] == 0 ) && ($order['fees'] == $params['fee'])) {
          		$datay['type']=$paytype;
          		$datay['statusz']=1;
          	 $res = pdo_update('mijia_lending_zige', $datay, array(
                        'tid' => $params['tid'],
                        'uniacid' => $params['uniacid']
                    ));
          	 // 判断客户是否关注，关注就发微信提示，要不就发短信提醒
				
				$mmts=pdo_fetch("SELECT openid FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$order['phone']));

				if($mmts){

					//放款成功通知
						$data1=array(
							'first'=>array(
							'value'=> "",
							'color'=>'#777777'
								) ,
							'keyword1'=>array(
							'value'=> '订单号：'.$params['tid'],
							'color'=>'#777777'
									) ,
							'keyword2'=>array(
							'value'=> '申请时间：'.date("Y-m-d H:i:s",$order['ztime']),
							'color'=>'#777777'
									) ,
							'remark'=>array(
							'value'=> '',
							'color'=>'#777777'
									) ,
							);

					$account_api = WeAccount::create();
					$account_api->sendTplNotice($mmts['openid'],'Z9U2q0iVn0c5Cm_A2ICUaPbIhafZrrBd5VXDjKVr6Ds', $data1, $url ='', $topcolor = '#FF683F');					

				}

			}



			


          		

        //此处会处理一些支付成功的业务代码
       
   			}  
   		
		    if ($params['from'] == 'return') {
		        if ($params['result'] == 'success') {
		        	setcookie('oid','');
		            message('支付成功！', $this->createMobileUrl('Personal'), 'success');
		        } else {
		            message('支付失败！',$this->createMobileUrl('Goumai'), 'error');
		        }
		    }
		
}

	//注销登录
	public function  doMobileZhuxiao(){
			global $_W,$_GPC;	
			$ops = array('display'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){

				setcookie('name','');
				setcookie('phone','');

				
				message("退出成功！",$this->createMobileUrl('Login'),"success");die;
				
				

			}
	}
	// 额度
	public function  doMobileEdu(){
			global $_W,$_GPC;	
			$ops = array('display'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';

			if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}
			if($op=='display'){
				$phone=$_GPC['phone'];
				$mmt=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admins')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$phone));

				if($mmt){
						$data=$mmt['status'];

						if($data==0){
							// 跳转到进度页面。

							message("提交的资料还在评估当中！是否要加速审核！",$this->createMobileUrl('Jindu'),"success");
						}else{

							if(checksubmit()){

							$phone=$_GPC['phone'];



								load()->func('tpl');

								$edu=$_GPC['edu'];
								if(empty($edu)){

									message("额度不能为空！");

								}
								if($edu < 18 ){

									message("额度小于18元！处于极度亏损状态！请重新填写。");
								}
								$shijian=$_GPC['shijian'];

								$mmt=pdo_fetch("SELECT * FROM".tablename('mijia_lending_zige')." WHERE `uniacid`=:uniacid and `phone`=:phone and `fees`=:fees",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':fees'=>$shijian));

								if($mmt){

									$user_data = array(
									    'phone' => $_GPC['phone'],
									    'status' => '0',
									    'sid'	=>$_GPC['sid'],
									    'edu'	=>$_GPC['edu'],
									    'shijian'=>$_GPC['shijian'],
									    'edtime' =>$_W['timestamp'],
									    'uniacid'=>$_W['uniacid'],
									    'oid'	 =>$_GPC['oid'],
									    'tatus'  =>'0',
									);

									$info=pdo_insert('mijia_lending_edu',$user_data);

									if(!empty($info)){
		    							$uid = pdo_insertid();

		    							// var_dump($uid);exit;
		   									$sset=pdo_fetch("SELECT * FROM".tablename('mijia_lending_edu')." WHERE `uniacid`=:uniacid and `phone`=:phone and `edid`=:edid",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':edid'=>$uid));	
												
										message("下一步",$this->createMobileUrl('hetong'),"success");

									}
									
								}else{

									message("额度{$shijian}天天数还没有购买权限！请确定权限");
								}



							}

						$sset=pdo_fetch("SELECT * FROM".tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_admins')."as a WHERE e.`uniacid`=:uniacid and e.`phone`=:phone and e.`status`=:status and e.sid=a.sid",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':status'=>0));

						if($sset){

							message("你好！只能同时申请一份额度，等审批通过在申请下一份！谢谢。。");
						}else{

							include $this->template('edu');	
						}



							

						}


				}else{

					message("还没有提交审核资料，请去提交！",$this->createMobileUrl('Personal',array('op'=>'mmd')),"success");

				}
				

			}
	}

	// 合同
	public function  doMobileHetong(){
			global $_W,$_GPC;	
			$ops = array('display','add','edit','edits'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if(empty($_GPC['name'])){

					message("信息不存在，请去登录！",$this->createMobileUrl('Login'),"success");die;
				}

			if($op=='display'){

				include $this->template('hetong');	

			}
			if($op=='add'){
				$phone=$_GPC['phone'];
				$sset=pdo_fetch("SELECT * FROM".tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_admins')."as a WHERE e.`uniacid`=:uniacid and e.`phone`=:phone and e.`status`=:status and e.sid=a.sid",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':status'=>0));

				if($sset){

					$mmt=pdo_fetch("SELECT * FROM".tablename('mijia_lending_hetong')." WHERE `uniacid`=:uniacid and `phone`=:phone and `hestatus`=:hestatus",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':hestatus'=>0));
					if($mmt){
						message("还在审核！请等待！",$this->createMobileUrl('Personal'),"success");die;

					}else{

						include $this->template('hetongqian');

					}


					

				}else{

					message("没有要签订的合同！",$this->createMobileUrl('Index'),"success");die;
				}
			}
			if($op=='edit'){
				load()->func('tpl');
				if($_GPC['user'] !== $_GPC['users']){

					message("合同名字和报备名字不相同！");
				}
				if($_GPC['shens'] !== $_GPC['shen']){

					message("身份证和报备身份证不相同！");
				}
				$name = $_GPC['users'];
				$kemu = $_GPC['shens'];
				$phone = $_GPC['phone'];
				$edus = $_GPC['edus'];

				//创建图像            
				$im = imagecreatefromjpeg('../addons/mejia_lending/static/img/moban.jpg');
				$black = imagecolorallocate($im, 0, 0, 0);
				$text = $name;
				
				//字体
				$font = '../addons/mejia_lending/static/font/STZHONGS.TTF'; 
				                                 
				//字体颜色
				$blacka = imagecolorallocate($im, 160,160, 163);  
		
				//姓名
				imagettftext($im, 12, 0, 131, 176, $blacka, $font, $name);   
				imagettftext($im, 12, 0, 131, 176, $blacka, $font, $name); 
				//身份证号
				imagettftext($im, 12, 0, 456, 177, $blacka, $font, $kemu);   
				imagettftext($im, 12, 0, 456, 177, $blacka, $font, $kemu); 
				//手机号
				imagettftext($im, 12, 0, 131, 220, $blacka, $font, $phone);   
				imagettftext($im, 12, 0, 131, 220, $blacka, $font, $phone); 
				//金额               
				imagettftext($im, 14, 0, 163, 303, $blacka, $font, $edus);   
				imagettftext($im, 14, 0, 163, 303, $blacka, $font, $edus); 
				// //科目
				// imagettftext($im, 14, 0, 251, 302, $blacka, $font, $kemu);  
				// imagettftext($im, 14, 0, 252, 302, $blacka, $font, $kemu); 
				// //科目综合（时间表）
				// imagettftext($im, 14, 0, 442, 585, $blacka, $font, $kemu);  
				// imagettftext($im, 14, 0, 443, 585, $blacka, $font, $kemu); 
				// //性别
				// imagettftext($im, 14, 0, 251, 341, $blacka, $font, $sex);  
				// imagettftext($im, 14, 0, 252, 341, $blacka, $font, $sex); 
				// //学校
				// imagettftext($im, 14, 0, 251, 452, $blacka, $font, $school);   
				// imagettftext($im, 14, 0, 252, 452, $blacka, $font, $school);    
				
				ob_end_clean() ;
				
				$data = time();
				$img_url = "../addons/mejia_lending/static/images/".$data."qqq.jpg";
				
				$urls = $data."qqq.jpg";
				imagejpeg($im,$img_url);

				$datas['edid']=$_GPC['edid'];
				$datas['users']=$_GPC['users'];
				$datas['phone']=$_GPC['phone'];
				$datas['shens']=$_GPC['shens'];
				$datas['edus']=$_GPC['edus'];
				$datas['hetime']=$_W['timestamp'];
				$datas['hestatus']=0;
				$datas['uniacid']=$_W['uniacid'];
				$datas['url']=$img_url;
				$info=pdo_insert('mijia_lending_hetong',$datas);

				if($info){

					message("签订成功,等待审核！",$this->createMobileUrl('Personal'),"success");
				}
			}
			if($op=='edits'){
				$phone=$_GPC['phone'];
				$sset=pdo_fetchall("SELECT * FROM".tablename('mijia_lending_hetong')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$phone));

				include $this->template('new');	
			}
			




	}
	// 我的订单
	public function  doMobileDingdan(){
			global $_W,$_GPC;	
			$ops = array('display'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';

		if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}
		if($op=='display'){
						//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 2; 
			$condition='';
						//关键字查询
		if (!empty($_GPC['keyword'])) {
				$condition .= " AND (z.phone LIKE '%{$_GPC['keyword']}%') ";
			} 

			$phone=$_GPC['phone'];

			$sql = "SELECT * FROM ".tablename('mijia_lending_zige')."as z ,".tablename('mijia_lending_admin')."as a ,".tablename('mijia_lending_order')."as o WHERE z.aid=a.aid AND z.oid=o.oid AND z.`uniacid`=:uniacid and z.`phone`=:phone".$condition."  ORDER BY z.zid DESC LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;

			// var_dump($sql);
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		            ':phone'   =>$phone,
		        ));

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_zige')."as z ,".tablename('mijia_lending_admin')."as a ,".tablename('mijia_lending_order')."as o WHERE z.aid=a.aid AND z.oid=o.oid AND z.`uniacid`=:uniacid and z.`phone`=:phone".$condition;
			$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			        ':phone'   =>$phone,
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

			include $this->template('dingdan');	

			}


	}
	// 审批的进度
	public function  doMobileJindu(){
			global $_W,$_GPC;	
			$ops = array('display','add','adds'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}
		if($op=='display'){

			$phone=$_GPC['phone'];

			$sset=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admins')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$phone));	

			// var_dump($sset);exit;
			$sql = "SELECT * FROM ".tablename('mijia_lending_yuanyin')."as y ,".tablename('mijia_lending_admins')."as a WHERE y.sid=a.sid AND y.`uniacid`=:uniacid";

		    	$row = pdo_fetch($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ));


			include $this->template('jindu');	

			}
			// 加速审核
			if($op=='add'){
				$sid=$_GPC['sid'];

				$data=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admins')." WHERE `uniacid`=:uniacid and `phone`=:phone and `sid`=:sid",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':sid'=>$sid));

				message("下一步",$this->createMobileUrl('Index',array('op'=>'ade')),"success");

			}
			if($op=='adds'){
				$sid=$_GPC['sid'];

				$data=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admins')." WHERE `uniacid`=:uniacid and `phone`=:phone and `sid`=:sid",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':sid'=>$sid));

				message("下一步",$this->createMobileUrl('Index',array('op'=>'ade')),"success");
			}

	}

	// 我申请额度
	public function  doMobileEdulist(){
			global $_W,$_GPC;	
			$ops = array('display','add','adds'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}
		if($op=='display'){
						//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 2; 
			$condition='';

			$phone=$_GPC['phone'];


			$sql = "SELECT * FROM ".tablename('mijia_lending_hetong')." WHERE `uniacid`=:uniacid and `phone`=:phone".$condition."  ORDER BY heid DESC LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;

			// var_dump($sql);
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		            ':phone'   =>$phone,
		        ));

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_hetong')." WHERE `uniacid`=:uniacid and `phone`=:phone".$condition;
			$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			        ':phone'   =>$phone,
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

			include $this->template('woedu');	

			}



	}

	// 还款
	public function doMobileHuankuan(){
				global $_W,$_GPC;
				$ops = array('display','mma'); 
				$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
				if(empty($_GPC['name'])){

					message("信息已过时，请重新登录！",$this->createMobileUrl('Login'),"success");die;
				}
			if($op=='display'){
				load()->func('tpl');
				$dang=date("Y-m-d");
				$dangshi=strtotime($dang);
				$condition=''; 
				$phone=$_GPC['phone'];

				$sql = "SELECT * FROM ".tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_hetong')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid AND o.`hetimez`=:hetimez and e.`phone`=:phone and e.`tatus`=:tatus";

			// var_dump($sql);
			
		    $res = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		            ':hetimez' =>$dangshi,
		            ':phone'   =>$phone,
		            ':tatus'   =>'0',
		        ));
				include $this->template("private");
			}

			if($op=='mma'){

				$edid=$_GPC['edid'];
				$phone=$_GPC['phone'];

				// var_dump($phone);exit;

				$sql = "SELECT * FROM ".tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_order')."as o WHERE e.oid=o.oid AND e.`uniacid`=:uniacid AND e.`edid`=:edid and e.`phone`=:phone";
			
		    	$res = pdo_fetch($sql,array(
		            ':uniacid' => $_W['uniacid'],
		            ':edid' =>$edid,
		            ':phone'   =>$phone,
		        ));


				$fill=$res['edu'];
				$oname=$res['oname'];

				// var_dump($res);exit;
					$params = array(
						'tid' => "MJ".date('ymd',time()).rand(100000000,999999999),
						'ordersn' => "MJ".date('ymd',time()).rand(100000000,999999999),
						'title' =>"$oname"."资格",         
						'fee' => "$fill",      
						'user' => $_W['member']['uid'],    
			
						);

					// var_dump($params);exit;
		        $phone=$_GPC['phone'];
		        $heid=$_GPC['heid'];
		        $data = pdo_fetch("SELECT * FROM".tablename('mijia_lending_hetong')." WHERE `uniacid`=:uniacid AND `phone`=:phone and `heid`=:heid",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':heid'=>$heid));

		        $mmt['names']=$data['users'];
		        $mmt['phone']=$phone;
		        $mmt['hutime']=$_W['timestamp'];
		        $mmt['uniacid']=$_W['uniacid'];
		        $mmt['shenfen']=$data['shens'];
		        $mmt['heid']=$heid;
		        $mmt['edid']=$edid;
		        $mmt['oid']=$res['oid'];
		        $mmt['edums']=$res['edu'];
				$mmt['oname']=$res['oname'];
		        $mmt['hstatus']=0;
		        $mmt['tid']=$params['tid'];   
		        $mmt['userid']=$params['user'];
		        pdo_insert('mijia_lending_huankuan',$mmt);

		        // var_dump($mmt);exit;
				$this->pay($params);

				
			}

	}


// ---------------------------------------------------PC端------------------------------------------

	//参数设置
	public function doWebProduct(){
			global $_W,$_GPC;	
			$ops = array('display','yanzheng','xinxi'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
				load()->func('tpl');
				$lock = pdo_fetch("SELECT * FROM".tablename('mijia_lending_product')." WHERE `uniacid`=:uniacid",array(':uniacid' => $_W['uniacid']));

				if(checksubmit()){
					$d = array(					
					'forcegz' => $_GPC['forcegz'],
					'uniacid'=>$_W['uniacid'],
				
						);

				$data = pdo_fetch("SELECT * FROM".tablename('mijia_lending_product')." WHERE `uniacid`=:uniacid",array(':uniacid' => $_W['uniacid']));

				if($data){
					$info = pdo_update('mijia_lending_product',$d,array('uniacid' => $_W['uniacid']));
						if($info){

							message('修改成功',$this->createWebUrl('Product'));
						}

				}else{

					$info = pdo_insert('mijia_lending_product',$d);

					if($info){

						message('添加信息成功',$this->createWebUrl('Product'));
					}
				}
	
				
				}				
				include $this->template('setting');
			}
			if($op=='yanzheng'){

				if(checksubmit()){
						$data['appkey']=$_GPC['appkey'];
						$data['tplid']=$_GPC['tplid'];
						$data['uniacid']=$_W['uniacid'];
						$data['motime']=$_W['timestamp'];

					$ass=pdo_insert('mijia_lending_duanxin',$data);

					if($ass){
						message("添加成功！",$this->createWebUrl('Product'),"success");
					}



				}



				include $this->template('setting');
			}

			if($op=='xinxi'){
				include $this->template('setting');
			}

			
			}

	//管理者列表
	public function doWebAdminlist(){
				global $_W,$_GPC;
				$ops = array('display','mda','mdb','mdc','mdd','mde'); 
				$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
				$pageindex = max(1,intval($_GPC['page']));
				$pagesize = 8;
				$condition='`uniacid` = :uniacid'; 
				//上班休班
				$tatus = isset($_GPC['tatus']) ? intval($_GPC['tatus']) : -1;
				if ($tatus >= 0) {
					$condition .= " and tatus LIKE {$_GPC['tatus']}";
				}
				//关键字查询
				if (!empty($keyword)) {
					$condition .= " and (name LIKE '%{$keyword}%' or phone LIKE '%{$keyword}%')";
				} 

				$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_member') . 'WHERE ' . $condition;
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));

				$sql = 'SELECT * FROM ' . tablename('mijia_lending_member') . 'WHERE ' . $condition . ' ORDER BY `adid` ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
		        $res = pdo_fetchall($sql, array(
		            ':uniacid' => $_W['uniacid'],
		            
		        ) , 'adid');


				$pager = pagination($total, $pageindex, $pagesize);

				include $this->template("web/adminlist/adminlist");
			}
			if($op=='mdb'){

			$lock = pdo_fetch("SELECT * FROM".tablename('mijia_lending_member')." WHERE `uniacid`=:uniacid and `adid`=:adid AND `status`=:status",array(':uniacid' => $_W['uniacid'],':adid'=>$_GPC['adid'],':status'=>1));
			if($lock){

				$res=pdo_update('mijia_lending_member',array('tatus'=>1),array('adid'=>$_GPC['adid']));
				if($res){
					message("已修改！上班啦！",$this->createWebUrl('Adminlist'),"success");
				}

			}else{

					message("修改失败！还没审核通过！",$this->createWebUrl('Adminlist'),"error");
			}

			
			}
			if($op=='mda'){

				$res=pdo_update('mijia_lending_member',array('tatus'=>0),array('adid'=>$_GPC['adid']));
				if($res){
					message("已修改！休假嗨皮愉快！",$this->createWebUrl('Adminlist'),"success");
				}
			}
			if($op=='mdc'){
				$res=pdo_update('mijia_lending_member',array('status'=>1),array('adid'=>$_GPC['adid']));

				if($res){
				message("已经启用",$this->createWebUrl('Adminlist'),"success");
				}
			}
			if($op=='mdd'){
				$res=pdo_update('mijia_lending_member',array('status'=>0),array('adid'=>$_GPC['adid']));
				if($res){

				message("已经禁用",$this->createWebUrl('Adminlist'),"success");
				}
			}
			if($op=='mde'){
				$res=pdo_update('mijia_lending_member',array('phone'=>$_GPC['phone']),array('adid'=>$_GPC['adid']));

				if($res){
				message("完善成功",$this->createWebUrl('Adminlist'),"success");
				}
			}

	}

	// 公告管理
	public function doWebGong(){
			global $_W,$_GPC;
			$ops = array('display','add','edit','insert','dele'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';

			if($op=='display'){
			load()->func('tpl');
			if (empty($starttime) || empty($endtime)) {
			    $starttime = strtotime('-1 month');
			    $endtime = TIMESTAMP;
			}
				//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 8; 
			$condition = '`uniacid` = :uniacid';

			if (!empty($_GPC['time'])) {
			        $starttime = strtotime($_GPC['time']['start']);
			        $endtime = strtotime($_GPC['time']['end']) + 86399;
			       
			        $condition .= " AND gtime >= $starttime AND gtime <= $endtime ";
			       
			        }		
			//关键字查询
			if (!empty($keyword)) {
				$condition .= " AND (title LIKE '%{$keyword}%')";
			} 

			$sql = 'SELECT * FROM ' . tablename('mijia_lending_gong') . 'WHERE ' . $condition . ' ORDER BY `gid` ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ) , 'gid');

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_gong') . 'WHERE ' . $condition;
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

			include $this->template("web/gong/add");
			

			}
			if($op=='add'){

				$options = array(
					'width'  => 300, // 上传后图片最大宽度
    				'global'=>false 
   					);
				
				$data = array(
				
					'title' => $_GPC['title'],
					'pic' => $_GPC['logo'],
					'one' => $_GPC['role'],
					'uniacid'=>$_W['uniacid'],
					'gtime'=>$_W['timestamp'],
					'status'=>0,
						);

				// var_dump($data);exit;
				
				$ass=pdo_insert('mijia_lending_gong',$data);
				if($ass){
					message("添加成功",$this->createWebUrl('Gong'),"success");
				}else{
					message("添加失败",$this->createWebUrl('Gong'),"error");
				}
			}
		if($op=='dele'){
			$gid=$_GPC['gid'];

			// var_dump($gid);exit;

			$data=pdo_delete('mijia_lending_gong',array('gid' => $_GPC['gid'],'uniacid' => $_W['uniacid']));

			if($data){

				message("删除成功",$this->createWebUrl('Gong'),"success");
			}
		}

			
	}

	// 幻灯片管理
	public function doWebTempletlist(){
			global $_W,$_GPC;
			$ops = array('display','add','edit','mmt','adds','mmd','mda','mdb','mdc','mddd','mde','mdf','mdg','mdh','mdr'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';

			if($op=='display'){
				load()->func('tpl');
			if (empty($starttime) || empty($endtime)) {
			    $starttime = strtotime('-1 month');
			    $endtime = TIMESTAMP;
			}
				//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 8; 
			$condition = '`uniacid` = :uniacid';

			if (!empty($_GPC['time'])) {
			        $starttime = strtotime($_GPC['time']['start']);
			        $endtime = strtotime($_GPC['time']['end']) + 86399;
			       
			        $condition .= " AND ttime >= $starttime AND ttime <= $endtime ";
			       
			        }		
			//关键字查询
			if (!empty($keyword)) {
				$condition .= " AND (title LIKE '%{$keyword}%')";
			} 

			$sql = 'SELECT * FROM ' . tablename('mijia_lending_templetlist') . 'WHERE ' . $condition . ' ORDER BY `tid` ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ) , 'tid');

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_templetlist') . 'WHERE ' . $condition;
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

				include $this->template("web/templetlist/templetlist");
			}

			if($op=='add'){

				if(checksubmit()){ //验证是否是表单提交
					if(empty($_GPC['name'])){

						message("标题不能为空",$this->createWebUrl('Templetlist'),"error");
						
					}
					if(empty($_GPC['thumb'])){

						message("图片不能为空",$this->createWebUrl('Templetlist'),"error");
						
					}
					$data['title']=$_GPC['name'];
					$data['pic']=$_GPC['thumb'];
					$data['uniacid']=$_W['uniacid'];
					$data['ttime']=$_W['timestamp'];
					$data['status']=0;

					$info = pdo_insert('mijia_lending_templetlist',$data);

					if($info){
						message("上传成功",$this->createWebUrl('Templetlist'),"success");
					}


					}

				include $this->template("web/templetlist/templetlist");
			}
			if($op=='edit'){
					load()->func('tpl');
			if (empty($starttime) || empty($endtime)) {
			    $starttime = strtotime('-1 month');
			    $endtime = TIMESTAMP;
			}
				//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 8; 
			$condition = '`uniacid` = :uniacid';

			if (!empty($_GPC['time'])) {
			        $starttime = strtotime($_GPC['time']['start']);
			        $endtime = strtotime($_GPC['time']['end']) + 86399;
			       
			        $condition .= " AND ttime >= $starttime AND ttime <= $endtime ";
			       
			        }		
			//关键字查询
			if (!empty($keyword)) {
				$condition .= " AND (title LIKE '%{$keyword}%')";
			} 

			$sql = 'SELECT * FROM ' . tablename('mijia_lending_templetlist1') . 'WHERE ' . $condition . ' ORDER BY `tid` ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ) , 'tid');

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_templetlist1') . 'WHERE ' . $condition;
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

				include $this->template("web/templetlist/templetlist");
			}

			if($op=='mmt'){
				if(checksubmit()){ //验证是否是表单提交
					if(empty($_GPC['title'])){

						message("标题不能为空",$this->createWebUrl('Templetlist'),"error");
						
					}
					if(empty($_GPC['thumb'])){

						message("图片不能为空",$this->createWebUrl('Templetlist'),"error");
						
					}
					$data['title']=$_GPC['title'];
					$data['pic']=$_GPC['thumb'];
					$data['uniacid']=$_W['uniacid'];
					$data['ttime']=$_W['timestamp'];
					$data['status']=0;

					$info = pdo_insert('mijia_lending_templetlist1',$data);

					if($info){
						message("上传成功",$this->createWebUrl('Templetlist'),"success");
					}


					}
				include $this->template("web/templetlist/templetlist");
			}

			if($op=='adds'){
					load()->func('tpl');
			if (empty($starttime) || empty($endtime)) {
			    $starttime = strtotime('-1 month');
			    $endtime = TIMESTAMP;
			}
				//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 8; 
			$condition = '`uniacid` = :uniacid';

			if (!empty($_GPC['time'])) {
			        $starttime = strtotime($_GPC['time']['start']);
			        $endtime = strtotime($_GPC['time']['end']) + 86399;
			       
			        $condition .= " AND ttime >= $starttime AND ttime <= $endtime ";
			       
			        }		
			//关键字查询
			if (!empty($keyword)) {
				$condition .= " AND (title LIKE '%{$keyword}%')";
			} 

			$sql = 'SELECT * FROM ' . tablename('mijia_lending_templetlist2') . 'WHERE ' . $condition . ' ORDER BY `tid` ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ) , 'tid');

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_templetlist2') . 'WHERE ' . $condition;
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);
				include $this->template("web/templetlist/templetlist");
			}

			if($op=='mmd'){
				if(checksubmit()){ //验证是否是表单提交
					if(empty($_GPC['title'])){

						message("标题不能为空",$this->createWebUrl('Templetlist'),"error");
						
					}
					if(empty($_GPC['thumb'])){

						message("图片不能为空",$this->createWebUrl('Templetlist'),"error");
						
					}
					if(empty($_GPC['name'])){

						message("简介不能为空",$this->createWebUrl('Templetlist'),"error");
						
					}
					$data['title']=$_GPC['title'];
					$data['tname']=$_GPC['name'];
					$data['pic']=$_GPC['thumb'];
					$data['uniacid']=$_W['uniacid'];
					$data['ttime']=$_W['timestamp'];
					$data['status']=0;

					$info = pdo_insert('mijia_lending_templetlist2',$data);

					if($info){
						message("上传成功",$this->createWebUrl('Templetlist'),"success");
					}


					}
				include $this->template("web/templetlist/templetlist");
			}
			if($op=='mda'){
				$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_templetlist') . 'WHERE ' ."uniacid=:uniacid AND `status`=:status";
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			        ':status'  => 1
 			    	));

				if($total == 4){
					message("启用失败！最多启用5张图片",$this->createWebUrl('Templetlist'),"error");
				}
				$res=pdo_update('mijia_lending_templetlist',array('status'=>1),array('tid'=>$_GPC['tid']));

				if($res){
				message("已经启用",$this->createWebUrl('Templetlist'),"success");
				}
			}
			if($op=='mdb'){
				$res=pdo_update('mijia_lending_templetlist',array('status'=>0),array('tid'=>$_GPC['tid']));
				if($res){

				message("已经禁用",$this->createWebUrl('Templetlist'),"success");
				}
			}
			if($op=='mdc'){
				$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_templetlist1') . 'WHERE ' ."uniacid=:uniacid AND `status`=:status";
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			        ':status'  => 1
 			    	));

				if($total == 6){
					message("启用失败！最多启用6张图片",$this->createWebUrl('Templetlist'),"error");
				}
				$res=pdo_update('mijia_lending_templetlist1',array('status'=>1),array('tid'=>$_GPC['tid']));

				if($res){
				message("已经启用",$this->createWebUrl('Templetlist'),"success");
				}
			}
			if($op=='mddd'){
				$res=pdo_update('mijia_lending_templetlist1',array('status'=>0),array('tid'=>$_GPC['tid']));
				if($res){

				message("已经禁用",$this->createWebUrl('Templetlist'),"success");
				}
			}
			if($op=='mde'){
				$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_templetlist2') . 'WHERE ' ."uniacid=:uniacid AND `status`=:status";
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			        ':status'  => 1
 			    	));

				if($total == 1){
					message("启用失败！最多启用1张图片",$this->createWebUrl('Templetlist'),"error");
				}
				$res=pdo_update('mijia_lending_templetlist2',array('status'=>1),array('tid'=>$_GPC['tid']));

				if($res){
				message("已经启用",$this->createWebUrl('Templetlist'),"success");
				}
			}
			if($op=='mdf'){
				$res=pdo_update('mijia_lending_templetlist2',array('status'=>0),array('tid'=>$_GPC['tid']));
				if($res){

				message("已经禁用",$this->createWebUrl('Templetlist'),"success");
				}
			}
			if($op=='mdg'){
				$tid=$_GPC['tid'];
				$data=pdo_fetch("SELECT * FROM ".tablename('mijia_lending_templetlist1')." where ".'`uniacid`=:uniacid AND `tid`=:tid',array(':uniacid'=>$_W['uniacid'],':tid'=>$tid));


				if(checksubmit()){ //验证是否是表单提交
					if(empty($_GPC['title'])){

						message("标题不能为空");die;
						
					}
					$mcm=$_GPC['thumb'];
					if(empty($mcm)){
					
					$data['title']=$_GPC['title'];
					// $data['pic']=$_GPC['thumb'];
					$res=pdo_update('mijia_lending_templetlist1',$data,array('tid'=>$_GPC['tid']));
					if($res){
						message("修改成功",$this->createWebUrl('Templetlist'),"success");
					}


					}else{
						$data['title']=$_GPC['title'];
						$data['pic']=$_GPC['thumb'];
						$res=pdo_update('mijia_lending_templetlist1',$data,array('tid'=>$_GPC['tid']));
						if($res){
							message("修改成功",$this->createWebUrl('Templetlist'),"success");
						}
					}
				}
				include $this->template("web/templetlist/update1");

			}
			if($op=='mdr'){
				$tid=$_GPC['tid'];
				$data=pdo_fetch("SELECT * FROM ".tablename('mijia_lending_templetlist')." where ".'`uniacid`=:uniacid AND `tid`=:tid',array(':uniacid'=>$_W['uniacid'],':tid'=>$tid));


				if(checksubmit()){ //验证是否是表单提交
					if(empty($_GPC['title'])){

						message("标题不能为空");die;
						
					}
					$mcm=$_GPC['thumb'];
					if(empty($mcm)){
					
					$data['title']=$_GPC['title'];
					// $data['pic']=$_GPC['thumb'];
					$res=pdo_update('mijia_lending_templetlist',$data,array('tid'=>$_GPC['tid']));
					if($res){
						message("修改成功",$this->createWebUrl('Templetlist'),"success");
					}


					}else{
						$data['title']=$_GPC['title'];
						$data['pic']=$_GPC['thumb'];
						$res=pdo_update('mijia_lending_templetlist',$data,array('tid'=>$_GPC['tid']));
						if($res){
							message("修改成功",$this->createWebUrl('Templetlist'),"success");
						}
					}
				}
				include $this->template("web/templetlist/update");
			}
			if($op=='mdh'){
				$tid=$_GPC['tid'];
				$data=pdo_fetch("SELECT * FROM ".tablename('mijia_lending_templetlist2')." where ".'`uniacid`=:uniacid AND `tid`=:tid',array(':uniacid'=>$_W['uniacid'],':tid'=>$tid));


				if(checksubmit()){ //验证是否是表单提交
					if(empty($_GPC['title'])){

						message("标题不能为空");die;
						
					}
					if(empty($_GPC['tname'])){

						message("简介不能为空");die;
						
					}
					$mcm=$_GPC['thumb'];
					if(empty($mcm)){
					
					$data['title']=$_GPC['title'];
					$data['tname']=$_GPC['tname'];
					$res=pdo_update('mijia_lending_templetlist2',$data,array('tid'=>$_GPC['tid']));
					if($res){
						message("修改成功",$this->createWebUrl('Templetlist'),"success");
					}


					}else{
						$data['title']=$_GPC['title'];
						$data['tname']=$_GPC['tname'];
						$data['pic']=$_GPC['thumb'];
						$res=pdo_update('mijia_lending_templetlist2',$data,array('tid'=>$_GPC['tid']));
						if($res){
							message("修改成功",$this->createWebUrl('Templetlist'),"success");
						}
					}
				}
				include $this->template("web/templetlist/update2");

			}
	}

	// 项目模块管理
	public function doWebOrder(){
			global $_W,$_GPC;
			$ops = array('display','add','aba','abb','edit','edits'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
			//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 8; 
			$condition = '`uniacid` = :uniacid';

			//关键字查询
			if (!empty($keyword)) {
				$condition .= " and (clid LIKE '%{$keyword}%' or clidname LIKE '%{$keyword}%')";
			} 

			$sql = 'SELECT * FROM ' . tablename('mijia_lending_order') . 'WHERE ' . $condition . ' ORDER BY `oid` ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ) , 'cid');


			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_order') . 'WHERE ' . $condition;
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

			include $this->template("web/order/add");
			}
			if($op=='add'){
				$time=$_GPC['time'];
				$data['oname']=$_GPC['oname'];
				$data['bill']=$_GPC['bill'];
				$data['pico']=$_GPC['pico'];
				$data['jian']=$_GPC['role'];
				$data['start']=$time['start'];
				$data['end']=$time['end'];
				$data['uniacid']=$_W['uniacid'];
				$data['otime']=$_W['timestamp'];
				$data['status']=0;

				$info = pdo_insert('mijia_lending_order',$data);

					if($info){
						message("上传成功",$this->createWebUrl('Order'),"success");
					}



			}
			if($op=='aba'){
				$res=pdo_update('mijia_lending_order',array('status'=>1),array('oid'=>$_GPC['oid']));

				if($res){
				message("已经上架",$this->createWebUrl('Order'),"success");
				}
			}
			if($op=='abb'){
				$res=pdo_update('mijia_lending_order',array('status'=>0),array('oid'=>$_GPC['oid']));
				if($res){

				message("已经下架",$this->createWebUrl('Order'),"success");
				}
			}
			if($op=='edit'){

				$lock = pdo_fetch("SELECT * FROM".tablename('mijia_lending_order')." WHERE `uniacid`=:uniacid and `oid`=:oid",array(':uniacid' => $_W['uniacid'],':oid'=>$_GPC['oid']));
				include $this->template("web/order/update");
			}
			if($op=='edits'){

					$time=$_GPC['time'];
					$start = strtotime($time['start']);
					$end = strtotime($time['end']);
					$mmt=strtotime($_GPC['start']);
					$mmts=strtotime($_GPC['end']);
					if(($start >= $mmt) || ($end >= $mmts)){

						if(empty($_GPC['pico'])){

							$data['oname']=$_GPC['oname'];
							$data['bill']=$_GPC['bill'];
							$data['jian']=$_GPC['role'];
							$data['start']=$time['start'];
							$data['end']=$time['end'];
							$data['otime']=$_W['timestamp'];

							$res=pdo_update('mijia_lending_order',$data,array('oid'=>$_GPC['oid']));
							if($res){
								message("修改成功",$this->createWebUrl('Order'),"success");
							}



						}else{

							$data['oname']=$_GPC['oname'];
							$data['bill']=$_GPC['bill'];
							$data['jian']=$_GPC['role'];
							$data['pico']=$_GPC['pico'];
							$data['start']=$time['start'];
							$data['end']=$time['end'];
							$data['otime']=$_W['timestamp'];
							$res=pdo_update('mijia_lending_order',$data,array('oid'=>$_GPC['oid']));
							if($res){
								message("修改成功",$this->createWebUrl('Order'),"success");
							}

						}

					}else{

						if(empty($_GPC['pico'])){

							$data['oname']=$_GPC['oname'];
							$data['bill']=$_GPC['bill'];
							$data['jian']=$_GPC['role'];
							$data['otime']=$_W['timestamp'];

							$res=pdo_update('mijia_lending_order',$data,array('oid'=>$_GPC['oid']));
							if($res){
								message("修改成功",$this->createWebUrl('Order'),"success");
							}



						}else{

							$data['oname']=$_GPC['oname'];
							$data['bill']=$_GPC['bill'];
							$data['jian']=$_GPC['role'];
							$data['pico']=$_GPC['pico'];
							$data['otime']=$_W['timestamp'];
							$res=pdo_update('mijia_lending_order',$data,array('oid'=>$_GPC['oid']));
							if($res){
								message("修改成功",$this->createWebUrl('Order'),"success");
							}

						}


					}
			

			}

		}

	// 资格管理
	public function doWebDoor(){
			global $_W,$_GPC;
			$ops = array('display'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op=='display'){
			load()->func('tpl');
		if (empty($starttime) || empty($endtime)) {
			    $starttime = strtotime('-1 month');
			    $endtime = TIMESTAMP;
			}
				//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 8; 
			$condition='';

		if (!empty($_GPC['time'])) {
			        $starttime = strtotime($_GPC['time']['start']);
			        $endtime = strtotime($_GPC['time']['end']) + 86399;
			       
			        $condition .= " AND z.ztime >= $starttime AND z.ztime <= $endtime ";
			       
			        }		
			//关键字查询
		if (!empty($_GPC['keyword'])) {
				$condition .= " AND (z.name LIKE '%{$_GPC['keyword']}%' or z.phone LIKE '%{$_GPC['keyword']}%' or z.tid LIKE '%{$_GPC['keyword']}%')";
			} 

			$sql = "SELECT * FROM ".tablename('mijia_lending_zige')."as z ,".tablename('mijia_lending_order')."as o WHERE z.oid=o.oid AND z.`uniacid`=:uniacid".$condition."  ORDER BY z.zid DESC LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;

			// var_dump($sql);
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ));

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_zige')."as z ,".tablename('mijia_lending_order')."as o WHERE z.oid=o.oid AND z.`uniacid`=:uniacid" . $condition;
			$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

				include $this->template("web/door/add");
			}

			
			

	}


	// 认证管理
	public function doWebVending(){
			global $_W,$_GPC;
			$ops = array('display','add','mda','mdb','mdc'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op=='display'){
			//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 8; 
			$condition = '`uniacid` = :uniacid AND `status` between 0 and 2';

			//关键字查询
			if (!empty($keyword)) {
				$condition .= " and (user LIKE '%{$keyword}%' or phone LIKE '%{$keyword}%')";
			} 

			$sql = 'SELECT * FROM ' . tablename('mijia_lending_admins') . 'WHERE ' . $condition . ' ORDER BY `sid` ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		            
		        ) , 'sid');


			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_order') . 'WHERE ' . $condition;
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			       
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

			include $this->template("web/vending/add");




		}

		if($op=='add'){
				include $this->template("web/vending/add");
			}

		if($op=='mda'){
			$sid=$_GPC['sid'];
			$phone=$_GPC['phone'];
			$res=pdo_update('mijia_lending_admins',array('status'=>1,'stime'=>$_W['timestamp']),array('sid'=>$_GPC['sid']));
				if($res){

					$lock = pdo_fetch("SELECT openid FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$phone));

					if($lock){
						$data=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admins')." WHERE `uniacid`=:uniacid and `sid`=:sid",array(':uniacid' => $_W['uniacid'],':sid'=>$sid));

						$data1=array(
							'name'=>array(
							'value'=> '资料审核：已通过',
							'color'=>'#777777'
								) ,
							'remark'=>array(
							'value'=> '通过时间：'.date('y-m-d H:i:s'),
							'color'=>'#777777'
									) ,
								 
								
							);

					$account_api = WeAccount::create();
					$account_api->sendTplNotice($lock['openid'],'fnPt3jaC0VNDS8C6pcqHNh2N-7I_FZuO11U6cAABm9E', $data1, $url ='', $topcolor = '#FF683F');
					}




					message("审核通过成功",$this->createWebUrl('Vending'),"success");



						}


		}
		// 审核驳回
		if($op=='mdb'){
			$sid=$_GPC['sid'];
			$phone=$_GPC['phone'];

			$data=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admins')." WHERE `uniacid`=:uniacid and `sid`=:sid",array(':uniacid' => $_W['uniacid'],':sid'=>$sid));

			if(checksubmit()){
				$user_data = array(
					'sid' => $_GPC['sid'],
					'phone' => $_GPC['phone'],
					'shen'	=>$_GPC['shen'],
					'role'	=>$_GPC['role'],
					'status'=>1,
					'bhtime'=>$_W['timestamp'],
					'uniacid'=>$_W['uniacid'],
				);
				$result = pdo_insert('mijia_lending_yuanyin', $user_data);
				if (!empty($result)) {
					
						$res=pdo_update('mijia_lending_admins',array('status'=>2),array('sid'=>$_GPC['sid']));
						if($res){

							$lock = pdo_fetch("SELECT openid FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$phone));

							if($lock){

								$data1=array(
									'name'=>array(
									'value'=> '资料审核：被驳回'."原因请登录查看！",
									'color'=>'#777777'
										) ,
									'remark'=>array(
									'value'=> '驳回时间：'.date('y-m-d H:i:s'),
									'color'=>'#777777'
											) ,
										 
										
									);

							$account_api = WeAccount::create();
							$account_api->sendTplNotice($lock['openid'],'fnPt3jaC0VNDS8C6pcqHNh2N-7I_FZuO11U6cAABm9E', $data1, $url ='', $topcolor = '#FF683F');
							}

							message("驳回成功",$this->createWebUrl('Vending'),"success");
					}else{

							message("驳回成功",$this->createWebUrl('Vending'),"success");
					}

				}

					


				}

			include $this->template("web/vending/update");

		}
		// 不良用户
		if($op=='mdc'){
			$sid=$_GPC['sid'];
			$phone=$_GPC['phone'];

			$data=pdo_fetch("SELECT * FROM".tablename('mijia_lending_admins')." WHERE `uniacid`=:uniacid and `sid`=:sid",array(':uniacid' => $_W['uniacid'],':sid'=>$sid));

			if(checksubmit()){
				$user_data = array(
					'sid' => $_GPC['sid'],
					'phone' => $_GPC['phone'],
					'shen'	=>$_GPC['shen'],
					'role'	=>$_GPC['role'],
					'statuss'=>2,
					'bhtime'=>$_W['timestamp'],
					'uniacid'=>$_W['uniacid'],
				);
				$result = pdo_insert('mijia_lending_yuanyin', $user_data);
				if (!empty($result)) {
					
						$res=pdo_update('mijia_lending_admins',array('status'=>3),array('sid'=>$_GPC['sid']));
						if($res){

							$lock = pdo_fetch("SELECT openid FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$phone));

							if($lock){

								$data1=array(
									'name'=>array(
									'value'=> '资料审核：抱歉！审核未通过。'."原因请登录查看！",
									'color'=>'#777777'
										) ,
									'remark'=>array(
									'value'=> '驳回时间：'.date('y-m-d H:i:s'),
									'color'=>'#777777'
											) ,
										 
										
									);

							$account_api = WeAccount::create();
							$account_api->sendTplNotice($lock['openid'],'fnPt3jaC0VNDS8C6pcqHNh2N-7I_FZuO11U6cAABm9E', $data1, $url ='', $topcolor = '#FF683F');
							}

							message("拉黑成功",$this->createWebUrl('Vending'),"success");
					}else{

							message("拉黑成功",$this->createWebUrl('Vending'),"success");
					}

				}

					


				}

			include $this->template("web/vending/update");


		}

	}


	//黑户管理 
	public function doWebBlacklist(){
			global $_W,$_GPC;
			$ops = array('display','add'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
		if($op=='display'){
			//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 8; 
			$condition='';
						//关键字查询
		if (!empty($_GPC['keyword'])) {
				$condition .= " AND (a.name LIKE '%{$_GPC['keyword']}%' or a.phone LIKE '%{$_GPC['keyword']}%')";
			} 

			$sql = "SELECT * FROM ".tablename('mijia_lending_yuanyin')."as y ,".tablename('mijia_lending_admins')."as a WHERE y.sid=a.sid AND y.`statuss`=:statuss AND y.`uniacid`=:uniacid".$condition."  ORDER BY y.bhid DESC LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;

			// var_dump($sql);
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		            ':statuss'  =>3,
		        ));

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_yuanyin')."as y ,".tablename('mijia_lending_admins')."as a WHERE y.sid=a.sid AND y.`statuss`=:statuss AND y.`uniacid`=:uniacid" . $condition;
			$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			        ':statuss'  =>3,
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

			include $this->template("web/blacklist/add");




			}
			// 恢复资格
			if($op=='add'){
				$bhid=$_GPC['bhid'];
				$res=pdo_update('mijia_lending_admins',array('status'=>0),array('sid'=>$_GPC['sid']));

				if($res){

				pdo_update('mijia_lending_yuanyin',array('statuss'=>0),array('bhid'=>$_GPC['bhid']));

					message("恢复成功！请从新审核！",$this->createWebUrl('Vending'),"success");
				}





			}
		}
	
	// 订单管理
	public function doWebOrderlist(){
			global $_W,$_GPC;
			$ops = array('display','add'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
				//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 2; 
			$condition='';
						//关键字查询
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND (z.phone LIKE '%{$_GPC['keyword']}%') ";
			} 
			if (!empty($_GPC['keywords'])) {
				$condition .= " AND (z.tid LIKE '%{$_GPC['keywords']}%')";
			}

			$sql = "SELECT * FROM ".tablename('mijia_lending_zige')."as z ,".tablename('mijia_lending_admin')."as a ,".tablename('mijia_lending_order')."as o WHERE z.aid=a.aid AND z.oid=o.oid AND z.`uniacid`=:uniacid".$condition."  ORDER BY z.zid DESC LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;

			// var_dump($sql);
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ));

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_zige')."as z ,".tablename('mijia_lending_admin')."as a ,".tablename('mijia_lending_order')."as o WHERE z.aid=a.aid AND z.oid=o.oid AND z.`uniacid`=:uniacid".$condition;
			$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

			include $this->template("web/orderlist/add");
			}
		}

	// 合同管理
	public function doWebCustomer(){
			global $_W,$_GPC;
			$ops = array('display','add'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){

				load()->func('tpl');
			if (empty($starttime) || empty($endtime)) {
			    $starttime = strtotime('-1 month');
			    $endtime = TIMESTAMP;
			}

			// var_dump(strtotime($_GPC['time']['start']));exit;
				//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 8; 
			$condition = '`uniacid` = :uniacid';

			if (!empty($_GPC['time'])) {
			        $starttime = strtotime($_GPC['time']['start']);
			        $endtime = strtotime($_GPC['time']['end']) + 86399;
			       
			        $condition .= " AND hetime >= $starttime AND hetime <= $endtime ";
			       
			        }
			
			//关键字查询
			if (!empty($keyword)) {
				$condition .= " AND (shens LIKE '%{$keyword}%')";
			} 

			$sql = 'SELECT * FROM ' . tablename('mijia_lending_hetong') . 'WHERE ' . $condition . ' ORDER BY `heid` ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ) , 'heid');

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_hetong') . 'WHERE ' . $condition;
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

			include $this->template("web/customer/add");

			}
		if($op=='add'){
			$heid=$_GPC['heid'];
			$phone=$_GPC['phone'];

			$res=pdo_update('mijia_lending_hetong',array('hestatus'=>1),array('heid'=>$_GPC['heid']));


			message("审核成功！请去审核额度",$this->createWebUrl('Edulist'),"success");

		}


	}

	// 额度审核
	public function doWebEdulist(){
			global $_W,$_GPC;
			$ops = array('display','mma'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
				//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 2; 
			$condition='';
						//关键字查询
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND (e.phone LIKE '%{$_GPC['keyword']}%') ";
			} 
			if (!empty($_GPC['keywords'])) {
				$condition .= " AND (o.users LIKE '%{$_GPC['keywords']}%')";
			}
			if (!empty($_GPC['keywor'])) {
				$condition .= " AND (e.edu LIKE '%{$_GPC['keywor']}%')";
			}

			$sql = "SELECT * FROM ".tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_hetong')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid".$condition."  ORDER BY e.edid DESC LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;

			// var_dump($sql);
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ));

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_hetong')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid".$condition;
			$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

			include $this->template("web/edulist/add");
			}

		if($op=='mma'){
				$edid=$_GPC['edid'];
				$phone=$_GPC['phone'];

				$sql = "SELECT * FROM ".tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_hetong')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid and e.edid=:edid and e.phone=:phone";
				
			    $row = pdo_fetch($sql,array(
			            ':uniacid' => $_W['uniacid'],
			            ':edid'	   =>$edid,
			            ':phone'   =>$phone,
  		        ));

  		    if(checksubmit()){

  		    	$mmts=pdo_fetch("SELECT * FROM".tablename('mijia_lending_edu')." WHERE `uniacid`=:uniacid and `phone`=:phone and `status`=:status",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':status'=>1));


  		    	if($mmts){

  		    		message("已经审核完毕！不要重复审核！");
  		    	}else{
  		    			$mmt=pdo_fetch("SELECT * FROM".tablename('mijia_lending_hetong')." WHERE `uniacid`=:uniacid and `phone`=:phone and `hestatus`=:hestatus",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':hestatus'=>1));

  		    	if($mmt){
  		    			load()->func('tpl');
						$time=$_GPC['time'];
						
						$url=$_GPC['url'];
							 // var_dump($url);exit;
							$start = strtotime($time['start']);
							$end = strtotime($time['end']);
							$mmt=$_W['timestamp'];

							if(($start == '-28800') && ($end=='-28800')){

								message("时间选取不正确");
							}
							
							// var_dump($start);
							// var_dump($mmt);exit;
					if(($start <= $mmt) || ($end >= $mmt)){
								$qi=$time['start'];
								$zhi=$time['end'];

									//创建图像            
						$im = imagecreatefromjpeg("$url");

						// var_dump($im);exit;
						$black = imagecolorallocate($im, 0, 0, 0);
						$text = $name;
						
						//字体
						$font = '../addons/mejia_lending/static/font/STZHONGS.TTF'; 
						                                 
						//字体颜色
						$blacka = imagecolorallocate($im, 160,160, 163);  
				
						//起止时间
						imagettftext($im, 12, 0, 165, 260, $blacka, $font, $qi.$zhi);   
						imagettftext($im, 12, 0, 165, 260, $blacka, $font, $qi.$zhi);  
						  
						
						ob_end_clean() ;

						

						$data = time();
						$img_url = "../addons/mejia_lending/static/images/".$data."q.jpg";
						
						$urls = $data."q.jpg";
						imagejpeg($im,$img_url);

						unlink($url);

						$res=pdo_update('mijia_lending_hetong',array('url'=>$img_url,'hetimeq'=>strtotime($time['start']),'hetimez'=>strtotime($time['end'])+86400),array('heid'=>$_GPC['heid']));

						if($res){

							$res=pdo_update('mijia_lending_edu',array('status'=>1,'edtimes'=>$_W['timestamp'],'lilv'=>$_GPC['lilv']),array('edid'=>$_GPC['edid']));


							message("审核通过成功!请去放款！",$this->createWebUrl('Recharge'),"success");
						}

							}else{

								message("时间选取不正确");

							}

	  		    	}else{

	  		    		message("合同还没有审核完成！");


	  		    	}


  		    	}


  		    	



				

			}

			    include $this->template("web/edulist/update");

		}

		}

	// 放款记录
	public function doWebRecharge(){
			global $_W,$_GPC;
			$ops = array('display','mma'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
				load()->func('tpl');
			if (empty($starttime) || empty($endtime)) {
			    $starttime = strtotime('-1 month');
			    $endtime = TIMESTAMP;
			}

			if (!empty($_GPC['time'])) {
			        $starttime = strtotime($_GPC['time']['start']);
			        $endtime = strtotime($_GPC['time']['end']) + 86399;
			       
			        $condition .= " AND hetime >= $starttime AND hetime <= $endtime ";
			       
			        }
			
					//分页
			$pageindex = max(1,intval($_GPC['page'])); 
			$pagesize = 2; 
			$condition='';
						//关键字查询
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND (e.phone LIKE '%{$_GPC['keyword']}%') ";
			} 
			if (!empty($_GPC['keywords'])) {
				$condition .= " AND (o.users LIKE '%{$_GPC['keywords']}%')";
			}
			if (!empty($_GPC['keywor'])) {
				$condition .= " AND (e.edu LIKE '%{$_GPC['keywor']}%')";
			}

			$sql = "SELECT * FROM ".tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_hetong')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid".$condition."  ORDER BY e.edid DESC LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;

			// var_dump($sql);
			
		    $row = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ));

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_hetong')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid".$condition;
			$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);

			include $this->template("web/recharge/add");

			}
		if($op=='mma'){

			$edid=$_GPC['edid'];
			$phone=$_GPC['phone'];

			$mmt=pdo_fetch("SELECT * FROM".tablename('mijia_lending_edu')." WHERE `uniacid`=:uniacid and `phone`=:phone and `edstatus`=:edstatus and `edid`=:edid",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,':edstatus'=>1,':edid'=>$edid));

			if($mmt){

				message("已经放款.不要重复放款！");

			}



			$res=pdo_update('mijia_lending_edu',array('edstatus'=>1,'fangkuantime'=>$_W['timestamp']),array('edid'=>$_GPC['edid']));


			if($res){
				// 判断客户是否关注，关注就发微信提示，要不就发短信提醒
				
				$mmt=pdo_fetch("SELECT openid FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,));

				if($mmt){
					$sql = "SELECT * FROM ".tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_hetong')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid and e.edid=:edid and e.phone=:phone";
				
			   		$row = pdo_fetch($sql,array(
			            ':uniacid' => $_W['uniacid'],
			            ':edid'	   =>$edid,
			            ':phone'   =>$phone,
  		       			 ));
			   		$endtime = strtotime($row['hetimez']) + 86399;

					//放款成功通知
						$data1=array(
							'name'=>array(
							'value'=> '申请额度：'.$row['edu'] .'元放款成功！时间'.date("Y-m-d H:i:s",$row['edtime']),
							'color'=>'#777777'
								) ,
							'remark'=>array(
							'value'=> '持续时间：'.$row['hetimeq']-$row['hetimez'].'截止时间:'.date("Y-m-d H:i:s",$endtime),
							'color'=>'#777777'
									) ,
								 
								
							);

					$account_api = WeAccount::create();
					$account_api->sendTplNotice('ozkcCxAtpuHXN_jvoeSwpRQ52xGE','fnPt3jaC0VNDS8C6pcqHNh2N-7I_FZuO11U6cAABm9E', $data1, $url ='', $topcolor = '#FF683F');


					message("放款成功！",$this->createWebUrl('Recharge'),"success");

				}else{

					message("放款成功！",$this->createWebUrl('Recharge'),"success");
				}

				


				
			}
			

		}


	}
		//会员管理
		public function doWebUserlist(){
				global $_W,$_GPC;
				$ops = array('display'); 
				$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
				$pageindex = max(1,intval($_GPC['page']));
				$pagesize = 8;
				$condition='`uniacid` = :uniacid'; 
				//上班休班
				$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
				if ($status >= 0) {
					$condition .= " and status LIKE {$_GPC['status']}";
				}
				//关键字查询
				if (!empty($keyword)) {
					$condition .= " and (name LIKE '%{$keyword}%' or phone LIKE '%{$keyword}%')";
				} 

				$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_admin') . 'WHERE ' . $condition;
				$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));

				$sql = 'SELECT * FROM ' . tablename('mijia_lending_admin') . 'WHERE ' . $condition . ' ORDER BY `aid` ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
		        $res = pdo_fetchall($sql, array(
		            ':uniacid' => $_W['uniacid'],
		            
		        ) , 'aid');


				$pager = pagination($total, $pageindex, $pagesize);

				include $this->template("web/userlist/user");
			}
	}

	// 到期催还
		public function doWebOvertime(){
				global $_W,$_GPC;
				$ops = array('display','edit'); 
				$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
				load()->func('tpl');
				$pageindex = max(1,intval($_GPC['page']));
				$pagesize = 8;

				$dang=date("Y-m-d");
				$dangshi=strtotime($dang);
				$condition=''; 
				//上班休班
				$status = isset($_GPC['hetimez']) ? intval($_GPC['hetimez']) : -1;
				if ($status == 1) {
					$data=strtotime(date('Y-m-d')) - 172800;
					$condition .= " and o.hetimez LIKE {$data}";
				}elseif ($status == 2) {
					$data=strtotime(date('Y-m-d')) - 86400;
					$condition .= " and o.hetimez LIKE {$data}";
				}elseif ($status == 0) {
					$data=strtotime(date('Y-m-d'));
					$condition .= " and o.hetimez LIKE {$data}";
				}elseif ($status == 3) {
					$data=strtotime(date('Y-m-d')) + 86400;
					$condition .= " and o.hetimez LIKE {$data}";
				}elseif ($status == 4) {
					$data=strtotime(date('Y-m-d')) + 172800;
					$condition .= " and o.hetimez LIKE {$data}";
				}

				// 已付和未付
				$tatus = isset($_GPC['tatus']) ? intval($_GPC['tatus']) : -1;
				if ($tatus >= 0) {
					$condition .= " and e.tatus LIKE {$_GPC['tatus']}";
				}

				// var_dump($condition);exit;
				//关键字查询
				if (!empty($keyword)) {
					$condition .= " and (users LIKE '%{$keyword}%' or phone LIKE '%{$keyword}%')";
				} 

				$sql = "SELECT * FROM ".tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_hetong')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid".$condition."  ORDER BY e.edid DESC LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;

			// var_dump($sql);
			
		    $res = pdo_fetchall($sql,array(
		            ':uniacid' => $_W['uniacid'],
		        ));

			$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_hetong')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid".$condition;
			$total = pdo_fetchcolumn($sql,array(
			        ':uniacid' => $_W['uniacid'],
			    	));
			$pager = pagination($total, $pageindex, $pagesize);


			// var_dump($res);exit;

				include $this->template("web/overtime/over");
			}
			if($op=='edit'){
				load()->func('tpl');
				$phone=$_GPC['phone'];
				$edid=$_GPC['edid'];

				// 判断客户是否关注，关注就发微信提示，要不就发短信提醒
				
				$mmt=pdo_fetch("SELECT openid FROM".tablename('mijia_lending_admin')." WHERE `uniacid`=:uniacid and `phone`=:phone",array(':uniacid' => $_W['uniacid'],':phone'=>$phone,));

				if($mmt){
					$sql = "SELECT * FROM ".tablename('mijia_lending_edu')."as e ,".tablename('mijia_lending_hetong')."as o WHERE e.edid=o.edid AND e.`uniacid`=:uniacid and e.edid=:edid and e.phone=:phone";
				
			   		$row = pdo_fetch($sql,array(
			            ':uniacid' => $_W['uniacid'],
			            ':edid'	   =>$edid,
			            ':phone'   =>$phone,
  		       			 ));

					//放款成功通知
						$data1=array(
							'keyword1'=>array(
							'value'=> '截止时间：'.date("Y-m-d",$row['hetimez']).'时间到期，请速度还款！',
							'color'=>'#777777'
									) ,
							'keyword2'=>array(
							'value'=> '申请额度：'.$row['edu'] .'放款时间'.date("Y-m-d H:i:s",$row['edtime']),
							'color'=>'#777777'
								) ,
							
								 
								
							);

					$account_api = WeAccount::create();
					$account_api->sendTplNotice($mmt['openid'],'ZYSfJJFaD4CqL3XKixQQzvX5-WnxNO6VO9yuQ3NmuP8', $data1, $url ='', $topcolor = '#FF683F');


					message("催款成功！",$this->createWebUrl('Overtime'),"success");

				}else{

					$da=pdo_fetch('select * from'.tablename('mijia_lending_duanxin').' WHERE '. "`uniacid`=:uniacid",array(':uniacid'=>$_W['uniacid']));

					// isetcookie ('phone', "$phone", 360);
					if($da){

						message('请配置短信的APPKEY');

					}
					$keys=$da['appkey'];
					$ids=$da['tplid'];



					$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
			            $code = rand(0001,9999);//验证码生成格式，请生成4-8位，数字或字母随机组合

			            $smsConf = array(
			                "key"       => "$keys", //您申请的APPKEY
			                "mobile"    => "$phone", //接受短信的用户手机号码
			                "tpl_id"    => "$ids", //您申请的短信模板ID，根据实际情况修改
			                "tpl_value" => "#code#=".$code."&#company#=聚合数据"//您设置的模板变量，根据实际情况修改
			            );

			            isetcookie ('code', "$code", 360);
			            $content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信
			            if($content){
			            	$result = json_decode($content,true);
			                $error_code = $result['error_code'];

			                if($error_code == 0){
			                	// 短信ID：".$result['result']['sid']
			                	echo 1;

			                }else{

			                	echo 0;

			                	
			                }


					message("催款成功！",$this->createWebUrl('Recharge'),"success");
				}


		}		
		}
	}


	// 还款记录
		public function doWebRodger(){
			global $_W,$_GPC;
			$ops = array('display','add'); 
			$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
			if($op=='display'){
			//分页
				$pageindex = max(1,intval($_GPC['page'])); 
				$pagesize = 8; 
				$condition = '`uniacid` = :uniacid';

				//关键字查询
				if (!empty($keyword)) {
					$condition .= " AND (names LIKE '%{$keyword}%' or tid LIKE '%{$keyword}%' or phone LIKE '%{$keyword}%')";
				} 
				$sql = 'SELECT * FROM ' . tablename('mijia_lending_huankuan') . 'WHERE ' . $condition . ' ORDER BY `huid` ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			    $row = pdo_fetchall($sql,array(
			            ':uniacid' => $_W['uniacid'],
			        ) , 'huid');

				$sql = 'SELECT COUNT(*) FROM '.tablename('mijia_lending_huankuan') . 'WHERE ' . $condition;
					$total = pdo_fetchcolumn($sql,array(
				        ':uniacid' => $_W['uniacid'],
				    	));
				$pager = pagination($total, $pageindex, $pagesize);
			include $this->template("web/rodger/add");
			}
		}

	//导出EXCEL
	public function doWebExcel(){
			global $_W,$_GPC;
			load()->library('phpexcel/PHPExcel');
			$objPHPExcel=new PHPExcel();
			$data=pdo_fetchall("SELECT * FROM".tablename('mijia_lending_huankuan')."WHERE uniacid=:uniacid AND hstatus=1",array(':uniacid'=>$_W['uniacid']));
			
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','姓名');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','电话');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','身份证号');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1','额度');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1','权限');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','还款时间');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','订单号');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','支付方式');
			//把数据循环写入excel中
			foreach($data as $key => $value){
			
			$key+=2;
			//var_dump($value['orderid']);
			
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$key,' '.$value['names']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$key,' '.$value['phone']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$key,' '.$value['shenfen']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$key,$value['edums']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$key,$value['oname']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$key,date('Y-m-d',$value['hutime']));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$key,$value['tid']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$key,$value['type']);
			}
			//导出代码
			$objPHPExcel->getActiveSheet() -> setTitle('SetExcelName');
			$objPHPExcel-> setActiveSheetIndex(0);
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			//$objWriter = $iofactory -> createWriter($objPHPExcel, 'Excel2007');
			
			$filename =time().'SetExcelName.xlsx';
			ob_end_clean();
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
		    header('Cache-Control: max-age=0');
			$objWriter -> save('php://output');
		
}


























}
