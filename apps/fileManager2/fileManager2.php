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
M2PC_<?=$appID?>_DSKTP_path='';
M2PC_<?=$appID?>_DSKTP_fileList=null;
M2PC_<?=$appID?>_exiting=false;
M2PC_<?=$appID?>_main=function(appId, params)
{
        M2PC_<?=$appID?>_wMain.innerDesktop=true;
	M2PC_<?=$appID?>_wMain.render();
        M2PC_<?=$appID?>_wConfirm.render();
        M2PC_<?=$appID?>_wConfirm.hide();
        M2PC_<?=$appID?>_wAbout.render();
        M2PC_<?=$appID?>_wAbout.hide();
	M2PC_<?=$appID?>_wMain.addToTaskbar('M2PC_TSKBR1');
	
        var t=$('M2PC_<?=$appID?>_list');
        if (K_ie)
        {
		t.oncontextmenu=M2PC_<?=$appID?>_rightMouseDown;
                t.onclick=M2PC_<?=$appID?>_deselectAll;
        }
	else if (document.addEventListener)
        {
		t.addEventListener('contextmenu',M2PC_<?=$appID?>_rightMouseDown,true);
                t.addEventListener('click',M2PC_<?=$appID?>_deselectAll,true);
        }
                
        var p=params.split('#');
	M2PC_<?=$appID?>_loadFiles(p[1]);
}

M2PC_<?=$appID?>_loadFiles=function(path)
{
        M2PC_<?=$appID?>_wMain.setStatus('');
	var p=path.split('/');
        
        var tit=p[p.length-2];
        if(tit=='Uploaded files')
            tit='<?=$L18?>';
        if(tit=='Trash')
            tit='<?=$L17?>';
        if(tit=='Desktop')
            tit='<?=$L16?>';
        M2PC_<?=$appID?>_wMain.setTitle(tit+' - <?=$L1?>');
        
	M2PC_<?=$appID?>_DSKTP_path=path;
        K_startBounce(K_staP+'icons/fileManager_32.png');
	K_loadAJAX(K_dynP+'apps/desktop/listFiles.php?l=1&path='+path+'&appID=<?=$appID?>','M2PC_<?=$appID?>_resLoadFiles');
}

M2PC_<?=$appID?>_resLoadFiles=function(xmlDoc)
{
        K_stopBounce();
        M2PC_<?=$appID?>_DSKTP_fileList=xmlDoc;
	var bar=$('M2PC_<?=$appID?>_bar');
	var p=M2PC_<?=$appID?>_DSKTP_path.split('/');
        var cad='';
        if(M2PC_<?=$appID?>_DSKTP_path.substr(0,4)!='/tmp')
        {
            cad+="<div style='cursor:default;float:left;text-align:center;padding:3px;border-right:1px dashed #7F7F7F;font-size:10px;background:";
            if(p.length==2)
                    cad+="#DADADA";
            else cad+="#BFBFBF";
            cad+="'  onclick=\"M2PC_<?=$appID?>_loadFiles('/')\"><img src='"+K_staP+"icons/hd_32.png' align='absmiddle'/><br /><?=$L4?></div>";
        }
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
                
		cad+="'  onclick=\"M2PC_<?=$appID?>_loadFiles('"+pathTemp+"')\"><img src='"+K_staP+"icons/folders_32/"+K_iconCache[pathTemp]+".png' align='absmiddle'/><br />"+p[x]+"</div>";
	}
	
	bar.innerHTML=cad;
	
        cad="<table id='M2PC_<?=$appID?>_trs' cellpadding='3' cellspacing='0' width='100%'>";
	var e=$t(xmlDoc,'e');
	for(var x=0;x<e.length;++x)
	{
		cad+="<tr id='M2PC_<?=$appID?>_r"+x+"' alt='"+$t(e[x],'p')[0].firstChild.data+"' style='cursor:default;-moz-user-select:none;' class='";
                if(x%2==0)cad+="fileRow0";else cad+="fileRow1";
                cad+="' ";
                if($a(e[x],'t')=='folder')
                    cad+="ondblclick=\"M2PC_<?=$appID?>_loadFiles('"+$t(e[x],'p')[0].firstChild.data+"')\" nclick='M2PC_<?=$appID?>_selectRow("+x+")' ";
                if($a(e[x],'t')=='file')
                    cad+="ondblclick=\"K_launchFile('"+$t(e[x],'p')[0].firstChild.data+"')\" nclick='M2PC_<?=$appID?>_selectRow("+x+")' ";
                    
                var nom=$t(e[x],'n')[0].firstChild.data;
                if(nom=='Uploaded files')
                    nom='<?=$L18?>';
                if(nom=='Trash')
                    nom='<?=$L17?>';
                if(nom=='Desktop')
                    nom='<?=$L16?>';
                    
                cad+="><td id='M2PC_<?=$appID?>_c_"+x+"_0' width='16'><img src='"+K_staP+'icons/'+$a(e[x],'if')+"_16/"+$a(e[x],'i')+"'/></td><td id='M2PC_<?=$appID?>_c_"+x+"_1' >"+nom+"</td><td id='M2PC_<?=$appID?>_c_"+x+"_2' width='50'>"+K_fileSize($a(e[x],'s'))+"</td><td id='M2PC_<?=$appID?>_c_"+x+"_3' width='150'>"+$a(e[x],'d')+' '+$a(e[x],'h')+"</td></tr>";
                if($a(e[x],'t')=='folder')
                {
                    var p=$a(e[x],'i').split('.');
                    K_iconCache[$t(e[x],'p')[0].firstChild.data]=p[0];
                }
	}
        cad+="</table>";
        $('M2PC_<?=$appID?>_list').innerHTML=cad;
        var t=$('M2PC_<?=$appID?>_trs');
        if (K_ie)
        {
		t.oncontextmenu=M2PC_<?=$appID?>_rightMouseDown;
                t.onclick=M2PC_<?=$appID?>_leftMouseDown;
                t.onselectstart=function(){return false};
        }
	else if (document.addEventListener)
        {
		t.addEventListener('contextmenu',M2PC_<?=$appID?>_rightMouseDown,true);
                t.addEventListener('click',M2PC_<?=$appID?>_leftMouseDown,true);
        }
}

M2PC_<?=$appID?>_rightMouseDown=function(e)
{
    var src='';
    if (K_ie)
	src=window.event.srcElement.id;
    else
	src=e.target.id;
    var x=y=0;
    if (K_ie || K_op)
    {
            x=event.clientX;
            y=event.clientY;
    }
    if (K_gec)
    {
            x=e.pageX;
            y=e.pageY;
    }
    var t='';
    var type=0;
    var p=src.split('_');
    
    
    if(src=='M2PC_<?=$appID?>_list')
        type=0;
    else
    {
        if(M2PC_<?=$appID?>_isRowSelected(p[3]))
            type=1;
        else type=0;
    }
    
    if(type==1)
    {
        t='M2PC_DESKTP1_SCCM';
        if(M2PC_<?=$appID?>_DSKTP_path=='/Trash/')
            window[t].setItemState('cmFolderTrash',1);
    }
    else
    {
        t='M2PC_DSKTP1_CM';
        window['M2PC_DSKTP1_CM'].setItemState('separatorGraph',2);
        window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmConfGraphics',2);
        window['M2PC_DSKTP1_CM'].setItemState('separatorLogout',2);
        window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmLock',2);
        window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmExit',2);
        if(K_clipboard.length<1)
		    window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmPaste',1);
    	else window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmPaste',0);
    }
    if (t!=null)
    {
            K_clearContextMenu();
            window[t].caller=p[1];
            K_lastRightClickX=x;
            K_lastRightClickY=y;
            window[t].moveTo(x,y);
            window[t].show();
            K_CMactive=t;
    }
    if (K_gec)
    {
            e.preventDefault();
            e.stopPropagation();
    }
    return false;
}

M2PC_<?=$appID?>_leftMouseDown=function(e)
{
    var src='';
    if (K_ie)
	src=window.event.srcElement.id;
    else
	src=e.target.id;
    var p=src.split('_');
    var index=parseInt(p[3]);
    var evtobj=window.event ? event : e;
    if (evtobj.ctrlKey==true)
    {
        if(M2PC_<?=$appID?>_isRowSelected(index))
            M2PC_<?=$appID?>_deselectRow(index,true);
        else M2PC_<?=$appID?>_selectRow(index,true);
    }
    
    if (evtobj.shiftKey==true)
    {
        var first=M2PC_<?=$appID?>_getFirstSelected();
        if(first==-1)
            M2PC_<?=$appID?>_selectRow(index,false);
        else
        {
            if(first<index)
            {
                for(var x=first;x<index+1;++x)
                    M2PC_<?=$appID?>_selectRow(x,true);
            }
            else
            {
                for(var x=index;x<first+1;++x)
                    M2PC_<?=$appID?>_selectRow(x,true);
            }
        }
    }
    if (evtobj.ctrlKey==false && evtobj.shiftKey==false)
    {
        if(!M2PC_<?=$appID?>_isRowSelected(index))
            M2PC_<?=$appID?>_selectRow(index,false);
        else M2PC_<?=$appID?>_deselectRow(index);
    }
}

M2PC_<?=$appID?>_getFirstSelected=function()
{
    var tr=$t($('M2PC_<?=$appID?>_trs'),'tr');
    for(var x=0;x<tr.length;++x)
        if(tr[x].className=='rowSelected')
            return parseInt(x);
    return -1;
}
M2PC_<?=$appID?>_isRowSelected=function(x)
{
    var tr=$t($('M2PC_<?=$appID?>_trs'),'tr');
    if(tr[x].className=='rowSelected')
        return true;
    else return false;
}
M2PC_<?=$appID?>_deselectRow=function(id)
{
    var tr=$t($('M2PC_<?=$appID?>_trs'),'tr');
    for(var x=0;x<tr.length;++x)
        if(x==id)
        {
            if(x%2==0)tr[x].className="fileRow0";else tr[x].className="fileRow1";
            break;
        }
}
M2PC_<?=$appID?>_deselectAll=function(e)
{
    var src='';
    if(e)
    {
        if (K_ie)
            src=window.event.srcElement.id;
        else
            src=e.target.id;
        if(src!='M2PC_<?=$appID?>_list')
            return;
    }
    var tr=$t($('M2PC_<?=$appID?>_trs'),'tr');
    for(var x=0;x<tr.length;++x)
        if(x%2==0)tr[x].className="fileRow0";else tr[x].className="fileRow1";
}
M2PC_<?=$appID?>_selectRow=function(id,keep)
{
    if(!keep)
        M2PC_<?=$appID?>_deselectAll();
        
    if(id<0)return;
    var tr=$('M2PC_<?=$appID?>_r'+id);
    tr.className='rowSelected';
}

M2PC_<?=$appID?>_getSelectedFiles=function()
{
    var files=new Array();
    var tr=$t($('M2PC_<?=$appID?>_trs'),'tr');
    for(var x=0;x<tr.length;++x)
        if(tr[x].className=='rowSelected')
            files[files.length]=$a(tr[x],'alt');
    return files;
}
M2PC_<?=$appID?>_getSelectedFileNames=function()
{
    var files=new Array();
    var tr=$t($('M2PC_<?=$appID?>_trs'),'tr');
    for(var x=0;x<tr.length;++x)
        if(tr[x].className=='rowSelected')
            files[files.length]=$t(tr[x],'td')[1].innerHTML;
    return files;
}

M2PC_<?=$appID?>_getSelectedIcons=function()
{
    var files=new Array();
    var tr=$t($('M2PC_<?=$appID?>_trs'),'tr');
    for(var x=0;x<tr.length;++x)
        if(tr[x].className=='rowSelected')
            files[files.length]=$t($t(tr[x],'td')[0],'img')[0].src;
    return files;
}

M2PC_<?=$appID?>_wMain_cbResizeEnd=function()
{
    $('M2PC_<?=$appID?>_list').style.height=window['M2PC_<?=$appID?>_wMain'].height-119+'px';
    //$('M2PC_<?=$appID?>_tEnc').style.width=window['M2PC_<?=$appID?>_wMain'].width-17+'px';
    $('M2PC_<?=$appID?>_tEnc').style.width=$('M2PC_<?=$appID?>_trs').offsetWidth;
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
        if(M2PC_<?=$appID?>_DSKTP_path.substr(0,4)=='/tmp')
        {
            var folder=M2PC_<?=$appID?>_DSKTP_path.split('/')[1];
            var cad=K_dynP+'apps/desktop/unlink.php?dummy=1&name0=/'+folder+'/';
            K_loadAJAX(cad,'');
        }
        M2PC_<?=$appID?>_exiting=true;
        M2PC_<?=$appID?>_wConfirm.destroy();
        M2PC_<?=$appID?>_wAbout.destroy();
        M2PC_<?=$appID?>_DSKTP_path=null;
        M2PC_<?=$appID?>_DSKTP_fileList=null;
        M2PC_<?=$appID?>_HC1=null;
        M2PC_<?=$appID?>_HC2=null;
        M2PC_<?=$appID?>_HC3=null;
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
]]></code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="550" h="375" mov="1" res="1" boundaries="auto,auto,auto,auto" minSize="320,210" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/fileManager_16.png" clsBut="1" maxBut="1" minBut="1" statusBar="1" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div id='M2PC_<?=$appID?>_bar' style='width:100%;height:50px;background:#BFBFBF'></div>
<table id='M2PC_<?=$appID?>_tEnc' style='position:absolute;left:0px;top:50px;' class='table' cellpadding='3' cellspacing='0' width='533'><tr class='enc'><td width='16'></td><td><?=$L13?></td><td width='50'><?=$L15?></td><td width='150'><?=$L14?></td></tr></table>
<div id='M2PC_<?=$appID?>_list' style='position:absolute;left:0px;top:77px;width:100%;height:256px;overflow:auto;'></div>
]]></html_code>
</window>

<window id="M2PC_<?=$appID?>_wConfirm" x="auto" y="auto" w="700" h="170" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L7?>" icon="<?=$static?>icons/warning_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC2" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table width='100%' height='100%' cellpadding='0' cellspacing='0' class='mbox'>
<tr><td height='100'><img style='margin-left:20px' src='<?=$static?>icons/warning_64.png'/></td><td width='620' align='center'><div ><b><?=$L5?></b><br/><br/>
<span id='M2PC_<?=$appID?>_rep'></span><br/><br/>
<?=$L6?>
</div></td></tr>
<tr><td colspan='2'><div style='margin-left:0px'><table border='0' width='100%' cellpadding='0' cellspacing='0'><tr>
<td align='center'><input style='width:130px' type='button' class='button' value='<?=$L8?>' onclick="M2PC_<?=$appID?>_cancelReplace()"/></td>
<td align='center'><input style='width:130px' type='button' class='button' value='<?=$L9?>' onclick="M2PC_<?=$appID?>_skipAll()"/></td>
<td align='center'><input style='width:130px' type='button' class='button' value='<?=$L10?>' onclick="M2PC_<?=$appID?>_replaceAll()"/></td>
<td align='center'><input style='width:130px' type='button' class='button' value='<?=$L11?>' onclick="M2PC_<?=$appID?>_skipFile()"/></td>
<td align='center'><input style='width:130px' type='button' class='button' value='<?=$L12?>' onclick="M2PC_<?=$appID?>_replaceFile()"/></td>
</tr></table>
</div></td></tr>
</table>
]]></html_code>
</window>
<window id="M2PC_<?=$appID?>_wAbout" x="auto" y="auto" w="400" h="118" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L114?> <?=$L1?>" icon="<?=$static?>icons/about_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC3" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table cellpadding='10' cellspacing='0' width='100%'><tr>
    <td width='64' align='center'><img src='<?=$static?>icons/fileManager_64.png'/></td>
    <td><b><?=$L1?></b><br/>
    <?=$L115?> 0.5&alpha;<br/>
    &copy; 2006-<?=date('Y')?> Karontek<br/>
    <div style='border-top:1px solid #000;margin-top:5px;padding-top:5px'>
    <?=$L117?> nuoveXT (<a href='http://nuovext.pwsp.net/' target='_blank'>http://nuovext.pwsp.net/</a>)<br/>
    </div>
    </td>
</tr></table>
]]></html_code>
</window>

</app>
