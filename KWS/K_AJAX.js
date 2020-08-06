function K_getHTTPObject() {
  var xmlhttp;
  /*@cc_on
  @if (@_jscript_version >= 5)
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
        xmlhttp = false;
      }
    }
  @else
  xmlhttp = false;
  @end @*/
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    try {
      xmlhttp = new XMLHttpRequest();
    } catch (e) {
      xmlhttp = false;
    }
  }
  return xmlhttp;
}

var K_appCache=new Array();

function K_runAppData(data,params)
{
  var d=new Date();
  data=data.replace(/@!@/g,d.getTime());
  var xmlDoc;
  if(window.ActiveXObject)
  {
    xmlDoc= new ActiveXObject("Microsoft.XMLDOM");
    xmlDoc.async="false";
    xmlDoc.loadXML(data);
  }
  else xmlDoc=(new DOMParser()).parseFromString(data,"text/xml");
  
  K_resLoadApp(xmlDoc, params);  
}
function K_loadAppAJAX(file, params, icon)
{
	for (key in K_appCache)
	{
	  if(key==file)
	    {
	      K_runAppData(K_appCache[key],params);
	      return;
	    }
	}
	
	var http=K_getHTTPObject();
	var ended=false;
	
	if($('K_loadIcon'))
		$('K_loadIcon').style.display='block';
	
	if(icon)
          K_startBounce(icon);
	
	http.open("GET", file, true);
	http.onreadystatechange = function()
	{
		if (http.readyState==4)
		{
			if($('K_loadIcon'))
					$('K_loadIcon').style.display='none';
			if (http.status == 200)
			{
				if (ended) return; ended=true;
				var xmlDoc=http.responseXML;
                                var stat=$t(xmlDoc,'stat')[0].firstChild.data;
				
				if (!K_parseXMLres(stat))
                                {
                                      K_stopBounce();
					return;
                                }
				
				if($a($t(xmlDoc,'app')[0],'cacheable')=='1')
				  K_appCache[file]=http.responseText;
				
				K_runAppData(http.responseText,params);
				
			}
			else
                        {
                          K_stopBounce();
				alert(K_L_appLoadError);
                        }
		}
	};
	http.send(null);
}
function K_loadAJAX(file, callback)
{
	var http=K_getHTTPObject();
	var ended=false;
	if($('K_loadIcon'))
		$('K_loadIcon').style.display='block';
	var pos=file.indexOf("?");
	if (pos>-1)
		file+='&K_rand='+Math.random();
	else
		file+='?K_rand='+Math.random();

	http.open("GET", file, true);
	http.onreadystatechange = function()
	{
		if (http.readyState==4)
		{
			if($('K_loadIcon'))
					$('K_loadIcon').style.display='none';
			if (http.status == 200)
			{
				if (ended) return; ended=true;
				var xmlDoc = http.responseXML;
                                var stat= $t(xmlDoc,'stat')[0].firstChild.data;
				if (!K_parseXMLres(stat))
					return;
				if(callback)
					eval (callback+"(xmlDoc);");
			}
			else
				alert(K_L_fileLoadError+' '+file);
		}
	};
	http.send(null);
}
function K_parseXMLres(res)
{
	if(res=='NOK')
	{
		alert('RPC App error');
		return 0;
	}
	if(res=='DBERR')
	{
		alert('RPC DB error');
		return 0;
	}
	if(res=='AUTHERR')
	{
		alert(K_L_AuthError);
		window.location.href=K_dynP;
		return 0;
	}
        if(res=='NOTINDEMO')
        {
            alert(K_L_notInDemo);
            return 0;
        }
	return 1;
}
