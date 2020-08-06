K_Accordion.prototype=new K_Base();
K_Accordion.prototype.constructor=K_Accordion;
K_Accordion.superclass=K_Base.prototype;

function K_Accordion(node,parent)
{
	if (arguments.length>0)
        	this.init(node,parent);
}

K_Accordion.prototype.init=function(node,parent)
{
	K_Accordion.superclass.init.call(this,$a(node,'id'),node,parent,'accordion');

	this.posx=$a(node,'x');
	this.posy=$a(node,'y');
	this.width=parseInt($a(node,'w'));
	this.height=parseInt($a(node,'h'));
	if (K_ie)
		this.height-=4;
	this.hasBorder=$a(node,'border');
	this.pages=new Array();
	this.activePage='';
	this.encHeight=0;

	var r=this.xmlDoc.childNodes;
	for (var x=0;x<r.length;x++)
		if (r[x].nodeName=='page')
			this.addPage($a(r[x],'id'),$a(r[x],'img'),$a(r[x],'caption'),$a(r[x],'bg'));

	this.build();
}

K_Accordion.prototype.render=function()
{
	var pN=$('K_int_'+this.parent);
	if ($('K_'+this.id)!=null)
		old.parentNode.removeChild(old);

	var st=document.createElement('div');
	st.id='K_'+this.id;
	st.style.position='absolute';
	st.style.left=this.posx+'px';
	st.style.top=this.posy+'px';
	st.style.overflow='hidden';
	st.style.width=this.width+'px';
	st.style.height=this.height+'px';
	st.className='accordion';
	if (this.hasBorder==0)
		st.style.border='0px';

	for (var x=0;x<this.pages.length;x++)
	{
		var p=this.pages[x].split('#@#');
		var id=p[0];
		var img=p[1];
		var caption=p[2];
		var bg=p[3];
		var divb=document.createElement('div');
		divb.id='K_'+id;
		divb.accordionId=this.id;
		divb.style.overflow='hidden';
		divb.style.position='relative';

		divb.className='defaultFont accordionHeader';

		divb.onclick=this.click;

		var cad="";
		if (img!='')
			cad="<img style='margin:2px' src='"+img+"' align='absmiddle'/>";
		cad+='&nbsp;&nbsp;'+caption;
		divb.innerHTML=cad;

		st.appendChild(divb);
		var divb=document.createElement('div');
		divb.id='K_int_'+id;
		divb.style.backgroundColor=bg;
		divb.style.overflow='auto';
		divb.style.position='relative';
		divb.style.width='100%';
		divb.style.height='0px';
		if(x!=0)
			divb.style.display='none';
		st.appendChild(divb);
	}
	pN.appendChild(st);

	var p=this.pages[0].split('#@#');
	var o=$('K_'+p[0]);
	this.encHeight=o.offsetHeight;
	this.activePage=p[0];
	$('K_int_'+p[0]).style.height=this.height-(this.encHeight*this.pages.length)+'px';

	this.renderChilds();
}

K_Accordion.prototype.addPage=function(id,img,caption,bg)
{
	this.pages[this.pages.length]=id+'#@#'+img+'#@#'+caption+'#@#'+bg;
}

K_Accordion.prototype.removePage=function(id)
{
	for (var x=0;x<this.pages.length;x++)
	{
		var p=this.pages[x].split('#@#');
		if(p[0]==id)
			this.pages.splice(x,1);
	}
}

K_Accordion.prototype.click=function(e)
{
	window[this.accordionId].openPage(this.id);
}

K_Accordion.prototype.openPage=function(dest)
{
	var id=dest.replace('K_','K_int_');
	$('K_int_'+this.activePage).style.display='none';
	$(id).style.height=this.height-(this.encHeight*this.pages.length)+'px';
	$(id).style.display='block';
	this.activePage=dest.replace('K_','');
}

K_AccordionPage.prototype=new K_Base();
K_AccordionPage.prototype.constructor=K_AccordionPage;
K_AccordionPage.superclass=K_Base.prototype;


function K_AccordionPage(node,parent)
{
	if (arguments.length>0)
       		this.init(node,parent);
}

K_AccordionPage.prototype.init=function(node,parent)
{
	K_AccordionPage.superclass.init.call(this,$a(node,'id'),node,$a(node,'id'),'accordionPage');

	this.build();
}

K_AccordionPage.prototype.render=function()
{
	this.renderChilds();
}
