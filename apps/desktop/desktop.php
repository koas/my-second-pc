<?
session_start();
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

$p=explode('#@#',$_SESSION['WALLPAPER']);
$imgWall=$p[1];
$wallRep=$p[0];

$zoneUser=new DateTimeZone($_SESSION['TIMEZONE']);
$dateUser=new DateTime("now",$zoneUser);
$p=explode('T',$dateUser->format(DATE_ATOM));
$userDate=$p[0].' '.substr($p[1],0,8);




echo'<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
?>
<app name="M2PC Desktop" id="M2PC_DESKTOP">
<stat>OK</stat>

<code><![CDATA[
M2PC_swfu=null;
M2PC_fileUploadAppStarted=false;
M2PC_DESKTOP_DSKTP1_path='/Desktop/';
M2PC_DESKTOP_main=function(appId, params)
{
	K_appCanvas='M2PC_DESKTOP_DSKTP1';
	M2PC_DESKTOP_DSKTP1.render();
	M2PC_DESKTOP_DSKTP1.reloadShortcuts();
	<?
	/*if($_SESSION['M2PC_USER']==1 && $_SESSION['RECOVER_PASSWORD']=='')
		echo "smLogin_click();";
	if($_SESSION['RECOVER_PASSWORD']!='')
		echo "recoverPassword();";*/
	?>
}

recoverPassword=function()
{
	K_loadAppAJAX('<?=$dynamic?>apps/login/recover.php','1','<?=$static?>icons/help_32.png')
}

M2PC_DSKTP1_CM_cmUpload_click=function(ref)
{
	var dest='';
	if(ref.charAt(0)=='M' && ref.charAt(1)=='2')
		dest='/Desktop/';
	else
	{
		dest=window['M2PC_'+ref+'_DSKTP_path'];
		K_CM_dsktp='';
	}

	K_loadAppAJAX('<?=$dynamic?>apps/upload/upload.php','1#'+dest,'<?=$static?>icons/upload_32.png');
}
M2PC_DSKTP1_CM_cmPaste_click=function(ref)
{
	var dest='';
	if(ref.charAt(0)=='M' && ref.charAt(1)=='2')
		dest='/Desktop/';
	else
	{
		dest=window['M2PC_'+ref+'_DSKTP_path'];
		K_CM_dsktp='';
	}
	var files='';
	for(var x=0;x<K_clipboard.length;++x)
	{
		if(K_clipboard[x]==dest)
			continue;
		files+=K_clipboard[x]+'_@_';
	}
	files=files.substring(0,files.length-3);
	var cad="<?=$dynamic?>apps/desktop/paste.php?op="+K_clipboardOp+"&appID="+ref+"&path="+dest+"&x="+K_lastRightClickX+"&y="+K_lastRightClickY+"&files="+files;
	K_loadAJAX(cad,'M2PC_DSKTP1_CM_resNewFile');
	K_clipboard=new Array();
}

M2PC_DSKTP1_CM_cmCreateFolder_click=function(ref)
{
	var dest='';
	if(ref.charAt(0)=='M' && ref.charAt(1)=='2')
		dest='/Desktop/';
	else
	{
		dest=window['M2PC_'+ref+'_DSKTP_path'];
		K_CM_dsktp=ref;
	}
	K_loadAppAJAX('<?=$dynamic?>apps/newFolder/newFolder.php','1#0#'+dest+'#1#'+K_CM_dsktp);
}
M2PC_DSKTP1_CM_cmCreateTextFile_click=function(ref)
{
	var dest='';
	if(ref.charAt(0)=='M' && ref.charAt(1)=='2')
		dest='/Desktop/';
	else
	{
		dest=window['M2PC_'+ref+'_DSKTP_path'];
		K_CM_dsktp='';
	}
	K_loadAJAX("<?=$dynamic?>apps/desktop/newFile.php?type=txt&appID="+ref+"&path="+dest+"&x="+K_lastRightClickX+"&y="+K_lastRightClickY,'M2PC_DSKTP1_CM_resNewFile');
}

M2PC_DSKTP1_CM_resNewFile=function(xmlDoc)
{
	var src=$t(xmlDoc,'src');
	
	var sc=$t(xmlDoc,'nsc');
	if(K_CM_dsktp!=K_appCanvas)
	{
		var p=K_CM_dsktp.split('_');
		window['M2PC_'+$a(sc[0],'appID')+'_loadFiles'](window['M2PC_'+$a(sc[0],'appID')+'_DSKTP_path']);
		var scD=$t(xmlDoc,'nscD');
		if(scD.length==1)
			window[K_appCanvas].reloadShortcuts();
	}
	else window[K_appCanvas].reloadShortcuts();
	for (var i=0;i<window[K_appCanvas].windowList.length;++i)
	{
			var appID=window[window[K_appCanvas].windowList[i]].id.split('_');
			appID=appID[1];
			if(window['M2PC_'+appID+'_DSKTP_path']==M2PC_DESKTOP_DSKTP1_path || (src.length==1 && window['M2PC_'+appID+'_DSKTP_path']==src[0].firstChild.data))
					window['M2PC_'+appID+'_loadFiles'](window['M2PC_'+appID+'_DSKTP_path']);
	}
	setTimeout("M2PC_TSKBR1.redrawWindows()",100);
}
cmFolderRename_click=function(ref)
{
	var p=ref.split('_');
	var dest='';
	var id=0;
	if(ref.charAt(0)=='s' && ref.charAt(1)=='c')
	{
		dest='/Desktop/';
		id=p[1];
	}
	else
	{
		dest=window['M2PC_'+ref+'_DSKTP_path'];
		K_CM_dsktp=ref;
	}
	K_loadAppAJAX('<?=$dynamic?>apps/newFolder/newFolder.php','1#1#'+dest+'#'+id+'#'+K_CM_dsktp);    
}
cmFolderDelete_click=function(ref)
{
	var dest='';
	if(ref.charAt(0)=='s' && ref.charAt(1)=='c')
		dest='/Desktop/';
	else
	{
		dest=window['M2PC_'+ref+'_DSKTP_path'];
		K_CM_dsktp=ref;
	}
	var p=ref.split('_');
	K_loadAppAJAX('<?=$dynamic?>apps/deleteFolder/deleteFolder.php','1#1#'+dest+'#'+p[1]+'#'+K_CM_dsktp);
}
cmFolderTrash_click=function(ref)
{
	var dest='';
	if(ref.charAt(0)=='s' && ref.charAt(1)=='c')
		dest='/Desktop/';
	else
	{
		dest=window['M2PC_'+ref+'_DSKTP_path'];
		K_CM_dsktp=ref;
	}
	var p=ref.split('_');
	K_loadAppAJAX('<?=$dynamic?>apps/sendTrash/sendTrash.php','1#1#'+dest+'#'+p[1]+'#'+K_CM_dsktp);
}
cmFolderCompress_click=function(ref)
{
	var dest='';
	if(ref.charAt(0)=='s' && ref.charAt(1)=='c')
		dest='/Desktop/';
	else
	{
		dest=window['M2PC_'+ref+'_DSKTP_path'];
		K_CM_dsktp=ref;
	}
	K_loadAppAJAX('<?=$dynamic?>apps/compressor/compressor.php','1#'+dest+'#'+K_CM_dsktp);
}
K_WCM_MAXI_click=function(caller)
{
	window[M2PC_TSKBR1.buttons[M2PC_TSKBR1.active]].clickMaximize();
}
K_WCM_MINI_click=function(caller)
{
	window[M2PC_TSKBR1.buttons[M2PC_TSKBR1.active]].clickMinimize();
}
K_WCM_REST_click=function(caller)
{
	window[M2PC_TSKBR1.buttons[M2PC_TSKBR1.active]].restore();
}
K_WCM_MOVE_click=function(caller)
{
	K_difX=Math.floor(window[caller].width/2);
	K_idDrag=caller;
}

K_WCM_CLOSE_click=function(caller)
{
	window[M2PC_TSKBR1.buttons[M2PC_TSKBR1.active]].destroy();
}
K_WCM_ABOUT_click=function(caller)
{
	window[caller+'_about']();
}
smFileManager_click=function()
{
	K_loadAppAJAX('<?=$dynamic?>apps/fileManager2/fileManager2.php','1#/','<?=$static?>icons/filemanager_32.png');
}
smEdit_click=function()
{
	K_loadAppAJAX('<?=$dynamic?>apps/edit/edit.php','1#','<?=$static?>icons/editor_32.png');
}
smMediaPlayer_click=function()
{
	K_loadAppAJAX('<?=$dynamic?>apps/mediaPlayer/mediaPlayer.php','1#','<?=$static?>icons/mplayer_32.png');
}
M2PC_DSKTP1_CM_cmConfGraphics_click=function()
{
	K_loadAppAJAX('<?=$dynamic?>apps/config/config.php','1#','<?=$static?>icons/confGraphics_32.png');
}
<?
if($_SESSION['M2PC_USER']==1)
{?>
smRegister_click=function()
{
	K_loadAppAJAX('<?=$dynamic?>apps/register/register.php','1#','<?=$static?>icons/register_32.png');
}
smLogin_click=function()
{
	K_loadAppAJAX('<?=$dynamic?>apps/login/login.php','1#','<?=$static?>icons/login_32.png');
}
<?}?>
smExit_click=function()
{
	if(confirm('<?=$L37?>'))
	window.location.href='<?=$dynamic?>?logout=1';
}
smTimezoneConfig_click=function()
{
	K_loadAppAJAX('<?=$dynamic?>apps/timeConfig/timeConfig.php','1#','<?=$static?>icons/timezone_32.png');
}
smSudoku_click=function()
{
	K_loadAppAJAX('<?=$dynamic?>apps/sudoku/sudoku.php','1#','<?=$static?>icons/sudoku_32.png');
}
smTetris_click=function()
{
	K_loadAppAJAX('<?=$dynamic?>apps/tetris/tetris.php','1#','<?=$static?>icons/tetris_16.png');
}
smConfGraphics_click=function()
{
	M2PC_DSKTP1_CM_cmConfGraphics_click();
}
cmFolderCopy_click=function(caller)
{
	K_clipboardOp='copy';
	if(caller.charAt(0)=='s' && caller.charAt(1)=='c')
		K_clipboard=window['M2PC_DESKTOP_DSKTP1'].getSelectedShortcutsPath();
	else K_clipboard=window['M2PC_'+caller+'_getSelectedFiles']();
}
cmFolderCut_click=function(caller)
{
	K_clipboardOp='cut';
	if(caller.charAt(0)=='s' && caller.charAt(1)=='c')
		K_clipboard=window['M2PC_DESKTOP_DSKTP1'].getSelectedShortcutsPath();
	else K_clipboard=window['M2PC_'+caller+'_getSelectedFiles']();
}
cmFolderDownload_click=function(caller)
{
	var cad='';
	if(caller.charAt(0)=='s' && caller.charAt(1)=='c')
		cad=window['M2PC_DESKTOP_DSKTP1'].getSelectedShortcutsPath();
	else cad=window['M2PC_'+caller+'_getSelectedFiles']();
	if(cad.length>1)
	{
		alert('<?=$L23?>\r\n\r\n<?=$L24?>');
		return;
	}
	if(cad[0].charAt(cad[0].length-1)=='/')
	{
		alert('<?=$L25?>');
		return;
	}
	$('K_Desktop_ifr').src='<?=$dynamic?>apps/desktop/download.php?cad='+cad.toString();
}
K_selectColor=function(window,id)
{
	var color=$(id).value;
	K_loadAppAJAX('<?=$dynamic?>apps/colorSelector/colorSelector.php',window+'@'+id+'@'+color);
}
]]>
</code>

<desktop id="M2PC_DESKTOP_DSKTP1" scColor='<?=$_SESSION['COLORSC']?>' context_menu="M2PC_DSKTP1_CM" color="black" image="<?=$imgWall?>" image_repeat="<?=$wallRep?>" main='1'>

<context_menu id="M2PC_DSKTP1_CM">
	<element id="M2PC_DSKTP1_CM_cmCreate" icon="<?=$static?>icons/create_16.png" caption="<?=$L1?>">
		<element id="M2PC_DSKTP1_CM_cmCreateFolder" icon="<?=$static?>icons/folder_16.png" caption="<?=$L2?>"/>
		<element id="M2PC_DSKTP1_CM_cmCreateTextFile" icon="<?=$static?>icons/files_16/txt.png" caption="<?=$L8?>"/>
	</element>
	<element id="M2PC_DSKTP1_CM_cmUpload" icon="<?=$static?>icons/upload_16.png" caption="<?=$L35?>"/>
	<element id="separatorPaste" icon="" caption=""/>
	<element id="M2PC_DSKTP1_CM_cmPaste" icon="<?=$static?>icons/paste_16.png" caption="<?=$L21?>"/>
	<element id="separatorGraph" icon="" caption=""/>
	<element id="M2PC_DSKTP1_CM_cmConfGraphics" icon="<?=$static?>icons/confGraphics_16.png" caption="<?=$L29?>"/>
	<element id="separatorLogout" icon="" caption=""/>
	<element id="M2PC_DSKTP1_CM_cmExit" icon="<?=$static?>icons/exit_16.png" caption="<?=$L31?>"/>
</context_menu>
<context_menu id="M2PC_DESKTP1_SCCM">
		<element id="cmFolderCut" icon="<?=$static?>icons/cut_16.png" caption="<?=$L19?>"/>
				<element id="cmFolderCopy" icon="<?=$static?>icons/copy_16.png" caption="<?=$L20?>"/>
				<element id="separator"/>
				<element id="cmFolderRename" icon="<?=$static?>icons/edit_16.png" caption="<?=$L5?>"/>
		<element id="separator"/>
				<element id="cmFolderCompress" icon="<?=$static?>icons/compress_16.png" caption="<?=$L17?>"/>
				<element id="cmFolderDownload" icon="<?=$static?>icons/down_16.png" caption="<?=$L22?>"/>
				<element id="separator"/>
		<element id="cmFolderTrash" icon="<?=$static?>icons/trashEmpty_16.png" caption="<?=$L4?>"/>
		<element id="cmFolderDelete" icon="<?=$static?>icons/delete_16.png" caption="<?=$L3?>"/>
</context_menu>

<taskbar id="M2PC_TSKBR1" context_menu="K_WCM" date="<?=$userDate?>">
<?if(0){?><tray>
	<icon id='M2PC_TSKBR1_tray_upload' img='<?=$static?>icons/upload_16.png' caption='<?=$L10?>' action="trayUploadClick()"/>
</tray><?}?>
<context_menu id="K_WCM">
		<element id="K_WCM_REST" icon="<?=$static?>KWS/img/icoRest.png" caption="<?=$L14?>"/>
		<element id="K_WCM_MINI" icon="<?=$static?>KWS/img/icoMin.png" caption="<?=$L15?>"/>
		<element id="K_WCM_MAXI" icon="<?=$static?>KWS/img/icoMax.png" caption="<?=$L16?>"/>
				<element id="K_WCM_MOVE" icon="<?=$static?>KWS/img/icoMove.png" caption="<?=$L13?>"/>
		<element id="separator"/>
				<element id="K_WCM_ABOUT" icon="<?=$static?>icons/about_16.png" caption="<?=$L11?>"/>
				<element id="separator"/>
		<element id="K_WCM_CLOSE" icon="<?=$static?>KWS/img/icoClose.png" caption="<?=$L12?>"/>
</context_menu>


<start_menu caption="<?=$L34?>" hint="<?=$L18?>">
	<?=$startMenu?>
	<element id='smGames' icon="<?=$static?>icons/games_24.png" caption="<?=$L40?>">
		<element id='smSudoku' icon="<?=$static?>icons/sudoku_16.png" caption="<?=$L41?>"/>
		<element id='smTetris' icon="<?=$static?>icons/tetris_16.png" caption="<?=$L42?>"/>
	</element>
	<element id='smPrograms' icon="<?=$static?>icons/programs_24.png" caption="<?=$L7?>">
		<element id='smEdit' icon="<?=$static?>icons/editor_16.png" caption="<?=$L9?>"/>
		<element id='smFileManager' icon="<?=$static?>icons/fileManager_16.png" caption="<?=$L6?>"/>
	</element>
	<element id='smConfig' icon="<?=$static?>icons/config_24.png" caption="<?=$L38?>">
		<element id='smConfGraphics' icon="<?=$static?>icons/confGraphics_16.png" caption="<?=$L29?>"/>
		<element id='smTimezoneConfig' icon="<?=$static?>icons/timezone_16.png" caption="<?=$L39?>"/>
	</element>
	<element id='separator'/>
<?
if($_SESSION['M2PC_USER']==1)
{?>
<!--<element id="smRegister" icon="<?=$static?>icons/register_24.png" caption="<?=$L33?>"/>
<element id="smLogin" icon="<?=$static?>icons/login_24.png" caption="<?=$L36?>"/>
<element id="separator"/>-->
<?}?>
	<!--
	<element id="smConfig" icon="<?=$static?>icons/config_24.png" caption="<?=$L27?>">
		<element id='smConfAccessData' icon="<?=$static?>icons/confAccessData_16.png" caption="<?=$L28?>"/>
		<element id='smConfGraphics' icon="<?=$static?>icons/confGraphics_16.png" caption="<?=$L29?>"/>
	</element>
	<element id='separator'/>-->
	<!--<element id="smHelp" icon="<?=$static?>icons/help_24.png" caption="<?=$L30?>"/>
	<element id="separator"/>-->
	<element id="smExit" icon="<?=$static?>icons/exit_24.png" caption="<?=$L32?>"/>
</start_menu>
</taskbar>

<?=$icons?>

</desktop>
</app>
