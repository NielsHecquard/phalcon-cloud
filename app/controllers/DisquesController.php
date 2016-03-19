<?php
class DisquesController extends \DefaultController {
	public function initialize(){
		parent::initialize();
		$this->model="Disque";
	}

    /**
     * Affecte membre à membre les valeurs du tableau associatif $_POST aux membres de l'objet $object<br>
     * Prévoir une sur-définition de la méthode pour l'affectation des membres de type objet<br>
     * Cette méthode est utilisée par update()
     * @see DefaultController::update()
     * @param multitype:$className $object
     */
	protected function setValuesToObject(&$object) {
		parent::setValuesToObject($object);
		//TODO 4.4.1
	}

	public function frmAction($id=NULL){
		$disque=$this->getInstance($id);
		$jquery=$this->jquery;
		$this->jquery->compile($this->view);
		$this->view->setVars(array("disque"=>$disque,"siteUrl"=>$this->url->getBaseUri(),"baseHref"=>$this->dispatcher->getControllerName()));

		$services = Service::find();
		foreach($services as $service) {
			$checkboxes[$service->getNom()] = array("services[]", "value" => $service->getId());
		}
		$this->view->checkboxes = $checkboxes;
		parent::frmAction($id);
	}

	public function updateAction()
	{
		$disque = new Disque();
		$disque->save(
			array(
				'nom' => $this->request->getPost("nom"),
				'idUtilisateur' => $this->request->getPost("idUtilisateur")
			)
		);
		foreach ($disque->getMessages() as $message) {
			$params[] = $message->getMessage();
		}
		$idDisque = $disque->getId();
		$params["idDisque"] = $idDisque;

		foreach($_POST["services"] as $service) {
			$disqueService = new DisqueService();
			$disqueService->save(
				array(
					'idDisque' => $idDisque,
					'idService' => $service
				)
			);
			foreach ($disqueService->getMessages() as $message) {
				$params[] = $message->getMessage();
			}
		}

		$disqueTarif = new DisqueTarif();
		$disqueTarif->save(
			array(
				'idDisque' => $idDisque,
				'idTarif' => $this->request->getPost("idTarif"),
				'startDate' => date("Y-m-d h:m:s")
			)
		);
		foreach ($disqueTarif->getMessages() as $message) {
			$params[] = $message->getMessage();
		}

		$this->_postUpdateAction($params);
	}

	/**
	 * Action à exécuter après update
	 * par défaut forward vers l'index du contrôleur en cours
	 * @param array $params
	 */
	protected function _postUpdateAction($params){
		$this->dispatcher->forward(array("controller"=>"Scan","action"=>"index","params"=>$params));
	}


	protected function _deleteMessage($object){
		return "Confirmez-vous la suppression du disque <b>".$object."</b> ?";
	}
}