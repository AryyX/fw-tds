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
        $this->repo->all();
        $count = DAO::count(Product::class);
        $this->loadView('StoreController/index.html', ["count"=>$count]);
	}
    /*#[Get(name: 'store.allProducts')]
    public function allProducts(){
        $this->LinkTo("store.allProducts");
    }

    #[Get(name: 'section.name')]
    public function section_name(){
        $names = DAO::getAll(Section::class );
        foreach($names as $name){
            echo $name->getName()."<br>";
        }

    }

    #[Get(name: 'section.description')]
    public function section_desc(){
        $descs = DAO::getAll(Section::class );
        foreach($descs as $desc){
            echo $desc->getDescription()."<br>";
        }

    }*/
    
}
