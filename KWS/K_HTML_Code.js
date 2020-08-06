K_HTML_Code.prototype=new K_Base();
K_HTML_Code.prototype.constructor=K_HTML_Code;
K_HTML_Code.superclass=K_Base.prototype;

function K_HTML_Code(node,parent)
{
	if (arguments.length>0)
       	 this.init(node,parent);
}

K_HTML_Code.prototype.init=function(node,parent)
{
	K_HTML_Code.superclass.init.call(this,$a(node,'id'),node,parent,'code');


	this.posx=$a(node,'x');
	this.posy=$a(node,'y');
	this.width=$a(node,'w');
	this.height=$a(node,'h');

	this.content=this.xmlDoc.firstChild.data;

	this.build();
}

K_HTML_Code.prototype.render=function()
{
	if ($('K_'+this.id)!=null)
		K_removeNode('K_'+this.id);

	var p=$('K_int_'+this.parent);
	if (this.posx=='auto')
		this.posx=0;
	else
		this.posx=parseInt(this.posx);
	if (this.posy=='auto')
		this.posy=0;
	else
		this.posy=parseInt(this.posy);
	if (this.width=='auto')
		this.width='100%';
	else
		this.width=this.width+'px';
	if (this.height=='auto')
		this.height='100%';
	else
		this.height=this.height+'px';

	var d=document.createElement('div');
	d.id='K_'+this.id;
	d.style.position='absolute';
	d.style.left=this.posx+'px';
	d.style.top=this.posy+'px';
	d.style.width=this.width;
	d.style.height=this.height;
	d.innerHTML=this.content;

	p.appendChild(d);
}
