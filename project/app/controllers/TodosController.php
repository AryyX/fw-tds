<?php
namespace controllers;
 use Ubiquity\attributes\items\router\Get;
 use Ubiquity\attributes\items\router\Post;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\cache\CacheManager;
 use Ubiquity\utils\http\URequest;
 use Ubiquity\utils\http\USession;

 /**
  * Controller TodosController
  */
 //#[Route('todos')]
class TodosController extends \controllers\ControllerBase{

    const CACHE_KEY = 'datas/lists/';
    const EMPTY_LIST_ID='not saved';
    const LIST_SESSION_KEY='list';
    const ACTIVE_LIST_SESSION_KEY='active-list';


     #[Route('_default', name : 'home')]
	public function index(){
		$list = USession::get(self::LIST_SESSION_KEY,[]);
        $this->displayList($list);

	}

    #[Post('todos/add', name : 'todos.add')]
    public function addElement(){
         $val = URequest::post('elm');
         $list = USession::get(self::LIST_SESSION_KEY,[]);
         $list[] = $val;
        USession::set(self::LIST_SESSION_KEY, $list);
        $this->displayList($list);

    }

    #[Post('todos/delete/{index}', name : 'todos.delete')]
    public function deleteElement($index){
        $list = USession::get(self::LIST_SESSION_KEY,[]);
        unset($list[$index]);
        USession::set(self::LIST_SESSION_KEY, array_values($list));
        $this->displayList($list);


    }

    #[Post('todos/edit/{index}', name : 'todos.edit')]
    public function editElement ($index){
        $val = URequest::post('elm');
        $list = USession::get(self::LIST_SESSION_KEY,[]);
        $list[$index] = $val;
        USession::set(self::LIST_SESSION_KEY, $list);
        $this->displayList($list);
    }

    #[Get('todos/loadlist/{uniqid}', name: 'todos.loadList')]
    public function loadList($id){
       if (CacheManager::$cache->exists(self::CACHE_KEY . $id)) {
            $list = CacheManager::$cache->fetch(self::CACHE_KEY . $id);
            $this->displayList($list);
        }
        else{
            $this->showMessage('',"la liste n'existe pas");
        }

    }

     #[Post('todos/loadlist', name : 'todos.loadListPost')]
     public function loadListFromForm(){

     }

     #[Get('todos/new/{force}', name : 'todos.new')]
     public function newlist($force){
            if ($force == false){
                $this->showMessage('',"la liste existe déjà et pas vide");
            }
            else{

            }
     }

     #[Get('todos/saveList', name : 'todos.save')]
     public function saveList(){
         $list = USession::get(self::LIST_SESSION_KEY,[]);
         $id=uniqid('',true);
         CacheManager::$cache->store(self::CACHE_KEY . $id, $list);
         $this->showMessage('',"Liste Sauvegardée avec l'id =" .$id);
         $this->showMessage('',"Acceder à la liste avec le lien http://127.0.0.1:8090/todos/loadlist/". $id);
     }

    private function showMessage(string $header, string $message, string $type = '', string $icon = 'info circle',array $buttons=[]) {
        $this->loadView('main/message.html', compact('header', 'type', 'icon', 'message','buttons'));
    }

    private function displayList(array $list) {
         $this->loadView( 'TodosController/displayList.html',['list'=> $list]);
    }


}