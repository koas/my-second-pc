K_MultipleSelect.prototype=new K_Base();
K_MultipleSelect.prototype.constructor=K_MultipleSelect;
K_MultipleSelect.superclass=K_Base.prototype;

function K_MultipleSelect(node,parent)
{
	if (arguments.length>0)
        	this.init(node,parent);
}

K_MultipleSelect.prototype.init=function(node,parent)
{
	K_MultipleSelect.superclass.init.call(this,$a(node,'id'),node,parent,'multipleselect');
	this.posx=$a(node,'x');
	this.posy=$a(node,'y');
	this.width=parseInt($a(node,'w'));
	this.height=parseInt($a(node,'h'));
	this.autoload=parseInt($a(node,'autoload'));
	this.multiple=parseInt($a(node,'multiple'));
	this.tag='option';
	if ($a(node,'tag'))
		this.tag=$a(node,'tag');
	this.data=null;
	this.dataSrc='';

	var r=$t(this.xmlDoc,'data')[0];
	var t=$a(r,'src');
	if (t=='inline')
		this.setData(r);
	else
		this.setDataSource(t);

	this.build();
}

K_MultipleSelect.prototype.render=function()
{
	if ($('K_'+this.id)!=null)
		K_removeNode('K_'+this.id);

	var pN=$('K_int_'+this.parent);

	var st=document.createElement('div');
	st.id='K_'+this.id;
	st.className='defaultFont MultipleSelect';
	st.style.position='absolute';
	st.style.left=this.posx+'px';
	st.style.top=this.posy+'px';
	st.style.width=this.width+'px';
	st.style.height=this.height+'px';

	if(this.autoload==1)
		st.innerHTML="<table width='100%' height='100%'><tr><td align='center'><span class='defaultFont loading'>"+K_L_loadingDataMessage+"</span></td></tr></table>";

	pN.appendChild(st);

	if(this.autoload==1)
	{
		if (this.dataSrc=='')
			this.dumpData();
		else
			this.loadDataSource();
	}
}

K_MultipleSelect.prototype.dumpData=function()
{
	var pN=$('K_int_'+this.parent);

	var st=$('K_'+this.id);
	st.innerHTML="<table width='100%' height='100%'><tr><td align='center'><span class='defaultFont loading'>"+K_L_loadingDataMessage+"</span></td></tr></table>";

	var cad="<table cellpadding='2' cellspacing='0' width='100%' id='K_"+this.id+"_table' parentKeepFocus='1'>";
	var e=$t(this.data,this.tag);
	for (var x=0;x<e.length;x++)
	{
		var aa=$a(e[x],'value');
		if(aa==null)
			aa=e[x].firstChild.data;
		cad+="<tr valign='top' valor='"+aa+"' class='off' id='K_"+this.id+"_r_"+aa+"' parentKeepFocus='1'><td width='16' onclick=\""+this.id+".check('"+aa+"')\" parentKeepFocus='1'><div parentKeepFocus='1' id='K_"+this.id+"_c_"+aa+"' class='selectOff'></div></td><td style='cursor:default' onclick=\""+this.id+".check('"+aa+"')\" class='txSelectOff' id='K_"+this.id+"_p_"+aa+"' parentKeepFocus='1'>"+e[x].firstChild.data+"</td></tr>";
	}
	cad+="</table>";
	st.innerHTML=cad;
}

K_MultipleSelect.prototype.check=function(value)
{
	if (this.multiple==0)
		this.reset();
	var t=$('K_'+this.id+'_r_'+value);
	if(t.className=='off')
	{
		$('K_'+this.id+'_c_'+value).className='selectOn';
		$('K_'+this.id+'_p_'+value).className='txSelectOn';
		t.className='on';
	}
	else
	{
		$('K_'+this.id+'_c_'+value).className='selectOff';
		$('K_'+this.id+'_p_'+value).className='txSelectOff';
		t.className='off';
	}
	eval ("var t=typeof "+this.id+"_onchange;");
	if (t=='function')
		eval(this.id+'_onchange();');
}

K_MultipleSelect.prototype.reset=function()
{
	var e=$t($('K_'+this.id+'_table'),'tr');
	for (var x=0;x<e.length;x++)
		if (e[x].className=='on')
		{
			var p=e[x].id.replace('r','c');
			$(p).className='selectOff';
			var p=e[x].id.replace('r','p');
			$(p).className='txSelectOff';
			e[x].className='off';
		}
}

K_MultipleSelect.prototype.getValues=function()
{
	var cad='';
	var e=$t($('K_'+this.id+'_table'),'tr');
	for (var x=0;x<e.length;x++)
		if (e[x].className=='on')
			cad+=$a(e[x],'valor')+';';
	return cad.substring(0,cad.length-1);
}

K_MultipleSelect.prototype.setValues=function(valores)
{
	this.reset();
	var p=valores.split(';');

	var e=$t($('K_'+this.id+'_table'),'tr');
	for (var x=0;x<e.length;x++)
	{
		var id=$a(e[x],'valor');
		for (var y=0;y<p.length;y++)
		{
			if(id==p[y])
			{
				var i=e[x].id.replace('r','c');
				$(i).className='selectOn';
				var i=e[x].id.replace('r','p');
				$(i).className='txSelectOn';
				e[x].className='on';
			}
		}
	}
}

K_MultipleSelect.prototype.setData=function(xmlDoc)
{
	this.data=xmlDoc;
}

K_MultipleSelect.prototype.setDataSource=function(src)
{
	this.dataSrc=src;
}


K_MultipleSelect.prototype.loadDataSource=function()
{
	var st=$('K_'+this.id);
	st.innerHTML="<table width='100%' height='100%'><tr><td align='center'><span class='defaultFont loading'>"+K_L_loadingDataMessage+"</span></td></tr></table>";
	var dest=this.dataSrc;
	var param='rand='+Math.random();
	var pos=dest.indexOf("?");
	if (pos>-1)
		dest+='&'+param;
	else
		dest+='?'+param;

	K_loadAJAX(dest, this.id+'.processDataSource');
}

K_MultipleSelect.prototype.processDataSource=function(xmlDoc)
{
	var e=$t(xmlDoc,'ws')[0];
	this.setData(e);
	this.dumpData();
}
