<?
session_start();
$appID='@!@';
include('../../php/conf.php');
include('locale.php');
$now = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0');
header('Last-Modified: ' . $now);
header('Cache-Control: no-store, no-cache, must-revalidate'); 
header('Cache-Control: pre-check=0, post-check=0, max-age=0');
header('Pragma: no-cache');
header('Content-Type: text/xml');

if ($_SESSION['M2PC_USER']<1)
die( '<?xml version="1.0" encoding="iso-8859-1" standalone="yes"?>
<res><stat>AUTHERR</stat></res>');

echo'<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
?>
<app name="<?=$L1?>" id="M2PC_<?=$appID?>" cacheable='1'>
<stat>OK</stat>
<code><![CDATA[
M2PC_<?=$appID?>_exiting=false;
M2PC_<?=$appID?>_main=function(appId, params)
{
		var p=params.split('#');
		M2PC_<?=$appID?>_wMain.render();
		M2PC_<?=$appID?>_wMain.innerDesktop=true;
		M2PC_<?=$appID?>_wAbout.render();
		M2PC_<?=$appID?>_wAbout.hide();
		
		M2PC_<?=$appID?>_wMain.addToTaskbar('M2PC_TSKBR1');
		K_stopBounce();
		initTetris();
}

M2PC_<?=$appID?>_wMain_destroy=function()
{
		M2PC_<?=$appID?>_exiting=true;
		M2PC_<?=$appID?>_wAbout.destroy();
		M2PC_<?=$appID?>_HC1=null;
		K_unloadApp('M2PC_<?=$appID?>');
		return true;
}

M2PC_<?=$appID?>_wAbout_destroy=function()
{
		if(!M2PC_<?=$appID?>_exiting)
		{
				M2PC_<?=$appID?>_wMain.lockContent(false,'');
				M2PC_<?=$appID?>_wMain.bringToFront();
				M2PC_<?=$appID?>_wAbout.hide();
				return false;
		}
		return true;
}

M2PC_<?=$appID?>_cmAbout_click=function()
{
		window['M2PC_<?=$appID?>_wMain'].lockContent(true,'M2PC_<?=$appID?>_wAbout');
		M2PC_<?=$appID?>_wAbout.show();
		M2PC_<?=$appID?>_wAbout.bringToFront();
}
M2PC_<?=$appID?>_wMain_about=function()
{
		M2PC_<?=$appID?>_cmAbout_click();
}

/* Variables globales */
var velocidad=500;


//Arrays de piezas
//Pieza larga
var tipo1_0=new Array (0,-1,0,1,0,2);
var tipo1_1=new Array (-2,0,-1,0,1,0);

//Ele
var tipo2_0=new Array (0,-1,0,1,1,1);
var tipo2_1=new Array (-1,0,1,0,1,-1);
var tipo2_2=new Array (-1,-1,0,-1,0,1);
var tipo2_3=new Array (-1,1,-1,0,1,0);

//Ele invertida
var tipo3_0=new Array (-1,0,1,0,1,1);
var tipo3_1=new Array (0,1,0,-1,1,-1);
var tipo3_2=new Array (-1,-1,-1,0,1,0);
var tipo3_3=new Array (-1,1,0,-1,0,1);

//Nave espacial
var tipo4_0=new Array (-1,0,1,0,0,1);
var tipo4_1=new Array (0,-1,1,0,0,1);
var tipo4_2=new Array (-1,0,0,-1,1,0);
var tipo4_3=new Array (-1,0,0,-1,0,1);

//Cuadrado
var tipo5_0=new Array (-1,0,-1,1,0,1);

//Cuatro invertido
var tipo6_0=new Array (0,-1,-1,0,-1,1);
var tipo6_1=new Array (-1,-1,0,-1,1,0);

//Cuatro
var tipo7_0=new Array (-1,-1,-1,0,0,1);
var tipo7_1=new Array (0,1,1,0,-1,1);


/* Inicio objetos */

/* Objeto pozo */
//Constructor
Pozo_=function(ancho, alto, posx, posy, anchopieza, altopieza)
{
	clearInterval(timer);
	velocidad=500;
	//Definíción propiedades y métodos
	this.nivel=1;
	this.nivelantiguo=0;
	this.lineas=0;
	this.lineasantiguas=-1;
	this.puntos=0;
	this.puntosantiguos=-1;
	this.ancho=ancho;
	this.alto=alto;
	this.anchopieza=anchopieza;
	this.altopieza=altopieza;
	this.tamanio=this.alto*this.ancho;
	this.posx=posx;
	this.posy=posy;
	this.contenido=new Array(this.tamanio);
	this.colores = new Array ('negro.png','rojo.png','morado.png','amarillo.png','verde.png','azul.png','naranja.png','cyan.png');
	this.limpia=limpia_;
	this.pinta=pinta_;
	this.celda=celda_;
	this.celda2=celda2_;
	this.chequealineas=check_;
	this.pintapuntos=pintapuntos_;

	var cad="<table width='"+(this.ancho*this.anchopieza)+"' height='"+(this.alto*this.altopieza)+"' cellpadding='0' border='0' cellspacing='0' style='border:0px solid #666666;position:absolute;left:"+(this.posx)+"px;top:"+(this.posy)+"px;'>";
	//Inicialización
	for (y=0;y<this.alto;y++)
	{
		cad+="<tr>";
		for (x=0;x<this.ancho;x++)
		{
			z=x+y*this.ancho;
			cad+="<td id='celda_"+z+"' style='width:20px;height:20px;padding:0;'><img src='<?=$static?>img/tetris/negro.png'/></td>";
			this.contenido[z]=0;
		}
		cad+="</tr>";
	}
	cad+="</table>";
	cad+="<div id='gameover'  style='visibility:hidden;font-family:Arial;font-size:11px;font-weight:bold;position:absolute;left:"+this.posx+"px;top:"+(this.posy+(this.alto*this.anchopieza/2)-20)+"px;width:"+(this.ancho*this.anchopieza-5)+"px;height:40px;background-color:#FFFFFF;border-width:3px;border-style:solid;border-color:#000;text-align:center'><br />GAME OVER</div>";
	cad+="<div id='puntos'  style='font-family:Arial;font-size:11px;font-weight:bold;position:absolute;left:"+(this.posx+(this.ancho*this.anchopieza)+5)+"px;top:"+(this.posy)+"px;color:#FFF;text-align:center'><?=$L3?>: 0</div>";
	cad+="<div id='nivel'  style='font-family:Arial;font-size:11px;font-weight:bold;position:absolute;left:"+(this.posx+(this.ancho*this.anchopieza)+5)+"px;top:"+(this.posy+20)+"px;color:#FFF;text-align:center'><?=$L4?>: "+this.nivel+" </div>";
	cad+="<div id='lineas'  style='font-family:Arial;font-size:11px;font-weight:bold;position:absolute;left:"+(this.posx+(this.ancho*this.anchopieza)+5)+"px;top:"+(this.posy+40)+"px;color:#FFF;text-align:center'><?=$L5?>: "+this.lineas+" </div>";
	cad+="<div id='reiniciar'  style='font-family:Arial;font-size:11px;font-weight:bold;position:absolute;left:"+(this.posx+(this.ancho*this.anchopieza)+5)+"px;top:"+(this.posy+70)+"px;color:#FFF;text-align:center'><input id='butTetris' type='button' class='button' style='width:84px' value='  <?=$L2?>  ' onclick=\"botTetris()\"/></div>";
	document.getElementById('mainTetris').innerHTML=cad;
	document.getElementById('butTetris').parentKeepFocus=1;
}

//Comprueba si ha habido una linea completa
check_=function()
{
var lines=0;
	for (y=this.alto-1;y>1;y--)
	{
		suma=0;
		for (x=0;x<this.ancho;x++)
			if (this.contenido[x+y*this.ancho]==0)
				suma++;
		if (suma==0)
		{
			//Ha habido línea completa, la eliminamos
			for (x=0;x<this.ancho;x++)
				this.contenido[x+y*this.ancho]=0;
			//Hacemos descender las anteriores
			for (y1=y;y1>1;y1--)
				for (x=0;x<this.ancho;x++)
					this.contenido[x+y1*this.ancho]=this.contenido[x+(y1-1)*this.ancho];
			y++;
			lines++;
		}
	}
	if (lines>0)
	{
		this.lineas+=lines;
		if(this.lineas%10==0)
		{
			++this.nivel;
			velocidad-=50;
			clearInterval(timer);
			timer=setInterval("mueve()",velocidad);

		}

		this.pinta();
	}
	if (lines==1) this.puntos+=100;
	if (lines==2) this.puntos+=150;
	if (lines==3) this.puntos+=250;
	if (lines==4) this.puntos+=400;

}

//Vacia el contenido del pozo
limpia_=function()
{
	for (x=0;x<this.tamanio;x++)
		this.contenido[x]=0;
}

//Dibuja en la pantalla el contenido del pozo
pinta_=function()
{
	for (x=0;x<this.ancho;x++)
		for (y=0;y<this.alto;y++)
			this.celda2(x,y);
}

//Dibuja los puntos
pintapuntos_=function()
{
	if (this.puntos!=this.puntosantiguos)
	{
		document.getElementById('puntos').innerHTML='<?=$L3?>: '+this.puntos;
		this.puntosantiguos=this.puntos;
	}
	if (this.nivel!=this.nivelantiguo)
	{
		document.getElementById('nivel').innerHTML='<?=$L4?>: '+this.nivel;
		this.nivelantiguo=this.nivel;
	}
	if (this.lineas!=this.lineasantiguas)
	{
		document.getElementById('lineas').innerHTML='<?=$L5?>: '+this.lineas;
		this.lineasantiguas=this.lineas;
	}
}

//Dibuja una celda del pozo con un color
celda_ =function(x, y, color)
{
	if (x>=this.ancho)
		x=this.ancho-1;
	if (y>=this.alto)
		y=this.alto-1;

	document.getElementById('celda_'+(x+y*this.ancho)).innerHTML="<img src='"+"<?=$static?>"+'img/tetris/'+color+"'/>";
}
//Dibuja una celda del pozo con su color
celda2_ =function(x, y)
{
	if (x>=this.ancho)
		x=this.ancho-1;
	if (y>=this.alto)
		y=this.alto-1;

	document.getElementById('celda_'+(x+y*this.ancho)).innerHTML="<img src='"+"<?=$static?>"+'img/tetris/'+this.colores[this.contenido[x+y*this.ancho]]+"'/>";
}

/* Objeto ficha */
//Constructor
Ficha_ =function(posx_ini)
{
	this.posx_ini=posx_ini;
	this.posx=posx_ini;
	this.posx_temp=this.posx;
	this.posy=1;
	this.posy_temp=this.posy;
	this.rotacion=0;
	this.rotacion_temp=0;
	rand = Math.random() * 6;
	rand = Math.round(rand);
	this.tipo=parseInt(1) + rand;
	this.tipo_temp=this.tipo;
	this.cayendo=0;
	this.x1=0;
	this.x1_temp=0;
	this.y1=0;
	this.y1_temp=0;
	this.x2=0;
	this.x2_temp=0;
	this.y2=0;
	this.y2_temp=0;
	this.x3=0;
	this.x3_temp=0;
	this.y3=0;
	this.y3_temp=0;
	this.acabada=0;
	this.pinta=pintaficha_;
	this.baja=bajaficha_;
	this.rodea=rodea_;
	this.colision=colision_;
	this.incluye=incluye_;
	this.rota=rota_;
	this.mueve=mueve_;
	this.finjuego=finjuego_;
	this.borra=borra_;
	this.copiadetemp=copiadetemp_;
	this.actualizatemp=actualizatemp_;
}


//Borra la ficha de la pantalla
borra_=function()
{
	pozo.celda2(this.posx,this.posy);
	pozo.celda2(this.x1,this.y1);
	pozo.celda2(this.x2,this.y2);
	pozo.celda2(this.x3,this.y3);
}

//Comprueba si se ha terminado el juego
finjuego_=function()
{
	if (this.posy==1)
		return 1;
	else
		return 0;
}

//Copia los datos de la ficha a la ficha temporal
actualizatemp_=function()
{
	this.posx_temp=this.posx;
	this.posy_temp=this.posy;
	this.rotacion_temp=this.rotacion;
	this.tipo_temp=this.tipo;
	this.x1_temp=this.x1;
	this.y1_temp=this.y1;
	this.x2_temp=this.x2;
	this.y2_temp=this.y2;
	this.x3_temp=this.x3;
	this.y3_temp=this.y3;
}

//Copia los datos de la ficha temporal a la ficha
copiadetemp_=function()
{
	this.posx=this.posx_temp;
	this.posy=this.posy_temp;
	this.rotacion=this.rotacion_temp;
	this.tipo=this.tipo_temp;
	this.x1=this.x1_temp;
	this.y1=this.y1_temp;
	this.x2=this.x2_temp;
	this.y2=this.y2_temp;
	this.x3=this.x3_temp;
	this.y3=this.y3_temp;
}


//Mueve la ficha dentro del pozo
mueve_=function(inc)
{
	//Bueno, la movemos
	this.actualizatemp();
	this.posx_temp+=inc;
	this.rodea();

	//Pero hay que comprobar colisiones
	if (!this.colision())
	{
		this.borra();
		this.copiadetemp();
		this.pinta();
	}
}

//Rota la ficha dentro del pozo
rota_=function()
{
	//Bueno, la rotamos
	this.actualizatemp();
	this.rotacion_temp++;
	//Si es la ficha larga o algun cuatro sólo rota dos veces
	if (this.tipo_temp==1 || this.tipo_temp==6 || this.tipo_temp==7)
		if (this.rotacion_temp==2)
			this.rotacion_temp=0;
	//Si es el cuadrado no rota
	if (this.tipo_temp==5)
		this.rotacion_temp--;
	//Si es alguna de las otras, y llega a la posicion de rotación 3, vuelve a la primera
	if (this.rotacion_temp==4)
		this.rotacion_temp=0;

	this.rodea();

	//Pero hay que comprobar colisiones
	if (!this.colision())
	{
		this.borra();
		this.copiadetemp();
		this.pinta();
	}

}

//Incorpora al ficha al pozo
incluye_=function()
{
	pozo.contenido[this.posx+this.posy*pozo.ancho]=this.tipo;
	pozo.contenido[this.x1+this.y1*pozo.ancho]=this.tipo;
	pozo.contenido[this.x2+this.y2*pozo.ancho]=this.tipo;
	pozo.contenido[this.x3+this.y3*pozo.ancho]=this.tipo;
	//Comprobamos si ha habido linea completa
	pozo.chequealineas();

}

//Dibuja la ficha en el pozo
pintaficha_=function()
{
	if (this.acabada) return;
	pozo.celda(this.posx, this.posy, pozo.colores[this.tipo]);
	pozo.celda(this.x1, this.y1, pozo.colores[this.tipo]);
	pozo.celda(this.x2, this.y2, pozo.colores[this.tipo]);
	pozo.celda(this.x3, this.y3, pozo.colores[this.tipo]);
}

//Comprueba si ha habido colisión
colision_=function()
{
	var colision=0;
	//Colisión con las paredes
	if (this.posx_temp<0 || this.posx_temp>pozo.ancho-1 || this.posy_temp>pozo.alto-1)
		colision=1;
	if (this.x1_temp<0 || this.x1_temp>pozo.ancho-1 || this.y1_temp>pozo.alto-1)
		colision=1;
	if (this.x2_temp<0 || this.x2_temp>pozo.ancho-1 || this.y2_temp>pozo.alto-1)
		colision=1;
	if (this.x3_temp<0 || this.x3_temp>pozo.ancho-1 || this.y3_temp>pozo.alto-1)
		colision=1;

	//Colisión con otras fichas
	if (pozo.contenido[this.posx_temp+this.posy_temp*pozo.ancho]>0)
		colision=1;
	if (pozo.contenido[this.x1_temp+this.y1_temp*pozo.ancho]>0)
		colision=1;
	if (pozo.contenido[this.x2_temp+this.y2_temp*pozo.ancho]>0)
		colision=1;
	if (pozo.contenido[this.x3_temp+this.y3_temp*pozo.ancho]>0)
		colision=1;
	return colision;
}

//Coloca las 3 piezas alrededor del centro de gravedad de la fecha
rodea_=function()
{
	eval ("this.x1_temp=tipo"+this.tipo_temp+"_"+this.rotacion_temp+"[0]+this.posx_temp;");
	eval ("this.y1_temp=tipo"+this.tipo_temp+"_"+this.rotacion_temp+"[1]+this.posy_temp;");
	eval ("this.x2_temp=tipo"+this.tipo_temp+"_"+this.rotacion_temp+"[2]+this.posx_temp;");
	eval ("this.y2_temp=tipo"+this.tipo_temp+"_"+this.rotacion_temp+"[3]+this.posy_temp;");
	eval ("this.x3_temp=tipo"+this.tipo_temp+"_"+this.rotacion_temp+"[4]+this.posx_temp;");
	eval ("this.y3_temp=tipo"+this.tipo_temp+"_"+this.rotacion_temp+"[5]+this.posy_temp;");
}

//Comprueba si se ha terminado el juego

//Baja una línea la ficha
bajaficha_=function()
{
	//Bueno, la bajamos
	this.actualizatemp();
	this.posy_temp++;
	this.rodea();

	//Pero hay que comprobar colisiones
	if (this.colision())
	{
		//Ha colisionado, la dejamos quieta y la incorporamos al pozo
		this.incluye();
		if (this.finjuego())
		{
			document.getElementById('gameover').style.visibility='visible';
			clearTimeout(timer);
			timer=null;
			document.getElementById('butTetris').value='<?=$L2?>';
		}
		else
		{
			//Creamos una ficha nueva y continuamos
			rand = Math.random() * 6;
			rand = Math.round(rand);
				this.tipo=parseInt(1) + rand;
				this.posy=1;
				this.posx=this.posx_ini;
				this.rotacion=0;
				this.acabada=0;
				this.cayendo=0;
			pozo.puntos+=10;
			pozo.pintapuntos();
			this.actualizatemp();
		}
	}
	else
	{
		this.borra();
		this.copiadetemp();
		this.pinta();
	}
}


/* Fin objetos */


mueve=function()
{
	ficha.baja();
}

var ficha=null;
var pozo=null;
var timer=null;
initTetris=function()
{
	ficha = new Ficha_(4);
	pozo = new Pozo_(10,20,10,10,20,20);
}

botTetris=function()
{
	if(timer==null)
	{
		timer=setInterval("mueve()",velocidad);
		document.getElementById('butTetris').value='Pausa';
	}
	else
	{
		clearInterval(timer);
		timer=null;
		document.getElementById('butTetris').value='Iniciar';
	}
}
tecla=function(e)
{
	var code;
	if(document.all)
		code = event.keyCode;
	else code = e.keyCode;
	//alert(event.keyCode);
	if (code==37)
		ficha.mueve(-1);
	if (code==39)
		ficha.mueve(1);
	if (code==40)
		ficha.baja();
	if (code==38)
		ficha.rota();
}
document.onkeydown=tecla;


]]></code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="312" h="450" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,auto" maxSize="auto,auto" title="Tetris v.1.5" icon="<?=$static?>icons/tetris_16.png" clsBut="1" maxBut="0" minBut="1" statusBar="0" bgColor="#FFF" doc="">

<html_code id="M2PC_TETRIS_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<div id='mainTetris' style="width:100%;height:100%;background-image:url('<?=$static?>img/tetris/fondo.jpg')"></div>

<div style='position:absolute;left:0px;top:0px;width:0px;height:0px;overflow:hidden;'><img src='<?=$static?>img/tetris/negro.png'/><img src='<?=$static?>img/tetris/rojo.png'/><img src='<?=$static?>img/tetris/morado.png'/><img src='<?=$static?>img/tetris/amarillo.png'/><img src='<?=$static?>img/tetris/verde.png'/><img src='<?=$static?>img/tetris/azul.png'/><img src='<?=$static?>img/tetris/naranja.png'/><img src='<?=$static?>img/tetris/cyan.png'/></div>
]]></html_code>


<html_code id="KNM_EnNe_HTCs4325wef" x="250" y="0" w="100" h="20"><![CDATA[

<a href='#' onclick="recarga();return false;"></a>

]]></html_code>


</window>


<window id="M2PC_<?=$appID?>_wAbout" x="auto" y="auto" w="400" h="152" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L114?> <?=$L1?>" icon="<?=$static?>icons/about_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC3" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table width='100%' cellpadding='10' cellspacing='0'><tr>
		<td width='64' align='center'><img src='<?=$static?>icons/tetris_16.png'/></td>
		<td><b><?=$L1?></b><br/>
		<?=$L115?> 1.0<br/>
		&copy; 2007-<?=date('Y')?> Karontek<br/>
		<div style='border-top:1px solid #000;margin-top:5px;padding-top:5px'>
		
		</div>
		</td>
</tr></table>
]]></html_code>
</window>

</app>
