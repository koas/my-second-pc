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

if ($_SESSION['M2PC_USER']==1)
die( '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<res><stat>NOTINDEMO</stat></res>');

$path=str_replace('..','.',$_REQUEST['path']);

echo'<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
?>
<app name="<?=$L1?>" id="M2PC_<?=$appID?>" cacheable='1'>
<stat>OK</stat>

<code><![CDATA[
M2PC_<?=$appID?>_path='';
M2PC_<?=$appID?>_fileName='';
M2PC_<?=$appID?>_callerAppID='';
M2PC_<?=$appID?>_callerAppCB='';
M2PC_<?=$appID?>_fileList=null;
M2PC_<?=$appID?>_exiting=false;
M2PC_<?=$appID?>_tmpFile='';
M2PC_<?=$appID?>_main=function(appId, params)
{
        K_stopBounce();
        M2PC_<?=$appID?>_wMain.render();
        M2PC_<?=$appID?>_wConfirm.render();
        M2PC_<?=$appID?>_wConfirm.hide();
	var p=params.split('#');
        M2PC_<?=$appID?>_path=p[1];
        M2PC_<?=$appID?>_fileName=p[2];
        M2PC_<?=$appID?>_callerAppID=p[3];
        M2PC_<?=$appID?>_callerAppCB=p[4];
        $('M2PC_<?=$appID?>_name').onkeyup=M2PC_<?=$appID?>_checkRet;
        window['M2PC_'+M2PC_<?=$appID?>_callerAppID+'_wMain'].lockContent(true,'M2PC_<?=$appID?>_wMain');
        var cad="<select onchange='M2PC_<?=$appID?>_processFiles()' id='M2PC_<?=$appID?>_filter' style='width:200px'>";
        if(p[5])
            M2PC_<?=$appID?>_wMain.setTitle(p[5]);
        if(p[6])
        {
            var e=p[6].split('@');
            for(var x=0;x<e.length;++x)
            {
                var ee=e[x].split('%');
                cad+="<option value='"+ee[1]+"'>"+ee[0]+" (*"+ee[1]+")</option>";
            }
        }
        else
        {
            cad+="<option value='.*'><?=$L12?> (*.*)</option>";
        }
        cad+="</select>";
        $('M2PC_<?=$appID?>_contSel').innerHTML=cad;
        if(M2PC_<?=$appID?>_fileName)
        {
            var t=$('M2PC_<?=$appID?>_name');
            t.value=M2PC_<?=$appID?>_fileName;
            var e=M2PC_<?=$appID?>_fileName.split('.');
            e=e[e.length-1];
            if(K_gec)
            {
                t.selectionStart=0;
                t.selectionEnd=t.value.length-e.length-1;
            }
            if(K_ie)
            {
                var tr=t.createTextRange();
                tr.moveStart('character',0);
                tr.moveEnd('character',-e.length-1);
                tr.select();
            }
            t.focus();
        }
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
                    cad+="ondblclick=\"M2PC_<?=$appID?>_askReplace('"+$t(e[x],'p')[0].firstChild.data+"')\" onclick='M2PC_<?=$appID?>_selectRow("+x+")' ";
                
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
    if(id<0)return;
    var tr=$('M2PC_<?=$appID?>_r'+id);
    tr.className='rowSelected';
    var t=$('M2PC_<?=$appID?>_name');
    t.value=$t(tr,'td')[1].innerHTML;
    var e=t.value.split('.');
    e=e[e.length-1];
    if(K_gec)
    {
        t.selectionStart=0;
        t.selectionEnd=t.value.length-e.length-1;
    }
    if(K_ie)
    {
        var tr=t.createTextRange();
        tr.moveStart('character',0);
        tr.moveEnd('character',-e.length-1);
        tr.select();
    }
    t.focus();
}
M2PC_<?=$appID?>_checkExists=function(file)
{
    M2PC_<?=$appID?>_tmpFile=file;
    var exists=false;
    var e=$t(M2PC_<?=$appID?>_fileList,'e');
    for(var x=0;x<e.length;++x)
        if($t(e[x],'p')[0].firstChild.data==file)
        {
            exists=true;
            break;
        }
    if(exists)
        M2PC_<?=$appID?>_askReplace(file);
    else M2PC_<?=$appID?>_replace();
}
M2PC_<?=$appID?>_askReplace=function(file)
{
    M2PC_<?=$appID?>_tmpFile=file;
    var n=file.split('/');
    n=n[n.length-1];
    $('M2PC_<?=$appID?>_repName').innerHTML="<i>"+n+"</i>";
    M2PC_<?=$appID?>_wConfirm.show();
    M2PC_<?=$appID?>_wConfirm.bringToFront();
    M2PC_<?=$appID?>_wMain.lockContent(true,'M2PC_<?=$appID?>_wConfirm');
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
M2PC_<?=$appID?>_cancelReplace=function()
{
    M2PC_<?=$appID?>_wMain.lockContent(false,'');
    setTimeout("window['M2PC_<?=$appID?>_wConfirm'].clickClose()",100);
}
M2PC_<?=$appID?>_replace=function()
{
    M2PC_<?=$appID?>_exiting=true;
    var filter=$('M2PC_<?=$appID?>_filter').value.toLowerCase();
    var filterL=filter.length;
    if(M2PC_<?=$appID?>_tmpFile.substring(M2PC_<?=$appID?>_tmpFile.length-filterL,M2PC_<?=$appID?>_tmpFile.length).toLowerCase()!=filter)
        M2PC_<?=$appID?>_tmpFile+=filter;
    window[M2PC_<?=$appID?>_callerAppCB](M2PC_<?=$appID?>_tmpFile);
    M2PC_<?=$appID?>_wMain.destroy();
}
M2PC_<?=$appID?>_wMain_destroy=function()
{
	window['M2PC_'+M2PC_<?=$appID?>_callerAppID+'_wMain'].lockContent(false,'');
        window['M2PC_'+M2PC_<?=$appID?>_callerAppID+'_wMain'].bringToFront();
        M2PC_<?=$appID?>_exiting=true;
        M2PC_<?=$appID?>_wConfirm.destroy();
        M2PC_<?=$appID?>_path=null;
        M2PC_<?=$appID?>_fileName=null;
        M2PC_<?=$appID?>_callerAppID=null;
        M2PC_<?=$appID?>_callerAppCB=null;
        M2PC_<?=$appID?>_exiting=null;
        M2PC_<?=$appID?>_tmpFile=null;
        M2PC_<?=$appID?>_HC1=null;
        M2PC_<?=$appID?>_HC2=null;
        M2PC_<?=$appID?>_HC3=null;
        M2PC_<?=$appID?>_fileList=null;
        K_unloadApp('M2PC_<?=$appID?>');
        return true;
}
M2PC_<?=$appID?>_checkRet=function(e)
{
    var kc=0;
    if (K_ie || K_op)
            kc=event.keyCode;
    if(K_gec)
            kc=e.keyCode;
    M2PC_<?=$appID?>_fileName=$('M2PC_<?=$appID?>_name').value;
    if(kc==13)
        M2PC_<?=$appID?>_checkExists(M2PC_<?=$appID?>_path+M2PC_<?=$appID?>_fileName);
}
]]></code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="550" h="415" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="550,375" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/fileManager_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table align='center' cellpadding='5' cellspacing='0'><tr><td align='center'><b><?=$L2?>:</b></td><td align='right'><input type='text' id='M2PC_<?=$appID?>_name' style='width:450px'/></td></tr></table>
<div id='M2PC_<?=$appID?>_bar' style='border-top:1px solid #000;border-bottom:1px solid #000;width:100%;height:51px;background:#BFBFBF;'></div>
<table style='clear:both' class='table' cellpadding='3' cellspacing='0' width='100%'><tr class='enc'><td width='16'></td><td><?=$L2?></td><td width='100'><?=$L3?></td></tr></table>
<div id='M2PC_<?=$appID?>_list' style='border-bottom:1px solid #000;position:absolute;left:0px;top:107px;width:548px;height:243px;overflow:auto;'></div>
]]></html_code>
<html_code id="M2PC_<?=$appID?>_HC3" x="0" y="360" w="auto" h="30"><![CDATA[
<table cellpadding='0' cellspacing='0' width='100%'><tr><td>&nbsp;&nbsp;<?=$L11?>&nbsp;&nbsp;<span id='M2PC_<?=$appID?>_contSel'></span></td><td align='right'><input style='width:100px' type='button' class='button' value='<?=$L8?>' onclick="M2PC_<?=$appID?>_wMain.destroy()"/></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align='right' width='100'><input style='width:100px' type='button' class='button' value='<?=$L10?>' onclick="M2PC_<?=$appID?>_checkExists(M2PC_<?=$appID?>_path+M2PC_<?=$appID?>_fileName)"/></td><td>&nbsp;&nbsp;</td></table>
]]></html_code>
</window>

<window id="M2PC_<?=$appID?>_wConfirm" x="auto" y="auto" w="550" h="170" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L5?>" icon="<?=$static?>icons/warning_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC2" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table border='0' width='100%' height='100%' cellpadding='0' cellspacing='0' class='mbox'>
<tr><td rowspan='2' height='128'><img style='margin-left:20px' src='<?=$static?>icons/warning_64.png'/></td><td align='center'><div ><b><?=$L6?></b><br/><br/><span id='M2PC_<?=$appID?>_repName'></span><br/><br/>
<?=$L7?>
</div></td></tr>
<tr><td><div style='margin-left:0px'><table border='0' width='100%' cellpadding='0' cellspacing='0'><tr>
<td align='center'><input style='width:150px' type='button' class='button' value='<?=$L8?>' onclick="M2PC_<?=$appID?>_cancelReplace()"/></td>
<td align='center'><input style='width:150px' type='button' class='button' value='<?=$L9?>' onclick="M2PC_<?=$appID?>_replace()"/></td>
</tr></table>
</div></td></tr>
</table>
]]></html_code>
</window>


</app>
