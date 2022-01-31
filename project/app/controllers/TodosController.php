<?php
namespace controllers;
 /**
  * Controller TodosController
  */
  use Ajax\php\ubiquity\UIService;
  use Ajax\semantic\components\validation\Rule;
  use Ajax\semantic\html\base\constants\TextAlignment;
  use Ajax\semantic\html\collections\HtmlMessage;
  use Ajax\semantic\html\elements\HtmlLabel;
  use Ajax\semantic\html\elements\HtmlList;
  use services\SessionStorageList;
  use services\UITodosService;
  use Ubiquity\attributes\items\router\Post;
  use Ubiquity\attributes\items\router\Get;
  use Ubiquity\attributes\items\router\Route;
  use Ubiquity\cache\CacheFile;
  use Ubiquity\cache\CacheManager;
  use Ubiquity\controllers\auth\AuthController;
  use Ubiquity\controllers\auth\WithAuthTrait;
  use Ubiquity\controllers\Router;
  use Ubiquity\utils\http\URequest;

class TodosController extends \controllers\ControllerBase{
  const CACHE_KEY = 'datas/lists/';
  const EMPTY_LIST_ID='not saved';
  const LIST_SESSION_KEY='list';
  const ACTIVE_LIST_SESSION_KEY='active-list';

  public $footerView = "TodosController/index.html";


  private function showMessage(string $header, string $message, string $type = '', string $icon = 'info circle',array $buttons=[]) {
    $this->loadView('main/message.html', compact('header', 'type', 'icon', 'message','buttons'));
  }

  private function displayList(array $list) {
		$this->uiService->displayList($list,$this->service->getTitle());
	}


  #[Route(path: "_default", name: 'home')]
	public function index(){
    if ($this->service->listExist()) {
			return $this->displayList($this->service->getList(false));
		}
		$bts=[
			['caption'=>'Créer une Nouvelle liste','url'=>['todos.new',[true]],'class'=>'basic']
		];
		if($this->getAuthController()->_isValidUser()){
			$bts[]=['caption'=>'Mes listes','url'=>['todos.myLists',[]],'class'=>'basic'];
		}
		$this->showMessage('Bienvenue !', "<p>TodoLists permet de gérer des listes diverses et variées...", 'error', 'info circle',$bts);
	}

  public function initialize()
  {
    parent::initialize();
		$this->service=new SessionStorageList($this);
		$this->uiService=new UITodosService($this);
		if(!URequest::isAjax()) {
			$this->loadView("TodosController/menu.html");
		}
  }


  #[Route(path: "/todos/add/", name: 'todos.add', methods: ['post'])]
  public function addElement(){
    echo "test add";
    $toAdd = URequest::post('element');
		$elms = URequest::post('elements');
		if($elms){
			$toAdd=explode("\n",$elms);
		}
		if(isset($toAdd)){
			$list=$this->service->addElements($toAdd);
			return $this->displayList($list);
		}
		$this->displayList($this->service->getList());
  }

  #[Route(path: "/todos/delete/{index}", name: 'todos.delete', methods: ['get'])]
  public function deleteElement($index){
    $list=$this->service->deleteElement($index);
		if (!$this->service->hasError()) {
			$this->showMessage('Suppression', $this->service->getMessage('success'), 'success', 'check square outline');
			return $this->displayList($list);
		}
  }

  #[Route(path: "/todos/edit/{index}", name: 'todos.edit', methods: ['post'])]
  public function editElement($index){
    $id=$this->service->persistentSave($this->getAuthController()->_getActiveUser());
		if($this->service->hasError()){
			$this->showMessage('Sauvegarde', $this->service->getMessage('error'), 'error', 'warning circle');
		}else {
			$this->showMessage('Sauvegarde', $this->service->getMessage('success'), 'success', 'check square outline');
		}
		$this->index();
  }

  #[Route(path: "/todos/loadList/{uniqid}", name: 'todos.loadList', methods: ['get'])]
  public function loadList($uniqid){     
    $uniqid=\str_replace('[DS]',DS,$uniqid);
		if ($list=$this->service->persistentGet($uniqid)){
			$this->showMessage('Chargement', "Liste chargée depuis <b>$uniqid</b>", 'success', 'check square outline');
			$this->displayList($list);
		} else {
			$this->showMessage('Chargement', "La liste d'id <b>$uniqid</b> n'existe pas", 'error', 'frown outline');
		}
  }

  #[Route(path: "/todos/loadList", name: 'todos.loadListPost', methods: ['post'])]
  public function lodListFromForm(){
    $id=URequest::post('id');
		if($id!=null){
			return $this->loadList($id);
		}
		$this->index();
  }
  
  #[Route(path: "/todos/new/{force}", name: 'todos.new', methods: ['get'])]
  public function newlist($force){
    if($force===false && $this->service->listExist()){
			return $this->showMessage('Nouvelle liste','Une liste a déjà été créée. Souhaitez-vous la vider ?','warning','warning circle alternate',[
				['url'=>['Home',[]],'caption'=>'Annuler','class'=>''],
				['url'=>['todos.new',[true]],'caption'=>'Confirmer la création','class'=>'green']
			]);
		}
		$this->showMessage('Nouvelle Liste', "Liste correctement créée.", 'success', 'check square outline');
		$this->displayList($this->service->createList());
    
  }

  protected function getAuthController(): AuthController {
		return $this->_auth??=new MyAuth($this);
	}

  #[Route(path: "/todos/saveList/", name: 'todos.save', methods: ['get'])]
  public function saveList(){
    $id=$this->service->persistentSave($this->getAuthController()->_getActiveUser());
		if($this->service->hasError()){
			$this->showMessage('Sauvegarde', $this->service->getMessage('error'), 'error', 'warning circle');
		}else {
			$this->showMessage('Sauvegarde', $this->service->getMessage('success'), 'success', 'check square outline');
		}
		$this->index();
  }

  public function p404($url){
		echo "<div class='ui error inverted message'><div class='header'>404</div>The page `$url` you are looking for doesn't exist!</div>";
	}

  public function testJquery(){
    $this->jquery->click("'button','$('.elm').toggle();'");
    $this->jquery->renderDefaultView();
  }

  private function createCopyButton(string $bt,string $elmText){
    $this->jquery->click($bt,'
                    let tmp = $("<input>");
                    $("body").append(tmp);
                    tmp.val($("'.$elmText.'").text()).select();
                    document.execCommand("copy");
                    tmp.remove();
                    $("'.$bt.'").popup({content: "Idenfifiant copié !"}).popup("show");
    ');
}


}
