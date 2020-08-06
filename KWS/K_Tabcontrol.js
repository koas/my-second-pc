K_Tabcontrol.prototype=new K_Base();
K_Tabcontrol.prototype.constructor=K_Tabcontrol;
K_Tabcontrol.superclass=K_Base.prototype;

function K_Tabcontrol(node,parent)
{
	if (arguments.length>0)
        this.init(node,parent);
}

K_Tabcontrol.prototype.init=function(node,parent)
{
	K_Tabcontrol.superclass.init.call(this,$a(node,'id'),node,parent,'tabcontrol');

	this.tabs=new Array();
	this.activeTab='';
	this.encHeight=0;
	this.posx=parseInt($a(node,'x'));
	this.posy=parseInt($a(node,'y'));
	this.width=parseInt($a(node,'w'));
	this.height=parseInt($a(node,'h'));

	var r=this.xmlDoc.childNodes;
	for (var x=0;x<r.length;x++)
		if (r[x].nodeName=='tab')
			this.addTab($a(r[x],'id'),$a(r[x],'img'),$a(r[x],'caption'));
	this.build();
}


K_Tabcontrol.prototype.render=function()
{
	if ($('K_'+this.id)!=null)
		K_removeNode('K_'+this.id);
	
	var pN=$('K_int_'+this.parent);

	var st=document.createElement('div');
	st.id='K_'+this.id;
	st.style.position='absolute';
	st.style.left=this.posx+'px';
	st.style.top=this.posy+'px';
	st.style.overflow='visible';
	st.style.width=this.width+'px';
	st.style.height=this.height+'px';

	var divb=document.createElement('div');
	divb.id='K_tC_'+this.id;
	divb.style.position='relative';
	divb.className='tabContainerDIV';
	divb.parentKeepFocus=1;
	var myul=document.createElement('ul');
	myul.className='tabContainerUL';
	myul.id='K_tCul_'+this.id;

	for (var x=0;x<this.tabs.length;x++)
	{
		var p=this.tabs[x].split('#@#');
		var id=p[0];
		var img=p[1];
		var caption=p[2];
		var myli=document.createElement('li');
		if (x!=0)
			myli.className='defaultFont tabLeftInactiveNS';
		else
			myli.className='defaultFont tabLeftInactiveS';
		myli.id='K_tabL_'+id;
		myul.appendChild(myli);
		var myli=document.createElement('li');
		myli.parentKeepFocus=1;
		myli.id='K_tabC_'+id;

		myli.tabId=id;
		myli.controlId=this.id;
		myli.onmouseover=this.mouseOver;
		myli.onmouseout=this.mouseOut;
		myli.onclick=this.click;

		if (x!=0)
			myli.className='defaultFont tabCenterInactiveNS';
		else
			myli.className='defaultFont tabCenterInactiveS';
		var divc=document.createElement('div');
		divc.id='K_tabCtx_'+id;
		if(K_gec)
			divc.className='tabText';
		else
			divc.className='tabTextIE';
		var cad='&nbsp;';
		if (img!='')
			cad="<img src='"+img+"' align='absmiddle'/>&nbsp;";
		cad+=caption+'&nbsp;';
		divc.innerHTML=cad;
		myli.appendChild(divc);
		myul.appendChild(myli);
		var myli=document.createElement('li');
		if(x!=0)
			myli.className='defaultFont tabRightInactiveNS';
		else
		{
			myli.className='defaultFont tabRightInactiveS';
			this.activeTab=id;
		}
		myli.id='K_tabR_'+id;
		myul.appendChild(myli);
	}
	divb.appendChild(myul);

	st.appendChild(divb);

	pN.appendChild(st);

	for (var x=0;x<this.tabs.length;x++)
	{
		var p=this.tabs[x].split('#@#');
		var divb=document.createElement('div');
		divb.id='K_int_'+p[0];
		divb.className='tabContentBorder';
		divb.style.width=this.width+'px';
		if (K_gec)
			divb.style.width=this.width-1+'px';
		divb.style.height=this.height-document.getElementById('K_tC_'+this.id).offsetHeight+'px';
		divb.style.overflow='auto';
		divb.style.position='relative';
		if (x!=0)
			divb.style.display='none';
		st.appendChild(divb);
	}

	this.renderChilds();
	setTimeout(this.id+".fillTab()",100);
}

K_Tabcontrol.prototype.fillTab=function()
{

	var e=$t($('K_tC_'+this.id),'li');
	var tW=0;
	for (var x=0;x<e.length;x++)
		tW+=e[x].offsetWidth;
	var u=$('K_tCul_'+this.id);
	var myli=document.createElement('li');
	myli.id='K_tabFill_'+this.id;
	myli.className='tabFill';
	myli.style.width=this.width-tW+'px';
	u.appendChild(myli);

	var zI=window[this.parent].newZindex();
	$('K_tC_'+this.id).style.zIndex=zI;
	--zI;
	for (var x=0;x<this.tabs.length;x++)
	{
		var p=this.tabs[x].split('#@#');
		$('K_int_'+p[0]).style.zIndex=zI;
	}
}

K_Tabcontrol.prototype.addTab=function(id,img,caption)
{
	this.tabs[this.tabs.length]=id+'#@#'+img+'#@#'+caption;
}

K_Tabcontrol.prototype.removeTab=function(id)
{
	for (var x=0;x<this.tabs.length;x++)
	{
		var p=this.tabs[x].split('#@#');
		if(p[0]==id)
			this.tabs.splice(x,1);
	}
}

K_Tabcontrol.prototype.mouseOver=function()
{
	$('K_tabL_'+this.tabId).className=$('K_tabL_'+this.tabId).className.replace('Inactive','Active');
	$('K_tabR_'+this.tabId).className=$('K_tabR_'+this.tabId).className.replace('Inactive','Active');
	$('K_tabC_'+this.tabId).className=$('K_tabC_'+this.tabId).className.replace('Inactive','Active');
}

K_Tabcontrol.prototype.mouseOut=function()
{
	$('K_tabL_'+this.tabId).className=$('K_tabL_'+this.tabId).className.replace('Active','Inactive');
	$('K_tabR_'+this.tabId).className=$('K_tabR_'+this.tabId).className.replace('Active','Inactive');
	$('K_tabC_'+this.tabId).className=$('K_tabC_'+this.tabId).className.replace('Active','Inactive');
}


K_Tabcontrol.prototype.click=function()
{
	eval(this.controlId+".openTab('"+this.tabId+"');");
	window[this.controlId].openTab(this.tabId);
}

K_Tabcontrol.prototype.openTab=function(id)
{
	for (var x=0;x<this.tabs.length;x++)
	{
		var p=this.tabs[x].split('#@#');
		if (p[0]!=id)
		{
			$('K_int_'+p[0]).style.display='none';
			$('K_tabL_'+p[0]).className='defaultFont tabLeftInactiveNS';
			$('K_tabC_'+p[0]).className='defaultFont tabCenterInactiveNS';
			$('K_tabR_'+p[0]).className='defaultFont tabRightInactiveNS';
		}
		else
		{
			$('K_int_'+p[0]).style.display='block';
			$('K_tabL_'+p[0]).className='defaultFont tabLeftInactiveS';
			$('K_tabC_'+p[0]).className='defaultFont tabCenterInactiveS';
			$('K_tabR_'+p[0]).className='defaultFont tabRightInactiveS';
		}
	}
	this.activeTab=id;
	eval ("var t=typeof "+id+"_focus;");
	if (t=='function')
		eval(id+'_focus();');

}

K_Tabcontrol.prototype.show=function()
{
        $('K_'+this.id).style.display='block';
}
K_Tabcontrol.prototype.hide=function()
{
        $('K_'+this.id).style.display='none';
}

K_Tab.prototype=new K_Base();
K_Tab.prototype.constructor=K_Tab;
K_Tab.superclass=K_Base.prototype;


function K_Tab(node,parent)
{
	if (arguments.length>0)
        this.init(node,parent);
}

K_Tab.prototype.init=function(node,parent)
{
	K_Tab.superclass.init.call(this,$a(node,'id'),node,$a(node,'id'),'tabPage');

	this.build();
}

K_Tab.prototype.render=function()
{
	this.renderChilds();
}
