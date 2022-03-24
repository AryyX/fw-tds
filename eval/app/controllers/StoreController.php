<?php
namespace controllers;

use models\Product;
use Ubiquity\orm\DAO;
use models\Section;
use Ubiquity\orm\repositories\ViewRepository;
use Ubiquity\attributes\items\router\Post;
use Ubiquity\attributes\items\router\Get;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\Router;
use Ubiquity\utils\http\URequest;
use Ubiquity\utils\http\USession;

/**
  * Controller StoreController
  */

#[Route('store')]
class StoreController extends \controllers\ControllerBase{

    private ViewRepository $repo;

    public function initialize() {
        parent::initialize();
        $this->repo??=new ViewRepository($this,Section::class);
    }

    #[Route('/_default', name : 'home')]
	public function index(){
        $this->repo->all("",["products"]);
        $sections = DAO::getAll(Section::class);
        $count = DAO::count(Product::class);
        $this->loadView('StoreController/index.html');
	}


    #[Get (path : "section/{id}",name: 'store.section')]
    public function store_section(int $id){
        $sectionid = $this->repo->byId( $id, ['products']);
        $this->loadView('StoreController/section.html',['section'=>$sectionid, 'card' => USession::get('card',["nombre"=>0,"thune"=>0])]);
    }




    
}
