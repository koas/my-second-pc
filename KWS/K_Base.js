function K_Base(id,xmlDoc,parent)
{
	if (arguments.length>0)
		this.init(id,xmlDoc,parent);
}

K_Base.prototype.init = function (id,xmlDoc,parent,type)
{
	this.id=id;
	this.xmlDoc=xmlDoc;
	if (parent=='')
		this.parent=K_appCanvas;
	else
		this.parent=parent;
	this.type=type;
	this.contextMenu=null;
}

K_Base.prototype.build=function()
{
	var e=this.xmlDoc.childNodes;
	for (var x=0;x<e.length;x++)
	{
		switch(e[x].nodeName)
		{
			case 'desktop':
				window[$a(e[x],'id')]=new K_Desktop(e[x],this.id);
				break;
			case 'taskbar':
				window[$a(e[x],'id')]=new K_Taskbar(e[x],this.id);
				break;
			case 'shortcut':
				window[$a(e[x],'id')]=new K_Shortcut(e[x],this.id);
				break;
			case 'window':
				window[$a(e[x],'id')]=new K_Window(this.xmlDoc.childNodes[x],this.id);
				break;
			case 'html_code':
				window[$a(e[x],'id')]=new K_HTML_Code(e[x],this.id);
				break;
			case 'accordion':
				window[$a(e[x],'id')]=new K_Accordion(e[x],this.id);
				break;
			case 'page':
				window[$a(e[x],'id')]=new K_AccordionPage(e[x],this.id);
				break;
			case 'datatable':
				window[$a(e[x],'id')]=new K_Datatable(e[x],this.id);
				break;
			case 'floater':
				window[$a(e[x],'id')]=new K_Floater(e[x],this.id);
				break;
			case 'group':
				window[$a(e[x],'id')]=new K_Group(e[x],this.id);
				break;
			case 'multipleselect':
				window[$a(e[x],'id')]=new K_MultipleSelect(e[x],this.id);
				break;
			case 'tabcontrol':
				window[$a(e[x],'id')]=new K_Tabcontrol(e[x],this.id);
				break;
			case 'tab':
				window[$a(e[x],'id')]=new K_Tab(e[x],this.id);
				break;	
			case 'context_menu':
				var p='K_int_'+K_appCanvas;
				window[$a(e[x],'id')]=new K_ContextMenu($a(e[x],'id'),0,0,p);
				var g=$t(e[x],'element');
				for (var y=0;y<g.length;y++)
				{
					var parent=$a(g[y].parentNode,'id');
					var id=$a(g[y],'id');
					var icon=$a(g[y],'icon');
					var caption=$a(g[y],'caption');
					var inactive=$a(g[y],'inactive');
					if (parent==$a(e[x],'id')) parent='';
					window[$a(e[x],'id')].addElement(parent,id,icon,caption,inactive);
				}
				break;
		}
	}
}
K_Base.prototype.renderChilds=function()
{
	var e=this.xmlDoc.childNodes;
	for (var x=0;x<e.length;x++)
	{
		if (e[x].nodeName=='desktop' || e[x].nodeName=='taskbar' || e[x].nodeName=='shortcut' || e[x].nodeName=='window' || e[x].nodeName=='html_code' || e[x].nodeName=='accordion' || e[x].nodeName=='page' || e[x].nodeName=='datatable' || e[x].nodeName=='floater' || e[x].nodeName=='group' || e[x].nodeName=='multipleselect' || e[x].nodeName=='tabcontrol' || e[x].nodeName=='tab' )
			window[$a(e[x],'id')].render();
	}
}

K_Base.prototype.newZindex=function()
{
	return ++K_zIndex;
}

K_Base.prototype.trans=function(opacity)
{
	var obj=document.getElementById('K_'+this.id);
  	opacity = (opacity == 100)?99.999:opacity;
  	obj.style.KhtmlOpacity = opacity/100;
  	obj.style.filter = "alpha(opacity:"+opacity+")";
  	obj.style.MozOpacity = opacity/100;
  	obj.style.opacity = opacity/100;
}
