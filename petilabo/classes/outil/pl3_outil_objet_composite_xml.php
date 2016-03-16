<?php

/**
 * Classe de gestion des objets composites
 */
 
abstract class pl3_outil_objet_composite_xml extends pl3_outil_objet_xml {
	protected $noms_elements = array();
	protected $elements = array();

	public function lire_element_valeur($nom_balise) {
		if (isset($this->elements[$nom_balise])) {
			$element = $this->elements[$nom_balise];
			return $element->get_valeur();
		}
		return null;
	}
	
	protected function declarer_element($nom_balise) {
		$this->noms_elements[] = $nom_balise;
	}
	
	protected function charger_elements_xml() {
		foreach ($this->noms_elements as $nom_element) {
			$element = $this->parser_balise_fille($nom_element);
			if ($element != null) {$this->elements[$nom_element] = $element;}
		}
	}
	
	protected function ecrire_elements_xml($niveau) {
		$xml = "";
		foreach ($this->elements as $element) {
			$xml .= $element->ecrire_xml($niveau);
		}
		return $xml;
	}
	
	protected function afficher_elements_xml() {
		foreach ($this->elements as $element) {
			$element->afficher();
		}
	}
	
	public function __call($methode, $args) {
		if (!(strncmp($methode, "get_", 4))) {
			$nom_balise = substr($methode, 4);
			return $this->lire_element_valeur($nom_balise);
		}
		else {die("ERREUR : Appel d'une méthode non définie dans un objet XML composite"); }
	}
}