K_FD_check=function(src,dst)
{
    if(K_getExtension(src)=='txt' && K_getExtension(dst)=='txt')
        return 'icons/appendTxt_32.png';
    return '';
}

K_FD_run=function(src,dst)
{
    if(K_getExtension(src)=='txt' && K_getExtension(dst)=='txt')
        K_loadAppAJAX(K_dynP+'apps/appendTxt/appendTxt.php','1#'+src+'#'+dst,K_staP+'icons/appendTxt_32.png');
}