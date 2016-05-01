<?php

/**
 * Classe de gestion des fiches media.xml
 */
 
class pl3_fiche_media extends pl3_outil_fiche_xml {
	const NOM_FICHE = "media";
	
	/* Constructeur */
	public function __construct($chemin, $id) {
		$this->declarer_objet("pl3_objet_media_image");
		$this->declarer_objet("pl3_objet_media_galerie");
		parent::__construct($chemin, $id);
	}
	
	/* Afficher */
	public function afficher() {
		$ret = "";
		$source_page = pl3_outil_source_page::Get();
		$liste_tailles = $source_page->chercher_liste_noms_par_fiche("theme", "pl3_objet_theme_taille_image");
		$liste_medias_par_taille = array();
		foreach($liste_tailles as $nom_taille) {
			$liste_medias_par_taille[$nom_taille] = array();
		}
		
		/* Classement des images selon les tailles */
		$liste_medias = $this->liste_objets["pl3_objet_media_image"];
		foreach($liste_medias as $media) {
			$nom_taille = $media->get_valeur_taille();
			if (in_array($nom_taille, $liste_tailles)) {
				$liste_medias_par_taille[$nom_taille][] = $media;
			}
		}
		
		$classe = "page_media".((($this->mode & _MODE_ADMIN) > 0)?" page_mode_admin":"");
		$ret .= "<div class=\"".$classe."\" name=\""._PAGE_COURANTE."\">\n";
		/* Liste des images taille par taille */
		foreach($liste_tailles as $nom_taille) {
			$ret .= "<h2>".$nom_taille."</h2>\n";
			$ret .= "<div class=\"taille_container\">\n";
			foreach($liste_medias_par_taille[$nom_taille] as $media) {
				$nom = $media->get_attribut_nom();
				$ret .= "<div class=\"vignette_container\">";
				$ret .= "<a class=\"vignette_apercu_lien\" href=\"\" title=\"Editer l'image ".$nom."\">";
				$ret .= $media->afficher($this->mode);
				$ret .= "</a>";
				$ret .= "<p class=\"vignette_legende_image\">".$nom."</p>";
				$ret .= "</div>\n";
			}
			$ret .= "<div class=\"vignette_container\">";
			$ret .= "<a class=\"fa fa-plus-circle vignette_plus\" href=\"#\" title=\"Ajouter une image au format ".strtolower($nom_taille)."\"></a>";
			$ret .= "</div>\n";
			$ret .= "<div class=\"clearfix\"></div>\n";
			$ret .= "</div>\n";
		}
		$ret .= "</div>\n";
		return $ret;
	}
}