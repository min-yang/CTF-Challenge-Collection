/*

+======================================================================+
|    Copyright (c) 2005, 2015 Oracle and/or its affiliates.            | 
|                         All rights reserved.                         | 
|                           Version 12.2.0                             | 
+======================================================================+ 

$Header: login.js 120.0.12020000.35 2018/09/26 12:25:56 srivishn noship $


Supporting JavaScript for Lightwheight login.


Do not load this file directly on the browser. 
The LoginHelper need to add important information to the script.

Always use

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Login</title>
        <script type="text/javascript" src=""></script>
    </head>

This will properly load CSS and Javascript on JSP/HTML page.


*/



/*
 * Initialize globals, just in case LoginHelper failed or was not called.
 */

var currentUser='';
var currentLang='US';
var hasSession=false;
var requestUrl='';
var cancelUrl='';
var logLevel=0;
var browserClass='';
var lockLanguage=false;
var lockADA=false;
var intialized=false;
/*
 * Bug 21323255: On iPad Chrome  you can't have a global var named 'message'
 */
var appsMessage={};
var errorSequence = 0;

/* Console doesn't exist on IE9 and lower versions */

if (!window.console) console = {log: function() {}};
/* Bug 20395127 - Changes for issues with chrome */
var browser = {
   isOpera : !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0,
   isFirefox : typeof InstallTrigger !== 'undefined',
   isSafari : Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0,
   isChrome : !!window.chrome && window.navigator.vendor==="Google Inc.",
   isIE : /*@cc_on!@*/false || !!document.documentMode
};
/*
 * Translatable tags
 * 
 *    this are the tags currently translated
 */
var tags = ['a', 'label', 'span', 'input','button','select','img'];
var inpFld = ['placeholder','title'];
/*
 * Field to set. 
 * For each of the translatable tags.
 */
var field = ['textContent', 'textContent', 'textContent', 'value','textContent', 'title'];
if (browser.isIE) {
  field = ['innerHTML', 'innerHTML', 'innerHTML', 'value','innerHTML', 'title'];
}

var accesibilityOption;



/*
 *  Call the service to log into FND Log Messages.
 *  
 */
function log( level , msg ) {
/* 6: Fatal Errir , 2: Procedure, 1: statement, 0 : off */ 
  if (logLevel>0 && level>=logLevel)
  {
    call('log',"level="+level+"&message="+encodeURIComponent(msg),
        function(r){ console.log(msg); }, true);
  }
}
function logException ( e )
{
   var m = e.name()+":"+e.toString()+" "+e.fileName+":"+e.lineNumber+
    "\n"+e.message+
    "\n"+e.stack();
  console.log(m);
}

function isLoaded()
{
  if (typeof window =='undefined') return false;
  if (typeof window.AppsLoginPage =='undefined') return false;
  if (typeof window.AppsLoginPage.loaded =='undefined') return false;
  return window.AppsLoginPage.loaded;
}


function getISOLang(lang)
{
  var slang = document.getElementById('slang');
  if (slang && slang.options) {
      for(var i=0;i<slang.options.length;i++)
      {
          if (lang==slang.options[i].value)
	  {
	  	return slang.options[i].lang;
	  }
      }
  }
  return ;
}
function adjustButtonBox()
{
/*
 * Bug 21236436: iPad safary retina display box.clientHeight was retuning actual pixels
 */
   var sz = browser.isSafari?90:35;
   var box= document.getElementById('ButtonBox');
   var elem = [  box.getElementsByTagName('button')[0].style, box.getElementsByTagName('button')[1].style ];
   elem[0].fontSize='';
   elem[0].paddingRight='6px';
   elem[0].paddingLeft='6px';
   elem[1].fontSize='';
   elem[1].paddingRight='6px';
   elem[1].paddingLeft='6px';
   elem[1].marginLeft='';//'5px';

   var padding = 5;
   console.log('Initial : '+box.clientHeight);
   while(padding>=0 && box.clientHeight>sz)
   {
       elem[0].paddingRight=padding+'px';
       elem[0].paddingLeft=padding+'px';
       elem[1].paddingRight=padding+'px';
       elem[1].paddingLeft=padding+'px';
       padding=padding-1;
       console.log('padding : '+padding+' '+box.clientHeight);

   }
   var margin=25;
   var i=1;
   while(margin>=0 && box.clientHeight>sz)
   {
       elem[1].marginLeft=margin+'px';
   	margin= margin-1;
       console.log('margin : '+margin+' '+box.clientHeight);
   }
   var i = 1;
   var sz=90;
   while(sz>30 && box.clientHeight>sz)
   {
       elem[0].fontSize=sz+"%";
       elem[1].fontSize=sz+"%";

       console.log('size : '+sz+' '+box.clientHeight);
       sz=sz-10;
   }
}
/*
 * Translate the document.
 * 
 * Traverse the document and apply translations with elemens that hass the attribute 'message'.
 * 
 * The setting is done asynchronosuly, since the appsMessage may require ranslation before it is applied.
 * @see forMessage
 * 
 */
function setDocumentLanguage(lang) {
    var html =  document.getElementsByTagName('html')[0];
    var isoLang = getISOLang(lang);
    if (isoLang!='') {
       html.lang = isoLang;
    } else {
       html.removeAttribute('lang');
    }
    html.className=  html.className.replace("rtl ","");
/*
 * Bug 20775848: Safari looks after 'ltr' after 'rtl'
 * Bug 25554568: Chrome too. Better always use it rather tan trust in defults
 */
     while( html.className.search(/rtl/)>=0 )
     {
	     html.className= html.className.replace(/rtl/,'');
     }
     if  (lang == 'AR' || lang == 'IW') 
     {
          html.dir = 'rtl';
          html.className='rtl '+html.className;
     } else {
          html.dir = 'ltr';
    }


/* But don't affect styles when ltr */

     html.className=(html.dir=='rtl'?'rlt ':' ')+ html.className;

    for (var j = 0;j < tags.length;j++) {
        var list = document.getElementsByTagName(tags[j]);
        var a;
        for (var i = 0;i < list.length;i++) {
            if (a = list[i].attributes.getNamedItem('message')) {
                if (tags[j]=='input') {
                     forMessage(lang,a.value,function(m){
                          for( f in inpFld ) {
                              if  (list[i].attributes.getNamedItem(inpFld[f])) {
                                  list[i][inpFld[f]]=m;
                                  //ADA:no placehoders: if (inpFld[f]=='placeholder') list[i][inpFld[f]]="  "+m;
                              }
                          }
                          
                     });
                }else if (tags[j]=='button') {
                     forMessage(lang,a.value,function(m){
			  list[i][field[j]]=m;
			  adjustButtonBox();                 
                       });
                }else { 
                   forMessage(lang,a.value,function(m){
		        try {
// During Bug 25974467 testingfs found an exception in this line
// sorround it by try/catch to stop problem propagation
                        list[i][field[j]]=m;
                       //Replaced hasAttribute for compatibility scenarios
                        if ('title' in list[i]) {
                            list[i].title = m;
                        }
			} catch(e)
			{
			}
                    } );
                }
            }

        }
    }
    var s = document.getElementById('accessibility');
    if (s) for (var i = 0;i < s.length;i++) {
        forMessage(lang,"LU/ACCESSIBILITY:" + s[i].value,function(m){s[i].firstChild.data =m;s[i].title=m;} );
    }
    var s = document.getElementById('slang');
try { 
    if (s) for (var i = 0;i < s.length;i++) {
        s[i].title = appsMessage[s[i].value]["LANG/TRANSLATION"][lang];
    }
}catch (e) {alert(e); } 

   var err= document.getElementById('errorBox');
   if (err) displayErrorCode(err['message']);
   // select current lang on the dropbox
   s=document.getElementById('slang');
   if (s) s=s.children;
    for (var i = 0;s && i < s.length;i++) { 
       s[i].selected = s[i].value==lang;
    }

}

function lookLikeaMessage ( code ) 
{

   var x = /[LU/ACCESSIBILITY:|LANG/]*[A-Z_0-9-]*/ig.exec(code);
   return x==code;
}
/**
 * 
 * If the appsMessage is already cached, simply call the the action.
 * If not, asynchronously, then translate the appsMessage, and only when it is ready display it.
 * 
 * 
 * @param lang langCode 
 * @param msg  appsMessageCode
 * @param action to call action(msg)
 */
function forMessage(lang, msg, action) {
    if (!lookLikeaMessage(msg))  return false;
    if (!appsMessage.hasOwnProperty(lang)) {
        // no translation avail
        action(instantiate(msg));
    }
    else if (appsMessage[lang].hasOwnProperty(msg)) {
        // appsMessage translation is already cached
        action(instantiate(appsMessage[lang][msg]));
    }
    else  
      // call the service to translate the appsMessage and action it when it is ready
      call('translate',"lang="+lang+"&message="+msg,
              function(result) {
                if (result.http_status === 200) {
                    var gotit = false;
                    for( i in result ) 
                        for(j in result[i]) {
                            appsMessage[i][j] = result[i][j];
                            gotit |= lang==i  && msg==j;
                          }
                    if (!gotit) {
			    if (appsMessage['US'][msg]) {
                                appsMessage[lang][msg]=appsMessage['US'][msg]+"(*)"
			    }
                            else { 
			         appsMessage[lang][msg]=msg+"(*)"; //msg+"(translation failed)"; // never try again
			    }
		    }
		
                    action(instantiate(appsMessage[lang][msg]));
                }
                else {
                    action(msg+'(translation failed ) code='+result.http_status);
                }
                
            }, true);
     return true;

}

function getCurrentLang() {
    try { return document.getElementById('slang').value.toUpperCase(); } 
    catch (e) {return "US"; } 
}



function changeLanguage() {
    setDocumentLanguage(getCurrentLang());
}

function checkSubmit(e) {
    if (typeof e=='undefined' && typeof event=='object') {
        e=event;
    }
    if (!e)
        return;
    if (e.keyCode == 13) {
        // Bug 22232696,22885581 :enter must trigger the action for links
	if (e.target && e.target.tagName=='A')
	{
	  e.target.onclick(); 
	}
        else {
	  submitCredentials();
        }
    }
}
// Bug 25974467 :
//  Create an explicit version and replace displayMessage('') 
//  using until now.
function clearErrorMessage()
{
   var ebox = document.getElementById('errorBox');
   if (browser.isIE)
   {
        // para IE actually we remove the element errorBox
	if (ebox)
	{
	   ebox.parentNode.removeChild(ebox);
	}
   } else {
      // for other browser just empty and hide it
      if (ebox)
      {
      	ebox.innerText='';
	ebox.style.display='none';
      }
   }
}
var msgCounter ;
//
// More that a fix is a hack
// JAWS insist on read repeatedly elements with role=alert
// The only why we found so far is to create the element role=alert
// and three seconds later remove the attribute role, so JAWS
// will not read it again
function jawsFix()
{
 var box =     document.getElementById("errorBox");
 if (box)
 {
    box.removeAttribute('role');
    console.log('role=alert removed');
 }


}
// Bug 25974467
//   For IE the element errorBox is removed and added after 'marker'
//
function displayErrorMessageIE(msg)
{
	clearErrorMessage();
	if (msg && msg.length>0)
	{
	   msgCounter++;
	   var marker =  document.getElementById('marker');
	   ebox=document.createElement('DIV');
	   ebox.class="control_box min_margin";
	   ebox.id='errorBox';
	   ebox.style.display='block';
	   ebox.setAttribute('role','alert');
	   ebox.innerText=msg;
	   marker.parentNode.insertBefore(ebox,marker);
	   // hack: remove the role attribute after 3 seconds
           // 5 seconds for the first error 
	   setTimeout(jawsFix,msgCounter==1?5000:3000);

	}

        return;
}
function displayErrorMessage(msg) {
    var box =     document.getElementById("errorBox");
    if ( browser.isIE  )
    {
       displayErrorMessageIE(msg);
    }
    else { 
       if (msg && msg!=''){
           if (box!=null){
		    box.innerHTML = msg;
	            box.style.display='block';
	   }
           else alert(msg);
       } else {
          clearErrorMessage();
       }
    }
}

function sayBye() {
  document.body.innerHTML="<img src=/OA_HTML/media/spinner.gif alt='Processing' >";
  document.body.className="bye-spinner";
}

var isLocked=false;
/*
 * Lock the document by displaying a semi transoarent div in front of all the rest
 */
function lock() {
   try { 
      setStyle('lock','');
   } catch (e) {
	logException(e);
       console.log("Above exception is just informative");
   }
    locked= true;
}

function unlock() {
   try { 
       Hide('lock');
    } catch (e) {
	logException(e);
       console.log("Above exception is just informative");
   } 
    locked=false;
}

/**
 * asynchronously display an alert with the translated errCode
 * @param errCode 
 */
function showErrorPopup( errCode ) {
    forMessage(getCurrentLang(),errCode,
       function(msg) {
           unlock();
           alert(msg);
       }
    );
}
function instantiate(msg )
{
   if (!msg || msg=='') return msg;
   msg = msg.replace('&USER',currentUser);
   msg = msg.replace('&PROMPT','');
   return msg;
}
function displayTranslatedErrorMessage( msg ) {
    var box = document.getElementById('errorBox');
    if (browser.isIE)
    {
      displayErrorMessageIE(msg);
    }
    else {
         if (box) {
	 	box.innerHTML=msg;
		box.style.display='block';
	 }
    
         // JAWS requirement
         var n="error-"+errorSequence;
         var prev = document.getElementById(n);
         if (prev && prev.parentNode) {
             prev.parentNode.removeChild(prev);
         }
         errorSequence ++;
         
         var aria = document.createElement('DIV');
         aria.id="error-"+errorSequence;
         aria.innerHTML=msg;
         aria.style.display='none';
         box.appendChild(aria);
         box.setAttribute('aria-describedby',aria.id);
    }

}
function displayErrorCode( errCode  )
{
   if (errCode && errCode!='') {
	   forMessage(getCurrentLang(),errCode, displayTranslatedErrorMessage );
   } else {
	  clearErrorMessage();
   }
   var box = document.getElementById('errorBox');
   if (box==null) {
      // theres no errorBox to present the message
       forMessage(getCurrentLang(),errCode, function(m) { alert( m )  } );
   } else {
      // set the message attribute for future translations
       box['message'] = errCode;
   }
}


/**
 * 
 * Validate form fields are not empty and call the Authenticateuser service.
 * 
 * Will lock the screen until the service responds.
 * 
 */
function submitCredentials() {
    clearErrorMessage();
    var login = document.getElementById('login');
    if (!login) {return;}
    var u = login.usernameField;
    if (!u) {return;}
    if (u.value == "") {
        return  displayErrorCode('FND_APPL_LOGIN_FAILED');
    }
    var p = login.passwordField;
    if (!p) {return;}
    if (p.value == "") {
        return  displayErrorCode('FND_APPL_LOGIN_FAILED');
    }
    lock();

    var params = "username=" + encodeURIComponent(u.value)
         + "&password=" + encodeURIComponent(p.value) ;
         
    var ac= document.getElementById('accessibility');
    if (ac) { params=params+ "&_lAccessibility=" + encodeURIComponent(ac.value); } 

    // bug 22928920: need to send both display and langCode parameter
    var sl= document.getElementById('slang');
    if (sl) { params=params+  "&displayLangCode=" + encodeURIComponent(sl.value);} 
    
    var lc = getParameter("langCode");
    if (lc) { params=params+  "&langCode=" + encodeURIComponent(lc);} 
         
    if (requestUrl && requestUrl != '')
        params += "&requestUrl=" + encodeURIComponent(requestUrl);
    if (cancelUrl && cancelUrl != '')
        params += "&cancelUrl=" + encodeURIComponent(cancelUrl);
        
    spinner(true);    
    call('AuthenticateUser',params,function(result) {
            if (result.http_status == 200) {
                if (result.status == 'failed') {
                    spinner(false);
		    unlock();
                    displayErrorCode(result.errorCode?result.errorCode:'FND_APPL_LOGIN_FAILED');
                    if (result.popup)  alert(result.popup);

                }
                else {
                
                    // print the failed attemps
                    if (result.popup) {
                       spinner(false);
                       alert(result.popup);
                    }
                    unlock();
                    // home page takes a lot to display
                    // we should had left the login screen locked, but 
                    // instead , we simply go to white , as a sign that authentication was successful
                    // and the rest of the time is consumed by the destnation url, no by the login 
                    sayBye();
                    window.location = result.url;
	       }
           }
   }, true);
}

/*
 * Lock the document by displaying a semi transoarent div in front of all the rest
 */
function confirmLogout( ) {
    forMessage(getCurrentLang(),"FND_SSO_LOGOUT_CONFIRM",function(msg) {
       if (window.confirm(msg)) logout() 
    });
}
function logout() {
   var params="username="+encodeURIComponent(currentUser)
        +"&requestUrl="+encodeURIComponent(requestUrl)
        +"&cancelUrl="+encodeURIComponent(cancelUrl)
        +"&lang="+getCurrentLang();
   spinner(true);
   call('logout',params,function(result) {
                if (result.http_status === 200 && result.status=='success') {
                    window.location= result.url;
                }
                else {
                    // CRITICAL: the logout service failed, we don't know what to do
                    // user must close the browser.
                    spinner(false);
                    document.body.style.display='none';
                    alert("Logout failed, please exit the browser");
                    lock();
                }       
   }, true);

}

function handleCancel() {
    if (cancelUrl && cancelUrl!='') {
        location = cancelUrl;
    } else {
        cleanForm();
    }
}
function cleanForm() {
    lock();
    clearErrorMessage();
    var login = document.getElementById('login');
    if (login) { 
        login.usernameField.value = currentUser;
        // login.usernameField.disabled = currentUser != '';
        login.passwordField.value = '';
        if (currentUser != '')
        {
                login.usernameField.setAttribute('readonly',true);
                login.passwordField.focus();
        }
        else { 
                login.usernameField.removeAttribute('readonly');
                login.usernameField.focus();
        }
    }
    unlock();
}

function getXMLHttpRequest()
{
  try { return  new XMLHttpRequest();} catch (e){}
  try { return  new ActivexObject("Msxml2.XMLHTTP");} catch (e){}
  try { return  new ActiveXObject("Microsoft.XMLHTTP")} catch (e){}
  alert('Your browser is not supported');
  lock();
  return null;
}

function spinner( mode) {
    if (mode) {
        setStyle('spinner','block');
    } else {
        setStyle('spinner','none');
        
    }
}
 
/**
 * 
 * Asynchronously calls a service.
 * Pass the result to the 'action' function.
 * The result will be something like
 *     result = { 
 *       http_status: 200 , // or whatever it was
 *       status:'success',  // or 'failed'
 *       ... pertinent reponse
 *       };
 * 
 * @param service  service name(implemented by AppsLocalLogin.jsp)
 * @param params  URL encoded parameters
 * @param action function to call when the service responds
 */
function call(service,params,action,async) {
console.log("call "+service);
    var xmlreq = getXMLHttpRequest();
    xmlreq.open("POST", "/OA_HTML/AppsLocalLogin.jsp?", async);
    xmlreq.setRequestHeader("X-Service", service);  
    xmlreq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlreq.setRequestHeader("Content-length", params.length);
    if (!browser.isSafari) xmlreq.setRequestHeader("Connection", 'close');

	var result;
	var text;
    if(async==true){
    xmlreq.onreadystatechange =function (e) {
        if (xmlreq.readyState === 4) {
           text = xmlreq.responseText;
           // IE doesn't like pretty-printed Json
           eval("result=" + text.replace(/[\n\r\t\s]/g,'').replace(/,}/g,'}'));
           result.http_status = xmlreq.status;
           action(result);
        };
    };
    }
    xmlreq.send(params);
	/* If the call is synchronous, actions to be performed onreadystatechange
	should be done after send() */
    if(async==false){
        text = xmlreq.responseText;
        // IE doesn't like pretty-printed Json
        eval("result=" + text.replace(/[\n\r\t\s]/g,'').replace(/,}/g,'}'));
        result.http_status = xmlreq.status;
        action(result);
    }
}



function changeAccessibility() {

}

function forgotPassword() {
    var params = "langCode="+getCurrentLang();
    if (requestUrl && requestUrl != '')
        params += "&requestUrl=" + encodeURIComponent(requestUrl);
    if (cancelUrl && cancelUrl != '')
        params += "&cancelUrl=" + encodeURIComponent(cancelUrl);


    lock(); // the service may take some time
    spinner(true);

    call('forgotPasswordLink',params,function (result) {
          if (result.status=='success') {
              //go to blank and redirect
             if (result.url!=null && result.url.trim().length>0) 
             { 
	       // Bug 23324832
	       //      For unknowun reason the destination page  becomes unresponsive
	       //      To improve users experience we clear the browser display
	       //      and set the destination on the URL bar.
	       unlock();
	       spinner(false);
	       document.body.innerHTML="";
               document.body.className="bye-spinner";
	       document.title="Loading...";
	       // This changes the text on URL Bar.
	       // It is HTML5  featurem, not available for older browser
	       try { window.history.pushState({page: 1}, "Loading...", result.url.replace(/^http.*:\/\/[^\/]*/,"")); } 
	       catch (e) { console.log("ERrror:"+e); }
	       console.log("Redirect to:"+result.url);
	       window.location = result.url;
	     }
          } else {
              forMessage(getCurrentLang(),result.errCode,
                      function(m){ 
                          unlock();
                          alert(m);
                    } );
          }
          unlock();

       }, true);    
}

function registerHere() {
    var params = "langCode="+getCurrentLang();

    lock(); // the service may take some time
    spinner(true);
    call('registerHereLink',params,function (result) {
          if (result.status=='success') {
              //go to blank and redirect
             if (result.url!=null && result.url.trim().length>0) 
             { 
	        window.location = result.url;
	     }
	     else
		spinner(false); 
          } else {
              forMessage(getCurrentLang(),result.errCode,
                      function(m){ 
                          unlock();
                          alert(m);
                    } );
          }
          unlock();

       }, true);    
}

function getQueryParams(qs) {
    var d=decodeURIComponent,r = {},s=/[?&]?([^=]+)=([^&]*)/g;
    while(i=s.exec(qs)){r[d(i[1])]=d(i[2]);}
    return r;
}
function getParameter(name)
{
   try{ return  getQueryParams (location.search)[name]; } catch (e){ return ''; }
}

/**
 * This is run when the Script is loaded, before the page is loaded,
 * only assign global variables .
 * 
 */
function jsStartup() {
   console.log("jsStartup");
   if (window.loadedLoginPage) {
       console.log("JS already initialized");
   }
   /* Bug 20567108 and 20453249 */
   var errorCode=getParameter('errCode');
   var param = '';
   var queryString  = location.search;
   if (!((typeof queryString == 'undefined') || queryString.length==0)) {
         param = queryString.slice(1); 
	/* location.search returns the query
	string beginning from a '?' */
   }

  call('jsStartup', param,function(ret) {
        window.AppsLoginPage= {
            loaded: true,
            state: ret,
            loadWindowSavedState: function () {
                currentUser=this.state.currentUser;
                currentLang=this.state.currentLang;
                hasSession=this.state.hasSession;
                requestUrl=this.state.requestUrl;
                cancelUrl=this.state.cancelUrl;
                browserClass= this.state.browserClass;
                lockLanguage=this.state.lockLanguage;
                lockADA=this.state.lockADA;
                appsMessage = this.state.translations;
                logLevel=this.state.logLevel;
             }
        };
       window.AppsLoginPage.loadWindowSavedState();
    }, false);

}

function createLanguageDropBox(id ) {
    var box = document.getElementById(id);
    if (typeof(box)=='undefined' || !box || box==null) return;
    
    if (box.length==0) {
        for(l in appsMessage) {
            if (!lockLanguage || currentLang==l)
            {
                var opt ;
                if (browser.isIE) {
                    opt = new Option(appsMessage[l]['LANG/NAME'],l);
                    opt.lang = appsMessage[l]['LANG/ISOCODE'] ;
                    opt.title = appsMessage[l]['LANG/TITLE'];
                    opt.selected = appsMessage[l]['LANG/SELECTED'];
                    box.options[box.options.length]=opt;
                }
                else {
                    opt = document.createElement('OPTION');
                    opt.lang = appsMessage[l]['LANG/ISOCODE'] ;
                    opt.title = appsMessage[l]['LANG/TITLE'];
                    opt.selected = appsMessage[l]['LANG/SELECTED'];
                    opt.value = l
                    opt.textContent = appsMessage[l]['LANG/NAME'];
                    box.appendChild(opt);
                }
            }
        }
    }
    
}

function createADADropbox(id) {
    var box = document.getElementById(id);
    if (typeof(box)=='undefined' || !box || box==null) return;
     var opt ;
     var selected = 0;
     var options = ['N','Y','S'];
     for( var l in options ) {
        var lu = 'LU/ACCESSIBILITY:'+options[l];
        if (browser.isIE) {
             opt = new Option(appsMessage[currentLang][lu],options[l]);
             if (selected==0){
                 opt.selected="1";
                 selected=1;
             }
             box.options[box.options.length]=opt;
        } else {
             opt = document.createElement('OPTION');
             opt.value = options[l];
             opt.textContent =appsMessage[currentLang][lu];
             if (selected==0){
                 opt.selected="1";
                 selected=1;
             }
             box.appendChild(opt);
       }         
     }

}

function setStyle(id, val) {
  try {document.getElementById(id).style.display=val;}  catch (ignored){}
    
}
function Hide ( id ) {
   setStyle(id,'none');
}

function forAll( list , doThis) {
    for(var i in list )try{doThis(list[i]);} catch(ignored){;}
}
function hideItems() {

   forAll(window.AppsLoginPage.state.hide.id, function(e){ document.getElementById(e).style.display='none'; } );
   forAll(tags,function(t) {
        forAll(document.getElementsByTagName(t), function(elem) {
             var m = elem.getAttribute('message')
            if (m &&  elem.style.display!='none' )
              forAll(window.AppsLoginPage.state.hide.appsMessage, function( msg ) {
                 if (msg==m) elem.style.display='none';
              });
        }) 
     });
   


}
function HTMLEncode( val) {
    return val?val
       .replace(/</g,'&lt;')
       .replace(/>/g,'&gt;')
       .replace(/&/g,'&amp;')
       .replace(/'/g,'&#39;')
       .replace(/"/g,'&quot;')
       :val;
}
function showLinks() {
    var box = document.getElementById('LinksBox');
    var links = window.AppsLoginPage.state.links;
    var lang = getCurrentLang();
    var li;
    if (box && box.clientWidth>0) { // if visible
        box.removeChild(box.children[0]);
        var ul=document.createElement('UL');
        for(i in links) {
            li = document.createElement("LI");
            var a = document.createElement("A");
            a.setAttribute('message',i);
            a.href=links[i];
            a.tabindex = 0;
            a.innerHTML= HTMLEncode(window.AppsLoginPage.state.translations[lang][i]);
            li.appendChild(a);
            ul.appendChild(li);
        }
        box.appendChild(ul);
    }
}
/** 
 * This is run when the page is loaded
 */
function startup()
{
var appsMessage = window.AppsLoginPage.state.translations;
   console.log("startup");
   console.log("starup: AppsLoginPage.loaded="+isLoaded());
   
   var ok = !(typeof appsMessage=='undefined')
       &&  appsMessage.hasOwnProperty('US')
       && appsMessage.US.hasOwnProperty('FND_SSO_LOGIN');
   console.log("Var test:"+ok);
   
   if (!isLoaded() || !ok ) {
       if ( typeof window.AppsLoginPage!='undefined'
          && typeof window.AppsLoginPage.loadWindowSavedState !='undefined')
      {
       window.AppsLoginPage.loadWindowSavedState();
      } else {
        console.log("Cannot reload window");
      }
 
   }

   if (! appsMessage.hasOwnProperty('US') || !appsMessage.US.hasOwnProperty('FND_SSO_LOGIN')) {
       alert('MESSAGES CORRUPTED');
       appsMessage = window.AppsLoginPage.state.translations;
        if (! appsMessage.hasOwnProperty('US') || !appsMessage.US.hasOwnProperty('FND_SSO_LOGIN')) {
                jsStartup() ;
        }
   }
    // include the CSS
    /*
    var link = document.createElement('LINK');
    link.href = "?";
    link.type = "text/css";
    link.rel = "stylesheet";
    document.head.appendChild(link);
    */
    
    // set the browser class for the style browser specific
    document.getElementsByTagName('HTML')[0].setAttribute('class',browserClass)
    
    
    // setup the login form
    var login = document.getElementById('login');
    if(login) {
        login.action='javascript:submitCredentials();';
        login.autocomplete='off';
        login.method='POST';
        //login.usernameField.disabled = currentUser!='' && currentUser!='GUEST';
        login.passwordField.value = '';
	if (currentUser!='' && currentUser!='GUEST')
        {
	       login.usernameField.setAttribute('readonly',true);
               login.passwordField.focus();
	} else { 
              login.usernameField.removeAttribute('readonly');
              login.usernameField.focus();
        }

        login.usernameField.readonly = currentUser!='' && currentUser!='GUEST';
        if (login.usernameField.disabled || login.usernameField.readonly) {
             setStyle('logoutLink','');
        }
        if (typeof login.passwordField=='string')login.passwordField = '';
        else login.passwordField.value='';
    }
    
    // disable ADA pick up
    if (false && lockADA) {
        Hide('adaLabel','none');
        Hide('accessibility');
       
    }
    
    // create the language drop box.
    // if the lockLanguage=true, only the currentLang will be displayed.
    // No More the login page can be used to change session language.
    createLanguageDropBox('slang');

    createADADropbox('accessibility');


try{
    setDocumentLanguage(currentLang);
} catch (e ) { alert(e); }

    
    var div = document.getElementById('login');
    if (div) {
        div.onkeypress = function(event) { return  checkSubmit(event); } ;
    }

    // setup forgot password link
   plink = document.getElementById('ForgotPasswordURL');
    if (plink) {
        plink.onclick=forgotPassword;
    }
    
    // Register Here 
    rlink = document.getElementById('RegisterHereURL');
    if (rlink) {
    	  rlink.onclick=registerHere;
    }
    
    // doesnt do much anyway
    if (document.getElementById('slang')) {
        document.getElementById('slang').onchange=changeLanguage;
    }
    
    // create the lock panel.
    if (!document.getElementById('lock')) {
        div = document.createElement('DIV');
        div.id = 'lock';
        div.className='lock';
        div.style.display='none';
        document.body.appendChild(div);
    }
    if (!document.getElementById('spinner')) {
        div = document.createElement('DIV');
        div.id = 'spinner';
        div.className='spinner';
        div.style.display='none';
        document.body.appendChild(div);
    }
    cleanForm();
    var errCode = getParameter('errCode');
    if (errCode && errCode!='') displayErrorCode(errCode);
                    var rq = getParameter("requestUrl");
                var cn = getParameter("cancelUrl");
                if ( (rq && rq.length>0 && requestUrl=='' ) || 
                      (cn && cn.length>0 && cancelUrl=='' ))
                {
                    displayErrorCode('FND_SSO_PHISH_ERROR');
                } 
    hideItems();
    showLinks();
    if (document.afterLoad) try{ document.afterLoad(); } catch (ignored){;} 
}
window.onpageshow=function(event) {
    console.log("Window.onshow: ");
    console.log('Window.onshow:'+event+" trusted:"+event.isTrusted+" persisted:"+event.persisted);
    console.log("AppsLoginPage.loaded="+window.AppsLoginPage.loaded);
    if (event.persisted) {
        console.log("page from bfcache, running setup");
        setup();
    }
}
window.onunload=function(event) {
    console.log("window.unload:");
    console.log(event);
    if (typeof window.AppsLoginPage != 'undefined')
    {
       window.AppsLoginPage.loaded=false;
    } else {
        window.AppLoginPage= { loaded:false};
    }
}


 jsStartup(); 
   console.log("loaded: login.js");
