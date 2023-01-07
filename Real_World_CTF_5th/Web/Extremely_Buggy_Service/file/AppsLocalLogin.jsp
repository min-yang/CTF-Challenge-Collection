<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">


<html>
<head>
	<title>Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,user-scalable=yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">
        <link type= "text/css" rel="stylesheet" href="?login.css" />
        <script type="text/javascript" src="?login.js"></script>

</head>
<body onLoad="startup()">
	
	<div id=logo>
		<img src="/OA_HTML/media/oracle_white_logo.png" alt="Oracle Logo" title="Oracle Logo" message=FND_ORACLE_LOGO class="logo">
	</div>
	
	<div class="login_panel">
	<div class="login_container">
        <form id=login>
		<div id=UsernameBox class="control_box min_margin">
			<label for="usernameField" message=FND_SSO_USER_NAME>User Name</label>
			<input tabindex=0 type="text" id="usernameField" name="usernameField" class="inp" value="" message=FND_SSO_USER_NAME >

		</div>

                <div class="control_box min_margin" id=logoutLink style='display:none'><a tabindex=0 name=logout message=FND_SSO_NOTSAME_USER onclick='confirmLogout()'> Not the same user?</a></div>
		<div class="control_box min_margin">
			<label for="passwordField" message=FND_SSO_PASSWORD>Password</label>
			<input tabindex=0 class="inp" type="password" id="passwordField" name="passwordField" value="" message=FND_SSO_PASSWORD >
		</div>
        </form>
		<div id=ButtonBox class="control_box center">
			
			<button tabindex=0 class="OraButton left" message=FND_SSO_LOGIN onclick="submitCredentials()">Login</button>
			<button tabindex=0 class="OraButton right"  message=FND_SSO_CANCEL onclick="handleCancel()">Cancel</button>
		</div>
		<div id=HelpLinkBox class="control_box">
			<a tabindex=0 id=ForgotPasswordURL title="Login Assistance"  message=FND_SSO_FORGOT_PASSWORD href="#" role="link">Login Assistance</a>			
			<br /><br />
			<a tabindex=0 id=RegisterHereURL title="Register Here" message=FND_SSO_REGISTER_HERE href="#" role="link">Register Here</a>
		</div>
		<div id=AccessibilityBox class="control_box min_margin">
			<label for="accessibility" id=adaLabel message=FND_SSO_ACCESSIBILITY>Accessibility</label>
			<select tabindex=0 name="Accessibility" id="accessibility" message=FND_SSO_ACCESSIBILITY class="selector" title="Accessibility">
                    </select>
		</div>
		<div id=LanguagePickerBox class="control_box min_margin">
			<label for="slang" message=FND_SSO_LANGUAGE>Language</label>
			<select tabindex=0 id="slang" name="currentLanguage" class="selector" title="Select a Language" message=FND_SSO_LOGIN_LANG_SELECT>
			</select>
		</div>
		<div id=marker style='display:none'></div>
                <div class="control_box min_margin" id=errorBox style='display:none' role="alert"></div>
	</div>
	</div>
	
	<div id=FooterBox class="footer">
		<div id=CopyrightBox class="copyright"><span message=FND_COPYRIGHT>Copyright Oracle 2014</span></div>
		<div id=LinksBox class="links_footer">
			<ul>
				<li><a tabindex=0 href="#" message=FND_SSO_TERMS_OF_USE>Terms of Use</a></li>
				<li><a tabindex=0 href="#" message=FND_SSO_LEGAL_NOTICE>Legal Notice</a></li>
				<li><a tabindex=0 href="#" message=FND_SSO_ABOUT_PRODUCT>About Product</a></li>
				<li><a tabindex=0 href="#" message=FND_SSO_PRIVACY_STMT>Privacy Statement</a></li>
			</ul>
		</div>
	</div>
</body>
</html>
