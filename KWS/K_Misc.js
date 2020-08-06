var K_iconCache=new Array();
K_iconCache['/Desktop/']='desktop';
K_iconCache['/Trash/']='trash';
K_iconCache['/Uploaded files/']='uploaded';

function K_getExtension(file)
{
	var ext=file.split('.');
	return ext[ext.length-1].toLowerCase();
}
function K_inArray(n,h)
{
	for(var x=0;x<h.length;++x)
		if(h[x]==n)
			return true;
	return false;
}
function K_arrayDiff(a1,a2)
{
        var ret=new Array();
        for(var x=0;x<a1.length;++x)
                if(!K_inArray(a1[x],a2))
                        ret[ret.length]=a1[x];
        return ret;
}
function K_removeNode(id)
{
	var e=document.getElementById(id);
	if(e)
	{
		while (e.firstChild)
	    	e.removeChild(e.firstChild);
		e.parentNode.removeChild(e);
	}
}

function K_clearContextMenu()
{
	if (K_CMactive!='' && window[K_CMactive])
		window[K_CMactive].clearAllSubMenus();
}

function K_trans(id,opacity)
{
	var obj=$(id);
  	opacity = (opacity == 100)?99.999:opacity;
  	obj.style.KhtmlOpacity = opacity/100;
  	obj.style.filter = "alpha(opacity:"+opacity+")";
  	obj.style.MozOpacity = opacity/100;
  	obj.style.opacity = opacity/100;
}

function K_strReverse(str) 
{
	var rev='';
	for (i = 0; i <= str.length; ++i)
		rev=str.charAt(i)+rev;
	return rev;
}

function K_fileSize(bytes)
{
        var ret='';
        var suf='';
        if (bytes >= 1099511627776) {
        ret=Math.round((bytes / 1024 / 1024 / 1024 / 1024)*10)/10;
        suf = "TB";
        } else if (bytes >= 1073741824) {
       ret = Math.round((bytes / 1024 / 1024 / 1024)*10)/10;
       suf = "GB";
   } else if (bytes >= 1048576) {
       ret = Math.round((bytes / 1024 / 1024)*10)/10;
       suf = "MB";
   } else if (bytes >= 1024) {
       ret = Math.round((bytes / 1024)*10)/10;
       suf = "KB";
   } else {
       ret = bytes;
       suf = "Bytes";
   }
   if (ret == 1) {
       ret += " " + suf;
   } else {
        
       ret += " " + suf ;
       if(suf!='Bytes') ret+='s';
   }
   return ret;
}

function K_launchFile(path)
{
	switch(K_getExtension(path))
	{
		case 'txt':
			K_loadAppAJAX(K_dynP+'apps/edit/edit.php','1#'+path,K_staP+'icons/editor_32.png');
			break;
                case 'htdoc':
			K_loadAppAJAX(K_dynP+'apps/htdoc/htdoc.php','1#'+path,K_staP+'icons/htdoc_32.png');
			break;
		case 'jpg':
		case 'png':
		case 'gif':
		case 'jpeg':
		case 'bmp':
			K_loadAppAJAX(K_dynP+'apps/imageViewer/imageViewer.php','1#'+path,K_staP+'icons/imageViewer_32.png');
			break;
		case 'flv':
		case 'mp3':
			K_loadAppAJAX(K_dynP+'apps/mediaPlayer/mediaPlayer.php','1#'+path,K_staP+'icons/mplayer_32.png');
			break;
                case 'zip':
                case 'rar':
                case 'gz':
                case 'bz2':
                case 'lzma':
                case 'lzo':
                case 'tar':
                        K_loadAppAJAX(K_dynP+'apps/decompressor/decompressor.php','1#'+path,K_staP+'icons/compress_32.png');
			break;
                        
		/*case 'pdf':
			K_loadAppAJAX(K_dynP+'apps/pdfViewer/pdfViewer.php','1#'+path,K_staP+'icons/files_32/pdf.png');
			break;*/
                default:
			$('K_Desktop_ifr').src=K_dynP+'php/desktop/download.php?cad='+path;
	}     	
}

