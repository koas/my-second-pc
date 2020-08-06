<?php
session_start();
$appID='@!@';
include('locale.php');
$now = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0');
header('Last-Modified: ' . $now);
header('Cache-Control: no-store, no-cache, must-revalidate'); 
header('Cache-Control: pre-check=0, post-check=0, max-age=0');
header('Pragma: no-cache');
header('Content-Type: text/xml');

if ($_SESSION['M2PC_USER']<1)
die( '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<res><stat>AUTHERR</stat></res>');

include('../../php/conf.php');
echo'<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
$wIni=550;
$hIni=550;
?>
<app name="Image Viewer" id="M2PC_<?=$appID?>" cacheable='1'>
<stat>OK</stat>

<code><![CDATA[
M2PC_<?=$appID?>_exiting=false;
var M2PC_<?=$appID?>_imgPath='';
var M2PC_<?=$appID?>_mode='fit';
var M2PC_<?=$appID?>_zoom=100;
var M2PC_<?=$appID?>_zoomLevels=Array(2,5,10,20,33,50,67,100,200,300,400,500,600,700,800,900,1000,1100,1200,1300,1400,1500,1600,1700,1800,1900,2000);
var M2PC_<?=$appID?>_canvasW=<?=$wIni-8?>;
var M2PC_<?=$appID?>_canvasH=<?=$hIni-98?>;
M2PC_<?=$appID?>_main=function(appId, params)
{
	var p=params.split('#');
        M2PC_<?=$appID?>_wMain.innerDesktop=1;
        M2PC_<?=$appID?>_wAbout.render();
        M2PC_<?=$appID?>_wAbout.hide();
	M2PC_<?=$appID?>_wMain.render();
        M2PC_<?=$appID?>_wMain_cbResizeEnd();
	M2PC_<?=$appID?>_wMain.addToTaskbar('M2PC_TSKBR1');
	if(!K_ie)
	{
		K_trans('M2PC_<?=$appID?>_b1',75);
		K_trans('M2PC_<?=$appID?>_b2',75);
		K_trans('M2PC_<?=$appID?>_b3',75);
		K_trans('M2PC_<?=$appID?>_b4',75);
                K_trans('M2PC_<?=$appID?>_b5',75);
	}
        $('M2PC_<?=$appID?>_d').innerHTML="<br/><br/><div align='center'><span class='loading'> <?=$L8?></span></div>";
	if(p[1])
		M2PC_<?=$appID?>_loadImg(p[1]);
        K_stopBounce();
}
M2PC_<?=$appID?>_loadImg=function(img)
{
	M2PC_<?=$appID?>_imgPath=K_dynP+'apps/imageViewer/load.php?file='+img;
        $('M2PC_<?=$appID?>_i').src=M2PC_<?=$appID?>_imgPath;
}
M2PC_<?=$appID?>_imgLoaded=function()
{
	M2PC_<?=$appID?>_putImg();
}
M2PC_<?=$appID?>_putImg=function()
{
	var img=$('M2PC_<?=$appID?>_i');
	if(K_ie)
		img.style.display='block';
	var imgW=img.width;
	var imgH=img.height;
	img.style.display='none';

	var nW=nH=imgZ=0;
	if(M2PC_<?=$appID?>_mode=='fit')
	{
		if(imgW<M2PC_<?=$appID?>_canvasW && imgH<M2PC_<?=$appID?>_canvasH)
		{
			nW=imgW;
			nH=imgH;
			imgZ=100;
		}
		else
		{
			nW=M2PC_<?=$appID?>_canvasW;
			nH=Math.floor(imgH*nW/imgW);
			imgZ=Math.floor(100*nW/imgW);
			if(nH>M2PC_<?=$appID?>_canvasH)
			{
				nH=M2PC_<?=$appID?>_canvasH;
				nW=Math.floor(imgW*nH/imgH);
				imgZ=Math.floor(100*nH/imgH);
			}
		}
		M2PC_<?=$appID?>_zoom=imgZ;
		$('M2PC_<?=$appID?>_d').innerHTML="<div id='M2PC_<?=$appID?>_canvas' style='background-image:url(<?=$static?>icons/grid.png);position:absolute;width:"+nW+"px;height:"+nH+"px;left:"+Math.floor((M2PC_<?=$appID?>_canvasW-nW)/2)+"px;top:"+Math.floor((M2PC_<?=$appID?>_canvasH-nH)/2)+"px'><img style='width:"+nW+"px;height:"+nH+"px' id='M2PC_<?=$appID?>_i2' src='"+M2PC_<?=$appID?>_imgPath+"' /></div>";
	}
	if(M2PC_<?=$appID?>_mode=='actual')
	{
		if(imgW<M2PC_<?=$appID?>_canvasW && imgH<M2PC_<?=$appID?>_canvasH)
		{
			nW=imgW;
			nH=imgH;
			imgZ=100;
			M2PC_<?=$appID?>_zoom=imgZ;
			$('M2PC_<?=$appID?>_d').innerHTML="<div id='M2PC_<?=$appID?>_canvas' style='background-image:url(<?=$static?>icons/grid.png);position:absolute;width:"+nW+"px;height:"+nH+"px;left:"+Math.floor((M2PC_<?=$appID?>_canvasW-nW)/2)+"px;top:"+Math.floor((M2PC_<?=$appID?>_canvasH-nH)/2)+"px'><img style='width:"+nW+"px;height:"+nH+"px' id='M2PC_<?=$appID?>_i2' src='"+M2PC_<?=$appID?>_imgPath+"' /></div>";

		}
		else
		{
			var d=$('M2PC_<?=$appID?>_d');
			var c=$('M2PC_<?=$appID?>_canvas');
			var i=$('M2PC_<?=$appID?>_i2');
			var centerX=(d.scrollLeft+parseInt(c.style.width.substr(0,c.style.width.length-2))/2)/(i.width/imgW);
			var centerY=(d.scrollTop+parseInt(c.style.height.substr(0,c.style.height.length-2))/2)/(i.width/imgW);
			
			imgZ=M2PC_<?=$appID?>_zoom;
			var imgWz=Math.floor(imgW*(M2PC_<?=$appID?>_zoom/100));
			var imgHz=Math.floor(imgH*(M2PC_<?=$appID?>_zoom/100));
			centerX=centerX*(M2PC_<?=$appID?>_zoom/100);
			centerY=centerY*(M2PC_<?=$appID?>_zoom/100);
					
			nW=M2PC_<?=$appID?>_canvasW;
			nH=M2PC_<?=$appID?>_canvasH;
			if(imgHz<M2PC_<?=$appID?>_canvasH)
				nH=imgHz;
			if(imgWz<M2PC_<?=$appID?>_canvasW)
				nW=imgWz;
			d.innerHTML="<div id='M2PC_<?=$appID?>_canvas' style='background-image:url(<?=$static?>icons/grid.png);position:absolute;width:"+nW+"px;height:"+nH+"px;left:"+Math.floor((M2PC_<?=$appID?>_canvasW-nW)/2)+"px;top:"+Math.floor((M2PC_<?=$appID?>_canvasH-nH)/2)+"px'><img style='width:"+imgWz+"px;height:"+imgHz+"px' id='M2PC_<?=$appID?>_i2' src='"+M2PC_<?=$appID?>_imgPath+"' /></div>";
			d.scrollLeft=Math.floor(centerX-nW/2);
			d.scrollTop=Math.floor(centerY-nH/2);
		}		
	}
	$('M2PC_<?=$appID?>_i2').onmousedown=M2PC_<?=$appID?>_mousedown;
	$('M2PC_<?=$appID?>_i2').onmouseup=M2PC_<?=$appID?>_mouseup;
	$('M2PC_<?=$appID?>_i2').onmousemove=M2PC_<?=$appID?>_mousemove;
	$('M2PC_<?=$appID?>_i2').onmouseout=M2PC_<?=$appID?>_mouseup;
	if(K_ie)
		$('M2PC_<?=$appID?>_i2').ondragstart=function(){return false};
	
	M2PC_<?=$appID?>_wMain.setStatus(imgW+' x '+imgH+' <?=$L5?> &nbsp;&nbsp;&nbsp;'+imgZ+'%',0);
}

var M2PC_<?=$appID?>_xElem=M2PC_<?=$appID?>_yElem=0;
var M2PC_<?=$appID?>_dragging=false;

M2PC_<?=$appID?>_mousedown=function(e)
{
	M2PC_<?=$appID?>_dragging=true;
	$('M2PC_<?=$appID?>_i2').style.cursor='move';
	if (K_ie || K_op)
	{
		M2PC_<?=$appID?>_xElem=event.offsetX;
		M2PC_<?=$appID?>_yElem=event.offsetY;
	}
	if (K_gec)
	{
		M2PC_<?=$appID?>_xElem=e.layerX;
		M2PC_<?=$appID?>_yElem=e.layerY;
	}
	return false;	
}
M2PC_<?=$appID?>_mouseup=function()
{
	$('M2PC_<?=$appID?>_i2').style.cursor='default';
	M2PC_<?=$appID?>_dragging=false;
}
M2PC_<?=$appID?>_mousemove=function(e)
{
	if(!M2PC_<?=$appID?>_dragging) return;
	var xElem=yElem=0;
	if (K_ie || K_op)
	{
		xElem=event.offsetX;
		yElem=event.offsetY;
	}
	if (K_gec)
	{
		xElem=e.layerX;
		yElem=e.layerY;
	}
	var d=$('M2PC_<?=$appID?>_d');
	d.scrollLeft+=M2PC_<?=$appID?>_xElem-xElem;
	d.scrollTop+=M2PC_<?=$appID?>_yElem-yElem;
}
M2PC_<?=$appID?>_setMode=function(mode)
{
	M2PC_<?=$appID?>_mode=mode;
	if(mode=='actual')
		M2PC_<?=$appID?>_zoom=100;
	M2PC_<?=$appID?>_putImg();
}

M2PC_<?=$appID?>_zoomIn=function()
{
	M2PC_<?=$appID?>_mode='actual';
	var l=M2PC_<?=$appID?>_zoomLevels.length;
	for(var x=0;x<l;++x)
	{
		if(M2PC_<?=$appID?>_zoomLevels[x]>M2PC_<?=$appID?>_zoom)
		{
			M2PC_<?=$appID?>_zoom=M2PC_<?=$appID?>_zoomLevels[x];
			break;
		}
	}
	M2PC_<?=$appID?>_putImg();
}
M2PC_<?=$appID?>_zoomOut=function()
{
	M2PC_<?=$appID?>_mode='actual';
	var l=M2PC_<?=$appID?>_zoomLevels.length;
	for(var x=l;x>-1;--x)
	{
		if(M2PC_<?=$appID?>_zoomLevels[x]<M2PC_<?=$appID?>_zoom)
		{
			M2PC_<?=$appID?>_zoom=M2PC_<?=$appID?>_zoomLevels[x];
			break;
		}
	}
	M2PC_<?=$appID?>_putImg();
}
M2PC_<?=$appID?>_over=function(id)
{
	if(!K_ie)
		K_trans(id,100);
}
M2PC_<?=$appID?>_out=function(id)
{
	if(!K_ie)
		K_trans(id,75);
}
M2PC_<?=$appID?>_wMain_cbResizeEnd=function()
{
	var d=$('M2PC_<?=$appID?>_d');
	M2PC_<?=$appID?>_canvasW=window['M2PC_<?=$appID?>_wMain'].width-8;
	M2PC_<?=$appID?>_canvasH=window['M2PC_<?=$appID?>_wMain'].height-92;
	d.style.width=M2PC_<?=$appID?>_canvasW+'px';
	d.style.height=M2PC_<?=$appID?>_canvasH+'px';
	M2PC_<?=$appID?>_putImg();
}
M2PC_<?=$appID?>_wMain_destroy=function()
{
        M2PC_<?=$appID?>_exiting=true;
        M2PC_<?=$appID?>_wAbout.destroy();
        M2PC_<?=$appID?>_exiting=null;
	M2PC_<?=$appID?>_zoomLevels=null;
	M2PC_<?=$appID?>_zoom=null;
	M2PC_<?=$appID?>_canvasW=null;
	M2PC_<?=$appID?>_canvasH=null;
	M2PC_<?=$appID?>_mode=null;
	M2PC_<?=$appID?>_imgPath=null;
	M2PC_<?=$appID?>_xElem=null;
	M2PC_<?=$appID?>_yElem=null;
	M2PC_<?=$appID?>_dragging=null;
        M2PC_<?=$appID?>_HC1=null;
        M2PC_<?=$appID?>_HCab=null;
        M2PC_<?=$appID?>_exiting=null;
	K_unloadApp('IME_<?=$appID?>');
        return true;
}
M2PC_<?=$appID?>_wAbout_destroy=function()
{
    if(!M2PC_<?=$appID?>_exiting)
    {
        M2PC_<?=$appID?>_wMain.lockContent(false,'');
        M2PC_<?=$appID?>_wMain.bringToFront();
        M2PC_<?=$appID?>_wAbout.hide();
        return false;
    }
    return true;
}
M2PC_<?=$appID?>_cmAbout_click=function()
{
    window['M2PC_<?=$appID?>_wMain'].lockContent(true,'M2PC_<?=$appID?>_wAbout');
    M2PC_<?=$appID?>_wAbout.show();
    M2PC_<?=$appID?>_wAbout.bringToFront();
}
M2PC_<?=$appID?>_wMain_about=function()
{
    M2PC_<?=$appID?>_cmAbout_click();
}
]]>
</code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="<?=$wIni?>" h="<?=$hIni?>" mov="1" res="1" boundaries="auto,auto,auto,auto" minSize="400,210" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/imageViewer_16.png" clsBut="1" maxBut="1" minBut="1" statusBar="1" bgColor="#EDECEB" doc="" overflow="hidden">

<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div style='width:100%;height:50px;border-bottom:1px solid #000;' class='mbox'>
<div id='M2PC_<?=$appID?>_b1' style='text-align:center;float:left;margin-left:15px;cursor:default;-moz-user-select: none;' onmouseOver="M2PC_<?=$appID?>_over(this.id)" onmouseOut="M2PC_<?=$appID?>_out(this.id)" onclick="M2PC_<?=$appID?>_zoomIn()"><img src='<?=$static?>icons/zoomIn_32.png'/><br/><?=$L6?></div>
<div id='M2PC_<?=$appID?>_b2' style='text-align:center;float:left;margin-left:15px;cursor:default;-moz-user-select: none;' onmouseOver="M2PC_<?=$appID?>_over(this.id)" onmouseOut="M2PC_<?=$appID?>_out(this.id)" onclick="M2PC_<?=$appID?>_zoomOut()"><img src='<?=$static?>icons/zoomOut_32.png'/><br/><?=$L2?></div>
<div id='M2PC_<?=$appID?>_b3' style='text-align:center;float:left;margin-left:15px;cursor:default;-moz-user-select: none;' onmouseOver="M2PC_<?=$appID?>_over(this.id)" onmouseOut="M2PC_<?=$appID?>_out(this.id)" onclick="M2PC_<?=$appID?>_setMode('actual')"><img src='<?=$static?>icons/zoomActual_32.png'/><br/><?=$L3?></div>
<div id='M2PC_<?=$appID?>_b4' style='text-align:center;float:left;margin-left:15px;cursor:default;-moz-user-select: none;' onmouseOver="M2PC_<?=$appID?>_over(this.id)" onmouseOut="M2PC_<?=$appID?>_out(this.id)" onclick="M2PC_<?=$appID?>_setMode('fit')"><img src='<?=$static?>icons/zoomFit_32.png'/><br/><?=$L4?></div>
<div id='M2PC_<?=$appID?>_b5' style='display:none;text-align:center;float:left;margin-left:15px;cursor:default;-moz-user-select: none;' onmouseOver="M2PC_<?=$appID?>_over(this.id)" onmouseOut="M2PC_<?=$appID?>_out(this.id)" onclick="M2PC_<?=$appID?>_editImage()"><img src='<?=$static?>icons/edit_32.png'/><br/><?=$L7?></div>
</div>
<div id='M2PC_<?=$appID?>_d' style='position:absolute;left:0px;top:51px;background:#EDECEB;width:<?=$wIni-8?>px;height:<?=$hIni-98?>px;overflow:auto;'></div>
<img id='M2PC_<?=$appID?>_i' style='position:absolute;left:0px;top:0px;display:none;' onload='M2PC_<?=$appID?>_imgLoaded()'/>
]]></html_code>
</window>

<window id="M2PC_<?=$appID?>_wAbout" x="auto" y="auto" w="400" h="152" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L114?> <?=$L1?>" icon="<?=$static?>icons/about_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HCab" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table width='100%' cellpadding='10' cellspacing='0'><tr>
    <td width='64' align='center'><img src='<?=$static?>icons/imageViewer_64.png'/></td>
    <td><b><?=$L1?></b><br/>
    <?=$L115?> 0.8&beta;<br/>
    &copy; 2006-<?=date('Y')?> Karontek<br/>
    <div style='border-top:1px solid #000;margin-top:5px;padding-top:5px'>
    <?=$L117?>:<br/>
    <b>&bull;</b> nuoveXT (<a href='http://nuovext.pwsp.net/' target='_blank'>http://nuovext.pwsp.net/</a>)<br/>
    <b>&bull;</b> Crystal Project (<a href='http://www.everaldo.com/crystal/' target='_blank'>http://www.everaldo.com/crystal/</a>)
    </div>
    </td>
</tr></table>
]]></html_code>
</window>
</app>
