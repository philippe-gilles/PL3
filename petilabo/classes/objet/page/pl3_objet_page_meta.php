<?php

/**
 * Classe de gestion des images
 */
 
class pl3_objet_page_meta_titre extends pl3_outil_objet_xml {
	const NOM_BALISE = "titre";
	public static $Noms_attributs = array();
	
	public function ecrire_xml($niveau) {
		$xml = $this->ouvrir_fermer_xml($niveau);
		return $xml;
	}
	
	public function afficher() {
		$valeur_titre = $this->get_valeur();
		if (strlen($valeur_titre) > 0) {
			echo "<title>".$this->get_valeur()."</title>\n";
		}
	}
}

class pl3_objet_page_meta_description extends pl3_outil_objet_xml { 
	const NOM_BALISE = "description";
	public static $Noms_attributs = array();
	
	public function ecrire_xml($niveau) {
		$xml = $this->ouvrir_fermer_xml($niveau);
		return $xml;
	}
	
	public function afficher() {
		$valeur_descr = $this->get_valeur();
		if (strlen($valeur_descr) > 0) {
			echo "<meta name=\"description\" content=\"".$this->get_valeur()."\" />\n";
		}
	}
}

class pl3_objet_page_meta extends pl3_outil_objet_composite_xml {
	const NOM_BALISE = "meta";
	public static $Noms_attributs = array();
	
	public function __construct($nom_fiche, $id_parent, &$noeud = null) {
		$this->declarer_element(pl3_objet_page_meta_titre::NOM_BALISE);
		$this->declarer_element(pl3_objet_page_meta_description::NOM_BALISE);
		parent::__construct($nom_fiche, $id_parent, $noeud);
	}

	public function charger_xml() {
		$this->charger_elements_xml();
	}

	public function ecrire_xml($niveau) {
		$xml = $this->ouvrir_xml($niveau);
		$xml .= $this->ecrire_elements_xml(1 + $niveau);
		$xml .= $this->fermer_xml($niveau);
		return $xml;
	}
	
	public function afficher() {
		$this->afficher_elements_xml();
	}
}