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
M2PC_<?=$appID?>_main=function(appId, params)
{
    K_stopBounce();
    M2PC_<?=$appID?>_wMain.render();
    var p=params.split('#');
    M2PC_<?=$appID?>_path=p[1];
    K_loadAJAX(K_dynP+'apps/decompressor/decompress.php?file='+M2PC_<?=$appID?>_path,'M2PC_<?=$appID?>_resDecompress');
}

M2PC_<?=$appID?>_resDecompress=function(xmlDoc)
{
    var folder=$t(xmlDoc,'f')[0].firstChild.data;
    K_iconCache['/'+folder+'/']='compress';
    K_loadAppAJAX(K_dynP+'apps/fileManager2/fileManager2.php','1#/'+folder+'/',K_staP+'icons/fileManager_32.png');
    M2PC_<?=$appID?>_wMain.destroy();
}

M2PC_<?=$appID?>_wMain_destroy=function()
{
    M2PC_<?=$appID?>_exiting=true;
    M2PC_<?=$appID?>_path=null;
    M2PC_<?=$appID?>_HC1=null;
    M2PC_<?=$appID?>_exiting=null;
    K_unloadApp('M2PC_<?=$appID?>');
    return true;
}
]]></code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="180" h="70" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="550,375" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/compress_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div style='padding:10px' align='center'><span class='loading'> <?=$L1?></span></div>
]]></html_code>
</window>

</app>
