<?php

/**
 * Classe de gestion des fiches site.xml
 */
 
class pl3_fiche_site extends pl3_outil_fiche_xml {
	const NOM_FICHE = "site";
	
	/* Constructeur */
	public function __construct($chemin) {
		$this->obligatoire = true;
		parent::__construct($chemin, 1);
	}
	
	/* Mutateurs */
	public function ajouter_page($nom_page) {
		$dossier = _CHEMIN_PAGES_XML.$nom_page."/";
		$ret = @mkdir($dossier);
		if ($ret) {
			$xml = $this->ouvrir_fiche_xml().$this->fermer_fiche_xml();
			$page = new pl3_fiche_page($dossier);
			$page->enregistrer_xml();
			if ($ret) {
				$fichier_touch = $dossier."/"._PAGE_TOUCH._SUFFIXE_XML;
				@touch($fichier_touch);
			}
		}
		return $ret;
	}
	public function supprimer_page($nom_page) {
		$ret = false;
		if (strcmp($nom_page, "index")) {
			$dossier = _CHEMIN_PAGES_XML.$nom_page."/";
			$liste = @glob($dossier."*");
			foreach ($liste as $elem_liste) {
				@unlink($elem_liste);
			}
			$ret = @rmdir($dossier);
		}
		return $ret;
	}

	/* Chargement */
	public function charger_xml() {
		parent::charger_xml();
	}

	/* Afficher */
	public function afficher() {
		$ret = "";
		$ret .= $this->afficher_head();
		$ret .= $this->afficher_body();
		return $ret;
	}
	
	public function afficher_head() {
		$ret = "";
		$ret .= "<!doctype html>\n";
		$ret .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" lang=\"fr\" dir=\"ltr\">\n";
		$ret .= "<head>\n";
		$ret .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
		$ret .= "<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n";
		
		/* Partie CSS */
		$ret .= $this->declarer_css("https://use.fontawesome.com/4624d65caf.css");
		$ret .= $this->declarer_css(_CHEMIN_CSS."pl3.css");
		$ret .= $this->declarer_css(_CHEMIN_CSS."pl3_admin.css");
		$ret .= $this->declarer_css(_CHEMIN_CSS."pl3_admin_site.css", _MODE_ADMIN_SITE_GENERAL);
		
		/* Partie JS */
		$ret .= $this->declarer_js("//code.jquery.com/jquery-1.12.0.min.js");
		$ret .= "</head>\n";
		return $ret;
	}

	public function afficher_body() {
		$ret = "";
		$ret .= $this->ouvrir_body();
		$ret .= $this->ecrire_body();
		$ret .= $this->fermer_body();
		return $ret;
	}	
	
	public function ouvrir_body() {
		$ret = "";
		$ret .= "<body>\n";
		return $ret;
	}

	public function ecrire_body() {
		$contenu_mode = "";
		$classe_mode = "page_parametres";
		if (($this->mode & _MODE_ADMIN_SITE_GENERAL) > 0) {
			$contenu_mode .= $this->ecrire_body_general();
		}
		elseif (($this->mode & _MODE_ADMIN_SITE_THEMES) > 0) {
			$contenu_mode .= $this->ecrire_body_themes();
		}
		elseif (($this->mode & _MODE_ADMIN_SITE_OBJETS) > 0) {

		}
		$classe = $classe_mode." page_mode_admin";
		$ret = "<div class=\"".$classe."\" name=\"site\">".$contenu_mode."</div>\n";
		return $ret;
	}
	
	public function fermer_body() {
		$ret = "";
		/* TEMPORAIRE : Ajout d'un lien pour switcher le mode */
		if (($this->mode & _MODE_ADMIN) > 0) {
			$ret .= "<p style=\"margin-top:20px;\"><a href=\"../"._PAGE_COURANTE._SUFFIXE_PHP."\">Mode normal</a></p>\n";
		}
		else {
			$ret .= "<p style=\"margin-top:20px;\"><a href=\"admin/"._PAGE_COURANTE._SUFFIXE_PHP."\">Mode admin</a></p>\n";
		}

		/* Appel des outils javascript */
		$ret .= $this->declarer_js(_CHEMIN_JS."pl3_admin.js");
		$ret .= $this->declarer_js(_CHEMIN_JS."pl3_admin_site.js", _MODE_ADMIN_SITE_GENERAL);
		$ret .= "</body>\n";
		$ret .= "</html>\n";
		return $ret;
	}
	
	public function ecrire_vignette_page($nom, $datec, $datem) {
		$ret = "";
		$classe = strcmp($nom, _PAGE_COURANTE)?"vignette_page":"vignette_page_active";
		$ret .= "<div class=\"".$classe."\">";
		$ret .= "<h2>".$nom."</h2>";
		$ret .= "<div class=\"vignette_page_info\">";
		$ret .= "<p>Création ".(($datec > 0)?("le ".date("d/m/Y à H:i",$datec)):"à une date inconnue")."</p>";
		$ret .= "<p>Modification ".(($datem > 0)?("le ".date("d/m/Y à H:i",$datem)):"à une date inconnue")."</p>";
		$ret .= "<hr><p class=\"vignette_icones\">";
		$ret .= "<a id=\"admin-mode-"._MODE_ADMIN_PAGE."\" class=\"vignette_icone_admin\" href=\"../admin/".$nom._SUFFIXE_PHP."\" title=\"Administrer la page ".$nom."\"><span class=\"fa fa-wrench\"></span></a>";
		if (strcmp($nom, "index")) {
			$ret .= "<a id=\"supprimer-".$nom."\" class=\"vignette_icone_supprimer\" href=\"#\" title=\"Supprimer la page ".$nom."\" target=\"_blank\"><span class=\"fa fa-trash\"></span></a>";
		}
		$ret .= "</p></div></div>";
		return $ret;
	}
	
	public function ecrire_liste_vignettes_page() {
		$ret = "";
		$liste_pages = $this->lire_liste_pages();
		foreach($liste_pages as $page) {
			$ret .= $this->ecrire_vignette_page($page["nom"], $page["datec"], $page["datem"]);
		}
		return $ret;
	}
	
	private function ecrire_body_general() {
		$ret = "";
		$ret .= "<h1>Liste des pages</h1>\n";
		$ret .= "<div id=\"liste-pages\" class=\"container_vignettes_page\">\n";
		$ret .= $this->ecrire_liste_vignettes_page();
		$ret .= "</div>\n";
		
		/* Création d'une nouvelle page */
		$ret .= "<div id=\"nouvelle-page\" class=\"container_vignettes_page\">\n";
		$ret .= "<div class=\"vignette_page_nouvelle\">";
		$ret .= "<h2>Création d'une nouvelle page</h2>";
		$ret .= "<div class=\"vignette_page_info\">";
		$ret .= "<form class=\"formulaire_nouvelle_page\" method=\"post\">";
		$ret .= "<p><label for=\"id-nouvelle-page\">Nom&nbsp;:</label><input id=\"id-nouvelle-page\" type=\"text\" name=\"nom-nouvelle-page\"/>";
		$ret .= "<input id=\"id-nom-page-courante\" type=\"hidden\" name=\"nom-page-courante\" value=\""._PAGE_COURANTE."\">"; 
		$ret .= "<input type=\"submit\" value=\" Créer la page \"></p>"; 
		$ret .= "</form></div></div></div>";

		return $ret;
	}
	
	private function ecrire_body_themes() {
		$ret = "";
		$ret .= "<h2>Liste des thèmes</h2>\n";
		$ret .= "<ul style=\"padding-left:30px;\">\n";
		$liste_themes = $this->lire_liste_themes();
		foreach($liste_themes as $theme) {
			$ret .= "<li>".$theme."</li>\n";
		}
		$ret .= "</ul>\n";
		return $ret;
	}
	
	private function ecrire_body_objets() {
		$ret = "";
		$ret .= "<h2>Liste des objets</h2>\n";
		$ret .= "<ul style=\"padding-left:30px;\">\n";
		$liste_objets = $this->lire_liste_objets();
		foreach($liste_objets as $balise => $icone) {
			$ret .= "<li><span class=\"fa ".$icone."\"></span>&nbsp;".$balise."</li>\n";
		}
		$ret .= "</ul>\n";
		return $ret;
	}
	
	public function lire_liste_pages() {
		$ret = array();
		$liste = @glob(_CHEMIN_PAGES_XML."*/".(pl3_fiche_page::NOM_FICHE)._SUFFIXE_XML);
		foreach ($liste as $elem_liste) {
			if (is_file($elem_liste)) {
				$nom_dossier = dirname($elem_liste);
				$datem = @filemtime($elem_liste);
				$datec = @filemtime($nom_dossier."/"._PAGE_TOUCH._SUFFIXE_XML);
				$ret[] = array("nom" => str_replace(_CHEMIN_PAGES_XML, "", $nom_dossier), "datec" => $datec, "datem" => $datem);
			}
		}
		return $ret;
	}
	
	private function lire_liste_themes() {
		$ret = array();
		$liste = @glob(_CHEMIN_THEMES_XML."*/".(pl3_fiche_theme::NOM_FICHE)._SUFFIXE_XML);
		foreach ($liste as $elem_liste) {
			if (is_file($elem_liste)) {
				$nom_dossier = @dirname($elem_liste);
				$ret[] = str_replace(_CHEMIN_THEMES_XML, "", $nom_dossier);
			}
		}
		return $ret;
	}
	
	// TODO : Supprimer le doublon avec pl3_fiche_page ! 
	private function lire_liste_objets() {
		$ret = array();
		$liste = @glob(_CHEMIN_OBJET.pl3_fiche_page::NOM_FICHE."/"._PREFIXE_OBJET.pl3_fiche_page::NOM_FICHE."_*"._SUFFIXE_PHP);
		foreach ($liste as $elem_liste) {
			if (@is_file($elem_liste)) {
				$nom_fichier = basename($elem_liste);
				$nom_classe = str_replace(_SUFFIXE_PHP, "", $nom_fichier);
				$nom_constante_balise = $nom_classe."::NOM_BALISE";
				$nom_constante_icone = $nom_classe."::NOM_ICONE";
				$nom_constante_fiche = $nom_classe."::NOM_FICHE";
				if ((@defined($nom_constante_balise)) && (@defined($nom_constante_icone)) && (@defined($nom_constante_fiche)))  {
					$nom_fiche = $nom_classe::NOM_FICHE;
					if (!(strcmp($nom_fiche, pl3_fiche_page::NOM_FICHE))) {
						$nom_balise = $nom_classe::NOM_BALISE;
						$nom_icone = $nom_classe::NOM_ICONE;
						$ret[$nom_balise] = $nom_icone;
					}
				}
			}
		}
		return $ret;
	}

	private function declarer_css($fichier_css, $mode = -1) {
		$ret = "";
		if (($mode == -1) || (($mode & $this->mode) > 0)) {
			$ret .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$fichier_css."\"/>\n";
		}
		return $ret;
	}
	private function declarer_js($fichier_js, $mode = -1) {
		$ret = "";
		if (($mode == -1) || (($mode & $this->mode) > 0)) {
			$ret .= "<script type=\"text/javascript\" src=\"".$fichier_js."\"></script>\n";
		}
		return $ret;
	}
}