/*Code for the Datatable heavily based in a TableWidget from:

	(C) www.dhtmlgoodies.com, October 2005

	This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.

	Terms of use:
	You are free to use this script as long as the copyright message is kept intact. However, you may not
	redistribute, sell or repost it without our permission.

	Thank you!

	www.dhtmlgoodies.com
	Alf Magne Kalleland

	*/

K_Datatable.prototype=new K_Base();
K_Datatable.prototype.constructor=K_Datatable;
K_Datatable.superclass=K_Base.prototype;

function K_Datatable(node,parent)
{
	if (arguments.length>0)
        	this.init(node,parent);
}

K_Datatable.prototype.init=function(node,parent)
{
	K_Datatable.superclass.init.call(this,$a(node,'id'),node,parent,'datatable');
	this.posx=$a(node,'x');
	this.posy=$a(node,'y');
	this.width=parseInt($a(node,'w'));
	this.height=parseInt($a(node,'h'));
	this.autoload=parseInt($a(node,'autoload'));
	this.maxRows=parseInt($a(node,'maxrows'));
	this.columns=new Array();
	this.data=null;
	this.dataSrc='';
	if (this.maxRows==0)
		this.maxRows=10;
	this.totalRows=0;
	this.activePage=0;
	this.totalPages=0;

	var r=$t($t(this.xmlDoc,'cols')[0],'col');
	for (var x=0;x<r.length;x++)
		this.addCol(r[x].firstChild.data,$a(r[x],'img'),$a(r[x],'action'),$a(r[x],'auto'),$a(r[x],'w'),$a(r[x],'align'),$a(r[x],'hint'));

	var r=$t(this.xmlDoc,'data')[0];
	var t=$a(r,'src');
	if (t=='inline')
		this.setData(r);
	else
		this.setDataSource(t);

	this.build();
}

K_Datatable.prototype.render=function()
{
	if ($('K_'+this.id)!=null)
		K_removeNode('K_'+this.id);

	var pN=$('K_int_'+this.parent);

	var st=document.createElement('div');
	st.id='K_'+this.id;
	st.className='defaultFont dataTable';
	st.style.position='absolute';
	st.style.left=this.posx+'px';
	st.style.top=this.posy+'px';
	st.style.width=this.width+'px';
	st.style.height=this.height+'px';
	st.style.overflow='hidden';
	if (K_ie && !K_gec && !K_op && !K_op && !K_saf && !K_ep &&!K_ns)
		st.style.overflowY='auto';

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

K_Datatable.prototype.dumpData=function()
{
	var pN=$('K_int_'+this.parent);

	var t=window[this.parent].type;
	if (t=='accordionPage' || t=='tabPage')
	{
		this.oldParentDisplay=pN.style.display;
		pN.style.display='block';
	}

	var st=$('K_'+this.id);
	st.innerHTML="<table width='100%' height='100%'><tr><td align='center'><span class='defaultFont loading'>"+K_L_loadingDataMessage+"</span></td></tr></table>";

	var cad="<div id='K_"+this.id+"_d1' style='width:"+this.width+"px;'><table id='K_"+this.id+"_mt' width='100%' border='0'><thead><tr id='K_"+this.id+"_enc'>";

	for (var x=0;x<this.columns.length;x++)
	{
		var p=this.columns[x].split('#@#');
		if (x==0)
			cad+="<td class='tableWidget_headerCell1' style='";
		else
			cad+="<td class='tableWidget_headerCell2' style='";
		if (p[4]!='' && !isNaN(p[4]))
			cad+="width:"+p[4]+"px;";
		if (p[5]=='center')
			cad+="text-align:center;";
		if (p[5]=='right')
			cad+="text-align:right;";
		cad+="' title='"+p[6]+"'>";
		if (p[1]!='')
			cad+="<img src='"+p[1]+"' align='absmiddle' style='margin-right:3px'/>";
		cad+=p[0]+"</td>";
	}
	cad+="</tr></thead><tbody class='scrollingContent'>";

	var e=$t(this.data,'row');
	if (this.dataSrc=='')
	{
		var init=this.activePage*this.maxRows;
		var end=init+parseInt(this.maxRows);
		if (end>e.length)
			end=e.length;
	}
	else
	{
		var init=0;
		var end=e.length;
	}


	for (var x=init;x<end;x++)
	{
		cad+="<tr id='K_"+this.id+"_row"+$a(e[x],'id')+"'>";
		var r=$t(e[x],'r');
		for (var y=0;y<this.columns.length;y++)
		{
			cad+="<td id='K_"+this.id+"_row"+$a(e[x],'id')+"_"+y+"' class='dataTableBody ";
			if (x%2==0)
				cad+="dataTableBG1";
			else
				cad+="dataTableBG2";
			cad+="' ";
			var p=this.columns[y].split('#@#');
			if (r[y])
				var valueId=$a(r[y],'id');
			else
				var valueId=0;
			cad+=" valueid='"+valueId+"' ";
			if (p[2]!='')
				cad+=" onclick=\""+p[2]+"('"+$a(e[x],'id')+"','"+y+"')\"";
			if (p[5]=='center')
				cad+=" style='text-align:center;'";
			if (p[5]=='right')
				cad+=" style='text-align:right;'";
			cad+=">";

			if (p[3]=='caption')
				cad+=p[0];
			if (p[3]=='img')
				cad+="<img src='"+p[1]+"' title='"+p[6]+"'/>";
			if (p[3]=="")
				cad+=r[y].firstChild.data;

			cad+="</td>";
		}
		cad+="</tr>";
	}

	cad+="</tbody></table></div>";

	var cadpag="<div id='K_"+this.id+"_pag' style='text-align:center;width:100%' class='dataTablePaginator'><table class='defaultFont dataTablePaginator' cellpadding='0' cellspacing='0' align='center'><tr valign='top'>";
	if (this.activePage!=0)
		cadpag+="<td><div class='dataTableFirst' onclick='"+this.id+".firstPage()' title='"+K_L_firstPage+"'></div></td><td>&nbsp;&nbsp;</td><td><div class='dataTablePrevious' onclick='"+this.id+".previousPage()' title='"+K_L_previousPage+"'></div></td>";
	cadpag+="<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td valign='middle'>"+ K_L_pageText+" &nbsp;</td><td><select class='defaultFont dataTablePaginator' onchange='"+this.id+".gotoPage(this.value)'>";
	for (var x=0;x<this.totalPages;x++)
	{
		cadpag+="<option value='"+x+"'";
		if (x==this.activePage) cadpag+=' selected="selected"';
		cadpag+=">"+(x+1)+"</option>";
	}
	cadpag+="</select></td><td valign='middle'> &nbsp;&nbsp;/ "+this.totalPages+"</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";

	if (this.activePage<this.totalPages-1)
		cadpag+="<td><div class='dataTableNext' onclick='"+this.id+".nextPage()' title='"+K_L_nextPage+"'></div></td><td>&nbsp;&nbsp;</td><td><div class='dataTableLast' onclick='"+this.id+".lastPage()' title='"+K_L_lastPage+"'></div></td>";
	cadpag+="</tr></table></div>";
	cad+=cadpag;

	st.innerHTML=cad;

	$('K_'+this.id+'_d1').style.height=this.height-$('K_'+this.id+'_pag').offsetHeight+'px';
	setTimeout(this.id+'.fixTable();',10);
}

K_Datatable.prototype.addEndCol=function(obj)
{
	if(document.all)return;
	var rows = $t(obj,'TR');
	for(var no=0;no<rows.length;no++){
		var cell = rows[no].insertCell(-1);
		cell.innerHTML = '&nbsp;';
		cell.style.width = '13px';
		cell.width = '13';
	}
}

K_Datatable.prototype.fixTable=function()
{
	var obj=$("K_"+this.id+"_mt");
	obj.style.width='100%';

	this.addEndCol(obj);

	obj.cellSpacing = 0;
	obj.cellPadding = 0;
	obj.className='tableWidget';

	var tHead = $t(obj,'THEAD')[0];
	var tBody = $t(obj,'TBODY')[0];

	if(document.all && navigator.userAgent.indexOf('Opera')<0){
		tBody.className='scrollingContent';
		tBody.style.display='block';
	}else{
		tBody.className='scrollingContent';
		var g1=$('K_'+this.id+'_d1').offsetHeight;
		var g2=tHead.offsetHeight;
		g1-=g2;
		tBody.style.height = g1 + 'px';
		if(navigator.userAgent.indexOf('Opera')>=0){
			obj.parentNode.style.overflow = 'auto';
		}
	}

	if (K_ie && !K_op && !K_saf && !K_ns && !K_gec)
	{
		obj.style.width=obj.offsetWidth-16+'px';
	}
	eval ("var t="+this.parent+".type;");
	if (t=='accordionPage' || t=='tabPage')
	{
		var pN=$('K_int_'+this.parent);
		pN.style.display=this.oldParentDisplay;
	}

}

K_Datatable.prototype.addCol=function(caption,img,action,auto,w,align,hint)
{
	if (caption=='#') caption='';
	this.columns[this.columns.length]=caption+'#@#'+img+'#@#'+action+'#@#'+auto+'#@#'+w+'#@#'+align+'#@#'+hint;
}

K_Datatable.prototype.removeCol=function(caption)
{
	for (var x=0;x<this.columns.length;x++)
	{
		var p=this.columns[x].split('#@#');
		if(p[0]==caption)
			this.columns.splice(x,1);
	}
}

K_Datatable.prototype.setData=function(xmlDoc)
{
	this.data=xmlDoc;
	var t=$t(this.data,'row');
	if (this.dataSrc=='')
	{
		this.totalRows=t.length;
		this.activePage=0;
	}
	this.totalPages=Math.ceil(this.totalRows/this.maxRows);
}

K_Datatable.prototype.setDataSource=function(src)
{
	this.dataSrc=src;
}


K_Datatable.prototype.loadDataSource=function()
{
	var st=$('K_'+this.id);
	st.innerHTML="<table width='100%' height='100%'><tr><td align='center'><span class='defaultFont loading'>"+K_L_loadingDataMessage+"</span></td></tr></table>";
	var dest=this.dataSrc;
	var param='page='+this.activePage+'&max='+this.maxRows+'&rand='+Math.random();
	var pos=dest.indexOf("?");
	if (pos>-1)
		dest+='&'+param;
	else
		dest+='?'+param;

	K_loadAJAX(dest, this.id+'.processDataSource');
}

K_Datatable.prototype.processDataSource=function(xmlDoc)
{
	var e=$t(xmlDoc,'tabledata')[0];
	this.totalRows=$a(e,'total');
	this.setData(e);
	this.dumpData();
}

K_Datatable.prototype.nextPage=function()
{
	++this.activePage;
	if (this.dataSrc=='')
		this.dumpData();
	else
		this.loadDataSource();
}

K_Datatable.prototype.previousPage=function()
{
	--this.activePage;
	if (this.dataSrc=='')
		this.dumpData();
	else
		this.loadDataSource();}

K_Datatable.prototype.firstPage=function()
{
	this.activePage=0;
	if (this.dataSrc=='')
		this.dumpData();
	else
		this.loadDataSource();}

K_Datatable.prototype.lastPage=function()
{
	this.activePage=this.totalPages-1;
	if (this.dataSrc=='')
		this.dumpData();
	else
		this.loadDataSource();}

K_Datatable.prototype.gotoPage=function(page)
{
	if (page<0 || page > this.totalPages)
		return;
	this.activePage=page;
	if (this.dataSrc=='')
		this.dumpData();
	else
		this.loadDataSource();
}
