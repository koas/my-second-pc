K_Shortcut.prototype=new
K_Base();
K_Shortcut.prototype.constructor=K_Shortcut;
K_Shortcut.superclass=K_Base.prototype;

function K_Shortcut(node,parent)
{
	if (arguments.length>0)
        this.init(node,parent);
}

K_Shortcut.prototype.init=function(node,parent)
{
	K_Shortcut.superclass.init.call(this,$a(node,'id'),node,parent,'shortcut');
	this.icon=$a(node,'icon');
	this.name=$a(node,'name');
	this.type=$a(node,'type');
	this.path=$a(node,'path');
	this.oldx=parseInt($a(node,'x'));
	this.oldy=parseInt($a(node,'y'));
        this.size=parseInt($a(node,'size'));
	this.posx=0;
	this.posy=0;
	this.width=75;
	this.height=75;
	this.minX='auto';
	this.minY='auto';
	this.maxX='auto';
	this.maxY='auto';
	this.movable=1;
	this.iniX=0;
	this.iniY=0;
	this.selected=false;
	this.iconChanged=false;
	this.contextMenu=$a(node,'context_menu');
	var p=this.icon.split('/');
	p=p[p.length-1].split('.');
	K_iconCache[this.path]=p[0];
	this.build();
}

K_Shortcut.prototype.render=function()
{
	this.posx=this.oldx-0+window[this.parent].offsetX;
	this.posy=this.oldy-0+window[this.parent].offsetY;
	if(!window[this.parent].isMain)
		this.posy+=71;
	window[this.parent].addShortcut(this.id);
	window[K_appCanvas].addShortcut(this.id);
	if ($('K_'+this.id)!=null)
		K_removeNode('K_'+this.id);

	var d=document.createElement('div');
	d.style.position='absolute';
	d.style.left=this.posx+'px';
	d.style.top=this.posy+'px';
	d.ondblclick=this.doubleClick;
	d.onmouseover=this.mouseOver;
	d.onmouseout=this.mouseOut;
	d.onmousedown=this.leftMouseDown;
	d.onmouseup=this.leftMouseUp;
	if (K_ie)
		d.oncontextmenu=this.rightMouseDown;
	else if (document.addEventListener)
		d.addEventListener('contextmenu',this.rightMouseDown,true);
	if (K_ie)
		d.onselectstart=function () {return false;};
	d.className='defaultFont shortcut';
	d.id='K_'+this.id;
	var im=document.createElement('img');
	im.id='K_sci_'+this.id;
	im.src=this.icon;
	if(K_ie)
		im.ondragstart=function () {return false;};
	if (K_ie)
		im.onselectstart=function () {return false;};
	im.style.width='32px';
	im.style.height='32px';
	d.appendChild(im);
	if (this.type=='shortcut')
	{
		var arrow=document.createElement('div');
		arrow.id='K_sca_'+this.id;
		arrow.className='shortcutArrow';
		d.appendChild(arrow);
	}

	var t=document.createElement('div');
	t.id='K_scn_'+this.id;
	t.style.marginTop='3px';
	if (K_ie)
		t.onselectstart=function () {return false};
	t.className='shortcutBackground';
	t.style.color=window[this.parent].scColor;
	t.innerHTML=this.name;
	d.appendChild(t);
	
	$('K_int_'+K_appCanvas).appendChild(d);

	var ww=$('K_'+this.id).offsetWidth;
	if(ww>75)
		$('K_'+this.id).style.width='75px';
	

	if (this.type=='shortcut')
	{
		var w=$('K_'+this.id).offsetWidth;
		$('K_sca_'+this.id).style.left=((w-32)/2)+'px';
	}
	this.width=$('K_'+this.id).offsetWidth;
	this.height=$('K_'+this.id).offsetHeight;
	this.bringToFront();
	this.renderChilds();
}

K_Shortcut.prototype.mouseOver=function(e)
{
	if((K_gec && !K_ch) || K_op)
	{
		var src=this.id.replace('K_','');
		window[src].trans(60);
	}
}
K_Shortcut.prototype.mouseOut=function(e)
{
	if((K_gec && !K_ch) || K_op)
	{
		var src=this.id.replace('K_','');
		if(!window[src].selected)
			window[src].trans(100);
	}
}

K_Shortcut.prototype.rightMouseDown=function(e)
{
	var i=this.id.replace('K_','');
	if (typeof window[i]!='object') return;
	K_CM_dsktp=window[i].parent;
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

	var t=window[i].contextMenu;
	if (t!=null)
	{
		K_clearContextMenu();
                var compressed=new Array('zip','rar','gz','bz2','lzma','lzo','tar');
                if(K_inArray(K_getExtension(window[i].name),compressed))
                        window[t].setItemState('cmFolderCompress',2);
                else
                        window[t].setItemState('cmFolderCompress',0);
                window[t].setItemState('cmFolderTrash',0);
		window[i].select(window[i].selected);
		window[t].caller=i;
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

K_Shortcut.prototype.leftMouseDown=function(e)
{
        var but=0;
	if(K_ie)
		but=window.event.button;
	else but=e.which;
	if(but!=1) return;
	
	var evtobj=window.event ? event : e;

	K_resize=0;
	var dragOld=this.id.replace('K_sci_','');
	dragOld=dragOld.replace('K_scn_','');
	dragOld=dragOld.replace('K_','');
	window[K_appCanvas].SCmain=dragOld;
	
	var evtobj=window.event ? event : e;
	if(evtobj.ctrlKey)
	{
		if(window[dragOld].selected)
			window[dragOld].deselect();
		else window[dragOld].select(true);
	}
	else
	{
		if(!window[dragOld].selected)
			window[dragOld].select(false);
	}
	
	var x_inidiv=window[dragOld].posx;
	var y_inidiv=window[dragOld].posy;

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

	var minX=window[dragOld].minX;
	var maxX=window[dragOld].maxX;
	var minY=window[dragOld].minY;
	var maxY=window[dragOld].maxY;

	if (minX=='auto')
		K_moveMinX=0;
	else
		K_moveMinX=parseInt(minX);
	if (minY=='auto')
		K_moveMinY=0;
	else
		K_moveMinY=parseInt(minY);
	if (maxX=='auto')
		K_moveMaxX=$('K_int_'+window[dragOld].parent).offsetWidth - window[dragOld].width;
	else
		K_moveMaxX=maxX - window[K_idDrag].width;
	if (maxY=='auto')
		K_moveMaxY=$('K_int_'+window[dragOld].parent).offsetHeight - window[dragOld].height;
	else
		K_moveMaxY=maxY - window[dragOld].height;


	window[dragOld].iniX=window[dragOld].posx;
	window[dragOld].iniY=window[dragOld].posy;

	K_difX=x_inidoc-x_inidiv;
	K_difY=y_inidoc-y_inidiv;
	
	K_movSC=window[dragOld].parent;
	
	window[window[dragOld].parent].SCcloned=false;
	return false;
}

K_Shortcut.prototype.leftMouseUp=function(e)
{
	var but=0;
	if(K_ie)
		but=window.event.button;
	else but=e.which;
	if(but!=1) return;

	K_resize=0;
	var dragOld=this.id.replace('K_sci_','');
	dragOld=dragOld.replace('K_scn_','');
	dragOld=dragOld.replace('K_','');
	var evtobj=window.event ? event : e;
	if(evtobj.ctrlKey)
		window[dragOld].select(true);
	else window[dragOld].select(window[window[dragOld].parent].numSCsel);
}

K_Shortcut.prototype.select=function(keepOthers)
{
        K_clearContextMenu();
	if(!keepOthers)
		 window[this.parent].deselectShortcuts();
	if($('K_scn_'+this.id))
	{
		$('K_scn_'+this.id).className='shortcutBackgroundSelected';
		if((K_gec && !K_ch) || K_op)
		{
			K_trans('K_'+this.id,60);
		}
	}
	this.selected=true;
        if(!window[this.parent].isMain)
                window[this.parent].sumSCsize();
}

K_Shortcut.prototype.deselect=function()
{
	if($('K_scn_'+this.id))
	{
		$('K_scn_'+this.id).className='shortcutBackground';
		if((K_gec && !K_ch) || K_op)
		{
			K_trans('K_'+this.id,100);
		}
	}
	this.selected=false;
        if(!window[this.parent].isMain)
                window[this.parent].sumSCsize();
}

K_Shortcut.prototype.moveTo=function(x,y)
{
	
	this.posx=x;
	this.posy=y;
	var obj=$('K_'+this.id);
	obj.style.left=x+'px';
	obj.style.top=y+'px';
	var overW=-1;
	
	for (var i=0;i<window[K_appCanvas].windowList.length;++i)
	{
		if(x>window[window[K_appCanvas].windowList[i]].posx-40
		   && x<(window[window[K_appCanvas].windowList[i]].posx+window[window[K_appCanvas].windowList[i]].width-40) 
		&& y>window[window[K_appCanvas].windowList[i]].posy-20
		&& y<(window[window[K_appCanvas].windowList[i]].posy+window[window[K_appCanvas].windowList[i]].height)-20)
		{
			overW=i;
		}
	}
	
	if(overW>-1)
	{
		this.iconChanged=true;
		if(typeof window[window[K_appCanvas].windowList[overW]+'_onDrop']=='function')
			$('K_sci_'+this.id).src=K_staP+'KWS/img/shortcut.png';
		else $('K_sci_'+this.id).src=K_staP+'KWS/img/noDrop.png';
	}
	if(overW==-1 && this.iconChanged)
	{
		this.iconChanged=false;
		$('K_sci_'+this.id).src=this.icon;
	}
/*
	this.posx=x;
	this.posy=y;
	var obj=$('K_'+this.id);
	obj.style.left=x+'px';
	obj.style.top=y+'px';
	if(this.id!=window[this.parent].SCmain) return;
	var overW=-1;
	var overSC=-1;
	var overOwnW=false;
	var dsk=this.parent;
	if(!window[this.parent].isMain)
		dsk=K_appCanvas;

	for (var i=0;i<window[dsk].windowList.length;++i)
	{
		if(x>window[window[dsk].windowList[i]].posx-40 && x<(window[window[dsk].windowList[i]].posx+window[window[dsk].windowList[i]].width-40) 
		&& y>window[window[dsk].windowList[i]].posy-20 && y<(window[window[dsk].windowList[i]].posy+window[window[dsk].windowList[i]].height)-20)
		{
			if(!window[this.parent].isMain && window[dsk].windowList[i]==K_SC_win)
				overOwnW=true;
			else
				overW=i;
		}
	}
	for (var i=0;i<window[dsk].shortcutList.length;++i)
	{
		if(window[window[dsk].shortcutList[i]].selected)
			continue;
		if(x>window[window[dsk].shortcutList[i]].posx-10 && x<(window[window[dsk].shortcutList[i]].posx+window[window[dsk].shortcutList[i]].width-0) 
		&& y>window[window[dsk].shortcutList[i]].posy-10 && y<(window[window[dsk].shortcutList[i]].posy+window[window[dsk].shortcutList[i]].height)-0)
			overSC=i;
	}
	if(overW>-1 && !overOwnW)
	{
		this.iconChanged=true;
		if(typeof window[window[dsk].windowList[overW]+'_onDrop']=='function')
			$('K_sci_'+this.id).src=K_staP+'KWS/img/shortcut.png';
		else $('K_sci_'+this.id).src=K_staP+'KWS/img/noDrop.png';
	}
	if(overSC>-1)
	{
		this.iconChanged=true;
                if(window[window[dsk].shortcutList[overSC]].type=='folder')
                        $('K_sci_'+this.id).src=K_staP+'KWS/img/shortcutFolder.png';
                if(window[window[dsk].shortcutList[overSC]].type=='file')
                {
                        var ic=K_FD_check(this.path,window[window[dsk].shortcutList[overSC]].path);
                        if(ic!='')
                                $('K_sci_'+this.id).src=K_staP+ic;
                }
	}
	if(overW==-1 && overSC==-1)
	{
		if(!window[this.parent].isMain && !overOwnW)
		{
			this.iconChanged=true;
			$('K_sci_'+this.id).src=K_staP+'KWS/img/shortcut.png';
		}
		else if(this.iconChanged)
		{
			this.iconChanged=false;
			$('K_sci_'+this.id).src=this.icon;
		}
	}*/
}

K_Shortcut.prototype.endMove=function()
{
	if(this.id!=window[this.parent].SCmain) return;
	var overW=-1;
	for (var i=0;i<window[K_appCanvas].windowList.length;++i)
	{

		if(window[this.id].posx>window[window[K_appCanvas].windowList[i]].posx-40 
		&& window[this.id].posx<(window[window[K_appCanvas].windowList[i]].posx+window[window[K_appCanvas].windowList[i]].width-40) 
		&& window[this.id].posy>window[window[K_appCanvas].windowList[i]].posy-20 
		&& window[this.id].posy<(window[window[K_appCanvas].windowList[i]].posy+window[window[K_appCanvas].windowList[i]].height)-20)
		{
			overW=i;
		}
	}
	
	if(overW>-1)
	{
		var params='';
		for(var x=0;x<window[K_appCanvas].shortcutList.length;++x)
			if(window[window[K_appCanvas].shortcutList[x]].selected)
			{
				window[window[K_appCanvas].shortcutList[x]].moveTo(window[window[K_appCanvas].shortcutList[x]].iniX,window[window[K_appCanvas].shortcutList[x]].iniY);
				params+=window[window[K_appCanvas].shortcutList[x]].path+'@@';
			}
                if(typeof window[window[K_appCanvas].windowList[overW]+'_onDrop']=='function')
                {
                        params=params.substring(0,params.length-2);
                        window[window[K_appCanvas].windowList[overW]+'_onDrop'](window[this.parent].id,params,window[K_appCanvas].windowList[overW]);
                }
	}
	else
	{
		
		var get=K_dynP+'apps/desktop/moveDesktopFolder.php?dummy=1';
		var c=0;
		for(var x=0;x<window[K_appCanvas].shortcutList.length;++x)
			if(window[window[K_appCanvas].shortcutList[x]].selected)
			{
				var p=window[K_appCanvas].shortcutList[x].split('_');
				get+='&x'+c+'='+window[window[K_appCanvas].shortcutList[x]].posx+'&y'+c+'='+window[window[K_appCanvas].shortcutList[x]].posy+'&id'+c+'='+p[1];
				++c;
			}
		if(c)
			K_loadAJAX(get);
	}
	
	
	/*
	if($('K_clon_'+this.id))
		$('K_'+this.id).parentNode.removeChild($('K_clon_'+this.id));
	
	if(!window[this.parent].isMain)
	{
		this.oldx=this.posx-window[this.parent].offsetX;
		this.oldy=this.posy-71-window[this.parent].offsetY;
	}
	if(this.id!=window[this.parent].SCmain) return;	
	var dsk=this.parent;
	if(!window[this.parent].isMain)
		dsk=K_appCanvas;
	var overW=-1;
	var overSC=-1;
	var overOwnW=false;
	for (var i=0;i<window[dsk].windowList.length;++i)
	{

		if(window[this.id].posx>window[window[dsk].windowList[i]].posx-40 
		&& window[this.id].posx<(window[window[dsk].windowList[i]].posx+window[window[dsk].windowList[i]].width-40) 
		&& window[this.id].posy>window[window[dsk].windowList[i]].posy-20 
		&& window[this.id].posy<(window[window[dsk].windowList[i]].posy+window[window[dsk].windowList[i]].height)-20)
		{
			if(!window[this.parent].isMain && window[dsk].windowList[i]==K_SC_win)
				overOwnW=true;
			else
				overW=i;
		}
	}
	for (var i=0;i<window[dsk].shortcutList.length;++i)
	{
		if(window[dsk].shortcutList[i]==this.id) continue;
		if(window[this.id].posx>window[window[dsk].shortcutList[i]].posx-10 
		&& window[this.id].posx<(window[window[dsk].shortcutList[i]].posx+window[window[dsk].shortcutList[i]].width-0) 
		&& window[this.id].posy>window[window[dsk].shortcutList[i]].posy-10 
		&& window[this.id].posy<(window[window[dsk].shortcutList[i]].posy+window[window[dsk].shortcutList[i]].height)-0)
			overSC=i;
	}
	if(overW==-1 && overSC==-1 && !overOwnW)
	{
		if(window[this.parent].isMain)
		{
			var get=K_dynP+'php/desktop/moveDesktopFolder.php?dummy=1';
			var c=0;
			for(var x=0;x<window[dsk].shortcutList.length;++x)
				if(window[window[dsk].shortcutList[x]].selected)
				{
					var p=window[dsk].shortcutList[x].split('_');
					get+='&x'+c+'='+window[window[dsk].shortcutList[x]].posx+'&y'+c+'='+window[window[dsk].shortcutList[x]].posy+'&id'+c+'='+p[1];
					++c;
				}
			if(c)
				K_loadAJAX(get);
		}
		else
		{
			if(overSC==-1)
			{
                                var params='';
                                for(var x=0;x<window[dsk].shortcutList.length;++x)
                                if(window[window[dsk].shortcutList[x]].selected)
                                {
                                        window[window[dsk].shortcutList[x]].moveTo(window[window[dsk].shortcutList[x]].iniX,window[window[dsk].shortcutList[x]].iniY);
                                        params+=window[window[dsk].shortcutList[x]].path+'@'+(window[window[this.parent].parent].posx-0+window[window[dsk].shortcutList[x]].oldx)+'@'+(window[window[this.parent].parent].posy-0+71+window[window[dsk].shortcutList[x]].oldy)+'@@';
                                }
                                params=params.substring(0,params.length-2);
                                window[window[window[this.parent].parent].id+'_onDrop'](window[this.parent].id,params,'K_appCanvas');
                        }
		}
	}
	if(overW>-1 && !overOwnW)
	{
                if(typeof window[window[dsk].windowList[overW]+'_onDrop']=='function')
                {
                        var params='';
                        for(var x=0;x<window[dsk].shortcutList.length;++x)
                                if(window[window[dsk].shortcutList[x]].selected)
                                {
                                        window[window[dsk].shortcutList[x]].moveTo(window[window[dsk].shortcutList[x]].iniX,window[window[dsk].shortcutList[x]].iniY);
                                        params+=window[window[dsk].shortcutList[x]].path+'@@';
                                }
                        params=params.substring(0,params.length-2);
                        window[window[dsk].windowList[overW]+'_onDrop'](window[this.parent].id,params,window[dsk].windowList[overW]);
                }
	}
	if(overSC>-1)
	{
                if(window[window[dsk].shortcutList[overSC]].type=='folder')
                {
                        var cad='';
                        var scDel=new Array();
                        for(var x=0;x<window[dsk].shortcutList.length;++x)
                                if(window[window[dsk].shortcutList[x]].selected)
                                {
                                        cad+=window[window[dsk].shortcutList[x]].path+'@@';
                                        scDel[scDel.length]=window[dsk].shortcutList[x];
                                }
                        cad=cad.substring(0,cad.length-2);
                        K_loadAJAX(K_dynP+'php/desktop/moveFolder.php?pathNew='+window[window[dsk].shortcutList[overSC]].path+'&files='+cad);
                        for(var x=0;x<scDel.length;++x)
                        {
                                K_removeNode('K_'+scDel[x]);
                                window[dsk].removeShortcut(scDel[x]);
                                window[K_appCanvas].removeShortcut(scDel[x]);
                        }
                        for (var i=0;i<window[dsk].windowList.length;++i)
                        {
                                var appID=window[window[dsk].windowList[i]].id.split('_');
                                appID=appID[1];
                                if(window['M2PC_'+appID+'_DSKTP_path']==window[window[dsk].shortcutList[overSC]].path)
                                        window['M2PC_'+appID+'_loadFiles'](window['M2PC_'+appID+'_DSKTP_path']);
                        }
                }
                if(window[window[dsk].shortcutList[overSC]].type=='file')
                        if(K_FD_check(this.path,window[window[dsk].shortcutList[overSC]].path))
                                K_FD_run(this.path,window[window[dsk].shortcutList[overSC]].path);
	}*/
}

K_Shortcut.prototype.bringToFront=function()
{
	if($('K_'+this.id))
	{
		var nZi=window[this.parent].newZindex();
		$('K_'+this.id).style.zIndex=nZi;
	}
}

K_Shortcut.prototype.doubleClick=function(e)
{
	var t=this.id.replace('K_','');
        if(!window[t])return;
	if(window[t].type=='folder')
	{
		var p=window[t].parent.split('_');
		if(p[1]=='DESKTOP')
			K_loadAppAJAX(K_dynP+'apps/fileManager2/fileManager2.php','1#'+window[t].path,K_staP+'icons/fileManager_32.png');
		else
			window['M2PC_'+p[1]+'_loadFiles'](window[t].path);
	}
	if(window[t].type=='file')
	{
		K_launchFile(window[t].path);  
	}
}

K_Shortcut.prototype.destroy=function()
{
	K_removeNode('K_'+this.id);
}