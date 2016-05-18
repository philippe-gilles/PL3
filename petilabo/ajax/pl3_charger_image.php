<?php
header('Content-type: application/json');

define("_CHEMIN_BASE_URL", "../../");
define("_CHEMIN_BASE_RESSOURCES", "../");
require_once(_CHEMIN_BASE_URL."petilabo/pl3_init.php");

/* Fonctions de service */
function nettoyer_nom_fichier($nom_fichier) {
	$ret = trim(strtolower($nom_fichier));
	$ret = str_replace(" ", "-", $ret);
	$ret = str_replace(".jpeg", ".jpg", $ret);
	return $ret;
}

function traduire_erreur_upload($erreur) {
	$ret = "ERREUR : ";
	switch($erreur) {
		case UPLOAD_ERR_INI_SIZE:
			$taille_autorisee = ini_get("upload_max_filesize");
			$ret .= "Le fichier sélectionné dépasse la taille maximum (".$taille_autorisee.")";
			break;
		case UPLOAD_ERR_PARTIAL:
			$ret .= "Le fichier n'a pu être que partiellement téléchargé.";
			break;
		case UPLOAD_ERR_NO_FILE:
			$ret .= "Aucun fichier n'a été téléchargé.";
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			$ret .= "Le dossier temporaire de téléchargement n'a pas été trouvé.";
			break;
		case UPLOAD_ERR_CANT_WRITE:
			$ret .= "Un problème est survenu lors de l'écriture du fichier.";
			break;
		default:
			$ret .= "Le fichier n'a pas pu être téléchargé pour une raison inconnue.";
	}
	return $ret;
}

/* Traitement de l'upload */
$info_sortie = "ERREUR : Le serveur n'a pas pu recevoir de fichiers.";
$retour_valide = false;
$index_taille = (int) (isset($_POST["taille"])?$_POST["taille"]:0);
	
$nom_champ_post = "img-".$index_taille;
if (isset($_FILES[$nom_champ_post])) {
	$ret_chargement = $_FILES[$nom_champ_post]["error"];
	if ($ret_chargement == UPLOAD_ERR_OK) {
		$fichier_temporaire = $_FILES[$nom_champ_post]["tmp_name"];

		/* Chargement de la fiche média locale */
		$nom_page = isset($_POST["page"])?$_POST["page"]:_PAGE_PRINCIPALE;
		define("_PAGE_COURANTE", $nom_page);
		define("_CHEMIN_PAGE_COURANTE", _CHEMIN_PAGES_XML.$nom_page."/");
		$fiche_media = new pl3_fiche_media(_CHEMIN_PAGE_COURANTE, 1);
		$retour_valide = $fiche_media->charger_xml();
		
		/* Rapatriement de l'image uploadée si la fiche média est disponible */
		if ($retour_valide) {
			$nom_origine = $_FILES[$nom_champ_post]["name"];
			$nom_destination = nettoyer_nom_fichier($nom_origine);
			$cible = _CHEMIN_XML."images/".$nom_destination;
			$retour_valide = move_uploaded_file($fichier_temporaire, $cible);
			if ($retour_valide) {
				list($largeur, $hauteur) = getimagesize($cible);
				$image = $fiche_media->instancier_image($nom_destination, $largeur, $hauteur);
				if ($image) {
					$fiche_media->ajouter_objet($image);
					$fiche_media->enregistrer_xml();
					$info_sortie = _CHEMIN_IMAGES_XML.$nom_destination;
				}
				else {
					$retour_valide = false;
					$info_sortie = "ERREUR : Impossible de créer l'élément XML pour ce média.";
				}
			}
			else {$info_sortie = "ERREUR : Le fichier téléchargé n'a pas pu être installé sur le site.";}
		}
		else {
			@unlink($fichier_temporaire);
			$info_sortie = "ERREUR : Impossible de charger la fiche XML des média.";
		}
	}
	else {
		$info_sortie = traduire_erreur_upload($ret_chargement);
	}
}

echo json_encode(array("code" => $retour_valide, "info" => $info_sortie));