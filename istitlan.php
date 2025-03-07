<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('max_execution_time', 0);
#set_time_limit(5000);
$format="";
$medium="";
$srtfile="";
$textfile="";
$srtdir='/var/www/html/abp/www/anaouder/srt/';
$eaffile="";
$racine="";
$URL="";
$newname="";
$langue="";
#trouver la version de anaouder
#$version=shell_exec('adskrivan -v 2>&1');
#echo $version;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<title>Istitlañ</title>
<link rel="stylesheet" type="text/css" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/7.5.0/wavesurfer.min.js"></script>
<!-- <script src="https://unpkg.com/wavesurfer.js@6.6.4"></script>-->
<script src="https://unpkg.com/wavesurfer.js@6.6.4/dist/plugin/wavesurfer.timeline.js"></script>
</head>
<BODY>
<div id="spinner" style="display: none;">
        <div class="spinner-container"></div>
</div>
<div style='text-align:right;width:100%;display:inline-block;margin-right:50px;'> <a href="https://abp.bzh/anaouder/">[Tous les outils] </a> <a href="anaouder/index-br.php"> [An holl ostilhoù]</a></div>
<div>
<table class="blank">
<tr><td width="250"><a href="fr-index.php"><img src='./img/istitlan.png' alt='logo' width="300" style="opacity:1;"></a></td>
<td width="750"> <h1 style="padding:20px 0 10px 20px;"> Istitlañ - Sous-titrer </h1>
<div style="margin-left:20px;margin-top:0px; text-align:center; "></div>
</td>
<td align="right" width="220">
<!--<div style="text-align:right;font-size:10pt;display:block;height:40px;margin-top:0px;">
[<a href="en-index.php ">English</a>] [<a href="fr-index.php">Français</a>]
</div>-->
</td></tr></table></div>
<div style="display:block; max-width:1200px;text-align:left;margin:50px 0 20px 80px; ">
<h2>Istitlañ un video - Sous-titrer une vidéo en breton</h2>	
<form enctype="multipart/form-data" method="post"  id="formupload">
<div style="display:block;margin-bottom:50px;">--><i>Un anaouder mouezh emgefre, graet gant ar meziantoù <a href="https://pypi.org/project/anaouder/">Anaouder -version 0.9.2</a>, Kaldi ha Vosk</i> Fait avec les logiciels open sources <a href="https://pypi.org/project/anaouder/">Anaouder</a>, Kaldi et Vosk</div>


<div>
<label for="langue">Langue de l'enregistrement :</label>
<select name="langue" id="langue">
<option value="breton">brezhoneg</option>
<option value="francais">français</option>
</select>
</div>
<div style="font-size:9pt;">Pas de traduction possible pour le moment. Votre video en breton sera sous-titrée en breton, en français sera sous-titrée en français.</div>
<div><input style="border:thin black dotted;border-radius: 3px;max-width:350px; " size="100"  name="userfile" type="file" accept=".mp4,.m4v,.mov" title ="fichiers acceptés : mov, mp4, m4v"><span style="color:#666;text-align:left;font-size:12pt;margin:30px 0 10px 0;"> ( prend les formats .mov, .mp4, .m4v) Bezit pasianted ! An treuzskrivañ a c'hell padout meur a vunutenn ! Soyez patient, le logiciel doit tout écouter !</span></div>
<div style="display:inline-block;margin:0 auto !important; padding-top:30px; max-width:1200px; width:100%; text-align:left; ">
<img src="img/bobine.png" alt="bobine" style="width:18px; margin-bottom:-6px;margin-right:4px;"><input type="SUBMIT" value =" Istitlañ / Soustitrer " name="button2" id="button2" title="Créer un fichier srt de sous-titrage" onclick="uploadAndProcess()">
<span style="color:#666;text-align:left;font-size:12pt;margin:30px 0 10px 0;">Krouiñ ur restr .srt -- Créer un fichier .srt compatible avec Youtube.</span>
</div>
<div><a href = "howto.php">[Howto]</a></div>
</form>
 <div style="width:100%;" id='waveform'></div>
 <div id='wave-timer'></div>
  <div>
<button style="border:thin #71A7D3 solid;border-radius: 3px;color:#fff;background:#71A7D3;" id='play-button' type="button">Play/Pause</button>
  </div>
 </div>
<?PHP
if ($_SERVER["REQUEST_METHOD"] == "POST") {
#echo "u pushed button2";
      if($_FILES['userfile']['name'] !=" "){
            $name_of_file=$_FILES['userfile']['name'];
            $_SESSION['name_of_file']=$name_of_file;
      }else{
         $name_of_file=$_SESSION['name_of_file'];
         }  
       $langue=$_POST['langue'];    
	$newname=strtolower($name_of_file);
	$newname = preg_replace("/[^a-zA-Z0-9.]/", "-", $newname);
     	$URL="https://abp.bzh/anaouder/uploads/" .$newname;
	$newname_array=explode(".",$newname);
     	$last=count($newname_array) - 1;
     	$extension=$newname_array[$last];
	$extension=strtolower($extension);
	if($extension =="php" OR $extension =="py" OR $extension=="js" OR $extension=="png" OR $extension=="jpg" OR $extension == "html"){
                echo "<br>Désolé, ce fichier n'est pas autorisé";
     	exit();
        }	
	putenv('PATH=' . getenv('PATH'));
	#$newname= $_COOKIE['filename'];
	#echo "newname : " . $newname;
       $newname_array=explode(".",$newname);
	$last=count($newname_array) - 1;
	$extension=$newname_array[$last];
	$racine=substr($newname,0,-3);
	$srtfile=$racine ."srt";
	$srtpath=$srtdir.$srtfile;

	#echo "<p>moving the file  to uploads";
	$srtpath="/var/www/html/abp/www/anaouder/srt/$srtfile";
	$upload="/var/www/html/abp/www/anaouder/uploads/".$newname;
	#echo "<p>uploading the file $newname";
	if(move_uploaded_file($_FILES['userfile']['tmp_name'],$upload)){
	#echo "<br>the file has been uploaded";
	}else{
	echo "<br>Echec pour $upload";
	echo "<br>Upload error code: " . $_FILES['userfile']['error'];

	exit;
	}
	#echo "<br>trying adskrivan.py";
try {
    // Execute the Python script and capture the output and return status
    putenv('PATH=' . getenv('PATH') . ':/usr/bin/ffmpeg');
    if($langue=="breton"){
      putenv('PATH=' . getenv('PATH') . ':/usr/bin/ffmpeg');
    $command = "/usr/bin/python3.10 /usr/local/lib/python3.10/dist-packages/anaouder/adskrivan.py /var/www/html/abp/www/anaouder/uploads/$newname -o $srtpath 2>&1";
    }elseif($langue=="francais"){
    #echo "c'est donc du francais";
     putenv('PATH=' . getenv('PATH') . ':/usr/bin/ffmpeg');
    $command ="/usr/local/bin/vosk-transcriber -i $upload  -o $srtpath -m /var/www/html/abp/www/anaouder/langues/vosk-model-fr-0.22 --output-type srt 2>&1";
    }else{
    echo "aucune langue choisie";
    exit();
    }
    $out = shell_exec($command);
    // Check if the output contains any errors or if the output is empty
    if ($out === null) {
        throw new Exception("The shell_exec() function returned null. The command might have failed.");
    }
    // You can check for specific error keywords from the Python script's output
    if (strpos($out, 'Error') !== false || strpos($out, 'Exception') !== false) {
       # throw new Exception("An error occurred: $out");
    }
    // If everything is successful, proceed
   # echo "SRT file created successfully.";
   # echo "Output: $out";
} catch (Exception $e) {
    // Catch any errors and handle them
    echo "An error occurred: " . $e->getMessage();
}
          if(file_get_contents($srtpath, true)){		
		$output=file_get_contents($srtpath);
		echo "<div><table cellspacing='2' style='margin:0 auto; margin-top:40px;width:100%;'><tr><td width='500' align='right'>";
		echo "<form action='saveSRTfile.php' method='POST' onsubmit='updateOutput2();'>";
		echo "Reizhañ amañ mar bez ezhomm / Corrigez ici si nécessaire <TEXTAREA rows=\"200\" cols=\"50\" style='text-align:left;padding:20px;color:#1434A4;' id='srtContent' >" .$output. "</textarea></td>";
		echo '<td valign="top" align="left"><button class="button-fin" type="button" onclick="downloadSRT()">Saveteiñ / Sauvegarder</button>';
	#echo "<input type = 'Submit' class='button-fin' name ='button4' value='Saveteiñ ar fichenn war ar servijer' >";
echo '<input type="hidden" name="srtfile" value="' . $srtfile . '">';
	echo '<input type ="hidden" name="output-updated" id="output-updated" value="" >';
echo "</form>";
echo "</td></tr></table></div>";
}#end of upload file
}#end of button 2
function timeToSeconds($timeString) {
    $timeComponents = explode(':', $timeString);
    $hours = intval($timeComponents[0]);
    $minutes = intval($timeComponents[1]);
    $seconds = floatval($timeComponents[2]);
    return round($hours * 3600 + $minutes * 60 + $seconds);
}
?>
<script>
function downloadSRT() {
    var srtContent = document.getElementById('srtContent').value;
    var blob = new Blob([srtContent], { type: 'text/plain' });
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = "<?PHP echo $srtfile;?>";
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
<script>
    // Initialize Wavesurfer
    var wavesurfer = WaveSurfer.create({
        container: '#waveform',
        waveColor: 'purple',
        progressColor: 'gray',
    });
    // Load an audio file
    wavesurfer.load("<?php echo $URL; ?>");
    // Handle play/pause button click
    document.getElementById('play-button').addEventListener('click', function() {
        wavesurfer.playPause();
    });
    wavesurfer.on('audioprocess', function (time) {
    updateTimer(time);
});
    function updateTimer(time) {
    var minutes = Math.floor(time / 60);
    var seconds = Math.floor(time % 60);
    var formattedTime = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    document.getElementById('wave-timer').innerText = formattedTime;
}
</script>
<script>
function copyleft() {
  confirm("J'accepte que cet enregistrement soit dans le domaine public et fasse parti du corpus breton (licence creative commons CC-BY). Il pourra être déconstruit afin de servir au développement de traducteurs, transcripteurs, soustitreurs, vérificateurs d'orthographe, rédacteurs intelligents, synthétiseurs de voix, etc.");
}
</script>
<script>
function updateOutput2() {
    var srtContent = document.getElementById('srtContent').value;
    // Update the hidden input field for the updated content
    document.getElementById('output-updated').value = srtContent;
}
</script>
 <script>
  function uploadAndProcess() {
    let formData = new FormData(document.getElementById("formupload"));
    showSpinner(); // Show spinner when uploading starts
    fetch('istitlan.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            // playBeep();
             //alert("Echu eo");
            // Hide spinner when upload and processing are completed
            //hideSpinner();
            console.log("Upload and processing completed.");
           //playAudioMessage();
        } else {
            throw new Error('Error during upload and processing.');
        }
    })
    .catch(error => {
        // Handle errors
        hideSpinner(); // Hide spinner in case of error
        console.error('Error:', error);
    });
}
        function showSpinner() {
            document.getElementById('spinner').style.display = 'block';
        }
        function hideSpinner() {
            document.getElementById('spinner').style.display = 'none';
        }
    </script>
</body>
</html>
