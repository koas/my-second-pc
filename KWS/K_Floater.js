K_Floater.prototype=new K_Base();
K_Floater.prototype.constructor=K_Floater;
K_Floater.superclass=K_Base.prototype;

function K_Floater(node,parent)
{
	if (arguments.length>0)
        	this.init(node,parent);
}

K_Floater.prototype.init=function(node,parent)
{
	K_Floater.superclass.init.call(this,$a(node,'id'),node,parent,'floater');

	this.posx=$a(node,'x');
	this.posy=$a(node,'y');
	this.width=parseInt($a(node,'w'));
	this.height=parseInt($a(node,'h'));
	this.boundaries=$a(node,'boundaries');
	this.caption=$a(node,'caption');
	this.bg=$a(node,'bg');
	this.canClose=$a(node,'close');

	var l=this.boundaries.split(',');
	this.minX=l[0];
	this.minY=l[1];
	this.maxX=l[2];
	this.maxY=l[3];
	this.movable=1;

	this.build();
}


K_Floater.prototype.render=function()
{
	var pN=$('K_int_'+this.parent);
	if ($('K_'+this.id)!=null)
		K_removeNode('K_'+this.id);

	var st=document.createElement('div');
	st.id='K_'+this.id;
	st.style.position='absolute';
	st.style.left=this.posx+'px';
	st.style.top=this.posy+'px';
	st.style.width=this.width+'px';
	st.style.height=this.height+'px';
	st.style.overflow='hidden';
	if (this.bg!='')
		st.style.background=this.bg;
	st.className='floater';
	if (K_ie)
		st.onselectstart=function () {return false;};


	var divb=document.createElement('div');
	divb.id='K_fH_'+this.id;
	divb.className='defaultFont floaterHeader';
	divb.innerHTML='&nbsp;'+this.caption;
	divb.onmousedown=this.mouseDown;
	st.appendChild(divb);

	if (this.canClose==1)
	{
		var divb=document.createElement('div');
		divb.id='K_fC_'+this.id;
		divb.className='floaterClose';
		divb.onclick=this.clickClose;
		st.appendChild(divb);
	}

	var divb=document.createElement('div');
	divb.id='K_fM_'+this.id;
	divb.className='floaterMin';
	divb.onclick=this.clickMin;
	if (this.canClose==0)
		divb.style.right='1px';
	st.appendChild(divb);



	var divb=document.createElement('div');
	divb.id='K_int_'+this.id;
	divb.style.width='100%';
	divb.style.position='relative';
	st.appendChild(divb);

	pN.appendChild(st);

	$('K_int_'+this.id).style.height=this.height-$('K_fH_'+this.id).offsetHeight+'px';
	this.renderChilds();
}

K_Floater.prototype.clickClose=function(e)
{
	var id=this.id.replace('K_fC_','');
	window[id].hide();
}

K_Floater.prototype.clickMin=function(e)
{
	var id=this.id.replace('K_fM_','');
	if(this.className=='floaterMin')
	{
		this.className='floaterMax';
		window[id].minimize();
	}
	else
	{
		this.className='floaterMin';
		window[id].maximize();
	}
}

K_Floater.prototype.minimize=function()
{
	$('K_'+this.id).style.height=$('K_fH_'+this.id).offsetHeight+'px';
}

K_Floater.prototype.maximize=function()
{
	$('K_int_'+this.id).style.display='block';
	$('K_'+this.id).style.height=this.height+'px';
}

K_Floater.prototype.bringToFront=function()
{
	var nZi=window[this.parent].newZindex();
	$('K_'+this.id).style.zIndex=nZi;
}

K_Floater.prototype.mouseDown=function(e)
{

	K_resize=0;
	K_idDrag=this.id.replace('K_fH_','');

	window[K_idDrag].bringToFront();

	var minX=window[K_idDrag].minX;
	var maxX=window[K_idDrag].maxX;
	var minY=window[K_idDrag].minY;
	var maxY=window[K_idDrag].maxY;

	if (minX=='auto')
		K_moveMinX=0;
	else
		K_moveMinX=parseInt(minX);
	if (minY=='auto')
		K_moveMinY=0;
	else
		K_moveMinY=parseInt(minY);

	if (maxX=='auto')
		K_moveMaxX=$('K_int_'+window[K_idDrag].parent).offsetWidth - window[K_idDrag].width;
	else
		K_moveMaxX=maxX - window[K_idDrag].width;
	if (maxY=='auto')
		K_moveMaxY=$('K_int_'+window[K_idDrag].parent).offsetHeight - window[K_idDrag].height;
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

K_Floater.prototype.moveTo=function(px,py)
{
	var obj=$('K_'+this.id);
	if (!isNaN(px)) this.posx=px;
	if (!isNaN(py)) this.posy=py;
	obj.style.left=this.posx+'px';
	obj.style.top=this.posy+'px';
}

K_Floater.prototype.endMove=function()
{
}

K_Floater.prototype.hide=function()
{
	$('K_'+this.id).style.display='none';
}
K_Floater.prototype.show=function()
{
	$('K_'+this.id).style.display='block';
}
