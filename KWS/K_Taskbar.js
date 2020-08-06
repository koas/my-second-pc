K_Taskbar.prototype=new K_Base();
K_Taskbar.prototype.constructor=K_Taskbar;
K_Taskbar.superclass=K_Base.prototype;

function K_Taskbar(node,parent)
{
	if (arguments.length>0)
        this.init(node,parent);
}

K_Taskbar.prototype.init=function(node,parent)
{
	K_Taskbar.superclass.init.call(this,node.getAttribute('id'),node,parent,'taskbar');

	this.buttons=new Array();
	this.active=-1;
	this.hasStartMenu=0;
	this.tabIniW=160;
	this.tabActW=this.tabIniW;
	this.trayIcons=new Array();
	this.startMenuCaption='';
	this.startMenuHint='';
	this.hasStartMenu=0;
	this.SMelements=new Array();
	this.SMsubmenus=new Array();
	this.contextMenu=$a(node,'context_menu');
        this.allowDateConfig=$a(node,'allow_date_config');
        
        var localDate=new Date();
        this.timestamp=localDate.getTime();
        var d=$a(node,'date');
        if(d!='')
		this.setLocalDate(d);
	this.history=new Array();

	var e=$t(this.xmlDoc,'start_menu');
	if(e.length==1)
	{
		e=$t(this.xmlDoc,'start_menu')[0];
		this.addStartMenu($a(e,'caption'),$a(e,'hint'));
		var elem=$t(e,'element');
		for (var x=0;x<elem.length;x++)
		{
			if ($a(elem[x],'id')=='separator')
				this.SMaddElement($a(elem[x].parentNode,'id'),$a(elem[x],'id'),'','');
			else
				this.SMaddElement($a(elem[x].parentNode,'id'),$a(elem[x],'id'),$a(elem[x],'icon'),$a(elem[x],'caption'));
		}
	}

	var e=$t(this.xmlDoc,'tray');
	if(e.length==1)
	{
		e=$t(this.xmlDoc,'tray')[0].getElementsByTagName('icon');
		for (var x=0;x<e.length;x++)
			this.trayIcons[this.trayIcons.length]=new Array($a(e[x],'id'),$a(e[x],'img'),$a(e[x],'caption'),$a(e[x],'action'));             
	}
	this.build();
}

K_Taskbar.prototype.render=function()
{
	if ($('K_'+this.id)!=null)
		K_removeNode('K_'+this.id);

	var divtask=document.createElement('div');
	divtask.id='K_'+this.id;
	divtask.className='taskbar';
        
	var tray=document.createElement('div');
	if (K_ie)
		tray.onselectstart=function () {return false;};
	
	tray.id='K_'+this.id+'_tray';

	
	tray.className='defaultFont taskbartray';
	var clock=document.createElement('div');
	clock.style.display='inline';
	clock.className='trayClock';
	clock.id='K_'+this.id+'_clock';
	tray.appendChild(clock);

	divtask.appendChild(tray);

	var list=document.createElement('ul');        
	if (K_ie)
		list.onselectstart=function () {return false;};
                        
	list.id="K_"+this.id+"_ul";
	list.className="taskbarlist";
        
	divtask.appendChild(list);
	$('K_int_'+this.parent).appendChild(divtask);
	this.hIni=$('K_'+this.id+'_ul').offsetHeight;
	if (K_ie)
		$('K_'+this.id).style.bottom='-1px';
	this.refClock();
	this.drawSM();
	if (this.hasStartMenu==1)
		this.addWindow('K_appStartMenu');
	window[this.parent].attachTaskbar(this.id);
	setInterval(this.id+'.refClock()',1000);
        for(var x=0;x<this.trayIcons.length;++x)
                this.addTrayIcon(this.trayIcons[x][0],this.trayIcons[x][1],this.trayIcons[x][2],this.trayIcons[x][3]);
        $('K_'+this.id+'_ul').style.width=$('K_'+this.id).offsetWidth-$('K_'+this.id+'_tray').offsetWidth-5+'px';
}

K_Taskbar.prototype.clockRightMouseDown=function()
{
        if(this.allowDateConfig!='0')
                K_loadAppAJAX(K_dynP+'apps/timeConfig/timeConfig.php','1',K_staP+'icons/timezone_32.png');
}

K_Taskbar.prototype.taskbarRightMouseDown=function(x,y)
{
        /*console.info('taskbarRightMouseDown -> ('+x+','+y+')');*/
}

K_Taskbar.prototype.addStartMenu=function(caption,hint)
{
	this.startMenuCaption=caption;
	this.startMenuHint=hint;
	this.hasStartMenu=1;
}

K_Taskbar.prototype.addWindow=function(id_window)
{
	var e=0;
	for(var x=0;x<this.buttons.length;x++)
		if (this.buttons[x]==id_window)
			e=1;
	if (!e)
	{
		if (id_window!='K_appStartMenu')
			this.active=this.buttons.length;
		this.buttons[this.buttons.length]=id_window;
		if(id_window!='K_appStartMenu')
			this.history.push(id_window);
		this.drawNewTab(id_window,1);
	}
	this.redraw();
}

K_Taskbar.prototype.clickStartTab=function()
{
	K_clearContextMenu();
	if (this.active!=0)
	{
		this.SMbringToFront();
		this.active=0;
		this.redraw();
		this.SMopenMenu('K_');
	}
	else
	{
		this.active=-1;
		this.redraw();
		this.SMclearAllSubmenus();
	}
}

K_Taskbar.prototype.drawNewTab=function(id,active)
{
	$('K_'+this.id+'_ul').style.width=$('K_'+this.id).offsetWidth-$('K_'+this.id+'_tray').offsetWidth-5+'px';
        var ob=$('K_'+this.id+'_ul');
	var a1=ob.offsetHeight;
        
	var cad='';
	if (id=='K_appStartMenu')
	{
		cad+="<li id='K_"+this.id+"_tab_K_appStartMenu' title='"+this.startMenuHint+"'";
		cad+=" onclick=\""+this.id+".clickStartTab()\" ";
		if (this.active==0)
			cad+=" class='tbStartButton tbbuttonActive' ";
		else
			cad+=" class='tbStartButton tbbutton' ";
		cad+=" onselectstart='return false'";
		cad+=">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if (K_ko==0)
			cad+="&nbsp;&nbsp;";
		cad+=this.startMenuCaption+"&nbsp;&nbsp;</li>";
	}
	else
	{
		var s='';
		if(active==1)
			s='Active';

		cad+="<li id='K_"+this.id+"_tab_"+id+"' style='width:"+this.tabActW+"px'";
		cad+=" class='tbbutton"+s+"' onselectstart='return false'";
		var title=window[id].title;
		cad+=" title='"+title+"'  onclick=\""+this.id+".clickTab('"+id+"')\">";
		var ico=window[id].icon;
		cad+="<div style='position:absolute;left:3px;top:3px'>";
		if (ico)
			cad+="<img style='height:16px;width:16px;position:absolute;left:0px;top:0px' src='"+ico+"' align='absmiddle'/>";
		cad+='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="K_'+this.id+'_tb_'+id+'">'+title+'</span>';
		cad+="</div></li>";
	}
	ob.innerHTML+=cad;
	
	for(var x=this.hasStartMenu;x<this.buttons.length;x++)
	{
		if (K_ie)
			$('K_'+this.id+'_tab_'+this.buttons[x]).oncontextmenu=this.rightMouseDown;
		else if (document.addEventListener)
			$('K_'+this.id+'_tab_'+this.buttons[x]).addEventListener('contextmenu',this.rightMouseDown,true);
	}
	var a2=ob.offsetHeight;
	if (a2>a1)
		this.resizeTabsAdd(a1,a2);
	this.cropTitles();
}
K_Taskbar.prototype.rightMouseDown=function(e)
{
	var but=0;
	if(K_ie)
		but=window.event.button;
	else but=e.which;
	but=(but==1)?1:2;
	if(but!=2) return;
	var x=y=0;
	if (K_ie || K_op){x=event.clientX;y=event.clientY;}
	if (K_gec){x=e.pageX;y=e.pageY;}	
	var i=K_strReverse(K_strReverse(this.parentNode.id).replace('lu_','')).replace('K_','');
	var w=this.id.replace('K_'+i+"_tab_",'');
	window[w].bringToFront();
	var t=window[i].contextMenu;
	if (t!=null)
	{
		if(window[w].minBut)
			window[t].setItemState('K_WCM_MINI',0);
		else window[t].setItemState('K_WCM_MINI',1);
		if(window[w].maxBut && !window[w].maximized)
			window[t].setItemState('K_WCM_MAXI',0);
		else
			window[t].setItemState('K_WCM_MAXI',1);
		if(!window[w].movable!='1')
			window[t].setItemState('K_WCM_MOVE',1);
		if(window[w].maximized)
		{
			window[t].setItemState('K_WCM_REST',0);
			window[t].setItemState('K_WCM_MOVE',1);
		}
		else
		{
			window[t].setItemState('K_WCM_REST',1);
			window[t].setItemState('K_WCM_MOVE',0);
		}

		K_clearContextMenu();
		window[t].caller=w;
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
K_Taskbar.prototype.clickTab=function(id)
{
	var index=this.getTabIndex(id);
	if(index==this.active)
	{
		window[this.buttons[index]].clickMinimize();
	}
	else
	{
		this.setActive(id);
		window[id].bringToFront();
	}
	this.redraw();
	K_clearContextMenu();
}
K_Taskbar.prototype.resizeTabsAdd=function(a1,a2)
{
	if (a1==this.hIni)
		return;
	var ob=$('K_'+this.id+'_ul');
	var h=ob.offsetHeight;
	var w=this.tabIniW;
	while (h!=a1)
	{
		w-=1;
		for(var x=this.hasStartMenu;x<this.buttons.length;x++)
			$('K_'+this.id+'_tab_'+this.buttons[x]).style.width=w+'px';
		h=ob.offsetHeight;
	}
	this.tabActW=w;
	this.cropTitles();
}

K_Taskbar.prototype.resizeTabsRemove=function()
{
	if (this.buttons.length==this.hasStartMenu)
		return;
	var ob=$('K_'+this.id+'_ul');
	var h=ob.offsetHeight;
	var a1=h;
	while (h==a1 && this.tabActW<this.tabIniW)
	{
		this.tabActW+=1;
		for(var x=this.hasStartMenu;x<this.buttons.length;x++)
			$('K_'+this.id+'_tab_'+this.buttons[x]).style.width=this.tabActW+'px';
		h=ob.offsetHeight;
	}
	this.tabActW-=1;
	for(var x=this.hasStartMenu;x<this.buttons.length;x++)
		$('K_'+this.id+'_tab_'+this.buttons[x]).style.width=this.tabActW+'px';
	this.cropTitles();
}


K_Taskbar.prototype.removeTab=function(id)
{	
	var index=this.getTabIndex(id);
	this.buttons.splice(index,1);
	this.redraw();
	$('K_'+this.id+'_ul').removeChild(document.getElementById('K_'+this.id+'_tab_'+id));
        $('K_'+this.id+'_ul').style.width=$('K_'+this.id).offsetWidth-$('K_'+this.id+'_tray').offsetWidth-5+'px';
	this.resizeTabsRemove();
	
	var newHistory=new Array();
	for(var x=0;x<this.history.length;++x)
		if(this.history[x]!=id)
			newHistory.push(this.history[x]);
	this.history=newHistory;
	this.setActive(this.history.pop());
}

K_Taskbar.prototype.cropTitles=function()
{
	var start=this.hasStartMenu;

	for(var x=start;x<this.buttons.length;x++)
	{
		var id=this.buttons[x];
		if (id=='K_appStartMenu')
			continue;
		var ob=$('K_'+this.id+'_tb_'+id);
		var c=0;
		var title=window[id].title;
		ob.innerHTML=title.substr(0,title.length-c);
		var h=ob.offsetHeight;
		while (h>25)
		{
			++c;
			ob.innerHTML=title.substr(0,title.length-c)+'...';
			h=ob.offsetHeight;
		}
	}
}

K_Taskbar.prototype.setActive=function(id)
{
	this.history.push(id);
	this.active=this.getTabIndex(id);
	this.redraw();
}

K_Taskbar.prototype.activateNextTab=function(id)
{
        var n=this.getActiveWindow();
        while(n==id)
                n=this.history.pop();
        
        if(n!=id)
                this.setActive(n);
}

K_Taskbar.prototype.getActiveWindow=function()
{
	return this.buttons[this.active];
}
K_Taskbar.prototype.redrawWindows=function()
{
        var active=this.getActiveWindow();
        for (var x=0;x<this.buttons.length;x++)
        if(window[this.buttons[x]])
        {
                if(window[this.buttons[x]].status!='minimized')
                        window[this.buttons[x]].bringToFront();
        }
        if(active)
                window[active].bringToFront();
}
K_Taskbar.prototype.resizeTabList=function()
{
	var i=9;
	$('K_'+this.id+'_ul').style.width=$('K_'+this.id).offsetWidth-$('K_'+this.id+'_tray').offsetWidth-i+'px';
}

K_Taskbar.prototype.redraw=function()
{
	for(var x=0;x<this.buttons.length;x++)
	{
		var id=this.buttons[x];
		if (id)
		{
			if (id=='K_appStartMenu' && $('K_'+this.id+'_tab_K_appStartMenu'))
			{
				if (this.active==0)
					$('K_'+this.id+'_tab_K_appStartMenu').className='tbStartButton tbbuttonActive';
				else
				{
					$('K_'+this.id+'_tab_K_appStartMenu').className='tbStartButton tbbutton';
					if (this.hasStartMenu)
						this.SMclearAllSubmenus();
				}
			}
			else
			if ($('K_'+this.id+'_tab_'+id))
			{
				if(x==this.active)
				{
					$('K_'+this.id+'_tab_'+id).className='tbbuttonActive';
					$('K_'+this.id+'_tb_'+id).className='tbbuttextActive';
					window[id].drawActive();
				}
				else
				{
					$('K_'+this.id+'_tab_'+id).className='tbbutton';
					$('K_'+this.id+'_tb_'+id).className='tbbuttext';
					window[id].drawInactive();
				}
			}
		}
	}
}



K_Taskbar.prototype.getTabIndex=function(id)
{
	for (var x=0;x<this.buttons.length;x++)
		if(this.buttons[x]==id)
			return x;
}

K_Taskbar.prototype.refClock=function()
{
        this.timestamp+=1000;
	var dat=new Date(this.timestamp);
        var d=dat.getDay();
	var dd=dat.getDate();
	var m=dat.getMonth();
	var y=dat.getFullYear();
        
        var cad='&nbsp;&nbsp;'+K_L_shortDateString;
        
        cad=cad.replace('%WN%',K_L_shortDays[d]);
        cad=cad.replace('%MN%',K_L_shortMonths[m]);
        cad=cad.replace('%DW%',dd);
	cad+='&nbsp;&nbsp;'+dat.getHours()+':';

	if (dat.getMinutes()<10)
		cad+='0';
	cad+=dat.getMinutes()+'&nbsp;&nbsp;';
	$('K_'+this.id+'_clock').innerHTML=cad;

	cad=K_L_dateString;
	
	cad=cad.replace('%DW%',K_L_days[d]);
	cad=cad.replace('%DM%',dd);
	cad=cad.replace('%M%',K_L_months[m]);
	cad=cad.replace('%Y%',y);
	$('K_'+this.id+'_clock').title=cad;
}

K_Taskbar.prototype.addTrayIcon=function(id,img,caption,action)
{
        if(this.trayIconIndex(id)==-1)
                this.trayIconIndex[this.trayIconIndex.length]=new Array(id,img,caption,action);
	var i=document.createElement('img');
        i.id=id;
	i.src=img;
	i.title=caption;
        i.align='absmiddle';
        i.tray=this.id;
	i.ondblclick=this.trayIconDblClick;
	$('K_'+this.id+'_tray').insertBefore(i,$('K_'+this.id+'_clock'));
        $('K_'+this.id+'_ul').style.width=$('K_'+this.id).offsetWidth-$('K_'+this.id+'_tray').offsetWidth-5+'px';
}
K_Taskbar.prototype.trayIconIndex=function(id)
{
        var idx=-1;
        for(var x=0;x<this.trayIcons.length;++x)
                if(this.trayIcons[x][0]==id)
                {
                        idx=x;
                        break;
                }
        return idx;
}

K_Taskbar.prototype.trayIconDblClick=function()
{
        eval(window[this.tray].trayIcons[window[this.tray].trayIconIndex([this.id])][3]);
}
K_Taskbar.prototype.removeTrayIcon=function(id)
{
	$('K_'+this.id+'_tray').removeChild($(id));
        $('K_'+this.id+'_ul').style.width=$('K_'+this.id).offsetWidth-$('K_'+this.id+'_tray').offsetWidth-5+'px';
}

K_Taskbar.prototype.SMaddElement=function(parentId,id,icon,caption)
{
	if (!parentId)
		parentId='';

	this.SMelements[this.SMelements.length]=new Array(parentId,id,icon,caption);
}

K_Taskbar.prototype.drawSM=function()
{
	for (var x=0;x<this.SMelements.length;x++)
	{
		var p=this.SMbuildParents(this.SMelements[x][1]);
		var a=p.split(',');
		var cad='';
		for (var y=0,l=a.length;y<l;y++)
			if (a[y]!='')
				cad+=a[y]+',';
		if (cad.charAt(cad.length)==',')
			cad=cad.substring(0,cad.length-2);
		a=cad.split(',');
		a.reverse();
		window['K_'+this.id+'_smp_'+this.SMelements[x][1]]=a;
	}
	this.SMbuildMenu('');
	for (var x=0;x<this.SMelements.length;x++)
		if (this.SMhasChilds(this.SMelements[x][1]))
			this.SMbuildMenu(this.SMelements[x][1]);
}

K_Taskbar.prototype.SMbuildParents=function(parentId)
{
	var cad='';
	for (var x=0;x<this.SMelements.length;x++)
	{
		if (this.SMelements[x][1]==parentId)
		{
			cad+=this.SMelements[x][0]+',';
			cad+=this.SMbuildParents(this.SMelements[x][0]);
		}
	}
	return cad;
}

K_Taskbar.prototype.SMbuildMenu=function(id)
{
	var divmen=document.createElement('div');
       
	divmen.parentKeepFocus=1;
	if (K_ie)
		divmen.onselectstart=function () {return false;};
	if (id=='')
	{
		divmen.id='K_sm_'+this.id;
		divmen.className='startMenuRoot defaultFont ';
		divmen.style.left='2px';
		divmen.style.visibility='hidden';
		divmen.style.bottom=$('K_'+this.id).offsetHeight+'px';
	}
	if (id!='')
	{
		divmen.id='K_sm_'+this.id+'_'+id;
		this.SMsubmenus.push('K_sm_'+this.id+'_'+id);
		divmen.className='startMenuChildren defaultFont ';
		divmen.parentId=id;
		divmen.positioned=0;
		divmen.style.visibility='hidden';
	}

	for (var x=0;x<this.SMelements.length;x++)
	{
		if (this.SMelements[x][0]==id)
		{
			var cad='';
			var divel=document.createElement('div');
			divel.parentKeepFocus=1;
			divel.id='K_sm_'+this.id+'_E'+this.SMelements[x][1];
			if (id=='')
			{
				if (this.SMhasChilds(this.SMelements[x][1]))
					divel.className='startMenuRootElement startMenuElementOff defaultFont startMenuChildrenArrowOff';
				else
					divel.className='startMenuRootElement startMenuElementOff defaultFont';
			}
			if(id!='' && id!='separator')
			{
				if (this.SMhasChilds(this.SMelements[x][1]))
					divel.className='startMenuChildrenElement startMenuElementOff defaultFont startMenuChildrenArrowOff';
				else
					divel.className='startMenuChildrenElement startMenuElementOff defaultFont';
			}
			if (this.SMelements[x][1]=='separator')
			{
				divel.className='startMenuSeparator';
				if (K_ie && !K_op)
				{
					divel.style.width='10px';
					divel.style.height='2px';
				}
			}
			else
			{
				divel.onmouseover=this.SMelementMouseOver;
				divel.onmouseout=this.SMelementMouseOut;
				divel.onclick=this.SMelementClick;
				divel.tbId=this.id;

				if (this.SMelements[x][2])
				{
					cad+="<img style='margin:5px;";
					if (id=='')
						cad+='width:24px;height:24px';
					else
						cad+='width:16px;height:16px';
					cad+="' src='"+this.SMelements[x][2]+"' align='absmiddle'>&nbsp;&nbsp;";
				}
				if (K_ie && !K_gec && !K_op && !K_op && !K_saf && !K_ep &&!K_ns)
				{
					if (id=='')
						cad+="<div style='display:inline;padding-top:10px;'>";
					else
						cad+="<div style='display:inline;padding-top:4px;'>";
				}
				cad+=this.SMelements[x][3]+'&nbsp;&nbsp;&nbsp;';
				if (K_ie && !K_op) cad+='</div>';
				divel.innerHTML=cad;
			}
			divmen.appendChild(divel);
		}

	}

	$('K_int_'+this.parent).appendChild(divmen);
	divmen.style.width=divmen.offsetWidth-0+25+'px';
	if(K_ie && !K_op)
	{
		var s=$t(divmen,'div');
		for (var x=0;x<s.length;x++)
		{
			if(s[x].className=='startMenuSeparator')
				s[x].style.width=divmen.offsetWidth-25+'px';
		}
	}
}

K_Taskbar.prototype.SMhasChilds=function(id)
{
	for (var x=0;x<this.SMelements.length;x++)
		if (this.SMelements[x][0]==id)
			return 1;
	return 0;
}



K_Taskbar.prototype.SMopenMenu=function(id)
{
	if (id=='K_')
	{
		$('K_sm_'+this.id).style.visibility='visible';
		return;
	}
	var g=id.replace('K_sm_'+this.id+'_E','K_sm_'+this.id+'_');
	var o=$(g);
	var p=$($(id).parentNode.id);

	if (o.positioned==0)
	{
		var px=parseInt(p.style.left.substr(0,p.style.left.length-2));
		o.style.left=p.offsetWidth-0+px+'px';

		var maxy=parseInt(p.style.bottom.substr(0,p.style.bottom.length-2));
		
		var py=maxy;

		var c=p.childNodes;
		var elem=c.length;
		var sep=0;
		var myel=new Array();
		for (var cc=0;cc<c.length;cc++)
		{
			myel.push(c[cc].id);
		}
		myel.reverse();

		var sepStep=0;
		if ($('K_sm_'+this.id+'_Eseparator')!=null)
			sepStep=$('K_sm_'+this.id+'_Eseparator').offsetHeight;
		var elemStep=$(id).offsetHeight;

		for (var cc=0;cc<myel.length;cc++)
		{
			if (myel[cc]==id)
				break;
			if (myel[cc]=='K_sm_'+this.id+'_Eseparator')
				py+=sepStep;
			else
				py+=elemStep;
		}
		if(py-0+o.offsetHeight>o.parentNode.offsetHeight)
			py=o.parentNode.offsetHeight-o.offsetHeight;
		o.style.bottom=py+'px';
		o.positioned=1;
	}
	o.style.visibility='visible';
}

K_Taskbar.prototype.SMclearAllSubmenus=function()
{
	for (var x=0;x<this.SMsubmenus.length;x++)
	{
		$(this.SMsubmenus[x]).style.visibility='hidden';
	}
	$('K_sm_'+this.id).style.visibility='hidden';
}

K_Taskbar.prototype.SMclearSubmenus=function(id)
{
	var i=id.replace('K_sm_'+this.id+'_','');
	var path=window["K_"+this.id+"_smp_"+i];
	if (path=='')
	{
		for (var x=0;x<this.SMsubmenus.length;x++)
			$(this.SMsubmenus[x]).style.visibility='hidden';
	}
	else
	for (var x=0;x<this.SMsubmenus.length;x++)
	{
		if (this.SMsubmenus[x]=='K_sm_'+this.id+'_'+id)
		{
			$(this.SMsubmenus[x]).style.visibility='visible';
			continue;
		}
		inPath=0;
		for (var y=0;y<path.length;y++)
			if('K_sm_'+this.id+'_'+path[y]==this.SMsubmenus[x])
				inPath=1;
		if (!inPath)
			$(this.SMsubmenus[x]).style.visibility='hidden';
	}
}

K_Taskbar.prototype.SMelementMouseOver=function()
{
	this.className=this.className.replace('startMenuElementOff','startMenuElementOn');
	this.className=this.className.replace('startMenuChildrenArrowOff','startMenuChildrenArrowOn');
	var i=this.id.replace('K_sm_'+this.tbId+'_E','');
	window[this.tbId].SMclearSubmenus(i);
	if (window[this.tbId].SMhasChilds(i))
		window[this.tbId].SMopenMenu(this.id);
}

K_Taskbar.prototype.SMelementMouseOut=function()
{
	this.className=this.className.replace('startMenuElementOn','startMenuElementOff');
	this.className=this.className.replace('startMenuChildrenArrowOn','startMenuChildrenArrowOff');
}

K_Taskbar.prototype.SMelementClick=function()
{
	var i=this.id.replace('K_sm_'+this.tbId+'_E','');
	if (typeof window[i+'_click']=='function')
	{
		window[this.tbId].clickStartTab();
		window[i+'_click']();
	}
}

K_Taskbar.prototype.SMbringToFront=function()
{
	var nZi=window[this.parent].newZindex();
	$('K_sm_'+this.id).style.zIndex=nZi;
	for (var x=0;x<this.SMsubmenus.length;x++)
	{
		$(this.SMsubmenus[x]).style.zIndex=nZi;
	}
}

K_Taskbar.prototype.setLocalDate=function(d)
{
	var p=d.split(' ');
	var pp=p[0].split('-');
	var ppp=p[1].split(':');
	
	var userDate=new Date(pp[0],pp[1]-1,pp[2],ppp[0],ppp[1],ppp[2]);
	this.timestamp=userDate.getTime();	
}