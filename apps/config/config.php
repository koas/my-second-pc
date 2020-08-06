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
?>
<app name="Image Viewer" id="M2PC_<?=$appID?>" cacheable='1'>
<stat>OK</stat>


<code><![CDATA[
M2PC_<?=$appID?>_exiting=false;
M2PC_<?=$appID?>_wallList=null;
M2PC_<?=$appID?>_main=function(appId, params)
{
	var p=params.split('#');
        M2PC_<?=$appID?>_wAbout.render();
        M2PC_<?=$appID?>_wAbout.hide();
	M2PC_<?=$appID?>_wMain.render();
	M2PC_<?=$appID?>_wMain.addToTaskbar('M2PC_TSKBR1');
        
        K_loadAJAX('<?=$dynamic?>apps/config/listWallpapers.php','M2PC_<?=$appID?>_resLoadData');
        
        K_stopBounce();
}

M2PC_<?=$appID?>_resLoadData=function(xmlDoc)
{
    M2PC_<?=$appID?>_wallList=xmlDoc;
    var cad="<select size='6' id='M2PC_<?=$appID?>_wallCombo' style='border:1px solid #000;width:215px;height:256px' onchange='M2PC_<?=$appID?>_testWall(this.value)'>";
    var g=$t(M2PC_<?=$appID?>_wallList,'g');
    for(var i=0;i<g.length;++i)
    {
        cad+="<optgroup label='";
        var n=$t(g[i],'gn');
        var name='';
        var name_en='';
        for(var y=0;y<n.length;++y)
        {
            if($a(n[y],'l')=='<?=strtolower($_SESSION['LAN'])?>')
                name=n[y].firstChild.data;
            if($a(n[y],'l')=='en')
                name_en=n[y].firstChild.data;
        }
        if(name=='') name=name_en;
        cad+=name+"'>";
              
        var e=$t(g[i],'w');
        for(var x=0;x<e.length;++x)
        {
            cad+="<option value='"+$t(e[x],'f')[0].firstChild.data+"'>";
            var n=$t(e[x],'n');
            var name='';
            var name_en='';
            if(n.length>0)
            {
                for(var y=0;y<n.length;++y)
                {
                    if($a(n[y],'l')=='<?=strtolower($_SESSION['LAN'])?>')
                        name=n[y].firstChild.data;
                    if($a(n[y],'l')=='en')
                        name_en=n[y].firstChild.data;
                }
                if(name=='') name=name_en;
            }
            else
                name=$t(e[x],'f')[0].firstChild.data;
            cad+=name;
            cad+="</option>";
        }
        cad+="</optgroup>";
    }
    cad+="</select>";
    $('M2PC_<?=$appID?>_contSel').innerHTML=cad;
    
    var t=M2PC_DESKTOP_DSKTP1.image.split('/');
    if(M2PC_DESKTOP_DSKTP1.image_repeat==0)
            $('M2PC_<?=$appID?>_TAB1_wallTiled').checked=true;
    else
            $('M2PC_<?=$appID?>_TAB1_wallCenter').checked=true;
    
    $('M2PC_<?=$appID?>_scColor').value=M2PC_DESKTOP_DSKTP1.scColor;
    $('M2PC_<?=$appID?>_scColorDiv').style.backgroundColor=M2PC_DESKTOP_DSKTP1.scColor;
    
    $('M2PC_<?=$appID?>_wallCombo').value=t[t.length-1];
    M2PC_<?=$appID?>_renderPreview();
}

M2PC_<?=$appID?>_getFileInfo=function(file)
{
    var data=new Array();
    var g=$t(M2PC_<?=$appID?>_wallList,'g');
    for(var x=0;x<g.length;++x)
    {
        var e=$t(g[x],'w');
        for(var y=0;y<e.length;++y)
            if($t(e[y],'f')[0].firstChild.data==file)
            {
                data[0]=$a(g[x],'id');
                data[1]=$a(e[y],'c');
                data[2]=$a(e[y],'m');
                data[3]='';
                var c=$t(e[y],'c');
                if(c.length>0)
                    data[3]=c[0].firstChild.data;
                return data;
            }
    }
    return data;
}

M2PC_<?=$appID?>_testWall=function(val)
{
        var fileData=M2PC_<?=$appID?>_getFileInfo(val);
        if(fileData[3]!='')
            $('M2PC_<?=$appID?>_wallCredit').innerHTML="<?=$L5?> "+fileData[3];
        else $('M2PC_<?=$appID?>_wallCredit').innerHTML='';
        
	$('M2PC_<?=$appID?>_scColor').value=fileData[1];
        $('M2PC_<?=$appID?>_scColorDiv').style.backgroundColor=fileData[1];
        if(fileData[2]=='c')
            $('M2PC_<?=$appID?>_TAB1_wallCenter').checked=true;
        else
            $('M2PC_<?=$appID?>_TAB1_wallTiled').checked=true;
        
	M2PC_<?=$appID?>_renderPreview();
}

M2PC_<?=$appID?>_renderPreview=function()
{
    var val=$('M2PC_<?=$appID?>_wallCombo').value;
    var cad='';

    if($('M2PC_<?=$appID?>_TAB1_wallTiled').checked==true)
    {
        for (var x=0;x<12;x++)
	    cad+="<img src='<?=$static?>wallpapers/"+val+"' style='width:28px;height:28px'/>";
    }
    else
        cad="<img src='<?=$static?>wallpapers/"+val+"' style='width:115px;height:86px;position:absolute;left:0px;top:0px;'/>";
    
    $('M2PC_<?=$appID?>_contPre').innerHTML=cad;
}

M2PC_<?=$appID?>_aplicaWall=function()
{
        var fileData=M2PC_<?=$appID?>_getFileInfo($('M2PC_<?=$appID?>_wallCombo').value);       
	M2PC_DESKTOP_DSKTP1.image='<?=$static?>wallpapers/'+$('M2PC_<?=$appID?>_wallCombo').value;
        M2PC_DESKTOP_DSKTP1.scColor=$('M2PC_<?=$appID?>_scColor').value;
        M2PC_DESKTOP_DSKTP1.reloadShortcuts();
	$('K_int_M2PC_DESKTOP_DSKTP1').style.backgroundImage="url(<?=$static?>wallpapers/"+$('M2PC_<?=$appID?>_wallCombo').value+")";
	var cent=1;
	if($('M2PC_<?=$appID?>_TAB1_wallTiled').checked==true)
	{
		cent=0;
                M2PC_DESKTOP_DSKTP1.image_repeat=1;
                $('K_int_M2PC_DESKTOP_DSKTP1').style.backgroundRepeat='';
                
	}
	else
	{
            M2PC_DESKTOP_DSKTP1.image_repeat=0;
            $('K_int_M2PC_DESKTOP_DSKTP1').style.backgroundRepeat='no-repeat';
            $('K_int_M2PC_DESKTOP_DSKTP1').style.backgroundPosition='center center';
            $('K_int_M2PC_DESKTOP_DSKTP1').style.backgroundSize='100% 100%';
	}
	var t=$('M2PC_<?=$appID?>_contButWall');
	t.old=t.innerHTML;
	t.innerHTML="<span class='loading'>&nbsp;&nbsp;Aplicando...</span>";
        var scColor=$('M2PC_<?=$appID?>_scColor').value.substr(1);
	K_loadAJAX('<?=$dynamic?>apps/config/changeWallpaper.php?groupId='+fileData[0]+'&wallpaper='+escape($('M2PC_<?=$appID?>_wallCombo').value)+'&center='+cent+'&scColor='+scColor,'M2PC_<?=$appID?>_resAplicaWall');
}

M2PC_<?=$appID?>_resAplicaWall=function(xmlDoc)
{
	var t=$('M2PC_<?=$appID?>_contButWall');
	t.innerHTML=t.old;
}

M2PC_<?=$appID?>_wMain_destroy=function()
{
        M2PC_<?=$appID?>_exiting=true;
        M2PC_<?=$appID?>_wAbout.destroy();
        M2PC_<?=$appID?>_exiting=null;
        M2PC_<?=$appID?>_HC1=null;
        M2PC_<?=$appID?>_HCab=null;
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

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="420" h="379" mov="1" res="1" boundaries="auto,auto,auto,auto" minSize="400,210" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/confGraphics_16.png" clsBut="1" maxBut="0" minBut="1" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">

<tabcontrol id="M2PC_<?=$appID?>_TABC1" x="10" y="10" w="398" h="335">
	<tab id="M2PC_<?=$appID?>_TAB1" img="<?=$static?>icons/confWallpaper_16.png" caption="<?=$L3?>">

<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div style='padding:10px'><?=$L4?></div>
<div style='position:absolute;right:20px;top:40px;width:120px;height:128px;background:#000' id='M2PC_<?=$appID?>_contPre'>
</div>
<img src='<?=$static?>icons/pantalla.png' style='position:absolute;right:20px;top:33px' />

<div style='position:absolute;right:20px;top:183px'>
<table cellpadding='0' cellspacing='0'>
<tr>
<td><input type='radio' style='border:0px' name='M2PC_<?=$appID?>_TAB1_radio1' id='M2PC_<?=$appID?>_TAB1_wallCenter' onclick="M2PC_<?=$appID?>_renderPreview()" value='1'/></td><td><?=$L8?>&nbsp;&nbsp;</td>
<td><input type='radio' style='border:0px' name='M2PC_<?=$appID?>_TAB1_radio1' id='M2PC_<?=$appID?>_TAB1_wallTiled' onclick="M2PC_<?=$appID?>_renderPreview()" value='1'/></td><td><?=$L9?></td>
</tr>
</table>

</div>

<div style='position:absolute;left:11px;bottom:5px;' id='M2PC_<?=$appID?>_wallCredit'></div>

<div style='position:absolute;right:34px;top:212px;text-align:center'><?=$L7?> &nbsp;<img onclick="K_selectColor('M2PC_<?=$appID?>_wMain','M2PC_<?=$appID?>_scColor')" style='cursor:pointer;' src='<?=$static?>icons/colorSelect_16.png' align='absmiddle'/><br/>
<input type='hidden' id='M2PC_<?=$appID?>_scColor' value='#FFF'/>
<div id='M2PC_<?=$appID?>_scColorDiv' style='margin-top:5px;width:100px;height:30px;border:1px solid #000;background:#FFF;cursor:pointer;'  onclick="K_selectColor('M2PC_<?=$appID?>_wMain','M2PC_<?=$appID?>_scColor')"></div> <br/><span id='M2PC_<?=$appID?>_contButWall'><input style='width:100px' type='button' class='button' value='    <?=$L6?>    ' onclick="M2PC_<?=$appID?>_aplicaWall()"/></span></div>

<div style='position:absolute;left:10px;top:34px;width:215px;height:206px' id='M2PC_<?=$appID?>_contSel'><span class='loading'> <?=$L2?></span></div>

]]></html_code>

<!--<multipleselect id="M2PC_<?=$appID?>_MS1" x="10" y="34" w="215" h="206" autoload="1" multiple="0" tag="w">
	<data src="<?=$static?>wallpapers/lista.xml"/>
</multipleselect>-->
	</tab>
</tabcontrol>

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
