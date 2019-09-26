<style>
ul{list-style-type:none; margin:0; padding:0;}
#login_box {width: 587px;  height:245px;margin: 0 auto; margin-top:150px;border-top:1px solid #039de6;}
#login_box1{position:relative; float:left; width: 587px; height:200px; background:url(../img/admin/img1.gif)  no-repeat  left top;}
#login_box2{position:relative;float:left; width: 587px; height:45px; background:url(../img/admin/img_copy.gif)  no-repeat  left top;}
#login_ctn{width: 322px; height:96px;  position:absolute; top:52px;left:243px;}
#login_ctn li{width: 322px; height:28px; margin-bottom:5px; text-align:left;}
#login_ctn li input{
	float:left; margin:0; padding:0; width: 129px; height:12px; line-height:12px; border:none;
	background-color:transparent; color:#ffffff; margin-left:75px; margin-top:7px; vertical-align:middle;
}
#login_ctn li.l_title{height:30px;}
#login_ctn li.l_id{background:url(../img/admin/bg_id.gif)  no-repeat  left top;}
#login_ctn li.l_pw{background:url(../img/admin/bg_pw.gif)  no-repeat  left top;}
#login_ctn li.l_pw a{width:105px; height:28px; display:block; float:right; background:url(../img/admin/btn_login.gif)  no-repeat  left top;}
</style>
<form name="login" method="post">
<div style="text-align:center;">
	<div id="login_box">
		<div id="login_box1">
			<div id="login_ctn">
				<ul>
					<li class="l_title"><img src="../img/admin/title_branch.gif" /></li>
					<li class="l_id"><input type="text" name="uCode" style="ime-mode:inactive;" onkeydown="if(event.keyCode == 13 && this.value != ''){document.getElementById('uPass').focus();}"/></li>
					<li class="l_pw"><input type="password" name="uPass" onkeydown="if(event.keyCode == 13 && this.value != ''){_Login();}"/><a href="#" onclick="_Login();"></a></li>
				</ul>
			</div>
		</div>
		<div id="login_box2">
		</div>
	</div>
</div>
</form>