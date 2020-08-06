K_Window.prototype=new K_Base();
K_Window.prototype.constructor=K_Window;
K_Window.superclass=K_Base.prototype;

function K_Window(node,parent)
{
	if (arguments.length>0)
        this.init(node,parent);
}

K_Window.prototype.init=function(node,parent)
{
	K_Window.superclass.init.call(this,$a(node,'id'),node,parent,'window');

	this.posx=$a(node,'x');
	this.posy=$a(node,'y');
	this.width=parseInt($a(node,'w'));
	this.height=parseInt($a(node,'h'));

	this.movable=$a(node,'mov');
	this.resizable=$a(node,'res');
	this.boundaries=$a(node,'boundaries');
	this.minSize=$a(node,'minSize');
	this.maxSize=$a(node,'maxSize');
	this.src=$a(node,'src');
	this.title=$a(node,'title');
	this.icon=$a(node,'icon');
	if(!this.icon)
		this.icon=K_staP+'KWS/img/view_remove.png';
	this.clsBut=$a(node,'clsBut');
	this.maxBut=parseInt($a(node,'maxBut'));
	this.minBut=parseInt($a(node,'minBut'));
	this.statusBar=$a(node,'statusBar');
	this.bgColor=$a(node,'bgColor');
	this.overflow=$a(node,'overflow');
	this.doc=$a(node,'doc');
	this.menu=new Array();
	this.taskbar=null;
	this.isWindow=true;
	this.childWindows=new Array();
	this.status='show';
	this.menuOpened=false;
	this.maximized=false;
	this.iconMenuOpened=false;
	this.locked=false;
	this.locker='';
        this.innerDesktop=false;
	this.zIndex=K_Window.superclass.newZindex.call();

	var t=$t(this.xmlDoc,'window');
	for (var x=0;x<t.length;x++)
		if ($a(t[x],'id')!=this.id && $a(t[x].parentNode,'id')==this.id)
			this.childWindows.push($a(t[x],'id'));
	this.childWindowActive=0;
	var e=$t(this.xmlDoc,'menu');
	if(e.length==1)
	{
		var e=$t($t(this.xmlDoc,'menu')[0],'element');
		for(var x=0;x<e.length;++x)
			this.menu[this.menu.length]=new Array($a(e[x],'id'),$a(e[x],'caption'),$a(e[x],'context_menu'));
	}
	this.build();
}

K_Window.prototype.render=function()
{
	var l=this.boundaries.split(',');
	this.minX=l[0];
	this.minY=l[1];
	this.maxX=l[2];
	this.maxY=l[3];
	l=this.minSize.split(',');
	this.minW=l[0];
	this.minH=l[1];
	l=this.maxSize.split(',');
	this.maxW=l[0];
	this.maxH=l[1];

	var old=$('K_'+this.id);
	if (old!=null)
		K_removeNode(old);

	var pN=$('K_int_'+this.parent);

	if (this.posx=='auto')
	{
		var w=pN.offsetWidth;
		this.posx=Math.round((w-this.width)/2)+(Math.floor((20-4)*Math.random()) + 5);
	}
	else
		this.posx=parseInt(this.posx);
	if (this.posy=='auto')
	{
		var h=pN.offsetHeight;
		this.posy=Math.round((h-this.height)/2)+(Math.floor((20-4)*Math.random()) + 5);
	}
	else
		this.posy=parseInt(this.posy);

	var titx=5;
	var divrec=document.createElement('div');
        divrec.className='window';
	divrec.style.position='absolute';
	divrec.style.left=this.posx+'px';
	divrec.style.top=this.posy+'px';
	divrec.style.width=this.width+'px';
	divrec.style.height=this.height+'px';
	divrec.style.overflow='hidden';
	divrec.style.zIndex=this.zIndex;
	divrec.id='K_'+this.id;
        
        var divLock=document.createElement('div');
	divLock.id='K_lock_'+this.id;
	divLock.style.width='100%';
	divLock.style.height='100%';
	divLock.style.position='absolute';
	divLock.style.left='0px';
	divLock.style.background='#000';
	divLock.style.mozOpacity=".20"; 
	divLock.style.opacity=".20";
	divLock.style.filter="alpha(opacity=20)";

	divLock.style.top='0px';
	divLock.style.display='none';
	divLock.onclick=this.canvasClick;
	divrec.appendChild(divLock);

	if (this.statusBar==1)
	{
		var divsta=document.createElement('div');
		divsta.id='K_stat_'+this.id;
		divsta.className='windowStatusBar defaultFont bgWindowStatusBarActive';
		divsta.onmousedown=this.mouseDown;
		var txsta=document.createElement('div');
		txsta.className='windowStatusBar';
		txsta.id='K_txStat_'+this.id;
		txsta.style.position='absolute';
		txsta.style.left='2px';
		txsta.style.top='2px';
		divsta.appendChild(txsta);
		divrec.appendChild(divsta);
	}
	if (this.resizable==1)
	{
		var divres=document.createElement('div');
		divres.id='K_resize_'+this.id;
		if (this.statusBar==1)
			divres.className='windowResize bgwindowResizeActive';
		else
			divres.className='windowResizeNoStatus';
		divres.onmousedown=this.mouseDownResize;
		divrec.appendChild(divres);
	}

	var divenc=document.createElement('div');
	divenc.id='K_enc_'+this.id;
	divenc.className='windowTitleBar defaultFont bgWindowTitleBarActive';
	divenc.onmousedown=this.mouseDown;
	if (this.resizable==1 && this.maxBut==1)
		divenc.ondblclick=this.clickMaximize;

	if (this.icon!='')
	{
		var ico=document.createElement('img');
		ico.id='K_icon_'+this.id;
		ico.src=this.icon;
		ico.style.position='absolute';
		ico.style.left='2px';
		ico.style.top='0px';
		ico.style.width='16px';
		ico.style.height='16px';
		ico.style.zIndex=this.zIndex;
		if(this.clsBut==1)
			ico.ondblclick=this.dblClickIcon;
		ico.onclick=this.clickIcon;
		titx=22;
		divenc.appendChild(ico);
	}
	else titx=2;

	var but=document.createElement('div');
	if (this.clsBut==1)
	{
		but.className='clsBut bgClsButActive';
		but.onclick=this.clickClose;
	}
	else but.className='clsButOff bgClsButOffActive';
	but.id='K_clsBut_'+this.id;
	but.style.position='absolute';
	but.style.right='2px';
	but.style.top='0px';
	but.style.zIndex=this.zIndex;
	divenc.appendChild(but);

	var but=document.createElement('div');
	if (this.maxBut==1)
	{
		but.className='maxBut bgMaxButActive';
		but.onclick=this.clickMaximize;
	}
	else but.className='maxButOff bgMaxButOffActive';
	but.id='K_maxBut_'+this.id;
	but.style.position='absolute';
	but.style.right='2px';
	but.style.top='0px';
	but.style.zIndex=this.zIndex;
	divenc.appendChild(but);


	var but=document.createElement('div');
	if (this.minBut==1)
	{
		but.className='minBut bgMinButActive';
		but.onmouseup=this.clickMinimize;
	}
	else but.className='minButOff bgMinButOffActive';
	but.id='K_minBut_'+this.id;
	but.style.position='absolute';
	but.style.right='2px';
	but.style.top='0px';
	but.style.zIndex=this.zIndex;
	divenc.appendChild(but);

	var divtitle=document.createElement('div');
	divtitle.id='K_title_'+this.id;
	divtitle.className='windowCaption';
        if(K_ie)
                divtitle.onselectstart=function(){return false};
	divtitle.style.position='absolute';
	divtitle.style.overflow='hidden';
	divtitle.style.left=titx+'px';
	divtitle.style.top='0px';
	var txtitle=document.createTextNode(this.title);
	divtitle.appendChild(txtitle);
	divtitle.style.zIndex=this.zIndex;
	divenc.appendChild(divtitle);
	divrec.appendChild(divenc);

	var divint=document.createElement('div');
	divint.id='K_int_'+this.id;
	divint.style.position='absolute';
	if (this.bgColor!='')
		divint.style.backgroundColor=this.bgColor;
	divint.onclick=this.canvasClick;
	if(this.overflow!='')
		divint.style.overflow=this.overflow;
	else divint.style.overflow='hidden';
	
	if (this.doc!="")
	{
		var ifrint=document.createElement('iframe');
		ifrint.id='K_ifrint_'+this.id;
		ifrint.style.position='absolute';
		ifrint.style.left='0px';
		ifrint.style.top='0px';
		ifrint.style.width='100%';
		ifrint.style.height='100%';
		ifrint.style.border='0';
		ifrint.src=this.doc;
		divint.appendChild(ifrint);
		var divt=document.createElement('div');
		divt.style.background='red';
		divt.style.left='0px';
		divt.style.top='0px';
	}
	divrec.appendChild(divint);

	var divb=document.createElement('div');
	divb.id='K_bl_'+this.id;
	divb.className='windowBorderLeft bgWindowBorderLeftActive';
	divb.style.zIndex=this.zIndex;
	divb.onmousedown=this.mouseDown;
	divrec.appendChild(divb);

	divb=document.createElement('div');
	divb.id='K_br_'+this.id;
	divb.className='windowBorderRight bgWindowBorderRightActive';
	divb.onmousedown=this.mouseDown;
	if (this.resizable==1 && (this.minW!=this.maxW || (this.minW=='auto' || this.maxW=='auto')))
		divb.style.cursor='w-resize';

	divb.onmousedown=this.mouseDownResize;

	divb.style.zIndex=this.zIndex;
	divrec.appendChild(divb);

	divb=document.createElement('div');
	divb.id='K_bt_'+this.id;
	divb.className='windowBorderTop bgWindowBorderTopActive';
	divb.onmousedown=this.mouseDown;
	divrec.appendChild(divb);

	divb=document.createElement('div');
	divb.id='K_bb_'+this.id;
	divb.className='windowBorderBottom bgWindowBorderBottomActive';
	divb.onmousedown=this.mouseDown;
	if (this.resizable==1 && (this.minH!=this.maxH || (this.minH=='auto' || this.maxH=='auto')))
		divb.style.cursor='n-resize';
	divb.onmousedown=this.mouseDownResize;

	divb.style.zIndex=this.zIndex;
	divrec.appendChild(divb);


	divb=document.createElement('div');
	divb.id='K_cTl_'+this.id;
	divb.className='windowTLcorner bgWindowTLcornerActive';
	divb.onmousedown=this.mouseDown;
	divb.style.zIndex=this.zIndex;
	divrec.appendChild(divb);
	divb=document.createElement('div');
	divb.id='K_cTr_'+this.id;
	divb.className='windowTRcorner bgWindowTRcornerActive';
	divb.onmousedown=this.mouseDown;
	divb.style.zIndex=this.zIndex;
	divrec.appendChild(divb);
	divb=document.createElement('div');
	divb.id='K_cBl_'+this.id;
	divb.className='windowBLcorner bgWindowBLcornerActive';
	divb.onmousedown=this.mouseDown;
	divb.style.zIndex=this.zIndex;
	divrec.appendChild(divb);
	divb=document.createElement('div');
	divb.id='K_cBr_'+this.id;
	divb.style.zIndex=this.zIndex;
	divb.className='windowBRcorner bgWindowBRcornerActive';
	divb.onmousedown=this.mouseDown;
	if (this.resizable==1)
		divb.style.cursor='se-resize';
	divb.onmousedown=this.mouseDownResize;

	divrec.appendChild(divb);

	if (this.visible==0)
		divrec.style.visibility='hidden';
	pN.appendChild(divrec);

	var intt=$('K_int_'+this.id);
	var bl=$('K_bl_'+this.id);
	var bbr=$('K_br_'+this.id);
	var bt=$('K_bt_'+this.id);
	var bb=$('K_bb_'+this.id);

	var wBl=bl.offsetWidth;
	var wBr=bbr.offsetWidth;
	var hBt=bt.offsetHeight;
        if(K_ie)
                hBt=1;
	var hBb=bb.offsetHeight;
	var enc=$('K_enc_'+this.id);
	var hEnc=enc.offsetHeight;
	var tit=$('K_title_'+this.id);
	hTit=tit.offsetHeight;
	tit.style.top=Math.floor((hEnc-hTit)/2)+'px';
	tit.style.width=tit.offsetWidth-wBr-wBl+'px';
	if (this.icon)
		$('K_icon_'+this.id).style.top=Math.floor((hEnc-16)/2)+'px';

	var wCls=$('K_clsBut_'+this.id).offsetWidth;
	var hCls=$('K_clsBut_'+this.id).offsetHeight;
	$('K_clsBut_'+this.id).style.top=Math.floor((hEnc-hCls)/2)+'px';
	$('K_clsBut_'+this.id).style.right=wBl+wBr+'px';
	$('K_maxBut_'+this.id).style.right=(wBl+wBr+wCls)+'px';
	var wMax=$('K_maxBut_'+this.id).offsetWidth;
	var hMax=$('K_maxBut_'+this.id).offsetHeight;
	$('K_maxBut_'+this.id).style.top=Math.floor((hEnc-hMax)/2)+'px';
	$('K_minBut_'+this.id).style.right=(wBl+wBr+wMax+wCls)+'px';
	var wMin=$('K_minBut_'+this.id).offsetWidth;
	var hMin=$('K_minBut_'+this.id).offsetHeight;
	$('K_minBut_'+this.id).style.top=Math.floor((hEnc-hMin)/2)+'px';

	var mxWTitle=10+wCls+wMax+wMin+wBr+wBl;
	if (this.icon)
		mxWTitle+=16;
	this.oTitle=mxWTitle;
	mxWTitle=$('K_enc_'+this.id).offsetWidth-mxWTitle;
	if (mxWTitle>0)
		$('K_title_'+this.id).style.width=mxWTitle+'px';


	this.intOx=wBl;
	this.intOy=hEnc+hBt;
	this.intOw=wBl+wBr;
	this.intOh=hEnc+hBt+hBb;

	if (this.statusBar==1)
		this.intOh+=$('K_stat_'+this.id).offsetHeight;

	intt.style.left=this.intOx+'px';
	intt.style.width=this.width-this.intOw+'px';
	if(this.menu.length==0)
	{
		intt.style.top=this.intOy+'px';
		intt.style.height=this.height-this.intOh+'px';
	}
	else
	{
		intt.style.top=this.intOy-0+20+'px';
		intt.style.height=this.height-20-this.intOh+'px';	
		var ulme=document.createElement('ul');
		ulme.className='menuBar';
		ulme.style.top=this.intOy+'px';
		ulme.style.left=this.intOx+'px';
		for(var x=0;x<this.menu.length;++x)
		{
			var lime=document.createElement('li');
			lime.innerHTML='&nbsp;&nbsp;&nbsp;'+this.menu[x][1]+'&nbsp;&nbsp;&nbsp;';
			lime.className='menuElement menuElementOff';
			lime.id='K_'+this.id+'_WM_'+this.menu[x][0];
			lime.setAttribute('cm',this.menu[x][2]);
			lime.setAttribute('win',this.id);
			lime.onmouseover=this.menuElementOver;
			lime.onmouseout=this.menuElementOut;
			lime.onclick=this.menuClick;
			ulme.appendChild(lime);
		}
		$('K_'+this.id).appendChild(ulme);
	}

	enc.style.left=wBl+'px';
	enc.style.top=hBt+'px';
	if (this.statusBar==1)
	{
		sta=$('K_stat_'+this.id);
		sta.style.left=wBl+'px';
		sta.style.bottom=hBb+'px';
		if (this.resizable==1)
		{
			var ir=$('K_resize_'+this.id);
			ir.style.bottom=hBb+1+'px';
			ir.style.right=wBr+1+'px';
		}
	}

	this.cropTitle();
	this.bringToFront();
	this.renderChilds();
	if(typeof window[this.parent]=='object' && window[this.parent].type!='window')
		window[this.parent].addWindow(this.id);
}

K_Window.prototype.menuClick=function()
{
	var cm=$a(this,'cm');
	var win=$a(this,'win');
	if(window[win].menuOpened)
	{
		K_clearContextMenu();
		window[win].menuOpened=false;
		return;
	}
	window[win].bringToFront();
	window[win].menuOpened=true;
	window[cm].caller=win;
	window[win].showMenu(this.id.replace('K_'+this.id+'_WM_',''),cm);
}

K_Window.prototype.showMenu=function(id,cm)
{
	K_clearContextMenu();
	var x=this.posx-0+this.menuElementOffset(id)+this.intOw;
	var y=this.posy-0+this.intOy+20;
	if(K_ie && !K_op)
		y+=3;
	K_lastRightClickX=x;
	K_lastRightClickY=y;
	window[cm].moveTo(x,y);
	window[cm].show();
	K_CMactive=cm;
}

K_Window.prototype.menuElementOffset=function(id)
{
	var i=id.replace('K_'+this.id+'_WM_','');
	var offset=0;
	for(var x=0;x<this.menu.length;++x)
	{
		if(this.menu[x][0]!=i)
			offset+=$(id).offsetWidth;
		else break; 
	}
	return offset;
}

K_Window.prototype.menuElementOver=function()
{
	this.className='menuElement menuElementOn';
	var win=$a(this,'win');
	var cm=$a(this,'cm');
	if(window[win].menuOpened)
		window[win].showMenu(this.id.replace('K_'+this.id+'_WM_',''),cm);
}

K_Window.prototype.menuElementOut=function()
{
	this.className='menuElement menuElementOff';
}


K_Window.prototype.addToTaskbar=function(taskbar)
{
	this.taskbar=taskbar;
	window[this.taskbar].addWindow(this.id);
}

K_Window.prototype.cropTitle=function()
{
	var p=$('K_title_'+this.id);
	var h=p.offsetHeight;
	var c=0;
	p.innerHTML=this.title.substr(0,this.title.length-c);
	h=p.offsetHeight;
	while (h>20)
	{
		++c;
		p.innerHTML=this.title.substr(0,this.title.length-c)+'...';
		h=p.offsetHeight;
	}
	if (p.offsetWidth<15)
		p.innerHTML='';
	p.style.top=Math.floor(($('K_enc_'+this.id).offsetHeight-h)/2)+'px';
}


K_Window.prototype.mouseDown=function(e)
{
	K_clearContextMenu();
	K_resize=0;
	K_idDrag=this.id.replace('K_fH_','');
	K_idDrag=K_idDrag.replace('K_stat_','');
	K_idDrag=K_idDrag.replace('K_bl_','');
	K_idDrag=K_idDrag.replace('K_br_','');
	K_idDrag=K_idDrag.replace('K_bb_','');
	K_idDrag=K_idDrag.replace('K_bt_','');
	K_idDrag=K_idDrag.replace('K_cTl_','');
	K_idDrag=K_idDrag.replace('K_cTr_','');
	K_idDrag=K_idDrag.replace('K_cBl_','');
	K_idDrag=K_idDrag.replace('K_cBr_','');

	K_idDrag=K_idDrag.replace('K_','');
	K_idDrag=K_idDrag.replace('enc_','');

	window[K_idDrag].bringToFront();

	if (K_ie)
		window[K_idDrag].hideIframe();

	
	if (window[K_idDrag].movable!='1' || window[K_idDrag].maximized)
        {
                K_idDrag='';
		return;
        }

	var minX=window[K_idDrag].minX;
	var maxX=window[K_idDrag].maxX;
	var minY=window[K_idDrag].minY;
	var maxY=window[K_idDrag].maxY;

	if (minX=='auto')
	{
		if(typeof window[K_idDrag].parent=='object' && window[window[K_idDrag].parent].isWindow)
			K_moveMinX=0;
		else K_moveMinX=-5000;
	}
	else
		K_moveMinX=parseInt(minX);
	if (minY=='auto')
		K_moveMinY=0;
	else
		K_moveMinY=parseInt(minY);
	if (maxX=='auto')
	{
		if(typeof window[K_idDrag].parent=='object' && window[window[K_idDrag].parent].isWindow)
			K_moveMaxX=window[window[K_idDrag].parent].width-window[K_idDrag].width-8;
		else K_moveMaxX=5000;
	}
	else
		K_moveMaxX=maxX - window[K_idDrag].width;
	if (maxY=='auto')
	{		
		if(typeof window[K_idDrag].parent=='object' && window[window[K_idDrag].parent].isWindow)
		{
			if(window[window[K_idDrag].parent].statusBar==0)
				K_moveMaxY=window[window[K_idDrag].parent].height-window[K_idDrag].height-28;
			else K_moveMaxY=window[window[K_idDrag].parent].height-window[K_idDrag].height-48;
		}
		else K_moveMaxY=$('K_int_'+window[K_idDrag].parent).offsetHeight ;
	}
	else
		K_moveMaxY=maxY - window[K_idDrag].height;

	var x_inidiv=window[K_idDrag].posx;
	var y_inidiv=window[K_idDrag].posy;

	if (K_ie ||K_op)
	{
		var x_inidoc=event.clientX;
		var y_inidoc=event.clientY;
	}
	if (K_gec)
	{
		var x_inidoc=e.pageX;
		var y_inidoc=e.pageY;
	}
	K_difX=x_inidoc-x_inidiv;
	K_difY=y_inidoc-y_inidiv;
	

	return false;
}

K_Window.prototype.mouseDownResize=function(e)
{
	K_resize=1;

	var idx=this.id.substr(2,2);
	if(idx=='br')
		K_resizingX=1;
	if(idx=='bb')
		K_resizingY=1;

	K_idDrag=this.id.replace('K_resize_','');
	K_idDrag=K_idDrag.replace('K_br_','');
	K_idDrag=K_idDrag.replace('K_bb_','');
	K_idDrag=K_idDrag.replace('K_cTl_','');
	K_idDrag=K_idDrag.replace('K_cTr_','');
	K_idDrag=K_idDrag.replace('K_cBl_','');
	K_idDrag=K_idDrag.replace('K_cBr_','');
	if (K_gec==1 || K_ie==1)
		window[K_idDrag].hideIframe();

	window[K_idDrag].bringToFront();

	var res=window[K_idDrag].resizable;
	if (res!='1')
        {
                K_idDrag='';
		return;
        }


	var minW=window[K_idDrag].minW;
	var maxW=window[K_idDrag].maxW;
	var minH=window[K_idDrag].minH;
	var maxH=window[K_idDrag].maxH;

	if (maxW=='auto')
		K_resizeMaxW=$('K_int_'+window[K_idDrag].parent).offsetWidth - window[K_idDrag].posx;
	else
		K_resizeMaxW=parseInt(maxW);
	if (maxH=='auto')
		K_resizeMaxH=$('K_int_'+window[K_idDrag].parent).offsetHeight - window[K_idDrag].posy;
	else
		K_resizeMaxH=parseInt(maxH);
	if (minW=='auto')
		K_resizeMinW=0;
	else
		K_resizeMinW=parseInt(minW);
	if (minH=='auto')
		K_resizeMinH=0;
	else
		K_resizeMinH=parseInt(minH);

	var taskbar=window[K_idDrag].taskbar;
	if (taskbar!=null)
	{
		K_resizeMaxH-=$('K_'+taskbar).offsetHeight;
	}

	K_wIni=window[K_idDrag].width;
	K_hIni=window[K_idDrag].height;

	if (K_ie ||K_op)
	{
		K_xIni=event.clientX;
		K_yIni=event.clientY;
	}
	if (K_gec)
	{
		K_xIni=e.pageX;
		K_yIni=e.pageY;
	}
	return false;
}


K_Window.prototype.moveTo=function(px,py)
{
	var obj=$('K_'+this.id);
	if (!isNaN(px)) this.posx=px;
	if (!isNaN(py)) this.posy=py;
	obj.style.left=this.posx+'px';
	obj.style.top=this.posy+'px';
	var t=typeof window[this.id+"_cbMove"];
	if (t=='function')
		window[this.id+'_cbMove'](px,py);
}

K_Window.prototype.resizeTo=function(w,h)
{
	if (w<66 ||h<20)
		return;
	var obj=$('K_'+this.id);
	if (!isNaN(w)) this.width=w;
	if (!isNaN(h)) this.height=h;
	obj.style.width=this.width+'px';
	obj.style.height=this.height+'px';
	if (this.status!='minimized')
	{
		var i=$('K_int_'+this.id);
		i.style.width=(this.width-this.intOw)+'px';
		i.style.height=(this.height-this.intOh)+'px';
                if(this.statusBar==1 && !this.innerDesktop)
                        i.style.height=(this.height-this.intOh-$('K_stat_'+this.id).offsetHeight-1)+'px';
               
	}
	$('K_title_'+this.id).style.width=w-this.oTitle+'px';
	this.cropTitle();
	for (var x=0;x<this.childWindows.length;x++)
		if (window[this.childWindows[x]].status=='maximized')
		{
			window[this.childWindows[x]].status='show';
			window[this.childWindows[x]].clickMaximize(0,1);
		}
}

K_Window.prototype.endResize=function()
{
	if (K_gec==1 || K_ie==1)
		this.showIframe();
	var t=typeof window[this.id+"_cbResizeEnd"];
	if (t=='function')
		window[this.id+'_cbResizeEnd']();

}

K_Window.prototype.endMove=function()
{
	if (K_ie)
		this.showIframe();
	var t=typeof window[this.id+"_cbMoveEnd"];
	if (t=='function')
		window[this.id+'_cbMoveEnd']();
}


K_Window.prototype.hideIframe=function()
{
	var ifrs=$t($('K_'+this.id),'iframe');
	for (var x=0;x<ifrs.length;x++)
		ifrs[x].style.display='none';
}


K_Window.prototype.showIframe=function()
{
	var ifrs=$t($('K_'+this.id),'iframe');
	for (var x=0;x<ifrs.length;x++)
		ifrs[x].style.display='block';
}

K_Window.prototype.lockContent=function(val,locker)
{
	if(val==true)
	{
		$('K_lock_'+this.id).style.zIndex=99999;
		$('K_lock_'+this.id).style.display='block';
		this.locked=true;
		this.locker=locker;
	}
	else
	{
		$('K_lock_'+this.id).style.display='none';
		this.locked=false;
		this.locker='';	
	}
}

K_Window.prototype.drawInactive=function()
{
        $('K_'+this.id).className='windowInactive';
        
	if(this.status=='active')
		this.status='inactive';
	if ($('K_enc_'+this.id))
		if ($('K_enc_'+this.id).className=='windowTitleBar defaultFont bgWindowTitleBarInactive')
			return;

	for (var x=0;x<this.childWindows.length;x++)
		window[this.childWindows[x]].drawInactive();

	$('K_enc_'+this.id).className='windowTitleBar defaultFont bgWindowTitleBarInactive';
	if (this.clsBut==1)
		$('K_clsBut_'+this.id).className='clsBut bgClsButInactive';
	else
		$('K_clsBut_'+this.id).className='clsBut bgClsButOffInactive';
	if (this.maxBut==1)
	{
		if (this.status=='maximized')
			$('K_maxBut_'+this.id).className='rstBut bgRstButInactive';
		else
			$('K_maxBut_'+this.id).className='maxBut bgMaxButInactive';
	}
	else
		$('K_maxBut_'+this.id).className='maxBut bgMaxButOffInactive';
	if (this.minBut==1)
	{
		if (this.status=='minimized')
			$('K_minBut_'+this.id).className='rstBut bgRstButInactive';
		else
			$('K_minBut_'+this.id).className='minBut bgMinButInactive';
	}
	else
		$('K_minBut_'+this.id).className='minBut bgMinButOffInactive';
	$('K_bl_'+this.id).className='windowBorderLeft bgWindowBorderLeftInactive';
	$('K_br_'+this.id).className='windowBorderRight bgWindowBorderRightInactive';
	$('K_bt_'+this.id).className='windowBorderTop bgWindowBorderTopInactive';
	$('K_bb_'+this.id).className='windowBorderBottom bgWindowBorderBottomInactive';
	$('K_cTl_'+this.id).className='windowTLcorner bgWindowTLcornerInactive';
	$('K_cTr_'+this.id).className='windowTRcorner bgWindowTRcornerInactive';
	$('K_cBl_'+this.id).className='windowBLcorner bgWindowBLcornerInactive';
	$('K_cBr_'+this.id).className='windowBRcorner bgWindowBRcornerInactive';
	if (this.statusBar==1)
		$('K_stat_'+this.id).className='windowStatusBar defaultFont bgWindowStatusBarInactive';
	if (this.resizable==1)
		if (this.statusBar==1)
			$('K_resize_'+this.id).className='windowResize bgWindowResizeInactive';

	if (K_op==1 || K_ko==1)
		this.hideIframe();
}


K_Window.prototype.drawActive=function()
{
        $('K_'+this.id).className='window';
        
	this.status='active';
	if ($('K_enc_'+this.id).className=='windowTitleBar defaultFont bgWindowTitleBarActive')
		return;

	var cwa=this.childWindows[this.childWindowActive];
	if (cwa)
		eval(cwa+".drawActive();");
	$('K_enc_'+this.id).className='windowTitleBar defaultFont bgWindowTitleBarActive';
	if (this.clsBut==1)
		$('K_clsBut_'+this.id).className='clsBut bgClsButActive';
	else
		$('K_clsBut_'+this.id).className='clsBut bgClsButOffActive';
	if (this.maxBut==1)
	{
		if (this.maximized)
			$('K_maxBut_'+this.id).className='rstBut bgRstButActive';
		else
			$('K_maxBut_'+this.id).className='maxBut bgMaxButActive';
	}
	else
		$('K_maxBut_'+this.id).className='maxBut bgMaxButOffActive';
	if (this.minBut==1)
	{
		if (this.status=='minimized')
			$('K_minBut_'+this.id).className='rstBut bgRstButActive';
		else
			$('K_minBut_'+this.id).className='minBut bgMinButActive';
	}
	else
		$('K_minBut_'+this.id).className='minBut bgMinButOffActive';
	$('K_bl_'+this.id).className='windowBorderLeft bgWindowBorderLeftActive';
	$('K_br_'+this.id).className='windowBorderRight bgWindowBorderRightActive';
	$('K_bt_'+this.id).className='windowBorderTop bgWindowBorderTopActive';
	$('K_bb_'+this.id).className='windowBorderBottom bgWindowBorderBottomActive';
	$('K_cTl_'+this.id).className='windowTLcorner bgWindowTLcornerActive';
	$('K_cTr_'+this.id).className='windowTRcorner bgWindowTRcornerActive';
	$('K_cBl_'+this.id).className='windowBLcorner bgWindowBLcornerActive';
	$('K_cBr_'+this.id).className='windowBRcorner bgWindowBRcornerActive';
	if (this.statusBar==1)
		$('K_stat_'+this.id).className='windowStatusBar defaultFont bgWindowStatusBarActive';
	if (this.resizable==1)
		if (this.statusBar==1)
			$('K_resize_'+this.id).className='windowResize bgWindowResizeActive';
	if (K_op==1 || K_ko==1)
		this.showIframe();
}

K_Window.prototype.bringToFront=function()
{
	var t=$('K_'+this.id);
	this.status='active';
	if(t.style.visibility=='hidden')
		t.style.visibility='visible';
        if(window[this.parent])
                window[this.parent].addWindow(this.id);
	if (this.taskbar!=null)
	{
		window[this.taskbar].active=0;
		window[this.taskbar].setActive(this.id);
	}
	$('K_'+this.id).style.zIndex=this.newZindex();
	if(typeof this.parent == 'object')
	{
		var pw=window[this.parent].isWindow;
		if (pw)
			window[this.parent].setChildWindowActive(this.id);
	}
	if(this.locked==true)
	{
		window[this.locker].bringToFront();
		return;
	}
	var t=typeof window[this.id+"_cbBringToFront"];
	if (t=='function')
		window[this.id+'_cbBringToFront']();
}

K_Window.prototype.canvasClick=function()
{
	var a=this.id.replace('K_int_','');
	a=a.replace('K_lock_','');
	window[a].menuOpened=false;
	K_clearContextMenu();
	window[a].bringToFront();
}

K_Window.prototype.clickMinimize=function()
{
	var i=this.id.replace('K_minBut_','');
        
        if(window[i].minBut<1)
                return;

	if (window[i].maxBut==1)
		$('K_maxBut_'+i).className='maxBut bgMaxButActive';
	
	var status=window[i].status;
	var taskbar=window[i].taskbar;
	if (taskbar!=null)
	{
		window[i].status='minimized';
		window[taskbar].activateNextTab(i);
		window[i].hide();
		var t=typeof window[this.id+"_cbMinimize"];
		if (t=='function')
			window[this.id+'_cbMinimize']();
	}
	else
	{
		if (status=='minimized')
		{
			window[i].status='active';
			window[i].moveTo(window[i].saveX,window[i].saveY);
			window[i].resizeTo(window[i].saveW,window[i].saveH);
			$('K_minBut_'+i).className='minBut bgMinButActive';
			return;
		}
		else
		{
			window[i].status='minimized';
			$('K_minBut_'+i).className='rstBut bgRstButActive';
			window[i].saveX=window[i].posx;
			window[i].saveY=window[i].posy;
			window[i].saveW=window[i].width;
			window[i].saveH=window[i].height;
			window[i].minimizeChildWindow(i);
		}
	}
}

K_Window.prototype.clickMaximize=function(e,val)
{
	var nx=0;
	var ny=0;
	var nw=0;
	var nh=0;
	var i=this.id.replace('K_maxBut_','');
	var i=i.replace('K_enc_','');
	var st=window[i].status;
	var mx=window[i].maxBut;
	var mn=window[i].minBut;
	if (window[i].maximized)
	{
		window[i].restore();
		return;
	}
	var min=0;
	if (st=='minimized')
		min=1;

	if (mn==1)
		$('K_minBut_'+i).className='minBut bgMinButActive';

	if (mx==1)
		$('K_maxBut_'+i).className='rstBut bgRstButActive';
	window[i].maximized=true;
	var minX=window[i].minX;
	var minY=window[i].minY;
	if (minX=='auto')
		nx=0;
	else
		nx=minX;
	if (minY=='auto')
		ny=0;
	else
		ny=minY;
	var maxW=window[i].maxW;
	var maxH=window[i].maxH;

	if (maxW=='auto')
		nw=$('K_int_'+window[i].parent).offsetWidth - nx;
		
	else
		nw=maxW;
	if (maxH=='auto')
		nh=$('K_int_'+window[i].parent).offsetHeight - ny;
		
	else
		nh=maxH;
	var taskbar=window[i].taskbar;
	if (taskbar!=null)
	{
		nh-=$('K_'+taskbar).offsetHeight;
	}
	if (val!=1 && min==0)
	{
		window[i].saveX=window[i].posx;
		window[i].saveY=window[i].posy;
		window[i].saveW=window[i].width;
		window[i].saveH=window[i].height;
	}
	window[i].moveTo(nx,ny);
	window[i].resizeTo(nw,nh);
	for (var x=0;x<window[i].childWindows.length;x++)
	{	
		if (window[i].childWindows[x].status=='maximized')
		{
			window[i].childWindows[x].status='show';
			window[i].childWindows[x].clickMaximize(0,1);
		}
	}
	window[i].endResize();
}

K_Window.prototype.clickClose=function()
{
	var i=this.id.replace('K_clsBut_','');
	window[i].destroy();
}


K_Window.prototype.restore=function()
{
	if (this.maxBut==1)
		$('K_maxBut_'+this.id).className='maxBut bgMaxButActive';
	this.resizeTo(this.saveW,this.saveH);
	this.moveTo(this.saveX,this.saveY);
	for (var x=0;x<this.childWindows.length;x++)
		if (window[this.childWindows[x]].status=='maximized')
		{
			window[this.childWindows[x]].status='show';
			window[this.childWindows[x]].clickMaximize(0,1);
		}
	this.status='active';
	this.maximized=false;
        window[this.parent].addWindow(this.id);
	setTimeout(this.id+".cropTitle();",10);
	this.endResize();
}


K_Window.prototype.hide=function()
{
	$('K_'+this.id).style.visibility='hidden';
        window[this.parent].removeWindow(this.id);
	var t=typeof window[this.id+"_cbMinimize"];
	if (t=='function')
		window[this.id+'_cbMinimize']();
}

K_Window.prototype.show=function()
{
	$('K_'+this.id).style.visibility='visible';
	window[this.parent].addWindow(this.id);
}

K_Window.prototype.destroy=function()
{
        var t=typeof window[this.id+"_destroy"];
        var okDest=true;
        if (t=='function')
		okDest=window[this.id+'_destroy']();
        if(okDest)
        {
                if (this.taskbar!=null)
                        window[this.taskbar].removeTab(this.id);

                K_removeNode('K_'+this.id);
                if(this.menu.length>0)
                        for(var x=0;x<this.menu.length;++x)
                        {
                                window[this.menu[x][2]].destroy();
                                window[this.menu[x][2]]=null;
                        }
                if(typeof window[this.parent]=='object')
                        window[this.parent].removeWindow(this.id);
                window[this.id]=null;
        }
}

K_Window.prototype.dblClickIcon=function()
{
	var i=this.id.replace('K_icon_','');
	window[i].clickClose();
        K_clearContextMenu();
        return false;
}
K_Window.prototype.clickIcon=function(e)
{
	var i=this.id.replace('K_icon_','');
	if(window[i].taskbar)
	{
		var t=window[window[i].taskbar].contextMenu;
		if (t!=null)
		{
			var x=y=0;
			if (K_ie || K_op){x=event.clientX;y=event.clientY;}
			if (K_gec){x=e.pageX;y=e.pageY;}
			x=window[i].posx+4;
			y=window[i].posy+24;
			if(K_ie) y+=4;
			if(window[i].minBut)
				window[t].setItemState('K_WCM_MINI',0);
			else window[t].setItemState('K_WCM_MINI',1);
			if(window[i].maxBut && !window[i].maximized)
				window[t].setItemState('K_WCM_MAXI',0);
			else window[t].setItemState('K_WCM_MAXI',1);
			if(window[i].maximized)
                        {
				window[t].setItemState('K_WCM_REST',0);
                                window[t].setItemState('K_WCM_MOVE',1);
                        }
			else
                        {
                                window[t].setItemState('K_WCM_REST',1);
                                window[t].setItemState('K_WCM_MOVE',0);
                        }
                        if(window[i].movable!='1')
                                window[t].setItemState('K_WCM_MOVE',1);
			K_clearContextMenu();
			window[t].caller=i;
			K_lastRightClickX=x;
			K_lastRightClickY=y;
			window[t].moveTo(x,y);
			window[t].show();
			K_CMactive=t;
		}
	}
}
K_Window.prototype.setChildWindowActive=function(id)
{
	for (var x=0;x<this.childWindows.length;x++)
	{
		if (this.childWindows[x]==id)
		{
			this.childWindowActive=x;
			window[id].drawActive();
		}
		else if($('K_int_'+this.childWindows[x]))
			window[this.childWindows[x]].drawInactive();
	}
}

K_Window.prototype.minimizeChildWindow=function(id)
{
	var mw=175;
	var mh=$('K_enc_'+this.id).offsetHeight+$('K_bt_'+this.id).offsetHeight;
	var w=$('K_int_'+this.id).offsetWidth;
	var h=$('K_int_'+this.id).offsetHeight;
	var tx=-mw;
	var ty=h-mh;
	window[id].resizeTo(mw,mh);

	for (var x=0;x<this.childWindows.length;x++)
	{
		var min=window[this.childWindows[x]].status;
		if (min=='minimized')
		{
			if (tx+mw*2>w)
			{
				tx=0;
				ty-=mh;
			}
			else
				tx+=mw;
			window[this.childWindows[x]].moveTo(tx,ty);
		}
	}
}

K_Window.prototype.setDoc=function(src)
{
	this.doc=src;
	if ($('K_ifrint_'+this.id))
		$('K_ifrint_'+this.id).src=src;
}

K_Window.prototype.setStatus=function(tx, timeout)
{
	if (this.statusBar!=1)
		return;
	$('K_txStat_'+this.id).innerHTML=tx;
	if (timeout!=0 && !isNaN(timeout))
		this.timeOut=setTimeout(this.id+".setStatus('');",timeout*1000);
}


K_Window.prototype.getStatus=function()
{
	return $('K_txStat_'+this.id).innerHTML;
}


K_Window.prototype.setTitle=function(tx)
{
	this.title=tx;
	$('K_title_'+this.id).innerHTML=tx;
	if (this.taskbar!=null)
		window[this.taskbar].cropTitles();
}


K_Window.prototype.getTitle=function(tx)
{
	return this.title;
}

