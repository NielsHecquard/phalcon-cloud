<?php

class MyDisquesController extends \ControllerBase {
	/**
	 * Affiches les disques de l'utilisateur
	 */
	public function indexAction(){
		$jquery=$this->jquery;
		$user = Auth::getUser($this);
		$this->view->user = $user;

		$pgbs=[];
		$noms=[];

		$disques = Disque::find(
			array(
				"conditions" => "idUtilisateur = 1"
			)
		);
		$nb=0;
		foreach($disques as $disque) {
			$nom = $disque->getNom();
			$occupation = ModelUtils::getDisqueOccupation($this->config->cloud, $disque);
			$tarif = Tarif::find(
				array(
					"conditions" => "id = ?1",
					"bind" => array(1 => $disque->getId())
				)
			);
			$quota = $tarif[0]->getQuota();
			$unite = $tarif[0]->getUnite();
			$conversion = ModelUtils::sizeConverter($unite);
			$quotaOctet = $quota * $conversion;
			$ratio = ($occupation/$quotaOctet)*100;
			$pgb = $jquery->bootstrap()->htmlProgressbar("pgb");
			$pgb->setStriped(true);
			if($ratio<10) {
				$pgb->setStyle("info");
			}
			elseif($ratio<50) {
				$pgb->setStyle("success");
			}
			elseif($ratio<80) {
				$pgb->setStyle("warning");
			}
			elseif($ratio<=100) {
				$pgb->setStyle("danger");
			}
			$pgb->setValue($ratio);
			$pgbs[$nb] = $pgb;
			$noms[$nom] = $nom;
			$nb+=1;
		}
		$this->view->pgbs = $pgbs;
		$this->view->noms = $noms;
		$this->view->nb = $nb;
	}
}