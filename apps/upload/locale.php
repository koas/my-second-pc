<?
switch ($_SESSION['LAN'])
{
	case 'es':
	$L1='Subir ficheros';
	$L2='Método clásico';
	$L3='Método avanzado';
	$L5="El método clásico no requiere ningún plugin adicional. A cambio no permite saber en tiempo real cuánta información se ha enviado y cuánto tiempo queda para terminar de subir el fichero.<br/><br/>Si tienes el plugin de Flash instalado, te recomendamos que utilices el Método avanzado.";
        $L6='Este método utiliza el plugin de Flash para ofrecer información detallada de la subida del fichero.';
        $L7="<br/><br/>Los ficheros que subas se colocarán en tu carpeta <a href='#' onclick=\"K_loadAppAJAX('{$dynamic}apps/fileManager2/fileManager2.php','1#/Uploaded files/','','{$static}icons/fileManager_32.png');return false;\">/Ficheros subidos/</a><br/><br/>Si quieres subir varios ficheros es más sencillo que los juntes en uno solo (zip) y luego descomprimas el fichero una vez  subido. <br/><br/>";
        $L8='Subir fichero';
        $L9='Subiendo fichero, espera por favor';
        $L10=' </em>subido correctamente.';
        $L11='Todos los ficheros';
        $L12='Seleccionar fichero(s)';
        $L13='Subir ficheros';
        $L14='Limpiar lista';
        $L15="El método avanzado utiliza SWFUpload (<a href='http://swfupload.org/' target='_blank'>http://swfupload.org/</a>)";
        $L16="Selecciona el fichero a subir:";
        $L17='Destino: ';
        
        $L114='Acerca de';
        $L115='Versión';
	$L116='Código de';
	$L117='Iconos de';
	break;

	case 'en':
	$L1='Upload files';
	$L2='Classic upload';
	$L3='Advanced upload';
	$L5="The classic upload requires no additional plugins. On the other hand it doesn't show real time information of the data transmitted and the time left to complete the upload.<br/><br/>If you have the Flash plugin installed, we recommend you the Advanced upload.<br/><br/>The uploaded files will be put in your <a href='#' onclick=\"K_loadAppAJAX('{$dynamic}apps/fileManager2/fileManager2.php','1#/Uploaded files/','','{$static}icons/fileManager_32.png');return false;\">/Uploaded files/</a> folder.<br/><br/>If you wish to upload several files, it's easier to group them in one (zip, rar, tar), upload that file and then extract it.";
        $L6='The Advanced upload uses the Flash plugin to show detailed information about the file upload.';
        $L7='Choose a file to upload:';
        $L8='Send file';
        $L9='Sending file, please wait';
        $L10=' </em>successfully uploaded.';
        $L11='All files';
        $L12='Select file(s)';
        $L13='Upload files';
        $L14='Cancel queue';
        $L15="Advanced upload uses SWFUpload (<a href='http://swfupload.org/' target='_blank'>http://swfupload.org/</a>)";
        $L16='Select file to upload';
        $L17='Target: ';
        
        $L114='About';
        $L115='Version';
	$L116='Code by';
	$L117='Icons by';
	break;

}

?>
