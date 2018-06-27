<?php
/**
 * 微信寄存柜模块微站定义
 *
 * 管理员申请
 * @author mejia
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_GPC, $_W;
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
var_dump($_W['openid']);die;
