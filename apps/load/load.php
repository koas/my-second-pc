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

$path=str_replace('..','.',$_REQUEST['path']);

echo'<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
?>
<app name="<?=$L1?>" id="M2PC_<?=$appID?>" cacheable='1'>
<stat>OK</stat>

<code><![CDATA[
M2PC_<?=$appID?>_path='';
M2PC_<?=$appID?>_callerAppID='';
M2PC_<?=$appID?>_callerAppCB='';
M2PC_<?=$appID?>_fileList=null;
M2PC_<?=$appID?>_main=function(appId, params)
{
        K_stopBounce();
        M2PC_<?=$appID?>_wMain.render();
        $('M2PC_<?=$appID?>_butOk').disabled=true;
	var p=params.split('#');
        M2PC_<?=$appID?>_path=p[1];
        M2PC_<?=$appID?>_callerAppID=p[2];
        M2PC_<?=$appID?>_callerAppCB=p[3];
        var cad="<select onchange='M2PC_<?=$appID?>_processFiles()' id='M2PC_<?=$appID?>_filter' style='width:200px'>";
        if(p[4])
            M2PC_<?=$appID?>_wMain.setTitle(p[4]);
        if(p[5])
        {
            var e=p[5].split('@');
            for(var x=0;x<e.length;++x)
            {
                var ee=e[x].split('%');
                cad+="<option value='"+ee[1]+"'>"+ee[0]+" (*"+ee[1]+")</option>";
            }
        }
        else
        {
            cad+="<option value='.*'><?=$L8?> (*.*)</option>";
        }
        cad+="</select>";
        $('M2PC_<?=$appID?>_contSel').innerHTML=cad;
        window['M2PC_'+M2PC_<?=$appID?>_callerAppID+'_wMain'].lockContent(true,'M2PC_<?=$appID?>_wMain');
	M2PC_<?=$appID?>_loadFiles(M2PC_<?=$appID?>_path);	
}

M2PC_<?=$appID?>_loadFiles=function(path)
{
	var p=path.split('/');
	M2PC_<?=$appID?>_path=path;
        K_startBounce(K_staP+'icons/fileManager_32.png');
	K_loadAJAX(K_dynP+'apps/desktop/listFiles.php?l=1&path='+path+'&appID=<?=$appID?>','M2PC_<?=$appID?>_resLoadFiles');
}

M2PC_<?=$appID?>_resLoadFiles=function(xmlDoc)
{
        K_stopBounce();
        M2PC_<?=$appID?>_fileList=xmlDoc;
        M2PC_<?=$appID?>_processFiles();
}

M2PC_<?=$appID?>_processFiles=function()
{
        var filter=$('M2PC_<?=$appID?>_filter').value.toLowerCase();
        var filterL=filter.length;
	var bar=$('M2PC_<?=$appID?>_bar');
	var p=M2PC_<?=$appID?>_path.split('/');
	var cad="<div style='cursor:default;float:left;text-align:center;padding:3px;border-right:1px dashed #7F7F7F;font-size:10px;background:";
	if(p.length==2)
		cad+="#DADADA";
	else cad+="#BFBFBF";
	cad+="'  onclick=\"M2PC_<?=$appID?>_loadFiles('/')\"><img src='"+K_staP+"icons/hd_32.png' align='absmiddle'/><br /><?=$L4?></div>";
	p.pop();
	var pathTemp='/';
	for(var x=1;x<p.length;++x)
	{
		pathTemp+=p[x]+'/';
		cad+="<div style='cursor:default;float:left;text-align:center;padding:3px;border-right:1px dashed #7F7F7F;font-size:10px;background:";
		if(x==p.length-1)
			cad+="#DADADA";
		else cad+="#BFBFBF";
                if(p[x]=='Uploaded files')
                    p[x]='<?=$L18?>';
                if(p[x]=='Trash')
                    p[x]='<?=$L17?>';
                if(p[x]=='Desktop')
                    p[x]='<?=$L16?>';
		cad+=";'  onclick=\"M2PC_<?=$appID?>_loadFiles('"+pathTemp+"')\"><img src='"+K_staP+"icons/folders_32/"+K_iconCache[pathTemp]+".png' align='absmiddle'/><br />"+p[x]+"</div>";
	}
	
	bar.innerHTML=cad;
	var cad="<table id='M2PC_<?=$appID?>_trs' cellpadding='3' cellspacing='0' width='100%'>";
	var e=$t(M2PC_<?=$appID?>_fileList,'e');
	for(var x=0;x<e.length;++x)
	{
                var name=$t(e[x],'n')[0].firstChild.data;
                if($a(e[x],'t')=='file' && filter!='.*' && name.substring(name.length-filterL,name.length).toLowerCase()!=filter)
                    continue;
                    
		cad+="<tr id='M2PC_<?=$appID?>_r"+x+"' style='cursor:default;-moz-user-select:none;' class='";
                if(x%2==0)cad+="fileRow0";else cad+="fileRow1";
                cad+="' ";
                if($a(e[x],'t')=='folder')
                    cad+="ondblclick=\"M2PC_<?=$appID?>_loadFiles('"+$t(e[x],'p')[0].firstChild.data+"')\" onclick='M2PC_<?=$appID?>_selectRow(-1)' ";
                if($a(e[x],'t')=='file')
                    cad+="ondblclick=\"M2PC_<?=$appID?>_selectFile('"+$t(e[x],'p')[0].firstChild.data+"')\" onclick='M2PC_<?=$appID?>_selectRow("+x+")' ";
                if(name=='Uploaded files')
                    name='<?=$L18?>';
                if(name=='Trash')
                    name='<?=$L17?>';
                if(name=='Desktop')
                    name='<?=$L16?>';
                cad+="><td width='16'><img src='"+K_staP+'icons/'+$a(e[x],'if')+"_16/"+$a(e[x],'i')+"'/></td><td>"+name+"</td><td width='100'>"+$a(e[x],'d')+' '+$a(e[x],'h')+"</td></tr>";
                if($a(e[x],'t')=='folder')
                {
                    var p=$a(e[x],'i').split('.');
                    K_iconCache[$t(e[x],'p')[0].firstChild.data]=p[0];
                }
	}
        cad+="</table>";
        $('M2PC_<?=$appID?>_list').innerHTML=cad;
}
M2PC_<?=$appID?>_selectRow=function(id)
{
    var tr=$t($('M2PC_<?=$appID?>_trs'),'tr');
    for(var x=0;x<tr.length;++x)
        if(x%2==0)tr[x].className="fileRow0";else tr[x].className="fileRow1";
    $('M2PC_<?=$appID?>_butOk').disabled=true;
    if(id<0)return;
    var tr=$('M2PC_<?=$appID?>_r'+id);
    tr.className='rowSelected';
    $('M2PC_<?=$appID?>_butOk').disabled=false;
}
M2PC_<?=$appID?>_selectFile=function(file)
{
    window[M2PC_<?=$appID?>_callerAppCB](file);
    M2PC_<?=$appID?>_wMain.destroy();
}
M2PC_<?=$appID?>_checkExists=function(file)
{
    M2PC_<?=$appID?>_selectFile(file);
}
M2PC_<?=$appID?>_wConfirm_destroy=function()
{
    if(!M2PC_<?=$appID?>_exiting)
    {
        M2PC_<?=$appID?>_wMain.lockContent(false,'');
        M2PC_<?=$appID?>_wMain.bringToFront();
        M2PC_<?=$appID?>_wConfirm.hide();
        return false;
    }
    return true;
}

M2PC_<?=$appID?>_wMain_destroy=function()
{
	window['M2PC_'+M2PC_<?=$appID?>_callerAppID+'_wMain'].lockContent(false,'');
        window['M2PC_'+M2PC_<?=$appID?>_callerAppID+'_wMain'].bringToFront();
        M2PC_<?=$appID?>_path=null;
        M2PC_<?=$appID?>_callerAppID=null;
        M2PC_<?=$appID?>_callerAppCB=null;
        M2PC_<?=$appID?>_fileList=null;
        M2PC_<?=$appID?>_HC1=null;
        M2PC_<?=$appID?>_HC3=null;
        K_unloadApp('M2PC_<?=$appID?>');
        return true;
}

]]></code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="550" h="415" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="550,375" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/fileManager_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div id='M2PC_<?=$appID?>_bar' style='border-top:1px solid #000;border-bottom:1px solid #000;width:100%;height:51px;background:#BFBFBF;'></div>
<table style='clear:both' class='table' cellpadding='3' cellspacing='0' width='100%'><tr class='enc'><td width='16'></td><td><?=$L2?></td><td width='100'><?=$L3?></td></tr></table>
<div id='M2PC_<?=$appID?>_list' style='border-bottom:1px solid #000;position:absolute;left:0px;top:77px;width:548px;height:273px;overflow:auto;'></div>
]]></html_code>
<html_code id="M2PC_<?=$appID?>_HC3" x="0" y="360" w="auto" h="30"><![CDATA[
<table cellpadding='0' cellspacing='0' width='100%'><tr><td>&nbsp;&nbsp;<?=$L7?>&nbsp;&nbsp;<span id='M2PC_<?=$appID?>_contSel'></span></td><td align='right'><input style='width:100px' type='button' class='button' value='<?=$L5?>' onclick="M2PC_<?=$appID?>_wMain.destroy()"/></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align='right' width='100'><input id='M2PC_<?=$appID?>_butOk' style='width:100px' type='button' class='button' value='<?=$L6?>' onclick="M2PC_<?=$appID?>_checkExists(M2PC_<?=$appID?>_path+M2PC_<?=$appID?>_fileName)"/></td><td>&nbsp;&nbsp;</td></table>
]]></html_code>
</window>
</app>
