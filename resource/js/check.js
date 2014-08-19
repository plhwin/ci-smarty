/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
String.prototype.len=function(){
	return this.replace(/[^\x00-\xff]/g, '**').length;
}

String.prototype.trim = function(){
	return this.replace(/(^\s*)|(\s*$)/g,"");
}

function pressKeydown(evt,fun_eval){//press the enter key
	evt = evt ? evt : (window.event ? window.event : null);
	if (evt.keyCode == 13){
		eval(fun_eval);
	}
}

function is_number(str){
	exp=/[^0-9()-]/g;
	if(str.search(exp) != -1){
		return false;
	}
	return true;
}


function is_email(email){
	//var regexp = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	var regexp = /^[a-zA-Z0-9\_\-\.]+@([a-zA-Z0-9\-\.])*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	return regexp.test(email);
}

function is_cellphone(cellphone){
	var regexp = /^(13|15|18){1}\d{9}$/;
	return regexp.test(cellphone);
}

function is_amount(amount){
	var regexp = /^(?![0\.]+$)(?:(?:[1-9]\d*?(?:\.\d{1,2})?)|(?:0\.\d{1,2}))$/;
	return regexp.test(amount);
}

function is_phonenumber(phone){
	if (is_cellphone(phone) || is_telphone(phone)){
		return true;
	}else{
		return false;
	}
}

function is_telphone(telphone){
	var regexp = /^(0){1}[0-9]{2,3}\-\d{7,8}$/;
	return regexp.test(telphone);
}

function is_url(url){
	var regexp = /http:\/\/([a-zA-Z0-9-]*\.)+[a-zA-Z]{2,3}/;
	return regexp.test(url);
}

function is_price(num) {
	var i,j,x,strTemp;
	strTemp = "0123456789.";
	if(num.length <= 0) {
		return false;
	} else {
		x = num.split('.').length - 1;//出现'.'的次数
		if(x > 1){
			return false;
		} else {
			var flag = false;
			for(var i=0; i<num.length; i++) {
			   if(strTemp.indexOf(num.charAt(i)) == -1) flag = true;
			}
			if(flag){ //有不为数字的字符
				return false;
			} else {
				return true;
			}
		}
	}
}

//计算字符串长度,1个中文字符长度为1，1个英文字符串长度为0.5
function cnstrlen(content){
	var strlen=content.length;
	strLen=0;
	for(i=0;i<strlen;i++){
		if(content.charCodeAt(i)>255) {
			strLen++;
		} else {
			strLen+=0.5;
		}
	}
	return strLen;
}

function textCount(field, maxlimit) {
	var strlen = cnstrlen(G(field).value);
	if(strlen > maxlimit){
		if (navigator.userAgent.toLowerCase().indexOf("firefox")!=-1){
			alert("对不起你输入的信息内容不能大于"+maxlimit+"个字符！");
			G(field).focus();
			return false;
		} else {
			G(field).value = G(field).value.substring(0, maxlimit);
		}
	}else{
		setWarning(field+"_info", "已输入<font color=blue>"+strlen+"</font>个汉字，还可以输入<font color=red>"+(maxlimit - strlen)+"</font>个汉字。")
	}
}

function checkAll(e,itemName){
	if(e.value == "全选"){
		e.value = "取消";
		var flag = true;
	} else {
		e.value = "全选";
		var flag = false;
	}
	var nameArr = document.getElementsByName(itemName);
	for (var i=0; i<nameArr.length; i++){
		nameArr[i].checked = flag;
	}
}
function selectAllBox(A,D){
	var C=document.getElementsByName(A);
	for(var B=0;B<C.length;B++){
		C[B].checked=D||!C[B].checked;
	}
}
function textFormat(id){
	var body = "\n"+Trim(document.getElementById(id).value);
	body = body.replace(/ |　/ig,"");
	body = body.replace(/\r\n/ig,"\n");
	body = body.replace(/\n\n/ig,"\n");
	body = body.replace(/\n\n/ig,"\n");
	body = body.replace(/\n\n/ig,"\n");
	body = body.replace(/\n\n/ig,"\n");
	body = body.replace(/\n/ig,"\n\n　　");
	body = body.replace("\n\n","");
	document.getElementById(id).value = body;
}

function showHide(objname){
	if(G(objname).style.display=="none") {
		G(objname).style.display = "";
	} else {
		G(objname).style.display="none";
	}
}
function checkDate(){
	var d1 = document.getElementById("_st").value;
	var d2 = document.getElementById("_et").value;
	ss1=d1.split("-");
	ss2=d2.split("-");
	date1 = new Date(ss1[0]+"/"+ss1[1]+"/"+ss1[2]);
	date2 = new Date(ss2[0]+"/"+ss2[1]+"/"+ss2[2]);
	if (date1>date2){
		setError('date_info','请选择正确的时间格式,结束时间应该大于或等于开始时间。');
		return false;
	}
	return true;
}

function checkInput(id) {
	if(G(id)) {
		var backColor = G(id).style.backgroundColor;
		if(backColor == '#ffff99' || backColor == 'rgb(255, 255, 153)'){
			G(id).style.backgroundColor='#ffffff';
		}
		if(G(id).value == ''){
			fieldReset(id);
			return;
		}
	}

	switch (id) {
		//注册
		case 'username':checkUserName();break;
		case 'email':checkEmail();break;
		case 'password_once':checkPwdOnce();break;
		case 'password_twice':checkPwdTwice();break;
		case 'seccode':checkSeccode();break;
	}
}


function check_cellphone(){
	var cellphone = $F('cellphone');
	if(cellphone == ''){
		setError('cellphone_info','手机号码不可为空');
		return false;
	} else {
		if(!is_cellphone(cellphone)){
			setError('cellphone_info','请输入11位正确的手机号码格式，无需加0');
			return false;
		} else {
			requestUri = 'cellphone='+encodeURIComponent(cellphone);
			postajax(SITE_URL+"/register/check_cellphone", requestUri, 'callback_cellphone');
		}
	}	
}

function callback_cellphone(requestStr){
	var r = explode('_', requestStr);
	var field = r[0];
	var result = r[1];
	if(result == 'success'){
		setWord(field+"_info", '&nbsp;');
		changeStyle(field+"_info", 'icon_ok');
	} else {
		setError(field+"_info", result);
	}
}


function checkEmail(){
	var email = $F('email');
	if(email == '') {
		fieldError('email','邮箱地址不能为空。');
	} else {
		if($F('password_once') == email){
			fieldError('email','为了您的帐户安全，请勿将密码设置和邮箱一致。');
		} else if(!is_email(email)){
			fieldError('email','请输入正确的邮箱格式。');
		}else {
			requestUri = 'email='+encodeURIComponent(email);
			postajax(SITE_URL+"/register/check_email", requestUri, 'callback_fieldcheck');
		}
	}
}

function checkUserName(){
	var username = $F('username');
	var pwdOnce = $F('password_once');
	var pwdTwice = $F('password_twice');
	var strlen = cnstrlen(username) * 2;
	if(username == ""){
		fieldError('username','会员名不能为空，请重新输入。');
	} else if(strlen < 4 || strlen > 20) {
		fieldError('username','会员名长度错误，应该在4-20个字符之间。一个汉字为两个字符。');
	} else if(!/^[a-z0-9]\w{1,25}$/i.test(username.replace(/[\u4e00-\u9fa5]/g, 'mm'))){
		fieldError('username','会员名中包含有特殊字符。只允许汉字，大小写字母，数字作为会员名。');
	} else if(username == pwdOnce){
		fieldError('username','为了您的帐户安全，请勿将会员名设置和密码一致。');
	} else if(username == pwdTwice){
		fieldError('username','为了您的帐户安全，请勿将会员名设置和密码一致。');
	} else {
		requestUri = 'username='+encodeURIComponent(username);
		postajax(SITE_URL+"/register/check_username", requestUri, 'callback_fieldcheck');
	}
}

function callback_fieldcheck(requestStr){
	var r = explode('_', requestStr);
	var field = r[0];
	var result = r[1];
	if(result == 'success'){
		fieldOk(field);
	} else {
		fieldError(field, result);
	}
}

function checkPwdOnce()
{
	var pwd = $F('password_once');
	var email = $F('email');
	
	var pwdTwice = $F('password_twice');
	
	if(pwdTwice != ""){
		if(pwdTwice == pwd){
			fieldOk('password_twice');
		} else {
			fieldError('password_twice','确认密码 输入不匹配。请再输入一遍您上面输入的密码，并确保两次输入一致。');
		}
	}
	
	if(pwd == '') {
		fieldError('password_once','密码不能为空。');
	} else if(pwd.length < 6 || pwd.length > 16) {
		fieldError('password_once','密码的长度必须在6-16个字符之间，强烈建议您</br>使用英文字母加数字或符号组合提高密码安全度。');
	} else if(pwd == $F('username')) {
		fieldError('password_once','密码不能和会员名一致。');
	}  else if(email != "" && email == pwd) {
		fieldError('password_once','为了您的帐户安全，请勿将密码设置和邮箱一致。');
	}  else {
		fieldOk('password_once');
	}
}

function checkPwdTwice()
{
	var pwd = $F('password_twice');
	if(pwd == '') {
		fieldError('password_twice','确认密码不能为空。');
	} else if($F('password_once') != pwd) {
		fieldError('password_twice','两次密码输入不一致。');
	} else {
		return checkPwdOnce();
	}
}

function checkPwdSafe(pwd){//检查密码安全性

	var pwdwidth = 0;
	var safeCoef = 0;//安全系数

	var low = new Array('red','弱');
	var middle = new Array('blue','中');
	var high = new Array('green','高');
	var user = low;

	if(pwd == '') {
		G("pwdsafetatus").style.display="none";
	} else {
		var safe_1= (pwd.search(/[a-zA-Z]/)!=-1) ? 1 : 0;
		var safe_2= (pwd.search(/[0-9]/)!=-1) ? 1 : 0;
		var safe_3= (pwd.search(/[^A-Za-z0-9]/)!=-1) ? 1 : 0;
		
		if(pwd.length < 6){//密码位数小于6位，不安全
			safeCoef = 0;
		} else if(pwd == G('username').value){//密码和用户名相同，不安全
			safeCoef = 0;
		} else {
			safeCoef=safe_1+safe_2+safe_3;	
		}
		
		pwdwidth = parseInt(((pwd.length/20)*100)+(safeCoef*10));	
		
		if(safeCoef==1){//不安全
			pwdwidth = pwdwidth > 50 ? 50 : pwdwidth;		
		}else if(safeCoef==2){//普通
			pwdwidth = pwdwidth > 80 ? 80 : pwdwidth;
		}else if(safeCoef==3){//安全
			pwdwidth = pwdwidth > 100 ? 100 : pwdwidth;
		}
		
		if(pwdwidth >= 80){
			user = high;
		} else if(pwdwidth >= 50){
			user = middle;
		}
		
		G("pwdsafetatus").style.display="";
	}
	G("pwdsafewidth").style.width = pwdwidth+"%";
	G("pwdsaferesult").innerHTML = '<span style="color:'+user[0]+'">'+user[1]+'</span>';
}

function checkSeccode() {
	var seccode = $F("seccode");
	if(seccode == ''){
		fieldError('seccode','验证码不能为空。');
	} else {
		requestUri = 'seccode='+seccode;
		postajax(SITE_URL+"/register/check_seccode", requestUri, 'callback_fieldcheck');
	}
}