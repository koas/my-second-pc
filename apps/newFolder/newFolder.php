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

/*if ($_SESSION['M2PC_USER']==1)
die( '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
	<res><stat>NOTINDEMO</stat></res>');*/

echo'<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
?>
<app name="<?=$L1?>" id="M2PC_<?=$appID?>" cacheable='1'>
<stat>OK</stat>

<code><![CDATA[
M2PC_<?=$appID?>_icon=-1;
M2PC_<?=$appID?>_folderName='';
M2PC_<?=$appID?>_path='';
M2PC_<?=$appID?>_folderID='';
M2PC_<?=$appID?>_desktopCaller='';
M2PC_<?=$appID?>_type='';
M2PC_<?=$appID?>_main=function(appID,params)
{
	var p=params.split('#');
	M2PC_<?=$appID?>_path=p[2];
	M2PC_<?=$appID?>_desktopCaller=p[4];
		
		M2PC_<?=$appID?>_type='folder';
		if(p[1]=='1')
		{
			if(isNaN(M2PC_<?=$appID?>_desktopCaller))
				M2PC_<?=$appID?>_type=window['sc_'+p[3]].type;
			else
			{
				var n=window['M2PC_'+M2PC_<?=$appID?>_desktopCaller+'_getSelectedFiles']()[0];
				if(n.charAt(n.length-1)=='/')
					M2PC_<?=$appID?>_type='folder';
				else M2PC_<?=$appID?>_type='file';
			}
			if(M2PC_<?=$appID?>_type=='folder')
				M2PC_<?=$appID?>_wMain.title="<?=$L7?>";
			if(M2PC_<?=$appID?>_type=='file')
				M2PC_<?=$appID?>_wMain.title="<?=$L9?>";
			M2PC_<?=$appID?>_wMain.icon=K_staP+'icons/edit_16.png';
			M2PC_<?=$appID?>_folderID=p[3];
	}
		M2PC_<?=$appID?>_wMain.render();
		$('M2PC_<?=$appID?>_name').onkeyup=M2PC_<?=$appID?>_checkRet;
		M2PC_<?=$appID?>_icon=0;
		if(p[1]=='1')
		{
				$('M2PC_<?=$appID?>_but').value='<?=$L8?>';
				if(isNaN(M2PC_<?=$appID?>_desktopCaller))
				{
					M2PC_<?=$appID?>_folderName=$('K_scn_sc_'+p[3]).innerHTML;
					$('M2PC_<?=$appID?>_name').value=$('K_scn_sc_'+p[3]).innerHTML;
				}
				else
				{
					M2PC_<?=$appID?>_folderName=window['M2PC_'+M2PC_<?=$appID?>_desktopCaller+'_getSelectedFileNames']()[0];
					$('M2PC_<?=$appID?>_name').value=window['M2PC_'+M2PC_<?=$appID?>_desktopCaller+'_getSelectedFileNames']()[0];
				}
				
				
				if(M2PC_<?=$appID?>_type=='folder')
				{
					if(isNaN(M2PC_<?=$appID?>_desktopCaller))
						var i=$('K_sci_sc_'+p[3]).src.split('/');
					else
						var i=window['M2PC_'+M2PC_<?=$appID?>_desktopCaller+'_getSelectedIcons']()[0].split('/');
					i=i[i.length-1].split('.');
						M2PC_<?=$appID?>_changeIcon(i[0]);
					if(K_gec)
					{
						$('M2PC_<?=$appID?>_name').selectionStart=0;
						$('M2PC_<?=$appID?>_name').selectionEnd=$('M2PC_<?=$appID?>_name').value.length;
					}
					if(K_ie)
					{
						var tr=$('M2PC_<?=$appID?>_name').createTextRange();
						tr.select();
					}
				}

				if(M2PC_<?=$appID?>_type=='file')
				{
					$('M2PC_<?=$appID?>_contD').style.display='none';
					M2PC_<?=$appID?>_wMain.resizeTo(274,100);
					var k=$('M2PC_<?=$appID?>_name').value.split('.');
					if(K_gec)
					{
						$('M2PC_<?=$appID?>_name').selectionStart=0;
						$('M2PC_<?=$appID?>_name').selectionEnd=$('M2PC_<?=$appID?>_name').value.length-k[k.length-1].length-1;
					}
					if(K_ie)
					{
						var tr=$('M2PC_<?=$appID?>_name').createTextRange();
						tr.findText($('M2PC_<?=$appID?>_name').value.substring(0,$('M2PC_<?=$appID?>_name').value.length-k[k.length-1].length-1));
						tr.select();
					}
				}
			}
		$('M2PC_<?=$appID?>_name').focus();
}

M2PC_<?=$appID?>_wMain_destroy=function()
{
	M2PC_<?=$appID?>_icon=null;
		M2PC_<?=$appID?>_folderName=null;
	M2PC_<?=$appID?>_path=null;
	M2PC_<?=$appID?>_folderID=null;
	M2PC_<?=$appID?>_desktopCaller=null;
		M2PC_<?=$appID?>_type=null;
		M2PC_<?=$appID?>_HC1=null;
		
		K_unloadApp('M2PC_<?=$appID?>');
		return true;
}

M2PC_<?=$appID?>_changeIcon=function(id)
{
		$t($('M2PC_<?=$appID?>_d'+M2PC_<?=$appID?>_icon),'img')[0].style.borderColor='#FFFFFF';
		$t($('M2PC_<?=$appID?>_d'+id),'img')[0].style.borderColor='#0078D1';
		M2PC_<?=$appID?>_icon=id;
}

M2PC_<?=$appID?>_create=function()
{
	<?
	if ($_SESSION['M2PC_USER']==1)
		echo 'alert(K_L_notInDemo);return;';
	?>
	var name=$('M2PC_<?=$appID?>_name').value;
		if(name=='')
		{
			$('M2PC_<?=$appID?>_name').focus();
			return;
		}
		var icon=escape(M2PC_<?=$appID?>_icon);
	var t=$('M2PC_<?=$appID?>_contBut');
	t.old=t.innerHTML;
	t.innerHTML="<span class='loading'>&nbsp;<?=$L6?>...</span>";
	var dskW=$('K_int_M2PC_DESKTOP_DSKTP1').offsetWidth;
	var dskH=$('K_int_M2PC_DESKTOP_DSKTP1').offsetHeight;
	K_loadAJAX(K_dynP+'apps/desktop/desktopFolder.php?type='+M2PC_<?=$appID?>_type+'&appID='+M2PC_<?=$appID?>_desktopCaller+'&path='+M2PC_<?=$appID?>_path+'&folderName='+Base64.encode(M2PC_<?=$appID?>_folderName)+'&name='+Base64.encode(name)+'&icon='+icon+'&x='+K_lastRightClickX+'&y='+K_lastRightClickY+'&dskW='+dskW+'&dskH='+dskH+'&folderID='+M2PC_<?=$appID?>_folderID,'M2PC_<?=$appID?>_resCreate');
}

M2PC_<?=$appID?>_resCreate=function(xmlDoc)
{
	var e=$t(xmlDoc,'err');
	if(e.length==1)
	{
		var t=$('M2PC_<?=$appID?>_contBut');
		t.innerHTML=t.old;
		document.getElementById('M2PC_<?=$appID?>_name').focus();
		alert("<?=$L2?>");
	}
	else
	{
			if(M2PC_<?=$appID?>_type=='folder')
			{
		var sc=$t(xmlDoc,'nsc');
		if(sc.length==1 && M2PC_<?=$appID?>_desktopCaller!=K_appCanvas)
		{
			var p=M2PC_<?=$appID?>_desktopCaller.split('_');
			window['M2PC_'+$a(sc[0],'appID')+'_loadFiles'](window['M2PC_'+$a(sc[0],'appID')+'_DSKTP_path']);
		}
		var sc=$t(xmlDoc,'nscD');
		if(sc.length==1)
		{
			window[K_appCanvas].reloadShortcuts();
						setTimeout("M2PC_TSKBR1.redrawWindows()",100);
		}
			}
			var m=xmlDoc.getElementsByTagName('mod');
			if(m.length==1)
			{
				if(isNaN(M2PC_<?=$appID?>_desktopCaller))
				{
					var id=$a(m[0],'id');
					var n=$a(m[0],'name');
					var ic=$a(m[0],'icon');
					$('K_scn_sc_'+id).innerHTML=n;
					window['sc_'+id].name=n;
					if(M2PC_<?=$appID?>_type=='file')
						window['sc_'+id].path=M2PC_<?=$appID?>_path+n;
					
					if(M2PC_<?=$appID?>_type=='folder')
					{
						window['sc_'+id].path=M2PC_<?=$appID?>_path+n+'/';
						$('K_sci_sc_'+id).src=ic;   
						var p=ic.split('/');
						p=p[p.length-1].split('.');
						K_iconCache[M2PC_<?=$appID?>_path+n+'/']=p[0];
					}
					var idD=parseInt($a(m[0],'idD'));
					if(idD>0 && M2PC_<?=$appID?>_desktopCaller!=K_appCanvas)
					{
							$('K_scn_sc_'+idD).innerHTML=n;
							window['sc_'+idD].name=n;
							if(M2PC_<?=$appID?>_type=='folder')
							{
								window['sc_'+idD].path=M2PC_<?=$appID?>_path+n+'/';
								$('K_sci_sc_'+idD).src=ic;
							}
							if(M2PC_<?=$appID?>_type=='file')
								window['sc_'+idD].path=M2PC_<?=$appID?>_path+n;
					}
				}
				else
				{
					if(M2PC_<?=$appID?>_path=='/Desktop/')
					{
						window[K_appCanvas].reloadShortcuts();
						setTimeout("M2PC_TSKBR1.redrawWindows()",100);
					}
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

	}
}
M2PC_<?=$appID?>_checkRet=function(e)
{
	var kc=0;
	if (K_ie || K_op)
			kc=event.keyCode;
	if(K_gec)
			kc=e.keyCode;
	if(kc==13)
		M2PC_<?=$appID?>_create();
}


]]>
</code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="274" h="230" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,auto" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/folder_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">

<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div style='padding:5px'>
<table cellpadding='5' cellspacing='0'>
<tr><td align='right'><b><?=$L4?>:</b></td><td><input type='text' id='M2PC_<?=$appID?>_name' style='width:188px'/></td></tr>
<tr valign='top' id='M2PC_<?=$appID?>_contD'><td align='right'><b><?=$L5?>:</b></td><td><div id='M2PC_<?=$appID?>_contIcons' style='width:198px;height:120px;border:0px solid red;overflow:auto;'>

<div id='M2PC_<?=$appID?>_d0' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/0.png' style='border:2px solid #0078D1;' onclick="M2PC_<?=$appID?>_changeIcon(0)"/></div>
<div id='M2PC_<?=$appID?>_d1' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/1.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(1)"/></div>
<div id='M2PC_<?=$appID?>_d2' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/2.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(2)"/></div>
<div id='M2PC_<?=$appID?>_d3' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/3.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(3)"/></div>
<div id='M2PC_<?=$appID?>_d4' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/4.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(4)"/></div>
<div id='M2PC_<?=$appID?>_d5' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/5.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(5)"/></div>
<div id='M2PC_<?=$appID?>_d6' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/6.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(6)"/></div>
<div id='M2PC_<?=$appID?>_d7' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/7.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(7)"/></div>
<div id='M2PC_<?=$appID?>_d8' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/8.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(8)"/></div>
<div id='M2PC_<?=$appID?>_d9' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/9.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(9)"/></div>
<div id='M2PC_<?=$appID?>_d10' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/10.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(10)"/></div>
<div id='M2PC_<?=$appID?>_d11' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/11.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(11)"/></div>
<div id='M2PC_<?=$appID?>_d12' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/12.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(12)"/></div>
<div id='M2PC_<?=$appID?>_d13' style='display:inline;'>
<img src='<?=$static?>icons/folders_32/13.png' style='border:2px solid #FFFFFF;' onclick="M2PC_<?=$appID?>_changeIcon(13)"/></div>
</div></td></tr>
<tr><td colspan='2' align='center' id='M2PC_<?=$appID?>_contBut'><input style='width:100px' type='button' id='M2PC_<?=$appID?>_but2' class='button' value='  <?=$L10?>  ' onclick="M2PC_<?=$appID?>_wMain.destroy()" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input style='width:100px' type='button' id='M2PC_<?=$appID?>_but' class='button' value='  <?=$L3?>  ' onclick="M2PC_<?=$appID?>_create()" /></td></tr>
</table>
</div>
]]></html_code>

</window>

</app>
