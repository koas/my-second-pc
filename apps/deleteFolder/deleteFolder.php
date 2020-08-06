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

if ($_SESSION['M2PC_USER']==1)
die( '<?xml version="1.0" encoding="iso-8859-1" standalone="yes"?>
<res><stat>NOTINDEMO</stat></res>');

echo'<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
?>
<app name="<?=$L1?>" id="M2PC_<?=$appID?>" cacheable='1'>
<stat>OK</stat>
<code><![CDATA[
M2PC_<?=$appID?>_DesktopId='';
M2PC_<?=$appID?>_folderName='';
M2PC_<?=$appID?>_folderId='';
M2PC_<?=$appID?>_path='';
M2PC_<?=$appID?>_fileList='';
M2PC_<?=$appID?>_main=function(appId, params)
{
	var p=params.split('#');
	M2PC_<?=$appID?>_path=p[2];
	M2PC_<?=$appID?>_folderId=p[3];
        M2PC_<?=$appID?>_DesktopId=p[4];
	M2PC_<?=$appID?>_wMain.render();
	if(K_sound)
		soundManager.play('mBox');
        if(M2PC_<?=$appID?>_DesktopId.charAt(0)=='M' && M2PC_<?=$appID?>_DesktopId.charAt(1)=='2')
            M2PC_<?=$appID?>_fileList=window[M2PC_<?=$appID?>_DesktopId].getSelectedShortcutsPath();
        else M2PC_<?=$appID?>_fileList=window['M2PC_'+M2PC_<?=$appID?>_DesktopId+'_getSelectedFiles']();
	var cad='';
        if(M2PC_<?=$appID?>_fileList.length==1)
        {
            cad+="<br/><br/><i>";
            var p=M2PC_<?=$appID?>_fileList[0].split('/');
            if(M2PC_<?=$appID?>_fileList[0].charAt(M2PC_<?=$appID?>_fileList[0].length-1)=='/')
                cad+=p[p.length-2];
            else cad+=p[p.length-1];
            cad+="</i>";
        }
        else cad+=M2PC_<?=$appID?>_fileList.length+" <?=$L7?>";
	$('M2PC_<?=$appID?>_name').innerHTML=cad;
}

M2PC_<?=$appID?>_wMain_destroy=function()
{
	M2PC_<?=$appID?>_DesktopId=null;
	M2PC_<?=$appID?>_folderName=null;
	M2PC_<?=$appID?>_folderId=null;
	M2PC_<?=$appID?>_path=null;
        M2PC_<?=$appID?>_HC1=null;
        M2PC_<?=$appID?>_fileList=null;
	K_unloadApp('M2PC_<?=$appID?>');
        return true;
}

M2PC_<?=$appID?>_moveTrash=function()
{
	$('M2PC_<?=$appID?>_cont').innerHTML="<span class='loading2' style='color:#000'> &nbsp;&nbsp;<?=$L6?>...</span>";
	
	var cad=K_dynP+'apps/desktop/unlink.php?dummy=1';
	var c=0;
	for(var x=0;x<M2PC_<?=$appID?>_fileList.length;++x)
	{
		if(M2PC_<?=$appID?>_fileList[x]=='/Desktop/' || M2PC_<?=$appID?>_fileList[x]=='/Trash/' || M2PC_<?=$appID?>_fileList[x]=='/Uploaded files/') continue;
		cad+="&name"+c+"="+M2PC_<?=$appID?>_fileList[x];
		++c;
	}
	K_loadAJAX(cad,'M2PC_<?=$appID?>_resMoveTrash');
}

M2PC_<?=$appID?>_resMoveTrash=function(xmlDoc)
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
        if(window['M2PC_'+appID+'_DSKTP_path']==M2PC_<?=$appID?>_path)
                window['M2PC_'+appID+'_loadFiles'](window['M2PC_'+appID+'_DSKTP_path']);
    } 
        
    M2PC_<?=$appID?>_wMain.destroy();
    if(K_sound)
        soundManager.play('trash');
}
]]></code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="400" h="175" mov="0" res="0" boundaries="auto,auto,auto,auto" minSize="180,auto" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/delete_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">

<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table width='100%' height='100%' cellpadding='0' cellspacing='0' class='mbox'><tr><td><img style='margin-left:20px' src='<?=$static?>icons/delete_64.png'/></td><td align='center'><div style='padding:10px' id='M2PC_<?=$appID?>_cont'><?=$L2?><span id='M2PC_<?=$appID?>_name'></span><br /><br /><?=$L3?><br /><br /><input type='button' class='button' style='width:75px' value='<?=$L4?>' onclick="M2PC_<?=$appID?>_moveTrash()"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' class='button' style='width:75px' value='<?=$L5?>' onclick="M2PC_<?=$appID?>_wMain.destroy()"/></div></td></tr></table>

]]></html_code>

</window>

</app>
