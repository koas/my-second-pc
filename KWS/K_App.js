K_App.prototype=new K_Base();
K_App.prototype.constructor=K_App;
K_App.superclass=K_Base.prototype;

function K_App(id,xmlDoc,name,parent)
{
	if (arguments.length>0)
		this.init(id,xmlDoc,name,parent);
}

K_App.prototype.init=function(id,xmlDoc,name,parent)
{
	this.name=name;
	if($t(xmlDoc,'code')[0])
		eval($t(xmlDoc,'code')[0].firstChild.data);
	K_App.superclass.init.call(this,id,xmlDoc,parent);
	this.build();
	this.xmlDoc='';
}

