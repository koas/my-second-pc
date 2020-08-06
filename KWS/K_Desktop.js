K_Desktop.prototype=new K_Base();
K_Desktop.prototype.constructor=K_Desktop;
K_Desktop.superclass=K_Base.prototype;

function K_Desktop(node,parent)
{
	if (arguments.length>0)
		this.init(node,parent);
}

K_Desktop.prototype.init=function(node,parent)
{
	K_Desktop.superclass.init.call(this,$a(node,'id'),node,parent,'desktop');
	this.bgColor=$a(node,'color');
	this.image=$a(node,'image');
	this.isMain=parseInt($a(node,'main'));
	this.image_repeat=$a(node,'image_repeat');
	this.taskbar=null;
	this.contextMenu=$a(node,'context_menu');
	this.scColor=$a(node,'scColor');
	this.shortcutList=new Array();
	this.windowList=new Array();
	this.xIni=0;
	this.yIni=0;
	this.SCcloned=false;
	this.numSCsel=0;
	this.SCmain='';
	this.recDeskInv=0;
	this.offsetX=0;
	this.offsetY=0;
	this.build();
}

K_Desktop.prototype.render=function()
{
	if ($('K_int_'+this.id)!=null)
		K_removeNode('K_int_'+this.id);

	var desktop=document.createElement('div');
	if (K_ie && 0)
		desktop.onselectstart=function () {return false;};
	desktop.id='K_int_'+this.id;
	desktop.style.backgroundColor=this.bgColor;
	desktop.style.position='absolute';
	desktop.style.textAlign='left';
	desktop.style.left='0px';
	
	desktop.style.top='0px';
	desktop.style.height='100%';
	this.curOut=true;
	
	desktop.style.width='100%';
	

	desktop.desktop=this.id;
	
	desktop.onmousedown=this.leftMouseDown;

	if(!this.contextMenu!=0)
	{
		if (K_ie)
			desktop.oncontextmenu=this.rightMouseDown;
		else if (document.addEventListener)
			desktop.addEventListener('contextmenu',this.rightMouseDown,true);
	}
	
	if (this.image)
	{
		desktop.style.backgroundImage="url('"+this.image+"')";
		if (this.image_repeat=='1')
		{
			desktop.style.backgroundRepeat='no-repeat';
			desktop.style.backgroundPosition='center center';
			desktop.style.backgroundSize='100% 100%';
			desktop.style.webkitBackgroundSize='100% 100%';
			desktop.style.oBackgroundSize='100% 100%';
			desktop.style.khtmlBackgroundSize='100% 100%';
		}
	}

	if($('K_loadIcon')==null)
	{
		var dl=document.createElement('div');
		dl.id='K_loadIcon';
		dl.style.position='absolute';
		dl.style.right='0px';
		dl.style.top='0px';
		dl.style.width='60px';
		dl.style.background='#FFF';
		dl.style.border='1px solid #000';
		dl.style.display='none';
		dl.innerHTML="<img src='"+K_staP+"icons/network_32.png' align='absmiddle'/> <span class='loading'></span>";
		desktop.appendChild(dl);
	}
	var bounce=document.createElement('img');
	bounce.id='K_ImgIconBounce';
	bounce.style.position='absolute';
	bounce.style.left='0px';
	bounce.style.top='0px';
	bounce.src=K_staP+'KWS/img/trans.png';
	desktop.appendChild(bounce);
	
	var ifr=document.createElement('iframe');
	ifr.id='K_Desktop_ifr';
	ifr.style.position='absolute';
	ifr.style.left='0px';
	ifr.style.top='0px';
	ifr.style.width='0px';
	ifr.style.height='0px';
	ifr.frameBorder=0;
	desktop.appendChild(ifr);
	
	$('K_int_'+this.parent).appendChild(desktop);

	this.renderChilds();
}

K_Desktop.prototype.sumSCsize=function()
{
	if(this.isMain) return;
	var nF=0;
	var nD=0;
	var s=0;
	for(var x=0;x<this.shortcutList.length;++x)
		if(window[this.shortcutList[x]].selected)
		{
			if(window[this.shortcutList[x]].type=='file')
			{
				++nF;
				s+=window[this.shortcutList[x]].size;
			}
			if(window[this.shortcutList[x]].type=='folder')
				++nD;
		}
	var cad='';
	if(nD>0)
	{
		cad+=nD+' ';
		if(nD==1)
			cad+=K_L_foldersSelected1;
		else cad+=K_L_foldersSelectedn;
	}
	if(nF>0)
	{
		if(nD>0)
			cad+=",&nbsp;";
		cad+=nF+' ';
		if(nF==1)
			cad+=K_L_filesSelected1;
		else cad+=K_L_filesSelectedn;
		cad+='&nbsp;('+K_fileSize(s)+')';
	}
	window[this.parent].setStatus(cad);
}
K_Desktop.prototype.addShortcut=function(id)
{
	var e=false;
	for(var x=0;x<this.shortcutList.length;++x)
		if(this.shortcutList[x]==id)
		{
			e=true;
			break;
		}
	if(!e)
		this.shortcutList[this.shortcutList.length]=id;	
}

K_Desktop.prototype.removeShortcut=function(id)
{
	for(var x=0;x<this.shortcutList.length;++x)
		if(this.shortcutList[x]==id)
		{
			window[this.shortcutList[x]].destroy();
			window[this.shortcutList[x]]=null;
			this.shortcutList.splice(x,1);
		}
}
K_Desktop.prototype.removeAllShortcuts=function()
{
	for(var x=0;x<this.shortcutList.length;++x)
	{
		window[this.shortcutList[x]].destroy();
		window[this.shortcutList[x]]=null;
	}

	this.shortcutList=new Array();
}

K_Desktop.prototype.reloadShortcuts=function()
{
	this.removeAllShortcuts();
	K_loadAJAX(K_dynP+'apps/desktop/loadShortcuts.php',this.id+'.resReloadShortcuts');
}

K_Desktop.prototype.resReloadShortcuts=function(xmlDoc)
{
	var brW=document.body.offsetWidth;var brH=document.body.offsetHeight;
	var sc=$t(xmlDoc,'sc');
	if(sc.length>0)
	for(var x=0;x<sc.length;++x)
	{
		window[$a(sc[x],'id')]=new K_Shortcut(sc[x],K_appCanvas);
		if($a(sc[x],'x')>brW) window[$a(sc[x],'id')].oldx=brW-75;if($a(sc[x],'y')>brH) window[$a(sc[x],'id')].oldy=brH-95;
		window[$a(sc[x],'id')].render();
	}	
}

K_Desktop.prototype.deselectShortcuts=function()
{
	for(var x=0;x<this.shortcutList.length;++x)
		window[this.shortcutList[x]].deselect();
	this.sumSCsize();
}

K_Desktop.prototype.getSelectedShortcuts=function()
{
	var list=new Array();
	for(var x=0;x<this.shortcutList.length;++x)
		if(window[this.shortcutList[x]].selected)
			list[list.length]=this.shortcutList[x];
	return list;
}

K_Desktop.prototype.getSelectedShortcutsPath=function()
{
	var list=new Array();
	for(var x=0;x<this.shortcutList.length;++x)
		if(window[this.shortcutList[x]].selected)
			list[list.length]=window[this.shortcutList[x]].path;
	return list;
}

K_Desktop.prototype.leftMouseDown=function(e)
{
	var src='';
	if (K_ie)
		src=window.event.srcElement.id;
	else
		src=e.target.id;
	var but=0;
	if(K_ie)
		but=window.event.button;
	else but=e.which;
	if (src=='' || (src!=this.id && src!='K_'+this.taskbar+'_clock') || but!=1) return;
	
	var desktopID=this.id.replace('K_int_','');
	var evtobj=window.event ? event : e;
	if (evtobj.ctrlKey==true)
	{
		window[desktopID].rightMouseDown(e,1);
		return;
	}
	
	if(src=='K_'+this.taskbar+'_clock')
	{
		if(src=='K_'+this.taskbar+'_clock')
			window[this.taskbar].clockRightMouseDown();
		if (K_gec)
		{
			e.preventDefault();
			e.stopPropagation();
		}
		return false;
	}

	K_clearContextMenu();
	window[desktopID].uncloneShortcuts();
	window[desktopID].deselectShortcuts();	
	
	if (this.taskbar!=null)
	{
		window[this.taskbar].active=0;
		window[this.taskbar].clickStartTab();
	}
	var x=y=0;
	if (K_ie || K_op)
	{
		x=event.offsetX;
		y=event.offsetY;
	}
	if (K_gec)
	{
		x=e.layerX;
		y=e.layerY;
	}	
	
	var rec=document.createElement('div');
	K_recDesktop=desktopID;
	rec.id='K_recDesktop';
	rec.style.position='absolute';
	rec.style.left=x+'px';
	rec.style.top=y+'px';
	rec.style.width='1px';
	rec.style.height='1px';
	rec.className='recDesktop';	
	window[desktopID].xIni=x;
	window[desktopID].yIni=y;
	$(this.id).appendChild(rec);
	if(!window[desktopID].isMain)
		window[K_appCanvas].deselectShortcuts();
}

K_Desktop.prototype.rightMouseDown=function(e,skip)
{	
	var src='';
	if (K_ie)
		src=window.event.srcElement.id;
	else
		src=e.target.id;
	
	var i=this.id;
	if(skip==1) i=src;
	if (src=='' || (src!=i && src!='K_'+this.taskbar+'_ul' && src!='K_'+this.taskbar+'_clock')) return;

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
	
	if(src=='K_'+this.taskbar+'_ul' || src=='K_'+this.taskbar+'_clock')
	{
		if(src=='K_'+this.taskbar+'_ul')
			window[this.taskbar].taskbarRightMouseDown(x,y);
		if(src=='K_'+this.taskbar+'_clock')
			window[this.taskbar].clockRightMouseDown();
		if (K_gec)
		{
			e.preventDefault();
			e.stopPropagation();
		}
		return false;
	}
	
	var i=src.replace('K_int_','');
	i=i.replace('K_sca_','');
	i=i.replace('K_icon_','');
	i=i.replace('K_scn_','');
	i=i.replace('K_int_','');
	K_CM_dsktp=i;
	if (typeof window[i]!='object') return;
	K_clearContextMenu();
	var t=window[i].contextMenu;
	
	if (t!=null)
	{
		if(!window[i].isMain)
		{
			window['M2PC_DSKTP1_CM'].setItemState('separatorGraph',2);
			window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmConfGraphics',2);
			window['M2PC_DSKTP1_CM'].setItemState('separatorLogout',2);
			window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmLock',2);
			window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmExit',2);
		}
		else
		{
			if(K_clipboard.length<1)
				window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmPaste',1);
			else window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmPaste',0);
			window['M2PC_DSKTP1_CM'].setItemState('separatorGraph',0);
			window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmConfGraphics',0);
			window['M2PC_DSKTP1_CM'].setItemState('separatorLogout',0);
			window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmLock',0);
			window['M2PC_DSKTP1_CM'].setItemState('M2PC_DSKTP1_CM_cmExit',0);		
		}
		window[t].caller=i;
		K_lastRightClickX=x;
		K_lastRightClickY=y;
		window[t].moveTo(x,y);
		window[t].show();
		K_CMactive=t;
		if (K_gec)
		{
			e.preventDefault();
			e.stopPropagation();
		}
		return false;
	}
}

K_Desktop.prototype.moveRecDesktop=function(x,y)
{
	var x1=this.xIni;
	var y1=this.yIni;
	var nw=x-this.xIni;
	var nh=y-this.yIni;
	if(nh>=0 && nw>0)
		this.recDeskInv=0;
	if(nh<0)
	{
		$('K_recDesktop').style.top=y+'px';
		y1=y;
		nh=this.yIni-y;
		this.recDeskInv=1;
	}
	if(nw<0)
	{
		x1=x;
		$('K_recDesktop').style.left=x+'px';
		nw=this.xIni-x;
		this.recDeskInv=1;
	}
	$('K_recDesktop').style.width=nw+'px';
	$('K_recDesktop').style.height=nh+'px';

	for (var i=0;i<this.shortcutList.length;++i)
	{
		var pX=window[this.shortcutList[i]].posx;
		var pY=window[this.shortcutList[i]].posy;
		if(!this.isMain)
		{
			pX=window[this.shortcutList[i]].oldx;
			pY=window[this.shortcutList[i]].oldy;
		}
		if (((x1<pX-0+window[this.shortcutList[i]].width)&&(pX<x1-0+nw)&&(y1<pY-0+window[this.shortcutList[i]].height))&&pY<y1-0+nh)
			window[this.shortcutList[i]].select(true);
		else window[this.shortcutList[i]].deselect();
	}
	if(!this.isMain)
		this.sumSCsize();
}

K_Desktop.prototype.endMoveRecDesktop=function()
{
	K_recDesktop='';
	$('K_recDesktop').parentNode.removeChild($('K_recDesktop'));
}

K_Desktop.prototype.moveShortcuts=function(x,y)
{
	if(Math.abs(x-K_xIni)<5 && Math.abs(y-K_yIni)<5)
		return false;
	if(!this.SCcloned)
		this.cloneShortcuts();

	for(var i=0;i<this.shortcutList.length;++i)
		if(window[this.shortcutList[i]].selected)
		{
			var nx=(x-K_difX);
			var ny=(y-K_difY);
			if(this.shortcutList[i]!=this.SCmain)
			{
				var incX=nx-window[this.SCmain].iniX;
				var incY=ny-window[this.SCmain].iniY;
				nx=window[this.shortcutList[i]].iniX-0+incX;
				ny=window[this.shortcutList[i]].iniY-0+incY;
			}
			
			if(this.isMain)
			{
				if (nx<K_moveMinX)
					nx=K_moveMinX;
				if (ny<K_moveMinY)
					ny=K_moveMinY;
				if (nx>K_moveMaxX)
					nx=K_moveMaxX;
				if (ny>K_moveMaxY)
					ny=K_moveMaxY;
			}

			window[this.shortcutList[i]].moveTo(nx,ny);
		}
	return false;
}

K_Desktop.prototype.endMoveShortcuts=function()
{
	K_movSC='';
	if(this.SCcloned)
	{
		this.uncloneShortcuts();
		for(var x=0;x<this.shortcutList.length;++x)
			if(window[this.shortcutList[x]].selected)
				window[this.shortcutList[x]].endMove();
	}
	this.numSCsel=0;
}



K_Desktop.prototype.cloneShortcuts=function()
{
	this.SCcloned=true;
	this.numSCsel=0;

	for(var x=0;x<this.shortcutList.length;++x)
		if(window[this.shortcutList[x]].selected)
		{
			++this.numSCsel;
			window[this.shortcutList[x]].bringToFront();
			var clon=$('K_'+this.shortcutList[x]).cloneNode(true);
			clon.id='K_clon_'+this.shortcutList[x];
			$t(clon,'img')[0].id='K_clon_sci_'+this.shortcutList[x];
			$t(clon,'div')[0].id='K_clon_scn_'+this.shortcutList[x];
			$t(clon,'div')[0].className='shortcutBackgroundSelected';
		
			$('K_'+this.shortcutList[x]).parentNode.appendChild(clon);
		
			if(this.shortcutList[x]!=this.SCmain && (K_gec||K_op))
				K_trans('K_'+this.shortcutList[x],20);

			window[this.shortcutList[x]].iniX=window[this.shortcutList[x]].posx;
			window[this.shortcutList[x]].iniY=window[this.shortcutList[x]].posy;		
		}
}

K_Desktop.prototype.uncloneShortcuts=function()
{
	this.SCcloned=false;
	for(var x=0;x<this.shortcutList.length;++x)
		if($('K_clon_'+this.shortcutList[x]))
		{
			$('K_'+this.shortcutList[x]).parentNode.removeChild($('K_clon_'+this.shortcutList[x]));
			if(K_gec || K_op)
				K_trans('K_'+this.shortcutList[x],60);	
		}
}

K_Desktop.prototype.addWindow=function(id)
{
	var e=false;
	for(var x=0;x<this.windowList.length;++x)
		if(this.windowList[x]==id)
		{
			e=true;
			break;
		}
	if(!e)
		this.windowList[this.windowList.length]=id;	
}

K_Desktop.prototype.removeWindow=function(id)
{
	for(var x=0;x<this.windowList.length;++x)
		if(this.windowList[x]==id)
			this.windowList.splice(x,1);
}

K_Desktop.prototype.attachTaskbar=function(taskbar)
{
	this.taskbar=taskbar;
	var tb=$('K_'+this.taskbar).cloneNode(true);
	tb.id='K_'+this.taskbar+'KTEMP';
	var o=$('K_int_'+this.id);
	var h=tb.offsetHeight;

	o.appendChild(tb);
	o.taskbar=this.taskbar;
	K_removeNode('K_'+this.taskbar);
	$('K_'+this.taskbar+'KTEMP').id='K_'+this.taskbar;
}

K_Desktop.prototype.destroy=function()
{
	K_removeNode('K_int_'+this.id);
}
