var K_staP='';
var K_dynP='';
var K_ie=0;
var K_gec=0;
var K_op=0;
var K_ko=0;
var K_ns=0;
var K_saf=0;
var K_ep=0;
var K_ch=0;
var K_appList=new Array();
var K_appCanvas='KBODY';
var K_lastRightClickX=0;
var K_lastRightClickY=0;
var K_lastMouseUpX=0;
var K_lastMouseUpY=0;
var K_CMactive='';
var K_groupMinusWidth=11;
var K_groupMinusHeight=19;
var K_hashCode='';
var K_zIndex=100;
var K_sound=0;
var K_CM_dsktp='';
var K_SC_win='';
var K_bounceId=null;
var K_bounceX=0;
var K_bounceY=0;
var K_bounceTimerId=0;
var K_clipboard=new Array();
var K_clipboardOp='';

function $(v) {return document.getElementById(v)}
function $t(x,v){return x.getElementsByTagName(v)}
function $a(x,v){return x.getAttribute(v)}

function K_init()
{
	var bNS=K_browserNotSupported();
	if (bNS)
	{
		K_abortInit(bNS);
		return;
	}

	var e=document.getElementById('KWS_conf');
	if (e==null)
	{
		K_abortInit(10);
		return;
	}

	var p=e.firstChild.data.split(' ');
	K_dynP=p[0];
	K_staP=p[1];
	e.parentNode.removeChild(e);
	

	document.body.style.overflow='hidden';
	document.body.style.margin='0';
	document.body.style.padding='0';
	document.body.style.width='100%';
	document.body.style.height='100%';
	document.body.id='K_int_KBODY';

	var e=document.getElementById('startApp');
	p=e.firstChild.data.split(' ');
	e.parentNode.removeChild(e);
	var xmlSrc=p[0];
	p=p.slice(1);
	var params=p.join(' ');
	K_loadAppAJAX(xmlSrc, params);
}

function K_resLoadApp(xmlDoc,params)
{
	var a=$t(xmlDoc,'app')[0];
	var appId=$a(a,'id');
	var appName=$a(a,'name');
	var single=$a(a,'singleInstance');
	if(single=='1' && K_inArray(appName,K_appList))
	{
		K_stopBounce();
		return;
	}
	
	K_appList.push(appName);
	window[appId]=new K_App('',a,appName,K_appCanvas);
	window[appId+"_main"](appId,params);
}

function K_unloadApp(appName)
{
	for (var x=0;x<K_appList.length;x++)
		if(K_appList[x]==appName)
			K_appList.splice(x,1);
}

function K_abortInit(msg)
{
	var errMsg=window["K_L_abortInitMessage"+msg];
	var cad="<table width='100%' height='100%' style=\"background:#FFF;font-family:'Trebuchet MS';font-size:11px;color:#000\"><tr><td align='center'><img src='"+K_staP+"KWS/img/2pclogo.png'/><br/><br/><br/>"+errMsg+"</td></tr></table>";
	$('K_body').innerHTML=cad;
}

function K_browserNotSupported()
{
	var abortInit=0;
	var pos=navigator.userAgent.indexOf("MSIE");if (pos>-1) K_ie=1;
	pos=navigator.userAgent.indexOf("Gecko");if (pos>-1) K_gec=1;
	pos=navigator.userAgent.indexOf("KHTML");if (pos>-1) K_ko=1;
	pos=navigator.userAgent.indexOf("Opera");if (pos>-1) K_op=1;
	pos=navigator.userAgent.indexOf("Netscape");if (pos>-1) K_ns=1;
	pos=navigator.userAgent.indexOf("Safari");if (pos>-1) K_saf=1;
	pos=navigator.userAgent.indexOf("Epiphany");if (pos>-1) K_ep=1;
	pos=navigator.userAgent.indexOf("Chrome");if (pos>-1) K_ch=1;
	if(K_ie && !K_gec && !K_ko && !K_op && !K_ns && !K_saf && !K_ep && !K_ch)
	{
		pos=navigator.userAgent.indexOf("MSIE");
		var majv=navigator.userAgent.charAt(pos+5);
		var minv=navigator.userAgent.charAt(pos+7)+navigator.userAgent.charAt(pos+8);
		minv=parseInt(minv);
		if (majv<5)
			abortInit=1;
		if (majv==5&&minv<5)
			abortInit=1;
	}
	
	return abortInit;
}

window.addEventListener?window.addEventListener("load",K_init,false):window.attachEvent("onload",K_init);
