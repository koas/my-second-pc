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

M2PC_<?=$appID?>_newGame=function()
{
	if (confirm('<?=$L2?>'))
	{
		var level=document.getElementById('M2PC_<?=$appID?>_level').value;
		var t=document.getElementById('M2PC_<?=$appID?>_botones');
		t.old=t.innerHTML;
		t.innerHTML="<center><span class='loading'>&nbsp;&nbsp;&nbsp;&nbsp;</span></center><br />&nbsp;";
		K_loadAJAX(K_dynP+'apps/sudoku/sudokuNewGame.php?level='+level,'M2PC_<?=$appID?>_resNewGame');
	}
}

M2PC_<?=$appID?>_resNewGame=function(xmlDoc)
{
	M2PC_<?=$appID?>_lastLoad=xmlDoc;
	M2PC_<?=$appID?>_resetGame();
	var t=document.getElementById('M2PC_<?=$appID?>_botones');
	t.innerHTML=t.old;
	document.getElementById('M2PC_<?=$appID?>_level').value=M2PC_<?=$appID?>_level;
	document.getElementById('M2PC_<?=$appID?>_resetBut').disabled=false;
	document.getElementById('M2PC_<?=$appID?>_solveBut').disabled=false;
}

M2PC_<?=$appID?>_resetGame=function()
{
	var d=M2PC_<?=$appID?>_lastLoad.getElementsByTagName('d')[0].firstChild.data;
	for (var x=0, y=1;x<81;x++, y++)
	{
		var c=document.getElementById('M2PC_<?=$appID?>_n'+y);
		if(d.charAt(x)!='0')
		{
			c.value=d.charAt(x);
			c.disabled=true;
			c.style.background='transparent';
			c.style.color='#000000';
			c.style.cursor='default';
			c.className='sudonum1';
		}
		else
		{
			c.disabled=false;
			c.value='';
			c.style.color='#6D7484';
			c.style.cursor='default';
			c.setAttribute('autocomplete','off');
			c.onkeyup=function(){this.blur()}
			c.className='sudonum2';
		}
	}
}

///Code for solving sudokus from http://homepage.ntlworld.com/valleyway/solver.html
//Simple Sudoku solver written by P. Hull 2005
//Modifications to detect/display multiple solutions by Julien de Prabère 2006

Board=function() {
 this.cells=new Array();
 for (var i=0; i<81; ++i)
	this.cells[i]=0;
}

CopyBoard=function(dest, src) {
 for (var i=0; i<81; ++i)
	dest.cells[i]=src.cells[i];
}
CountConstraints=function(val) {
 var cc=0;
 for (var i=1; i<=9; ++i)
	if (((1<<i) & val)!=0) ++cc;
 return cc;
}
MostConstrained=function() {
 var max=-1, maxp=-1;
 for (var i=0; i<81; ++i) {
	if ((this.cells[i] & 1)==0) {
	 v=CountConstraints(this.cells[i]);
	 if (v>=max) {
		max=v;
		maxp=i;
	 }
	}
 }
 return maxp;
}

Board.prototype.mostConstrained=MostConstrained;

AllOptions=function(val) {
 var cc=new Array;
 var n=0;
 for (var i=1; i<=9; ++i)
	if (((1<<i) & val)==0) cc[n++]=i;
 return cc;
}

SetValue=function(pos, val) {
	var x=pos%9;
	var y=Math.floor(pos/9);
	var x0=Math.floor(x/3)*3;
	var y0=Math.floor(y/3)*3;
	var add=(1<<val);
	for (var k=0; k<9; ++k) {
		this.cells[x+k*9]|=add;
		this.cells[k+y*9]|=add;
		this.cells[x0+(k%3)+9*(y0+Math.floor(k/3))]|=add;}
	this.cells[pos]=1023-(1<<val);
}
Board.prototype.setValue=SetValue;

CellText=function(d) {
 if (d&1) {
	for (var i=1; i<=9; ++i)
	 if ((d | (1<<i))==1023) return ""+i;
	return "_";
 }
 else {
	return "?"+AllOptions(d);
 }
}
AsHTML=function() {

 var ans="";
 var z=1;
 for (var y=0; y<9; ++y) {
	for (var x=0; x<9; ++x) {
	 c=document.getElementById('M2PC_<?=$appID?>_n'+z);
	 if(c.value=='')
	 {
		c.value=CellText(this.cells[x+y*9]);
		c.style.color='#008F4E';
	}
	 ++z;
	}
 }
}

Board.prototype.asHTML=AsHTML;

IsOK=function() {
 for (var i=0; i<81; ++i) {
	if ((this.cells[i] & 1022)==1022) {
		return false;
	}
 }
 return true;
}

IsSolved=function() {
 for (var i=0; i<81; ++i) {
	if ((this.cells[i] & 1)==0) return false;
 }
 return true;
}

Board.prototype.isSolved=IsSolved;
Board.prototype.isOK=IsOK;

var theOne=new Board();
var numSol;

SearchSolutions=function() {
	while (this.isOK()) {
		if (this.isSolved()) {
			if (1<++numSol) return this;
			CopyBoard(theOne,this);return null;}
		var p=this.mostConstrained();
		if (p<0) return null;
		var l=AllOptions(this.cells[p]);
		if (l.length<1) return null;
		for (var i=1; i<l.length; ++i) {
			var nb=new Board();
			CopyBoard(nb, this);
			nb.setValue(p, l[i]);
			nb=nb.searchSolutions();
			if (nb) return nb;}
		this.setValue(p, l[0]);}
	return null;
}
Board.prototype.searchSolutions=SearchSolutions;

solve_click=function() {
	var theSec=new Board();
	numSol=0;
	for (var i=0, j=1; i<81; ++i, ++j) {
		var v=document.getElementById("M2PC_<?=$appID?>_n"+j).value
		if (v>="1" && v<="9") theSec.setValue(i, parseInt(v));}
	var rsp=theSec.searchSolutions();
	var ans=0;
	if (numSol==1)
	{
		ans=1;
		theOne.asHTML();
	}
	else alert("<?=$L3?>");
}
]]></code>

<window id="M2PC_<?=$appID?>_wMain" x="auto" y="auto" w="434" h="409" mov="1" res="1" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L1?>" icon="<?=$static?>icons/sudoku_16.png" clsBut="1" maxBut="0" minBut="1" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
 
<html_code id="M2PC_<?=$appID?>_HC1" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<style type='text/css'>
.sudocell{width:34px;height:34px;border-right:1px solid #B9D4FA;border-bottom:1px solid #B9D4FA;background:#FFF;}
.sudonum1{border:0px;font-size:19px;text-align:center;}
.sudonum2{border:0px;font-size:19px;text-align:center;color:#6D7484;}
</style>
<div style="padding:10px;background-image:url('<?=$static?>img/sudoku/fondo1.jpg');">
<div id='M2PC_<?=$appID?>_contTables' align='center' style="-moz-opacity:0.75;opacity:0.75;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=75);">
<table cellpadding='0' cellspacing='0'>
<tr><td>

		<table cellpadding='0' cellspacing='0' style='border:2px solid #3E597A;'>
		<tr>
						<td align='center' class='sudocell'>
										<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n1'/>
						</td>
						<td class='sudocell'>
										<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n2'/>
						</td>
						<td class='sudocell'>
										<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n3'/>
						</td>
		</tr>
		<tr>
						<td align='center' class='sudocell'>
										<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n10'/>
						</td>
						<td class='sudocell'>
										<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n11'/>
						</td>
						<td class='sudocell'>
										<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n12'/>
						</td>
		</tr>
		<tr>
						<td align='center' class='sudocell'>
										<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n19'/>
						</td>
						<td class='sudocell'>
										<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n20'/>
						</td>
						<td class='sudocell'>
										<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n21'/>
						</td>
		</tr>
		</table>
</td>
<td>
<table cellpadding='0' cellspacing='0' style='border:2px solid #3E597A;border-left:0px;'>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n4'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n5'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n6'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n13'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n14'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n15'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n22'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n23'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n24'/>
	</td>
</tr>
</table>
</td>
<td>
<table cellpadding='0' cellspacing='0' style='border:2px solid #3E597A;border-left:0px;'>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n7'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n8'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n9'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n16'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n17'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n18'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n25'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n26'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1' id='M2PC_<?=$appID?>_n27'/>
	</td>
</tr>
</table>
</td>
</tr>
<tr><td>

<table cellpadding='0' cellspacing='0' style='border:2px solid #3E597A;border-top:0px;'>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n28'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n29'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n30'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n37'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n38'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n39'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n46'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n47'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n48'/>
	</td>
</tr>
</table>
</td>
<td>
<table cellpadding='0' cellspacing='0' style='border:2px solid #3E597A;border-left:0px;border-top:0px;'>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n31'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n32'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n33'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n40'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n41'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n42'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n49'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n50'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n51'/>
	</td>
</tr>
</table>
</td>
<td>
<table cellpadding='0' cellspacing='0' style='border:2px solid #3E597A;border-left:0px;border-top:0px;'>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n34'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n35'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n36'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n43'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n44'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n45'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n52'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n53'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n54'/>
	</td>
</tr>
</table>
</td>
</tr>
<tr><td>

<table cellpadding='0' cellspacing='0' style='border:2px solid #3E597A;border-top:0px;'>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n55'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n56'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n57'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n64'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n65'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n66'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n73'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n74'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n75'/>
	</td>
</tr>
</table>
</td>
<td>
<table cellpadding='0' cellspacing='0' style='border:2px solid #3E597A;border-left:0px;border-top:0px;'>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n58'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n59'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n60'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n67'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n68'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n69'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n76'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n77'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n78'/>
	</td>
</tr>
</table>
</td>
<td>
<table cellpadding='0' cellspacing='0' style='border:2px solid #3E597A;border-left:0px;border-top:0px;'>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n61'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n62'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n63'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n70'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n71'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n72'/>
	</td>
</tr>
<tr>
	<td align='center' class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n79'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n80'/>
	</td>
	<td class='sudocell'>
		<input type='text' size='2' maxlength='1' class='sudonum1'  id='M2PC_<?=$appID?>_n81'/>
	</td>
</tr>
</table>
</td>
</tr>
</table>
<div align='center' id='M2PC_<?=$appID?>_botones' style='margin-top:14px'>
&nbsp;<select id='M2PC_<?=$appID?>_level' onchange="M2PC_<?=$appID?>_level=this.value"><option value='1'><?=$L8?></option><option value='2'><?=$L9?></option><option value='3'><?=$L10?></option><option value='4'><?=$L11?></option></select> <input style='width:80px' type='button' class='button' value='<?=$L7?>' onclick="M2PC_<?=$appID?>_newGame()"/>
<input id='M2PC_<?=$appID?>_resetBut' style='width:80px' type='button' class='button' disabled='disabled' value='<?=$L4?>' onclick="if(confirm('<?=$L5?>'))M2PC_<?=$appID?>_resetGame()"/>
<input id='M2PC_<?=$appID?>_solveBut' style='width:80px' type='button' class='button' value='<?=$L6?>' onclick="solve_click()"/>
</div>
</div>
</div>
]]></html_code>

</window>

<window id="M2PC_<?=$appID?>_wAbout" x="auto" y="auto" w="400" h="152" mov="1" res="0" boundaries="auto,auto,auto,auto" minSize="180,200" maxSize="auto,auto" title="<?=$L114?> <?=$L1?>" icon="<?=$static?>icons/about_16.png" clsBut="1" maxBut="0" minBut="0" statusBar="0" bgColor="#FFF" doc="" overflow="hidden">
<html_code id="M2PC_<?=$appID?>_HC3" x="auto" y="auto" w="auto" h="auto"><![CDATA[
<table width='100%' cellpadding='10' cellspacing='0'><tr>
		<td width='64' align='center'><img src='<?=$static?>icons/sudoku_64.png'/></td>
		<td><b><?=$L1?></b><br/>
		<?=$L115?> 1.0<br/>
		&copy; 2007-<?=date('Y')?> Karontek<br/>
		<div style='border-top:1px solid #000;margin-top:5px;padding-top:5px'>
		<?=$L117?>:<br/>
		<b>&bull;</b> Human-O2 (<a href='http://schollidesign.deviantart.com/art/Human-O2-Iconset-105344123' target='_blank'>Oliver Scholtz (and others)</a>)<br/>
		Simple Sudoku solver written by P. Hull 2005<br />
		Modifications to detect/display multiple solutions by Julien de Prabère 2006
		</div>
		</td>
</tr></table>
]]></html_code>
</window>

</app>
