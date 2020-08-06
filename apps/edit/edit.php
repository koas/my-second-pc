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
die( '<?xml version="1.0" encoding="iso-8859-1" standalone="yes"?>
<res><stat>AUTHERR</stat></res>');

echo'<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
$w=600;
$h=450;
?>
<app name="<?=$L1?>" id="M2PC_<?=$appID?>" cacheable="1">
<stat>OK</stat>
<code><![CDATA[
M2PC_<?=$appID?>_tmpFile='';
M2PC_<?=$appID?>_file='';
M2PC_<?=$appID?>_fileName='';
M2PC_<?=$appID?>_filePath='';
M2PC_<?=$appID?>_docChanged=false;
M2PC_<?=$appID?>_exiting=false;
M2PC_<?=$appID?>_main=function(appId, params)
{
	var p=params.split('#');
	M2PC_<?=$appID?>_wMain.render();
	M2PC_<?=$appID?>_wConfirmExit.render();
	M2PC_<?=$appID?>_wConfirmExit.hide();
	M2PC_<?=$appID?>_wAbout.render();
	M2PC_<?=$appID?>_wAbout.hide();
	M2PC_<?=$appID?>_wMain_cbResizeEnd();
	M2PC_<?=$appID?>_wMain.addToTaskbar('M2PC_TSKBR1');
	if(p[1])
		M2PC_<?=$appID?>_loadFile(p[1]);
	else
	{
		K_stopBounce();
		$('M2PC_<?=$appID?>_d').focus();
		window['M2PC_<?=$appID?>_cm1'].setItemState('M2PC_<?=$appID?>_cmSave',1);
	}
}
M2PC_<?=$appID?>_wMain_cbResizeEnd=function()
{
	var t=$('M2PC_<?=$appID?>_d');
	t.style.width=M2PC_<?=$appID?>_wMain.width-M2PC_<?=$appID?>_wMain.intOw+'px';
	t.style.height=M2PC_<?=$appID?>_wMain.height-M2PC_<?=$appID?>_wMain.intOy-41+'px';
}

M2PC_<?=$appID?>_wMain_onDrop=function(dsktp,files,dsktpRcvr)
{
	var e=files.split('@@');
	for(var x=0;x<e.length;++x)
	{
		var j=e[x].split('@');
		K_loadAppAJAX(K_dynP+'apps/edit/edit.php','1#'+j[0],K_staP+'icons/editor_32.png');
	}
}
M2PC_<?=$appID?>_parseFile=function(file)
{
	M2PC_<?=$appID?>_file=file;
	var k=M2PC_<?=$appID?>_file.split('/');
	M2PC_<?=$appID?>_fileName=k[k.length-1];
	M2PC_<?=$appID?>_filePath=M2PC_<?=$appID?>_file.substring(0,M2PC_<?=$appID?>_file.length-M2PC_<?=$appID?>_fileName.length);
	M2PC_<?=$appID?>_wMain.setTitle(M2PC_<?=$appID?>_fileName+" ("+M2PC_<?=$appID?>_filePath.substring(0,M2PC_<?=$appID?>_filePath.length-1)+") - <?=$L1?>");
}
M2PC_<?=$appID?>_loadFile=function(file)
{
	M2PC_<?=$appID?>_parseFile(file);
	K_loadAJAX(K_dynP+'apps/edit/load.php?f='+escape(M2PC_<?=$appID?>_file),'M2PC_<?=$appID?>_resLoadFile');
}
M2PC_<?=$appID?>_resLoadFile=function(xmlDoc)
{
	$('M2PC_<?=$appID?>_d').value=$t(xmlDoc,'d')[0].firstChild.data;
	$('M2PC_<?=$appID?>_d').focus();
	K_stopBounce();
	window['M2PC_<?=$appID?>_cm1'].setItemState('M2PC_<?=$appID?>_cmSave',1);
}
M2PC_<?=$appID?>_cmExit_click=function()
{
	window["M2PC_<?=$appID?>_wMain"].clickClose();
}
M2PC_<?=$appID?>_cmOpen_click=function()
{
	var path='/';
	if(M2PC_<?=$appID?>_file)
	{
		var k=M2PC_<?=$appID?>_file.split('/');
		file=k[k.length-1];
		path=M2PC_<?=$appID?>_file.substring(0,M2PC_<?=$appID?>_file.length-file.length);
	}
	K_loadAppAJAX(K_dynP+'apps/load/load.php','1#'+path+'#<?=$appID?>#M2PC_<?=$appID?>_cbOpen',K_staP+'icons/fileopen_16.png');
}
M2PC_<?=$appID?>_cbOpen=function(file)
{
	if(M2PC_<?=$appID?>_docChanged)
		K_loadAppAJAX(K_dynP+'apps/edit/edit.php','1#'+file,K_staP+'icons/editor_32.png');
	else M2PC_<?=$appID?>_loadFile(file);
}
M2PC_<?=$appID?>_cmSaveAs_click=function()
{
	var path='/';
	var file='';
	if(M2PC_<?=$appID?>_file)
	{
		var k=M2PC_<?=$appID?>_file.split('/');
		file=k[k.length-1];
		path=M2PC_<?=$appID?>_file.substring(0,M2PC_<?=$appID?>_file.length-file.length);
	}
	K_loadAppAJAX(K_dynP+'apps/saveAs/saveAs.php','1#'+path+'#'+file+'#<?=$appID?>#M2PC_<?=$appID?>_cbSaveAs##<?=$L14?>%.txt',K_staP+'icons/filesaveas_32.png');
}
M2PC_<?=$appID?>_cbSaveAs=function(file)
{
	M2PC_<?=$appID?>_parseFile(file);
	M2PC_<?=$appID?>_docChanged=true;
	M2PC_<?=$appID?>_cmSave_click();
}
M2PC_<?=$appID?>_cmSave_click=function()
{
	if(!M2PC_<?=$appID?>_docChanged &&M2PC_<?=$appID?>_file!='')
		return;
	if(M2PC_<?=$appID?>_file=='')
	{
		M2PC_<?=$appID?>_cmSaveAs_click();
		return;
	}
	<?
	if ($_SESSION['M2PC_USER']==1)
		echo 'alert(K_L_notInDemo);return;';
	?>
	
	$('M2PC_<?=$appID?>_f1').name.value=M2PC_<?=$appID?>_file;
	$('M2PC_<?=$appID?>_f1').appID.value='<?=$appID?>';
	K_startBounce(K_staP+'icons/filesave_32.png');
	$('M2PC_<?=$appID?>_f1').submit();
}

M2PC_<?=$appID?>_endSave=function()
{
	K_stopBounce();
	M2PC_<?=$appID?>_docChanged=false;
	window['M2PC_<?=$appID?>_cm1'].setItemState('M2PC_<?=$appID?>_cmSave',1);
	var k=M2PC_<?=$appID?>_file.split('/');
	M2PC_<?=$appID?>_fileName=k[k.length-1];
	M2PC_<?=$appID?>_filePath=M2PC_<?=$appID?>_file.substring(0,M2PC_<?=$appID?>_file.length-M2PC_<?=$appID?>_fileName.length);
	M2PC_<?=$appID?>_wMain.setTitle(M2PC_<?=$appID?>_fileName+" ("+M2PC_<?=$appID?>_filePath.substring(0,M2PC_<?=$appID?>_filePath.length-1)+") - <?=$L1?>");
	
	var k=M2PC_<?=$appID?>_file.split('/');
	file=k[k.length-1];
	path=M2PC_<?=$appID?>_file.substring(0,M2PC_<?=$appID?>_file.length-file.length);
	for (var i=0;i<window[K_appCanvas].windowList.length;++i)
	{
			var appID=window[window[K_appCanvas].windowList[i]].id.split('_');
			appID=appID[1];
			if(window['M2PC_'+appID+'_DSKTP_path']==path)
					window['M2PC_'+appID+'_loadFiles'](window['M2PC_'+appID+'_DSKTP_path']);
	}
	
	if(M2PC_<?=$appID?>_filePath=='/Desktop/')
	{
		K_loadAJAX(K_dynP+'apps/desktop/saveIconDesktop.php?filename='+M2PC_<?=$appID?>_fileName,'M2PC_<?=$appID?>_resIconDesktop');
	}
	else
	{
		if(M2PC_<?=$appID?>_exiting)
			M2PC_<?=$appID?>_wMain.destroy();
		else setTimeout("M2PC_<?=$appID?>_wMain.bringToFront()",1000);
	}
}
M2PC_<?=$appID?>_resIconDesktop=function(xmlDoc)
{
	var sc=$t(xmlDoc,'nscD');
	if(sc.length>0)
		for(var x=0;x<sc.length;++x)
		{
				window[$a(sc[x],'id')]=new K_Shortcut(sc[x],K_appCanvas);
				window[$a(sc[x],'id')].render();
		}
	if(M2PC_<?=$appID?>_exiting)
			M2PC_<?=$appID?>_wMain.destroy();
	else M2PC_<?=$appID?>_wMain.bringToFront();
}
M2PC_<?=$appID?>_docChange=function()
{
	window['M2PC_<?=$appID?>_cm1'].setItemState('M2PC_<?=$appID?>_cmSave',0);
	M2PC_<?=$appID?>_docChanged=true;
	if(M2PC_<?=$appID?>_fileName)
		M2PC_<?=$appID?>_wMain.setTitle('*'+M2PC_<?=$appID?>_fileName+" ("+M2PC_<?=$appID?>_filePath.substring(0,M2PC_<?=$appID?>_filePath.length-1)+") - <?=$L1?>");
}
M2PC_<?=$appID?>_wMain_destroy=function()
{
	if(M2PC_<?=$appID?>_docChanged && !M2PC_<?=$appID?>_exiting)
	{
		M2PC_<?=$appID?>_wConfirmExit.show();
		M2PC_<?=$appID?>_wConfirmExit.bringToFront();
		M2PC_<?=$appID?>_wMain.lockContent(true,'M2PC_<?=$appID?>_wConfirmExit');
		return false;
	}
	M2PC_<?=$appID?>_exiting=true;
	M2PC_<?=$appID?>_wConfirmExit.destroy();
	M2PC_<?=$appID?>_wAbout.destroy();
	M2PC_<?=$appID?>_file=null;
	M2PC_<?=$appID?>_fileName=null;
	M2PC_<?=$appID?>_filePath=null;
	M2PC_<?=$appID?>_docChanged=null;
	M2PC_<?=$appID?>_exiting=null;
	M2PC_<?=$appID?>_HC1=null;
	M2PC_<?=$appID?>_HC2=null;
	M2PC_<?=$appID?>_HC3=null;
	M2PC_<?=$appID?>_tmpFile=null;
	K_unloadApp('M2PC_<?=$appID?>');
	return true;
}

M2PC_<?=$appID?>_wConfirmExit_destroy=function()
{
	if(!M2PC_<?=$appID?>_exiting)
	{
		M2PC_<?=$appID?>_wMain.lockContent(false,'');
		M2PC_<?=$appID?>_wMain.bringToFront();
		M2PC_<?=$appID?>_wConfirmExit.hide();
		return false;
	}
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
M2PC_<?=$appID?>_exitNoSave=function()
{
	M2PC_<?=$appID?>_exiting=true;
	M2PC_<?=$appID?>_wMain.destroy();
}
M2PC_<?=$appID?>_cancelExit=function()
{
	setTimeout("window['M2PC_<?=$appID?>_wConfirmExit'].clickClose()",100);
}
M2PC_<?=$appID?>_saveFile=function()
{
	M2PC_<?=$appID?>_exiting=true;
	M2PC_<?=$appID?>_wConfirmExit.hide();
	M2PC_<?=$appID?>_cmSave_click();
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

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="<?=$w?>" h="<?=$h?>" mov="1" res="1" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/editor_16.png" clsBut="1" maxBut="1" minBut="1" statusBar="1" bgColor="#FFF" doc="" overflow="hidden">
 
<context_menu id="M2PC_<?=$appID?>_cm1">
		<element id="M2PC_<?=$appID?>_cmOpen" icon='<?=$static?>icons/fileopen_16.png' caption="<?=$L12?>"/>
		<element id="separator"/>
		<element id="M2PC_<?=$appID?>_cmSave" icon='<?=$static?>icons/filesave_16.png' caption="<?=$L3?>"/>
		<element id="M2PC_<?=$appID?>_cmSaveAs" icon='<?=$static?>icons/filesaveas_16.png' caption="<?=$L11?>"/>
		<element id="separator"/>
		<element id="M2PC_<?=$appID?>_cmExit" icon='<?=$static?>icons/exit_16.png' caption="<?=$L4?>"/>
</context_menu>
<context_menu id="M2PC_<?=$appID?>_cm2">
		<element id="M2PC_<?=$appID?>_cmAbout" icon='<?=$static?>icons/about_16.png' caption="<?=$L114?>"/>
</context_menu>
<menu>
	<element id='M2PC_<?=$appID?>_m1' caption='<?=$L2?>' context_menu="M2PC_<?=$appID?>_cm1"/>
		<element id='M2PC_<?=$appID?>_m2' caption='<?=$L13?>' context_menu="M2PC_<?=$appID?>_cm2"/>
</menu>

<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<iframe name='M2PC_<?=$appID?>_ifr' style='position:absolute;left:0px;top:0px;width:0px;height:0px' frameborder='0'></iframe>
<form action='<?=$dynamic?>apps/edit/save.php' method='post' id='M2PC_<?=$appID?>_f1' target='M2PC_<?=$appID?>_ifr'>
<input type='hidden' name='name' value=''/>
<input type='hidden' name='appID' value=''/>
<div style='position:fixed'><textarea name='data' id='M2PC_<?=$appID?>_d' onkeyup="M2PC_<?=$appID?>_docChange()" style='position:absolute;left:0px;top:0px;background:#FFF;border:0px;width:5px;height:5px'></textarea></div>
</form>
]]></html_code>
</window>

<window id="M2PC_<?=$appID?>_wConfirmExit" x="auto" y="auto" w="700" h="170" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L7?>" icon="<?=$static?>icons/warning_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC2" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table border='0' width='100%' height='100%' cellpadding='0' cellspacing='0' class='mbox'>
<tr><td rowspan='2' height='128'><img style='margin-left:20px' src='<?=$static?>icons/warning_64.png'/></td><td align='center'><div ><b><?=$L5?></b><br/><br/>
<?=$L6?>
</div></td></tr>
<tr><td><div style='margin-left:0px'><table border='0' width='100%' cellpadding='0' cellspacing='0'><tr>
<td align='center'><input style='width:150px' type='button' class='button' value='<?=$L8?>' onclick="M2PC_<?=$appID?>_exitNoSave()"/></td>
<td align='center'><input style='width:150px' type='button' class='button' value='<?=$L9?>' onclick="M2PC_<?=$appID?>_cancelExit()"/></td>
<td align='center'><input style='width:150px' type='button' class='button' value='<?=$L10?>' onclick="M2PC_<?=$appID?>_saveFile()"/></td>
</tr></table>
</div></td></tr>
</table>
]]></html_code>
</window>

<window id="M2PC_<?=$appID?>_wAbout" x="auto" y="auto" w="400" h="152" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L114?> <?=$L1?>" icon="<?=$static?>icons/about_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC3" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table width='100%' cellpadding='10' cellspacing='0'><tr>
	<td width='64' align='center'><img src='<?=$static?>icons/editor_64.png'/></td>
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
