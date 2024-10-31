<?php
/**
 * Plugin Name: Scan Upload par JM Créa
 * Plugin URI: http://www.jm-crea.com
 * Description: Scannez votre dossier Upload et détectez les fichiers php, js, htaccess, exe, sql, rar, zip, czip, tar.gz et supprimez-les.
 * Version: 1.6
 * Text Domain: scan-upload-par-jm-crea
 * Author: JM Créa
 * Author URI: http://www.jm-crea.com/
 */

//On créé le menu
function menu_scanupload() {
add_submenu_page( 'tools.php', 'Scann Upload', 'Scann Upload', 'manage_options', 'scanupload', 'afficher_scanupload' ); 
}
add_action('admin_menu', 'menu_scanupload');
add_action( 'admin_enqueue_scripts', 'style_su_jm_crea' );


function style_su_jm_crea() {
	wp_register_style('Scann_Upload_Par_JM_Crea', plugins_url( 'css/style.css', __FILE__ ));
	wp_enqueue_style('Scann_Upload_Par_JM_Crea');	
}

function note_su() {

echo "
<div id='rate'>
<h2>NOTEZ-MOI SUR<br>WORDPRESS.ORG</h2>
<a href='https://wordpress.org/plugins/scan-upload-par-jm-crea/' target='_blank'><img src='" . plugins_url( 'img/star.png', __FILE__ ) . "' alt='notez-moi' /></a>
</div>";	

}

function clean_su() {

$upload_dir = wp_upload_dir();
$rep = $upload_dir['basedir'];

//echo $rep;
//$rep = '../wp-content/uploads/';

$iter = new RecursiveDirectoryIterator( $rep );
echo "<p><a href='" . get_site_url() . "/wp-admin/tools.php?page=scanupload'><input type='submit' name='nettoyer' id='nettoyer' value='Scanner le dossier upload' class='button button-primary' /></a></p>";

echo "<div id='scanner'>";
echo "<form enctype='multipart/form-data' method='post'>";
echo "<table class='wp-list-table widefat striped'>";
echo "<th scope='row'>N°</th>";
echo "<th scope='row'>SUP</th>";
echo "<th scope='row'>FICHIERS TROUVES</th>";
echo "<th scope='row'>DERNIERES MODIFICATIONS</th>";
echo "<th scope='row'>TAILLES</th>";
echo "<th scope='row'>ACTION</th>";

$i = 0;
foreach (new RecursiveIteratorIterator($iter) as $file) {
$extension = pathinfo($file->getFilename(),PATHINFO_EXTENSION);
$url_fichier = 	$file->getPath() . "/" . $file->getFilename();

if ( ($extension == 'php')||($extension == 'PHP')||($extension == 'JS')||($extension == 'js')||($extension == 'htaccess')||($extension == 'HTACCESS')||($extension == 'exe')||($extension == 'EXE')||($extension == 'sql')||($extension == 'SQL')||($extension == 'zip')||($extension == 'ZIP')||($extension == 'rar')||($extension == 'RAR')||($extension == 'czip')||($extension == 'CZIP')||($extension == 'gz')||($extension == 'GZ')||($extension == 'bak')||($extension == 'BAK')||($extension == 'old')||($extension == 'OLD')||($extension == 'cache')||($extension == 'CACHE')||($extension == 'bak')||($extension == 'BACK')||($extension == 'bash')||($extension == 'BASH')) {

//Conversion des tailles
$bytes = $file->getSize();
if ($bytes >= 1073741824) {
$bytes = number_format($bytes / 1073741824, 2) . ' GB';
}
elseif ($bytes >= 1048576) {
$bytes = number_format($bytes / 1048576, 2) . ' MB';
}
elseif ($bytes >= 1024) {
$bytes = number_format($bytes / 1024, 2) . ' KB';
}
elseif ($bytes > 1) {
$bytes = $bytes . ' B';
}
elseif ($bytes == 1) {
$bytes = $bytes . ' B';
}
else {
$bytes = '0 B';
}


//Convervion des dates
$info = new SplFileInfo(__FILE__);
$date_fichier = "Le " . date('d/m/Y', $file->getCTime()) . " à " . date('H:i', $file->getCTime()) . 'h';

echo "<tr>";
echo "<td>#" . $i . "</td>"; //N°
echo "<td><input type='checkbox' name='nbr_id[]' id='nbr_id' value='" . $url_fichier . "' /></td>"; //SUP
echo "<td>" . $file->getPath() . "/<strong>" . $file->getFilename() . "</strong></td>"; //URL Dossier
echo "<td>" . $date_fichier . "</td>"; //Dernières modifications
echo "<td>" . $bytes . "</td>"; //Taille
echo "<td><a href='tools.php?page=scanupload&action=sup&url_fichier=$url_fichier'>Supprimer</a></td>"; //Action
echo "</tr>";
$i++;
}
$url_fichier2 = $file->getPath() . "/<strong>" . $file->getFilename();
}  

$total_fichiers_trouves = $i;
echo "
<tr><td colspan='3' align='left'><strong class='texte_rouge'>TOTAL DE FICHIERS DETECTES : " . $i . "</strong></td><td colspan='3' align='right'><input type='submit' name='nettoyer' id='nettoyer' value='Supprimer ces fichiers' class='button button-primary' /></td></tr></p>
</table>
</form>";
echo "</div>";

if (isset($_POST['nettoyer'])) {	
$valeur = $_POST["nbr_id"];
foreach ($valeur as $val) {
if(file_exists($val)) { 
unlink($val);
}
}
echo "<script>document.location.href='tools.php?page=scanupload&action=sup'</script>";
}
if ($total_fichiers_trouves > 0) {
echo "<script>document.getElementById('scanner').style.display = 'block';</script>";
}
if ($total_fichiers_trouves <= 0) {
echo "<script>document.getElementById('scanner').style.display = 'none';</script>";
}
}

function afficher_scanupload() {
global $wpdb;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

echo "<h1>Scann upload par JM Créa</h1>";
echo "<p>Scann upload par JM Créa détecte les fichiers suspects de votre dossier upload Wordpress.</p>";
echo "<p><u>ATTENTION</u> : Certains fichiers peuvent faire partis de vos plugins, faites attention aux fichiers que vous supprimez.</p>";
echo "<p>Types de fichiers automatiquement détectés par le plugin : <code>.php</code>, <code>.js</code>, <code>.htaccess</code>, <code>.exe</code>, <code>.sql</code>, <code>.zip</code>, <code>.rar</code>, <code>.czip</code>, <code>.tar.gz</code>, <code>.old</code>, <code>.back</code>, <code>.cache</code>, <code>.bash</code></p>";

if ( (isset($_GET['action']))&&($_GET['action']) == 'sup' ) {
echo "<p class='texte_vert'>Fichier(s) supprimé(s) avec succés!</p>";	
}

//Sup 1 par 1
if (isset($_GET['action'])&&($_GET['action']) == "sup") {
$url_fichier = 	$_GET['url_fichier'];

if(file_exists($url_fichier)) { 
unlink($url_fichier);
}
}

note_su();
clean_su();
}

function head_meta_su_jm_crea() {
echo("<meta name='Scan Upload par JM Créa' content='1.6' />\n");
}
add_action('wp_head', 'head_meta_su_jm_crea');
?>