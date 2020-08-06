function K_ContextMenu(id,posx,posy,parent)
{
	this.id=id;
	this.posx=0;
	this.posy=0;
	this.posx2=posx;
	this.posy2=posy;
	this.hDespDir='right';

	this.elements=new Array();
	this.submenus=new Array();
	this.parent=parent;
	this.caller=null;
}

K_ContextMenu.prototype.moveTo=function(x,y)
{
	if (x!='auto')
		this.posx2=x;
	if (y!='auto')
		this.posy2=y;
}

K_ContextMenu.prototype.show=function()
{
	this.hDespDir='right';
	this.build();
	this.render();
	this.openMenu('');
}

K_ContextMenu.prototype.addElement=function(parentId,id,icon,caption,state)
{
	if (!parentId)
		parentId='';
	this.elements[this.elements.length]=new Array(parentId,id,icon,caption,state);
}

K_ContextMenu.prototype.removeElement=function(id)
{
	for(var x=0;x<this.elements.length;++x)
		if(this.elements[x][1]==id)
		{
			this.elements.splice(x,1);
			break;
		}
}

K_ContextMenu.prototype.destroy=function()
{
	K_removeNode('K_cm'+this.id+'_');
}
K_ContextMenu.prototype.build=function()
{
	var old=$('K_cm'+this.id+'_');
	if (old!=null)
		this.removeElements();
	for (var x=0;x<this.elements.length;x++)
	{
		var p=this.buildParents(this.elements[x][1]);
		var a=p.split(',');
		var cad='';
		for (var y=0,l=a.length;y<l;y++)
			if (a[y]!='')
				cad+=a[y]+',';
		if (cad.charAt(cad.length)==',')
			cad=cad.substring(0,cad.length-2);
		a=cad.split(',');
		a.reverse();
		window['K_smp_'+this.elements[x][1]]=a;
	}
	this.buildMenu('');
	for (var x=0;x<this.elements.length;x++)
		if (this.hasChilds(this.elements[x][1]))
			this.buildMenu(this.elements[x][1]);	
}

K_ContextMenu.prototype.render=function()
{
	var w=$('K_cm'+this.id+'_').offsetWidth;
	var h=$('K_cm'+this.id+'_').offsetHeight;
	var wp=$(this.parent).offsetWidth;
	var hp=$(this.parent).offsetHeight;
	var nx=this.posx2;
	var ny=this.posy2;

	ny=hp-this.posy2-h;

	if (this.posx2+w>wp)
	{
		nx=this.posx2-w;
		this.hDespDir='left';
	}
	if (ny<0)
		ny=hp-this.posy2;

	this.posx=nx;
	this.posy=ny;
	this.build();
}

K_ContextMenu.prototype.removeElements=function()
{
	var p=$(this.parent);
	for (var x=0;x<this.elements.length;x++)
		if (this.hasChilds(this.elements[x][1]))
		{
			var t=$('K_cm'+this.id+'_'+this.elements[x][1]);
			if (t!=null)
				p.removeChild(t);
		}
	var t=$('K_cm'+this.id+'_');
	if (t!=null)
		p.removeChild(t);
}

K_ContextMenu.prototype.buildParents=function(parentId)
{
	var cad='';
	for (var x=0;x<this.elements.length;x++)
	{
		if (this.elements[x][1]==parentId)
		{
			cad+=this.elements[x][0]+',';
			cad+=this.buildParents(this.elements[x][0]);
		}
	}
	return cad;
}

K_ContextMenu.prototype.hasChilds=function(id)
{
	for (var x=0;x<this.elements.length;x++)
		if (this.elements[x][0]==id)
			return 1;
	return 0;
}

K_ContextMenu.prototype.setItemState=function(id,val)
{
	for (var x=0;x<this.elements.length;x++)
		if (this.elements[x][1]==id)
		{
			this.elements[x][4]=val;
			break;
		}
}

K_ContextMenu.prototype.buildMenu=function(id)
{
	var divmen=document.createElement('div');
	if(K_ie && !K_op)
	{
		divmen.style.width='1px';
		divmen.onselectstart=function () {return false;};
	}
	if (id=='')
	{
		divmen.id='K_cm'+this.id+'_';
		divmen.className='subMenu defaultFont ';
		divmen.style.left=this.posx+'px';
		divmen.style.bottom=this.posy+'px';
		divmen.style.visibility='hidden';
	}
	if (id!='')
	{
		divmen.id='K_cm'+this.id+'_'+id;
		this.submenus.push('K_cm'+this.id+'_'+id);
		divmen.className='subMenu defaultFont ';
		divmen.parentId=id;
		divmen.positioned=0;
		divmen.style.visibility='hidden';
	}
	
	divmen.style.zIndex=++K_zIndex;
	divmen.taskbar=1;

	for (var x=0;x<this.elements.length;x++)
	{
		if (this.elements[x][0]==id && this.elements[x][4]!=2)
		{
			var cad='';
			var divel=document.createElement('div');
			if(id.substr(0,9)!='separator')
				divel.id='K_cm'+this.id+'_E'+this.elements[x][1];
			else divel.id='K_cm'+this.id+'_Eseparator';
			if(id.substr(0,9)!='separator')
			{
				if (this.hasChilds(this.elements[x][1]))
					cad='cMenuElement defaultFont startMenuChildrenArrowOff';
				else
					cad='cMenuElement defaultFont';
				if(this.elements[x][4])
					cad+=" menuElementDeactivated";
				else cad+=" menuElementOff";
				divel.className=cad;
				cad='';
			}
			if (this.elements[x][1].substr(0,9)=='separator')
			{
				divel.className='menuSeparator';
			}
			else
			{
				divel.menuId=this.id;
				divel.onmouseover=this.elementMouseOver;
				divel.onmouseout=this.elementMouseOut;
				divel.onclick=this.elementClick;

				if (this.elements[x][2])
				{
					cad+="<img style='margin:1px;width:16px;height:16px' src='"+this.elements[x][2]+"' align='absmiddle'>&nbsp;";
				}
				if (K_ie && !K_op)
				{
					cad+="<div id='K_cm_ie_"+this.id+"' style='display:inline;vertical-align:middle;width:100px'>";
				}
				cad+='&nbsp;&nbsp;&nbsp;&nbsp;'+this.elements[x][3]+'&nbsp;&nbsp;';
				if (K_ie && !K_op) cad+='</div>';
				divel.innerHTML=cad;
			}
			divmen.appendChild(divel);
		}
	}

	$(this.parent).appendChild(divmen);
	divmen.style.width=divmen.offsetWidth-0+25+'px';
	if(K_ie && !K_op)
	{
		var t=$('K_cm_ie_'+this.id).offsetWidth;
		var s=$t(divmen,'div');
		for (var x=0;x<s.length;x++)
			s[x].style.width=t-0+25+'px';
	}
}

K_ContextMenu.prototype.elementMouseOver=function()
{
	this.className=this.className.replace('menuElementOff','menuElementOn');
	this.className=this.className.replace('startMenuChildrenArrowOff','startMenuChildrenArrowOn');
	window[this.menuId].clearSubmenus(this.id.replace('K_cm'+this.menuId+'_E',''));
	if (window[this.menuId].hasChilds(this.id.replace('K_cm'+this.menuId+'_E','')))
	{
		window[this.menuId].openMenu(this.id);
	}
}

K_ContextMenu.prototype.elementMouseOut=function()
{
	this.className=this.className.replace('menuElementOn','menuElementOff');
	this.className=this.className.replace('startMenuChildrenArrowOn','startMenuChildrenArrowOff');
}

K_ContextMenu.prototype.elementClick=function()
{
	if(this.className.indexOf('menuElementDeactivated')>-1)
		return;
	var caller=window[this.menuId].caller;
	if(window[caller])
		window[caller].menuOpened=false;
	var i=this.id.replace('K_cm'+this.menuId+'_E','');
	var c=window[this.menuId].hasChilds(i);
	if (c)
		return;
	window[this.menuId].clearAllSubMenus();	
	if (typeof window[i+'_click']=='function')
		window[i+'_click'](caller);
	
}

K_ContextMenu.prototype.openMenu=function(id)
{
	if (id=='')
		id='K_cm'+this.id+'_';
	var g=id.replace('K_cm'+this.id+'_E','K_cm'+this.id+'_');
	var o=$(g);
	var p=$($(id).parentNode.id);

	if (o.positioned==0)
	{
		var px=parseInt(p.style.left.substr(0,p.style.left.length-2));
		if (this.hDespDir=='right')
		{
			if (p.offsetWidth+px+o.offsetWidth>document.getElementById(this.parent).offsetWidth)
			{
				this.hDespDir='left';
				o.style.left=px-o.offsetWidth+'px';
			}
			else
				o.style.left=p.offsetWidth-0+px+'px';
		}
		else
			o.style.left=px-o.offsetWidth+'px';
		var c=p.getElementsByTagName('div');
		var elem=0;
		var sep=0;
		for (var cc=0;cc<c.length;cc++)
		{
			if (c[cc].id==id)
				break;
			if (c[cc].id=='K_cm'+this.id+'_Eseparator')
				++sep;
			else
				++elem;
		}
		var py=parseInt(p.style.bottom.substr(0,p.style.bottom.length-2));
		py+=p.offsetHeight;

		if (K_ie&&!K_op)
			elem=elem/2;
		var ny=py-o.offsetHeight;
		ny-=$(id).offsetHeight*elem;
		if ($('K_cm'+this.id+'_Eseparator'))
			ny-=$('K_cm'+this.id+'_Eseparator').offsetHeight*sep;
		if (ny>0)
			o.style.bottom=ny+'px';
		else
		{
			ny=py-($(id).offsetHeight*(cc+1));
			if (ny+o.offsetHeight<$(this.parent).offsetHeight)
				o.style.bottom=ny+'px';
			else
			{
				ny=$(this.parent).offsetHeight-o.offsetHeight;
				o.style.bottom=ny+'px';
			}
		}
		o.positioned=1;
	}
	o.style.visibility='visible';
}

K_ContextMenu.prototype.clearSubmenus=function(id)
{
	var i=id.replace('K_cm'+this.id+'_','');
	var path=window['K_smp_'+i];
	if (path=='')
	{
		for (var x=0;x<this.submenus.length;x++)
			$(this.submenus[x]).style.visibility='hidden';
	}
	else
	for (var x=0;x<this.submenus.length;x++)
	{
		if (this.submenus[x]=='K_cm'+this.id+'_'+id)
		{
			$(this.submenus[x]).style.visibility='visible';
			continue;
		}
		inPath=0;
		for (var y=0;y<path.length;y++)
			if('K_cm'+this.id+'_'+path[y]==this.submenus[x])
				inPath=1;
		if (!inPath)
			$(this.submenus[x]).style.visibility='hidden';
	}

}

K_ContextMenu.prototype.clearAllSubMenus=function()
{
	for (var x=0;x<this.submenus.length;x++)
	{
		var t=$(this.submenus[x]);
		if (t!=null)
			t.style.visibility='hidden';
	}
	var t=$('K_cm'+this.id+'_');
	if (t!=null)
		t.style.visibility='hidden';
}
