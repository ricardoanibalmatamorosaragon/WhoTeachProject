
<?php
	/************************************************************************
	 *	ATTENZIONE:															*
	 *	IN QUESTA PAGINA SI UTILIZZA IN UN PUNTO DEL CODICE IL BACKQUOTE.	*
	 *  E' IMPORTANTE LASCIARE IL BACKQUOTE E NON SOSTITUIRLO, PENA IL		*
	 *  NON FUNZIONAMENTO DELL'ANTEPRIMA DELLE RISORSE.						*
	 ************************************************************************/


	require_once('../config.php');
	require_once($CFG->libdir .'/mysql_conn.php');
	require_once('../metadata/metadata_page/aux_functions.php');

	session_start();

    $PAGE->set_pagetype('site-index');
	$PAGE->set_docs_path('');
	$PAGE->set_pagelayout('frontpage');
	//$editing = $PAGE->user_is_editing();
	$PAGE->set_title($SITE->fullname);
	$PAGE->set_heading($SITE->fullname);
	//$courserenderer = $PAGE->get_renderer('core', 'course');
	require_login();
$PAGE->set_context(context_system::instance());
	echo $OUTPUT->header();

if (has_capability('moodle/site:config', context_system::instance()))
{
	
	//----------------------------CONTROLLO CAMBIO LINGUA-------------------------------------	
	if(!isset($_SESSION['lingua_t0']))
	{
		$_SESSION['lingua_t0'] = current_language();
		$_SESSION['scrivi_socket'] = 1;
	}
	else
	{
		$_SESSION['lingua_t-1'] = $_SESSION['lingua_t0'];
		$_SESSION['lingua_t0'] = current_language();
		
		if($_SESSION['lingua_t-1'] != $_SESSION['lingua_t0'])
			$_SESSION['scrivi_socket'] = 0;
		else
			$_SESSION['scrivi_socket'] = 1;
	}

	//----------------------------------------------------------------------------------------
	

	if (isset($_POST['i_back']))
	{
		$_SESSION['parte_corrente'] = $_POST['i_back'];
		$_SESSION['scrivi_socket'] = 0;
		
		// Devo chiedere al Server.java di ripristinare l'ultimo dataset per la parte_corrente in corso
		$address = '::1';
		$port = 20005;
		$token = $_SESSION['data']['token'];
		
		// Se sto lasciando la parte corrente cliccando 
		$message = array('token' => $token, 'nuovaParteCorrente' => $_SESSION['parte_corrente'], 'doFase' => -4);
		$message = json_encode($message) . "\r\n";		
		
		
		if (($sock = socket_create(AF_INET6, SOCK_STREAM, 0)) === false) {
			echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
			exit(0);
		}

		if (socket_connect($sock, $address, $port) === false) {
			echo "socket_connect() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			exit(0);
		}

		if (socket_write($sock, $message, strlen($message)) === false) {
			echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			exit(0);
		}

		socket_close($sock);
	}


	$corrente = $_SESSION["parte_corrente"];
	$nop = $_SESSION["data"]["nop"];
	
	//se ho riaggiornato la pagina SENZA CAMBIARE LINGUA
	if($_SESSION['scrivi_socket'] == 1)
	{
		$resources = $_POST["resources"];	
		
		unset($_SESSION["course"]["parts"][$corrente]);
		$_SESSION["course"]["parts"][$corrente]["name"] = $_POST["modn"];		
		$_SESSION['modn'] = $_POST["modn"];
		$_SESSION['resources'] = $resources;
		
		
			
		$i=0;
		foreach($resources as $res)
		{
			$p = explode(";",$res);
			$_SESSION["course"]["parts"][$corrente]["resources"][$i]["id"] = $p[0];
			$_SESSION["course"]["parts"][$corrente]["resources"][$i]["name"] = $p[1];
			$_SESSION["course"]["parts"][$corrente]["resources"][$i]["module"] = $p[2];
			$i++;
		}
	}
	else
	{
		if ($_SESSION["course"]["parts"][$corrente]["name"] === '')
			$_SESSION["course"]["parts"][$corrente]["name"] = $_SESSION['modn'];
		
		$_SESSION['modn'] = $_SESSION["course"]["parts"][$corrente]["name"];
		$resources = $_SESSION['course']['parts'][$corrente]['resources'];
	}

	// Se tutte le parti hanno dei corsi, allora sono tutti completato
	foreach ($_SESSION['course']['parts'] as $parts)
	{
		if (count($parts['resources']) <= 0)
		{
			$_SESSION['allcomplete'] = false;
			break;
		}
		else
			$_SESSION['allcomplete'] = true;
	}
	
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>NETT RS</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link href="themes/multiselect/css/multi-select.css" media="screen" rel="stylesheet" type="text/css">
	</head>
	<body>
		<br>
		<div>
			<center>
				<a href="#">
					<img src="themes/img/logo_small.png" alt="" />
				</a>
			</center>
		</div>
		<br>
		<center>
			<h1><font color="#383a3d"><?php echo convert_RS('COURSE RECAP'); ?></font></h1>
			<br>
			<p><b><?php echo convert_RS('COURSE NAME'); ?></b></p>
			<p><i><?php echo $_SESSION['course']['name'];?></i></p>
			<br>
			<p><b><?php echo convert_RS('KNOWLEDGE AREA'); ?></b></p>
			<p><i><?php echo $_SESSION['course']['ka'];?></i></p>
			<br>
			<p><b><?php echo convert_RS('PARTS'); ?></b></p>
			<form id="myformSelf" name="myformSelf" method="post" action="conclude.php">
			<?php
				for($i = 1; $i <= count($_SESSION['course']['parts']); $i++)
				{
					$conta_risorse = count($_SESSION['course']['parts'][$i]['resources']);
					
					if ($i == $corrente)
						print '<font color="red"><p><b>'.$i.' - '.convert_RS($_SESSION['course']['parts'][$i]['name']).'</b></p></font>';
					else
					{
						// Faccio modificare solo i corsi che presentano delle risorse
						if ($conta_risorse > 0)
							print '<p><a onclick="selectThis('.$i.')" style="cursor: pointer;"><b>'.$i.' - '.convert_RS($_SESSION['course']['parts'][$i]['name']).'</b></a></p>';
						else
							print '<p><b>'.$i.' - '.convert_RS($_SESSION['course']['parts'][$i]['name']).'</b></p>';
					}
					
					for($j = 0; $j < $conta_risorse; $j++)
					{
						$id_risorsa = $_SESSION['course']['parts'][$i]['resources'][$j]['id'];
						$name_orig = $_SESSION['course']['parts'][$i]['resources'][$j]['name'];
						$type = $_SESSION['course']['parts'][$i]['resources'][$j]['module'];
						
						$query = "SELECT externalurl FROM mdl_url WHERE id=".$id_risorsa;
						$connection = GetMyConnection();
						if ($connection < 0)
						{
							print errorDB($connection);
							die();
						}
						$result = mysqli_query($connection, $query);
						
						$url = mysqli_fetch_array($result);
						$url = $url['externalurl'];
						$name = str_ireplace("''","\'",$name_orig);
			?>
						
						<p>
							<?php print convert_RS('Resource Name: '); ?>
							<i onmouseover="Tip('<a href=\'javascript:void(0)\' onclick=\'openPreview(`<?php print $url; ?>`,`<?php print $name; ?>`); return false; \'><?php print convert_RS('Resource Preview'); ?> </a>',CLOSEBTN,'true',STICKY,'true',FONTSIZE,'11.5pt',BGCOLOR,'#d1e0e7',
									BORDERCOLOR,'#5392b3',CLOSEBTNCOLORS,'#74A9CC',DELAY,'300',DURATION,'2000',FOLLOWMOUSE,'false',EXCLUSIVE,'true',TITLE,'<?php print $name; ?>')"  onmouseout="UnTip()"><?php print $name; ?></i>
						</p>
						<p>
							<?php print convert_RS('Resource Type: '); ?>
							<i> <?php print str_replace("mdl_", "",$type); ?></i>
						</p><?php
					}
			}
			?>
				<input type="hidden" id="cambia_parte_corrente" name="cambia_parte_corrente"/>
			</form>
			
			<form method="post" action="step4.php" style="display: inline;">
				<input type="submit" name="back" id="back" value="<?php print convert_RS('BACK'); ?>" class="btn btn-large btn-primary" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;">
			</form>
			
			<?php
				if (/*count($_SESSION['parteFinita']) < $nop - 1*/$_SESSION['allcomplete'] == false)
				{
					print '
						<form id="myformMore" name="myformMore" method="post" action="conclude.php" style="display:inline;">
							<input type="hidden" name="step" value="more"/>   
							<input type="submit" value="' .convert_RS("CONTINUE TO COMPILING"). '"  class="btn btn-large" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;">
						</form>
						<br><br>';
				}
				
				
				if (/*count($_SESSION['parteFinita']) >= $nop || */$_SESSION['allcomplete'] == true)
				{
					$sessione = $_SESSION;
					$sessione = json_encode($sessione);
					$sessione_64 = base64_encode($sessione);
					/*$_SESSION['allcomplete'] = true;*/
					print '
						<form id="formCreateCourse" method="POST" action="create_course.php" name="myform">
							<input type="hidden" name="session" value="'.$sessione_64.'"/>
							<input type="submit" name="createCourse" id="createCourse" value="'.convert_RS('Create Course').'" class="btn btn-large" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;">
							<p align="center">
								<img id="loading" src="themes/spinner/ajax-loader.gif" style="visibility:hidden;">
							</p>
						</form>';
				}
			?>
			<!--<form id="myformEnd" name="myformEnd" method="post" action="conclude.php"> 
				<input type="hidden" name="step" value="end"/>  
				<input type="submit" value="'.convert_RS('CONTINUE LATER').'" class="btn btn-large" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;">
			</form>-->
			

		</center>

		<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
		<script src="themes/multiselect/js/jquery.multi-select.js" type="text/javascript"></script>
		<script type="text/javascript" src="themes/js/wz_tooltip.js"></script> <!-- Plugin scaricato da http://www.walterzorn.de/en/index.htm -->
		<script type="text/javascript">
			function openPreview(url, name) {
				window.open(url, "Preview: " + name, "width=500,height=500");
			}
			
			function selectThis(id) {
				var nuova_parte_corrente = document.getElementById('cambia_parte_corrente');
				var form = document.getElementById('myformSelf');
				
				nuova_parte_corrente.value = id; 
				form.submit();
			}
		</script>
		<script>
			$(document).ready(function(){
				$("#formCreateCourse").submit(function(event)
				{
					$("#loading")[0].style.visibility = "visible";
					$("#back")[0].style.visibility = "hidden";
					$("#createCourse")[0].style.visibility = "hidden";
				});			
			});
		</script>
	</body>
</html>

<?php	
	echo $OUTPUT->footer();
?>
