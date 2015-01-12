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

			<div class="jumbotron">
				<div class="container">
			    	<h1>Check Http Code</h1>
			  		<p>用于检测某个网站上所有href链接，返回404网页链接</p>
			  		<p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a></p>
			  	</div>
			</div>
			<!-- input data -->
			<div class="panel panel-primary">
				<div class="panel-heading">检索条件</div>
				<div class="panel-body">
					<form class="form-horizontal" method="post" action="./curl/check_curl.php">
					  <div class="form-group">
					    <label for="url" class="col-sm-1 control-label">URL</label>
					    <div class="col-sm-11">
					      <input type="text" class="form-control" id="url" name="url" placeholder="输入需要检索的网站地址，如：http://www.baidu.com/，如果需要登陆，请填写POST页面地址">
					    </div>
					  </div>
					  <div class="form-group">
					  	<label for="need_login" class="col-sm-1 control-label">需要登陆</label>
					    <div class="col-sm-10">
					      <div class="checkbox">
					      	<label>
					          <input type="checkbox" id="need_login" name="need_login"> 
					        </label>  
					      </div>
					    </div>
					  </div>
					  <div class="form-group" id="user_info">
					    <label for="username" class="col-sm-1 control-label">用户名</label>
					    <div class="col-sm-5">
					      <input type="text" class="form-control" id="username" name="username" placeholder="格式,用户名字段:用户名，如username:admin">
					    </div>
					    <label for="password" class="col-sm-1 control-label">密码</label>
					    <div class="col-sm-5">
					      <input type="text" class="form-control" id="password" name="password" placeholder="格式,密码字段:密码，如password:123456">
					    </div>
					  </div>
					  <div class="form-group">
					    <label for="keyword" class="col-sm-1 control-label">关键字</label>
					    <div class="col-sm-5">
					      <input type="text" class="form-control" id="keyword" name="keyword" placeholder="需要检索网站关键字，如baidu，防止检索所有外链">
					    </div>
					    <label for="unkeyword" class="col-sm-1 control-label">排除项</label>
					    <div class="col-sm-5">
					      <input type="text" class="form-control" id="unkeyword" name="unkeyword" placeholder="无需检索的关键字，如输入tieba，将不会搜索带有tieba的链接">
					    </div>
					  </div>
					  <div class="form-group">
					  	<label for="output" class="col-sm-1 control-label">输出结果</label>
					  	<div class="col-sm-5">
						    <label class="radio-inline">
							  <input type="radio" name="output" value="0" checked="checked"> 只输出404链接
							</label>
							<label class="radio-inline">
							  <input type="radio" name="output" value="1"> 输出所有链接
							</label>
						</div>
					  </div>
					  <div class="form-group">
						<label for="check_url" class="col-sm-1 control-label">设置延迟</label>
					  	<div class="col-sm-5">
						    <label class="radio-inline">
							  <input type="radio" name="sleep" value="0"> 0秒
							</label>
							<label class="radio-inline">
							  <input type="radio" name="sleep" value="1" checked="checked"> 1秒
							</label>
							<label class="radio-inline">
							  <input type="radio" name="sleep" value="5"> 5秒
							</label>
						</div>
					  </div>
					  <div class="form-group">
					    <div class="col-sm-offset-1 col-sm-11">
					      	<button type="submit" class="btn btn-primary" id="start_check">开始检索</button>
					    </div>
				  	  </div>
					</form>
				</div>
			</div>
			  <!-- input data end -->
		</div>
	</div>
	<div id="foot"></div>

</body>
<script type="text/javascript">
    $("document").ready(function(){
		$("#user_info").hide();
        $("#need_login").click(function(){
            var need_login      = $(this).is(":checked")? 1 : 0;
            if (need_login == 1) {
                $("#user_info").show();
            } else{
                $("#user_info").hide();
            };
            
        });

        
    })
</script>
</html>