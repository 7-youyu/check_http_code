<?php
	header("Content-type:text/html;charset=utf-8");
	set_time_limit(0);   //设置PHP运行时间，以免长时间运行崩溃，0表示不限制
 	ob_end_clean();     //在循环输出前，要关闭输出缓冲区  

	$cookiefile      = 'cookie.txt';
	$fp=fopen("$cookiefile", "w+"); 
	if ( !is_writable($cookiefile) ){
	      die("文件:" .$cookiefile. "不可写，请检查！");
	}
	$url        = $_POST['url'];
	$need_login = $_POST['need_login'];
	$user_data  = null;

	if ($need_login) {
		list($a,$b) = explode(":", $_POST['username']);	
		list($c,$d) = explode(":", $_POST['password']);
		$user_data  = array($a => $b, $c => $d);
	}
	$keyword    = $_POST['keyword'];
	$unkeyword  = $_POST['unkeyword'];
	
	define("COOKIE_FILE", $cookiefile);
	define("HTTP_CODE", $_POST['output']);
	define("HTTP_SLEEP",$_POST['sleep']);

	function check_http_code($url, $user_data, $keyword, $unkeyword){
		if (is_null($user_data)) {
			$go = check_go($url, $keyword, $unkeyword);
			if ($go['code']!= 0) {
				echo $go['msg'].':'.$go['data'];
				return false;
			}
		}else{

			$init = check_init($url, $user_data, $keyword, $unkeyword);
			if ($init['code']!= 0) {
				echo $init['msg'].':'.$init['data'];
				return false;
			}
		}
	}

	function check_go($url, $keyword, $unkeyword){
		$ch = curl_init($url);    
		curl_setopt($ch, CURLOPT_HEADER, 0);    
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		$content = curl_exec($ch);    
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);    

		if ($http_code !== 200) {
			$ret = array('code'=>40040,'msg'=>'检索失败','data'=>$error);
			return $ret;
		}
		preg_match_all("/(http\:\/\/[^ '\"]+)/i", $content, $content_links);   
		$check_arr = array();
		$i = 0;
		check_all($content_links[0],$check_arr,$i, $keyword, $unkeyword);
		$ret = array('code'=>0,'msg'=>'检索完毕','data'=>$error);
		return $ret;
	}

	function check_init($url, $user_data, $keyword, $unkeyword){
		$curlobj = curl_init();									// 初始化
		curl_setopt($curlobj, CURLOPT_URL, $url );				// 设置访问网页的URL
		curl_setopt($curlobj, CURLOPT_RETURNTRANSFER, true);	// 执行之后不直接打印

		// Cookie相关设置，这部分设置需要在所有会话开始之前设置
		date_default_timezone_set('PRC'); 						// 使用Cookie前先设置时区
		
		curl_setopt($curlobj, CURLOPT_COOKIESESSION, TRUE); 
		curl_setopt($curlobj, CURLOPT_COOKIEFILE,COOKIE_FILE); 	//读取cookie
		curl_setopt($curlobj, CURLOPT_COOKIEJAR,COOKIE_FILE);  	//保存cookie
		curl_setopt($curlobj, CURLOPT_HEADER, 0); 				//不输出头文件
		curl_setopt($curlobj, CURLOPT_FOLLOWLOCATION, 1); 		// 这样能够让cURL支持页面链接跳转

		curl_setopt($curlobj, CURLOPT_POST, 1);  				//发送一个常规的POST请求
		curl_setopt($curlobj, CURLOPT_POSTFIELDS, $user_data);	//POST数据
		$content = curl_exec($curlobj);							// 执行

		$return_code = curl_getinfo($curlobj, CURLINFO_HTTP_CODE); //返回状态码
		$error = curl_error($curlobj);
		curl_close($curlobj);									// 关闭cURL
		
		if ($return_code !== 200 || $error) {
			$ret = array('code'=>40050,'msg'=>'初始化失败，无法登陆','data'=>$error);
			return $ret;
		}

		preg_match_all("/(http\:\/\/[^ '\"]+)/i", $content, $content_links);
		// var_dump($content_links);
		$check_arr = array();
		$i = 0;
		check_all($content_links[0],$check_arr,$i, $keyword, $unkeyword);

		$ret = array('code'=>0,'msg'=>'检索完毕','data'=>$error);
		return $ret;
	}
	
	function check_all($links,$check_arr,$i, $keyword, $unkeyword){
		global $check_arr;
		global $i;
		foreach ($links as $v) {
			if (!strstr($v,$keyword)) {
				continue;
			}
			if (strstr($v,$unkeyword)) {
				continue;
			}
			if (count($check_arr)!=0) {
				if (in_array($v, $check_arr)) {
					continue;
				}
			}
			
			$check_arr[] = $v;
			// var_dump($check_arr);

			$ch = curl_init($v);    
			curl_setopt($ch, CURLOPT_HEADER, 0);    
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
			curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE_FILE);    
			$content = curl_exec($ch);    
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
			if (HTTP_CODE == 0 && $http_code == 404){
				$i++;
				$v1 = '<tr>';
		        $v1.= '<td>'.$i.'</td>';
		        $v1.= '<td>'.$v.'</td>';
		        $v1.= '<td>'.$http_code.'</td>';
		        $v1.= '</tr>';
				echo $v1;
			}else if(HTTP_CODE == 1){
				$i++;
				$v1 = '<tr>';
		        $v1.= '<td>'.$i.'</td>';
		        $v1.= '<td>'.$v.'</td>';
		        $v1.= '<td>'.$http_code.'</td>';
		        $v1.= '</tr>';
				echo $v1;
			}
			
		  	flush(); 

		  	sleep(HTTP_SLEEP);

			curl_close($ch);    

			preg_match_all("/(http\:\/\/[^ '\"]+)/i", $content, $content_links);   

			check_all($content_links[0],$check_arr,$i, $keyword, $unkeyword);
			
		}
	  
	}	
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<title>check_http_code</title>
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap.min.css">
	<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
	<script src="http://cdn.bootcss.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
</head>
<body>
	<div id="head">
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		  <div class="container">
		    ...
		  </div>
		</nav>
	</div>
	<div id="content" style="margin-top:70px">
		<div class="container">
			<div class="panel panel-primary">
				<div class="panel-heading">检索结果(链接过多，请耐心等待)</div>
				<div class="panel-body">
					<table class="table table-striped">
				      <thead>
				        <tr>
				          <th>数量</th>
				          <th>URL地址</th>
				          <th>状态码</th>
				        </tr>
				      </thead>
				      <tbody>
				        <?php check_http_code($url, $user_data, $keyword, $unkeyword);?>
				      </tbody>
				    </table>
				</div>
			</div>
	    </div>
	</div>
	<div id="foot"></div>

</body>
</html>
