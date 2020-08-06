<?
session_start();
$appID='@!@';
include('../../php/conf.php');
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

/*if ($_SESSION['M2PC_USER']==1)
die( '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<res><stat>NOTINDEMO</stat></res>');*/


$path=str_replace('..','.',$_REQUEST['path']);

echo'<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
?>
<app name="<?=$L1?>" id="M2PC_<?=$appID?>" cacheable='1'>
<stat>OK</stat>

<code><![CDATA[
M2PC_<?=$appID?>_path='';
M2PC_<?=$appID?>_fileList=null;
M2PC_<?=$appID?>_exiting=false;
M2PC_<?=$appID?>_DesktopId='';
M2PC_<?=$appID?>_main=function(appId, params)
{
		K_stopBounce();
		M2PC_<?=$appID?>_wAbout.render();
		M2PC_<?=$appID?>_wAbout.hide();
		M2PC_<?=$appID?>_wMain.render();
		M2PC_<?=$appID?>_wMain.addToTaskbar('M2PC_TSKBR1');
	var p=params.split('#');
		M2PC_<?=$appID?>_path=p[1];
		M2PC_<?=$appID?>_DesktopId=p[2];
		if(isNaN(M2PC_<?=$appID?>_DesktopId))
			M2PC_<?=$appID?>_fileList=window[M2PC_<?=$appID?>_DesktopId].getSelectedShortcutsPath();
		else M2PC_<?=$appID?>_fileList=window['M2PC_'+M2PC_<?=$appID?>_DesktopId+'_getSelectedFiles']();
		if(M2PC_<?=$appID?>_fileList.length>1)
		{
			var k=p[1].split('/');
			$('M2PC_<?=$appID?>_archiveName').value=k[k.length-2];
			$('M2PC_<?=$appID?>_single').innerHTML='';
		}
		else
		{
			var k=M2PC_<?=$appID?>_fileList[0].split('/');
			if(M2PC_<?=$appID?>_fileList[0].charAt(M2PC_<?=$appID?>_fileList[0].length-1)=='/')
			{
				$('M2PC_<?=$appID?>_archiveName').value=k[k.length-2];
				$('M2PC_<?=$appID?>_single').innerHTML='';
			}
			else
			{
				$('M2PC_<?=$appID?>_archiveName').value=k[k.length-1];
				$('M2PC_<?=$appID?>_multiple').innerHTML='';
			}
		}
		if(K_gec)
		{
			$('M2PC_<?=$appID?>_archiveName').selectionStart=0;
			$('M2PC_<?=$appID?>_archiveName').selectionEnd=$('M2PC_<?=$appID?>_archiveName').value.length;
		}
		if(K_ie)
		{
			var tr=$('M2PC_<?=$appID?>_archiveName').createTextRange();
			tr.select();
		}
		$('M2PC_<?=$appID?>_archiveName').focus();
		$('M2PC_<?=$appID?>_archiveName').onkeyup=M2PC_<?=$appID?>_checkRet;
}

M2PC_<?=$appID?>_checkRet=function(e)
{
	var kc=0;
	if (K_ie || K_op)
			kc=event.keyCode;
	if(K_gec)
			kc=e.keyCode;
	if(kc==13)
		M2PC_<?=$appID?>_compress();
}
M2PC_<?=$appID?>_compress=function()
{
	<?
	if ($_SESSION['M2PC_USER']==1)
		echo 'alert(K_L_notInDemo);return;';
	?>
	if($('M2PC_<?=$appID?>_archiveName').value=='')
	{
		$('M2PC_<?=$appID?>_archiveName').focus();
		return;
	}
	$('M2PC_<?=$appID?>_but1').style.display='none';
	var t=$('M2PC_<?=$appID?>_contBut1');
	t.old=t.innerHTML;
	t.innerHTML="<span class='loading'><?=$L6?></span>";
	var cad="?type="+M2PC_<?=$appID?>_archiveType.value+'&name='+M2PC_<?=$appID?>_archiveName.value+'&path='+M2PC_<?=$appID?>_path;
	for(var x=0;x<M2PC_<?=$appID?>_fileList.length;++x)
		cad+="&f"+x+"="+M2PC_<?=$appID?>_fileList[x];
	cad+="&c="+x;
	K_loadAJAX(K_dynP+'apps/compressor/compress.php'+cad,'M2PC_<?=$appID?>_resCompress');
}
M2PC_<?=$appID?>_resCompress=function(xmlDoc)
{
	if(isNaN(M2PC_<?=$appID?>_DesktopId))
	{
		window[K_appCanvas].reloadShortcuts();
		setTimeout("M2PC_TSKBR1.redrawWindows()",100);
	}
	else
	{
		window['M2PC_'+M2PC_<?=$appID?>_DesktopId+'_loadFiles'](window['M2PC_'+M2PC_<?=$appID?>_DesktopId+'_DSKTP_path']);
		var dsktp=$t(xmlDoc,'dsktp');
		if(dsktp.length==1)
		{
			window[K_appCanvas].reloadShortcuts();
			setTimeout("M2PC_TSKBR1.redrawWindows()",100);
		}
	}
	for (var i=0;i<window[K_appCanvas].windowList.length;++i)
	{
		var appID=window[window[K_appCanvas].windowList[i]].id.split('_');
		appID=appID[1];
		if(window['M2PC_'+appID+'_DSKTP_path']=='/Trash/')
				window['M2PC_'+appID+'_loadFiles'](window['M2PC_'+appID+'_DSKTP_path']);
	} 
	M2PC_<?=$appID?>_exiting=true;
	M2PC_<?=$appID?>_wMain.destroy();
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
M2PC_<?=$appID?>_wMain_destroy=function()
{
		M2PC_<?=$appID?>_exiting=true;
		M2PC_<?=$appID?>_wAbout.destroy();
		M2PC_<?=$appID?>_path=null;
		M2PC_<?=$appID?>_fileList=null;
		M2PC_<?=$appID?>_DesktopId=null;
		M2PC_<?=$appID?>_HC1=null;
		M2PC_<?=$appID?>_HCab=null;
		M2PC_<?=$appID?>_exiting=null;
		K_unloadApp('M2PC_<?=$appID?>');
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
]]></code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="540" h="125" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="550,375" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/fileManager_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div style='padding:10px'>
<table cellpadding='8' cellspacing='0' align='center'>
<tr><td align='right'><b><?=$L2?>:</b></td><td><input style='width:200px' type='text' id='M2PC_<?=$appID?>_archiveName'/>&nbsp;<span id='M2PC_<?=$appID?>_single'><select id='M2PC_<?=$appID?>_archiveType'>
<option value='zip'>.zip</option>
<option value='rar'>.rar</option>
<option value='gz'>.gz</option>
<option value='bz2'>.bz2</option>
<option value='lzma'>.lzma</option>
<option value='lzo'>.lzo</option>
<option value='tar'>.tar</option>
<option value='tar.gz'>.tar.gz</option>
<option value='tar.bz2'>.tar.bz2</option>
<option value='tar.lzma'>.tar.lzma</option>
<option value='tar.lzo'>.tar.lzo</option>
</select></span><span id='M2PC_<?=$appID?>_multiple'><select id='M2PC_<?=$appID?>_archiveType'>
<option value='zip'>.zip</option>
<option value='rar'>.rar</option>
<option value='tar'>.tar</option>
<option value='tar.gz'>.tar.gz</option>
<option value='tar.bz2'>.tar.bz2</option>
<option value='tar.lzma'>.tar.lzma</option>
<option value='tar.lzo'>.tar.lzo</option>
</select></span></td></tr>
<tr><td align='right'><input id='M2PC_<?=$appID?>_but1' style='width:100px' type='button' class='button' value='<?=$L5?>' onclick="M2PC_<?=$appID?>_wMain.destroy()"/></td><td align='right' id='M2PC_<?=$appID?>_contBut1'><input id='M2PC_<?=$appID?>_butOk' style='width:100px' type='button' class='button' value='<?=$L4?>' onclick="M2PC_<?=$appID?>_compress()"/></td></table>
</div>
]]></html_code>
</window>

<window id="M2PC_<?=$appID?>_wAbout" x="auto" y="auto" w="400" h="152" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L114?> <?=$L1?>" icon="<?=$static?>icons/about_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HCab" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table width='100%' cellpadding='10' cellspacing='0'><tr>
	<td width='64' align='center'><img src='<?=$static?>icons/compress_64.png'/></td>
	<td><b><?=$L1?></b><br/>
	<?=$L115?> 1.0<br/>
	&copy; 2006-<?=date('Y')?> Karontek<br/>
	<div style='border-top:1px solid #000;margin-top:5px;padding-top:5px'>
	<?=$L117?>:<br/>
	<b>&bull;</b> nuoveXT (<a href='http://nuovext.pwsp.net/' target='_blank'>http://nuovext.pwsp.net/</a>)<br/>
	</div>
	</td>
</tr></table>
]]></html_code>
</window>
</app>
