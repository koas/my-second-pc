var K_resize=0;
var K_idDrag='';
var K_difX=0;
var K_difY=0;
var K_wIni=0;
var K_hIni=0;
var K_xIni=0;
var K_yIni=0;
var K_moveMinX=0;
var K_moveMaxX=0;
var K_moveMinY=0;
var K_moveMaxY=0;
var K_resizeMinW=0;
var K_resizeMaxW=0;
var K_resizeMinH=0;
var K_resizeMaxH=0;
var K_resizingX=0;
var K_resizingY=0;
var K_recDesktop='';
var K_movSC='';
var K_bounce=new Array();
K_bounce[K_bounce.length]=new Array(16,16,0,0);
K_bounce[K_bounce.length]=new Array(17,15,0,1);
K_bounce[K_bounce.length]=new Array(18,14,-1,2);
K_bounce[K_bounce.length]=new Array(19,13,-1,3);
K_bounce[K_bounce.length]=new Array(20,12,-2,4);
K_bounce[K_bounce.length]=new Array(21,11,-3,5);
K_bounce[K_bounce.length]=new Array(20,12,-2,4);
K_bounce[K_bounce.length]=new Array(19,13,-1,3);
K_bounce[K_bounce.length]=new Array(18,14,-1,2);
K_bounce[K_bounce.length]=new Array(17,15,0,1);
K_bounce[K_bounce.length]=new Array(16,16,0,0);
K_bounce[K_bounce.length]=new Array(15,17,0,-4);
K_bounce[K_bounce.length]=new Array(14,18,0,-8);
K_bounce[K_bounce.length]=new Array(13,19,1,-12);
K_bounce[K_bounce.length]=new Array(12,20,1,-16);
var K_bounceState=0;
var K_bounceStep=1;
function K_doBounce()
{
	K_bounceId.style.width=K_bounce[K_bounceState][0]*2+'px';
	K_bounceId.style.height=K_bounce[K_bounceState][1]*2+'px';
	K_bounceId.style.left=K_bounceX-0+K_bounce[K_bounceState][2]*2+'px';
	K_bounceId.style.top=K_bounceY-0+K_bounce[K_bounceState][3]*2+'px';

	K_bounceState+=K_bounceStep;

	if(K_bounceStep==1 && K_bounceState==K_bounce.length)
	{
		K_bounceState=K_bounce.length-1;
		K_bounceStep=-1;
	}
	if(K_bounceStep==-1 && K_bounceState==5)
	{
		K_bounceState=6;
		K_bounceStep=1;
	}
}
function K_startBounce(icon)
{
	clearInterval(K_bounceTimerId);
	K_bounceId=$('K_ImgIconBounce');
	if(K_bounceId)
	{
		K_bounceId.src=K_staP+'KWS/img/trans.png';
		K_bounceId.style.left=K_bounceX+'px';
		K_bounceId.style.top=K_bounceY+'px';
		K_bounceId.style.zIndex='100000';
		K_bounceId.old=K_bounceId.src;
		K_bounceId.src=icon;
		K_bounceTimerId=setInterval('K_doBounce()',50);
	}
}
function K_stopBounce()
{
	clearInterval(K_bounceTimerId);
	if(K_bounceId)
		K_bounceId.src=K_bounceId.old;
}

function K_mouseMove(e)
{
	var src='';
	if (K_ie)
		src=window.event.srcElement.id;
	else
		src=e.target.id;
	
	var x=y=xElem=yElem=0;
	if (K_ie || K_op)
	{
		x=event.clientX;
		y=event.clientY;
		xElem=event.offsetX;
		yElem=event.offsetY;
	}
	if (K_gec)
	{
		x=e.pageX;
		y=e.pageY;
		xElem=e.layerX;
		yElem=e.layerY;
	}

	K_bounceX=x-0+12;
	K_bounceY=y-0+12;
	if(K_recDesktop)
	{
		if(isNaN(xElem) || isNaN(yElem))
			return;
		if(src=='K_recDesktop')
		{
			if(window[K_recDesktop].recDeskInv)
				window[K_recDesktop].moveRecDesktop(window[K_recDesktop].xIni-0-$('K_recDesktop').offsetWidth+xElem,window[K_recDesktop].yIni-0-$('K_recDesktop').offsetHeight+yElem);
			else
				window[K_recDesktop].moveRecDesktop(window[K_recDesktop].xIni-0+xElem,window[K_recDesktop].yIni-0+yElem);
		}
	 	else
	 	{
	 		if(src=='K_int_'+K_recDesktop)
	 			window[K_recDesktop].moveRecDesktop(xElem,yElem);
	 		else 
	 		{
	 			if(!window[K_recDesktop].isMain)
		 			window[K_recDesktop].moveRecDesktop(x-window[K_recDesktop].offsetX,y-window[K_recDesktop].offsetY-71);
		 		else window[K_recDesktop].moveRecDesktop(x-window[K_recDesktop].offsetX,y-window[K_recDesktop].offsetY);
	 		}
	 	}
	}

	if(K_movSC)
		window[K_movSC].moveShortcuts(x,y);
	if (!K_idDrag)
		return;


	if (K_resize==0)
	{
		var mov=window[K_idDrag].movable;
		if (mov!=1)
			return;
		var nx=(x-K_difX);
		var ny=(y-K_difY);
		if (nx<K_moveMinX)
			nx=K_moveMinX;
		if (ny<K_moveMinY)
			ny=K_moveMinY;
		if (nx>K_moveMaxX)
			nx=K_moveMaxX;
		if (ny>K_moveMaxY)
			ny=K_moveMaxY;
		window[K_idDrag].moveTo(nx,ny);
	}
	else
	{
		var nw=K_wIni-K_xIni+x;
		var nh=K_hIni-K_yIni+y;
		if (nw>K_resizeMaxW)
			nw=K_resizeMaxW;
		if (nh>K_resizeMaxH)
			nh=K_resizeMaxH;
		if (nw<K_resizeMinW)
			nw=K_resizeMinW;
		if (nh<K_resizeMinH)
			nh=K_resizeMinH;
		if (K_resizingX==1)
			window[K_idDrag].resizeTo(nw,window[K_idDrag].height);
		else if (K_resizingY==1)
			window[K_idDrag].resizeTo(window[K_idDrag].width,nh);
		else
			window[K_idDrag].resizeTo(nw,nh);
	}
	return false;
}




function K_mouseUp(e)
{
	if (K_ie || K_op)
	{
		K_lastMouseUpX=event.clientX;
		K_lastMouseUpY=event.clientY;
	}
	if (K_gec)
	{
		K_lastMouseUpX=e.pageX;
		K_lastMouseUpY=e.pageY;
	}
	
	if(K_recDesktop)
		window[K_recDesktop].endMoveRecDesktop();
	if(K_movSC)
		window[K_movSC].endMoveShortcuts();
	if (!K_idDrag)
		return;
	if (!K_resize)
	{
		var posx=parseInt(window[K_idDrag].posx);
		var posy=parseInt(window[K_idDrag].posy);
		var ancho=parseInt(window[K_idDrag].width);
		var alto=parseInt(window[K_idDrag].height);
		window[K_idDrag].endMove();
	}
	else
	{
		window[K_idDrag].endResize();
	}
	K_idDrag='';
	K_resizingX=0;
	K_resizingY=0;
}

document.addEventListener?document.addEventListener("mousemove",K_mouseMove,false):document.attachEvent("onmousemove",K_mouseMove);
document.addEventListener?document.addEventListener("mouseup",K_mouseUp,false):document.attachEvent("onmouseup",K_mouseUp);
