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

    #[Route('_default', name : 'home')]
	public function index(){
        $this->repo->all("",["products"]);
        $count = DAO::count(Product::class);
        $this->loadView('StoreController/index.html', ["count"=>$count]);
	}

    #[Route(path: "store/idSection", name: 'store.section')]
    public function sectionroute(int $id) {
        $this->repo->all($id);
       /* $count = DAO::count(Product::class);
        $countsection = DAO::count(Section::class);*/
        $this->loadView('StoreController/index.html');

    }

    
}
