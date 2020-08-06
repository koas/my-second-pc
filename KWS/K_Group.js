K_Group.prototype=new K_Base();
K_Group.prototype.constructor=K_Group;
K_Group.superclass=K_Base.prototype;

function K_Group(node,parent)
{
	if (arguments.length>0)
        	this.init(node,parent);
}

K_Group.prototype.init=function(node,parent)
{
	K_Group.superclass.init.call(this,$a(node,'id'),node,parent,'group');
	this.posx=parseInt($a(node,'x'));
	this.posy=parseInt($a(node,'y'));
	this.width=parseInt($a(node,'w'));
	this.height=parseInt($a(node,'h'));
	this.image=$a(node,'img');
	this.overflow=$a(node,'ovr');
	this.caption=$a(node,'caption');
	this.build();
}


K_Group.prototype.render=function()
{
	if ($('K_'+this.id)!=null)
		K_removeNode('K_'+this.id);

	var pN=$('K_int_'+this.parent);

	var st=document.createElement('div');
	st.id='K_'+this.id;
	st.className='defaultFont';
	st.style.position='absolute';
	st.style.left=this.posx+'px';
	st.style.top=this.posy+'px';
	st.style.cursor='default';
	st.style.width=this.width+'px';
	st.style.height=this.height+'px';

	var divb=document.createElement('div');
	divb.style.position='absolute';
	divb.style.left='0px';
	divb.style.top='8px';
	divb.style.width=this.width-2+'px';
	divb.style.height=this.height-2-8+'px';
	divb.className='group';
	st.appendChild(divb);

	var cad="&nbsp;&nbsp;<span id='K_"+this.id+"_caption'>"+this.caption+'</span>';
	if (this.image!='')
		cad="&nbsp;<img src='"+this.image+"' align='absmiddle'/>"+cad+'&nbsp;&nbsp;';
	var divb=document.createElement('div');
	divb.className='defaultFont groupCaption';
	divb.innerHTML=cad;
	st.appendChild(divb);

	var divb=document.createElement('div');
	divb.id='K_int_'+this.id;
	divb.className='defaultFont groupContent';
	divb.style.width=this.width-K_groupMinusWidth+'px';
	divb.style.height=this.height-6-K_groupMinusHeight+'px';
	divb.style.overflow=this.overflow;
	st.appendChild(divb);

	pN.appendChild(st);

	this.renderChilds();
}


K_Group.prototype.setCaption=function(value)
{
	$('K_'+this.id+'_caption').innerHTML=value;
}

K_Group.prototype.getCaption=function()
{
	return $('K_'+this.id+'_caption').innerHTML;
}
