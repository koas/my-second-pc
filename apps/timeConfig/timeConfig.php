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
$iniX=780;
$iniY=500;
?>
<app name="<?=$L1?>" id="M2PC_<?=$appID?>" cacheable='0' singleInstance='1'>
<stat>OK</stat>


<code><![CDATA[
M2PC_<?=$appID?>_exiting=false;
M2PC_<?=$appID?>_timezone='<?=$_SESSION['TIMEZONE']?>';
M2PC_<?=$appID?>_main=function(appId, params)
{
	var p=params.split('#');
        M2PC_<?=$appID?>_wMain.innerDesktop=1;
        M2PC_<?=$appID?>_wAbout.render();
        M2PC_<?=$appID?>_wAbout.hide();
	M2PC_<?=$appID?>_wMain.render();
	M2PC_<?=$appID?>_wMain.addToTaskbar('M2PC_TSKBR1');
        
        K_loadAJAX('<?=$dynamic?>apps/timeConfig/timezones.xml','M2PC_<?=$appID?>_resLoadData');
        
        K_stopBounce();
}

M2PC_<?=$appID?>_resLoadData=function(xmlDoc)
{
    var cont=$('M2PC_<?=$appID?>_mapCont');
    var e=$t(xmlDoc,'tz');
    var cad="<select onchange='M2PC_<?=$appID?>_citySelected(this.value)' id='M2PC_<?=$appID?>_sel'>";
    for(var x=0;x<e.length;++x)
    {
        var i=document.createElement('img');
        i.src='<?=$static?>icons/punto.png';    
        i.style.position='absolute';
        i.style.left=$a(e[x],'x')+'px';
        i.style.top=$a(e[x],'y')+'px';
        i.id=e[x].firstChild.data;
        i.title=i.id;
        i.onmouseover=M2PC_<?=$appID?>_cityOver;
        i.onmouseout=M2PC_<?=$appID?>_cityOut;
        i.onclick=M2PC_<?=$appID?>_cityClicked;
        cont.appendChild(i);
        cad+="<option value='"+i.id+"'>"+i.id+"</option>";
    }
    cad+="</select>";
    $('M2PC_<?=$appID?>_contSel').innerHTML=cad;
    M2PC_<?=$appID?>_citySelected(M2PC_<?=$appID?>_timezone);
    setTimeout('M2PC_<?=$appID?>_blinkSelected()',500);
}

M2PC_<?=$appID?>_cityOver=function()
{
    M2PC_<?=$appID?>_wMain.setStatus(this.id,0);
}

M2PC_<?=$appID?>_cityOut=function()
{
    M2PC_<?=$appID?>_wMain.setStatus('',0);
}

M2PC_<?=$appID?>_cityClicked=function()
{
    M2PC_<?=$appID?>_citySelected(this.id);
}

M2PC_<?=$appID?>_citySelected=function(id)
{
    $(M2PC_<?=$appID?>_timezone).src='<?=$static?>icons/punto.png';
    $(M2PC_<?=$appID?>_timezone).style.display='inline';
    M2PC_<?=$appID?>_timezone=id;
    $('M2PC_<?=$appID?>_sel').value=M2PC_<?=$appID?>_timezone;
    $(M2PC_<?=$appID?>_timezone).src='<?=$static?>icons/punto2.png';
    $('M2PC_<?=$appID?>_mapCont').scrollTop=$(M2PC_<?=$appID?>_timezone).offsetTop-($('M2PC_<?=$appID?>_mapCont').offsetHeight/2);
    $('M2PC_<?=$appID?>_mapCont').scrollLeft=$(M2PC_<?=$appID?>_timezone).offsetLeft-($('M2PC_<?=$appID?>_mapCont').offsetWidth/2);
}

M2PC_<?=$appID?>_blinkSelected=function()
{
    var i=$(M2PC_<?=$appID?>_timezone);
    if(!i)
        return;
    if(i.style.display=='none')
        i.style.display='inline';
    else i.style.display='none';
    setTimeout('M2PC_<?=$appID?>_blinkSelected()',500);
}

M2PC_<?=$appID?>_wMain_cbResizeEnd=function()
{
    $('M2PC_<?=$appID?>_mapCont').style.width=M2PC_<?=$appID?>_wMain.width-40+'px';
    $('M2PC_<?=$appID?>_mapCont').style.height=M2PC_<?=$appID?>_wMain.height-94+'px';
    M2PC_<?=$appID?>_citySelected(M2PC_<?=$appID?>_timezone);
}

M2PC_<?=$appID?>_apply=function()
{
    K_loadAJAX('<?=$dynamic?>apps/timeConfig/changeTimezone.php?t='+M2PC_<?=$appID?>_timezone,'M2PC_<?=$appID?>_resApply');
}

M2PC_<?=$appID?>_resApply=function(xmlDoc)
{
    var d=$t(xmlDoc,'d')[0].firstChild.data;
    M2PC_TSKBR1.setLocalDate(d);
    alert("<?=$L5?>");
}

M2PC_<?=$appID?>_wMain_destroy=function()
{
        M2PC_<?=$appID?>_exiting=true;
        M2PC_<?=$appID?>_wAbout.destroy();
        M2PC_<?=$appID?>_exiting=null;
        M2PC_<?=$appID?>_HC1=null;
        M2PC_<?=$appID?>_HCab=null;
	K_unloadApp('<?=$L1?>');
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

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="<?=$iniX?>" h="<?=$iniY?>" mov="1" res="1" boundaries="auto,auto,auto,auto" minSize="400,210" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/timezone_16.png" clsBut="1" maxBut="1" minBut="1" statusBar="1" bgColor="#FFF" doc="" overflow="hidden">

<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div style='padding:10px' align='center'>
<?=$L2?>&nbsp;&nbsp;&nbsp;<span id='M2PC_<?=$appID?>_contSel'><span class='loading'> <?=$L3?></span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' class='button' value='   <?=$L4?>   ' onclick='M2PC_<?=$appID?>_apply()' /><br/>
<?=$L6?>
<div style='position:relative;margin:10px;border:1px solid #000;overflow:auto;width:<?=$iniX-40?>px;height:<?=$iniY-114?>px' id='M2PC_<?=$appID?>_mapCont'>
<img style='position:absolute;left:0px;top:0px;' width='1560' src='<?=$static?>icons/mapa.jpg' />
</div>
</div>

]]></html_code>
	
</window>

<window id="M2PC_<?=$appID?>_wAbout" x="auto" y="auto" w="400" h="152" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L114?> <?=$L1?>" icon="<?=$static?>icons/about_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HCab" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table width='100%' cellpadding='10' cellspacing='0'><tr>
    <td width='64' align='center'><img src='<?=$static?>icons/timezone_64.png'/></td>
    <td><b><?=$L1?></b><br/>
    <?=$L115?> 1.0&beta;<br/>
    &copy; 2006-<?=date('Y')?> Karontek<br/>
    <div style='border-top:1px solid #000;margin-top:5px;padding-top:5px'>
    <?=$L117?>  IconShock (<a href='http://www.iconshock.com/' target='_blank'>http://www.iconshock.com/</a>)<br/>
    <?=$L118?>  psdGraphics (<a href='http://www.psdgraphics.com' target='_blank'>http://www.psdgraphics.com/</a>)
    
    </div>
    </td>
</tr></table>
]]></html_code>
</window>
</app>
