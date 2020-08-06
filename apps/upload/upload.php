<?
session_start();

$now = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0');
header('Last-Modified: ' . $now);
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0');
header('Pragma: no-cache');
header('Content-Type: text/xml');


/*if ($_SESSION['M2PC_USER']==1)
die( '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
	<res><stat>NOTINDEMO</stat></res>');*/

$appID='@!@';
include('../../php/conf.php');
include('locale.php');

if ($_SESSION['M2PC_USER']<1)
die( '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<res><stat>AUTHERR</stat></res>');

$path=str_replace('..','.',$_REQUEST['path']);

echo'<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
?>
<app name="<?=$L1?>" id="M2PC_<?=$appID?>" cacheable='1'>
<stat>OK</stat>

<code><![CDATA[
M2PC_<?=$appID?>_exiting=false;
M2PC_<?=$appID?>_path='';
M2PC_<?=$appID?>_main=function(appId, params)
{
		K_stopBounce();
		M2PC_<?=$appID?>_wMain.render();
		var p=params.split('#');
		M2PC_<?=$appID?>_path=p[1];
		$('M2PC_<?=$appID?>_dest').value=p[1];
		$('M2PC_<?=$appID?>_contMsg').innerHTML='<b><?=$L17?></b> '+p[1];
		
}
M2PC_<?=$appID?>_send=function()
{
	<?
	if ($_SESSION['M2PC_USER']==1)
		echo 'alert(K_L_notInDemo);return;';
	?>
	if($('M2PC_<?=$appID?>_f1').Filedata.value=='')
		return;
	var t=$('M2PC_<?=$appID?>_contBut');
	t.old=t.innerHTML;
	t.innerHTML="<span class='loading'> <?=$L9?></span>";
	$('M2PC_<?=$appID?>_contMsg').innerHTML='';
	$('M2PC_<?=$appID?>_f1').submit();
}
M2PC_<?=$appID?>_fileSent=function(name)
{
	var t=$('M2PC_<?=$appID?>_contBut');
	t.innerHTML=t.old;
	$('M2PC_<?=$appID?>_contMsg').innerHTML='<em>'+name+"<?=$L10?>";
	M2PC_<?=$appID?>_reloadFileManagers();
}
M2PC_<?=$appID?>_reloadFileManagers=function()
{
	if(M2PC_<?=$appID?>_path=='/Desktop/')
		M2PC_DESKTOP_DSKTP1.reloadShortcuts();
	var windowReloaded=new Array();
	for (var i=0;i<window[K_appCanvas].windowList.length;++i)
	{
			var appID=window[window[K_appCanvas].windowList[i]].id.split('_');
			appID=appID[1];
			if(window['M2PC_'+appID+'_DSKTP_path']==M2PC_<?=$appID?>_path)
			{
				if(!K_inArray(appID,windowReloaded))
				{
					window['M2PC_'+appID+'_loadFiles'](window['M2PC_'+appID+'_DSKTP_path']);
					windowReloaded[windowReloaded.length]=appID;
				}
			}
	}
}
M2PC_<?=$appID?>_wMain_destroy=function()
{
	K_unloadApp('M2PC_<?=$appID?>');
	return true;
}
M2PC_<?=$appID?>_cmAbout_click=function()
{
}
M2PC_<?=$appID?>_wMain_about=function()
{

}

]]></code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="350" h="120" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L16?>" icon="<?=$static?>icons/upload_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">

<!--<tabcontrol id="M2PC_<?=$appID?>_TABC1" x="10" y="10" w="527" h="277">

<tab id="M2PC_<?=$appID?>_TAB1" img="<?=$static?>icons/upload_16.png" caption="<?=$L2?>">-->
<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div style='padding:10px;padding-top:5px;' align='center'>
<iframe name='M2PC_<?=$appID?>_ifr1' style='position:absolute;left:0px;top:0px;width:0px;height:0px' frameborder='0'></iframe>
<form id='M2PC_<?=$appID?>_f1' method='post' target='M2PC_<?=$appID?>_ifr1' action='<?=$dynamic?>apps/upload/receiver.php' enctype='multipart/form-data'>
<input type='hidden' name='MAX_FILE_SIZE' value='100000000'/>
<input type='hidden' name='appID' value='<?=$appID?>'/>
<input type='hidden' name='rand' value='<?=time()?>'/>
<input type='hidden' id='M2PC_<?=$appID?>_dest' name='dest' value=''/>


<input type='file' name='Filedata'/><br/><br/>
<div id='M2PC_<?=$appID?>_contBut'><input class='button' type='button' style='width:150px' value='<?=$L8?>' onclick='M2PC_<?=$appID?>_send()'/></div>
<br/><div id='M2PC_<?=$appID?>_contMsg'></div>
</form>
</div>
]]></html_code>
<!--</tab>

<tab id="M2PC_<?=$appID?>_TAB2" img="<?=$static?>icons/uploadAdvanced_16.png" caption="<?=$L3?>">
<html_code id="M2PC_<?=$appID?>_HC2" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div id="M2PC_<?=$appID?>_SWFUploadTarget" style='margin-left:10px;margin-top:10px;'></div>
<br class="clr" />
<div id='M2PC_swfu_queueinfo' style='margin-top:10px;margin-left:10px;float:left;'></div>
<div class="swfuploadbtn" id="cancelqueuebtn"><a href="javascript:cancelQueue();"><?=$L14?></a></div>
<br class="clr" />
<div id="SWFUploadFileListingFiles"></div>
<br class="clr" />
	 </div>

]]></html_code>
</tab>

</tabcontrol>-->
</window>

</app>
