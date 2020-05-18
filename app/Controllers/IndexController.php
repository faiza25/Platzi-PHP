<?php
namespace App\Controllers;
use App\Models\{Job, Project};

class IndexController extends BaseController{
  public function indexAction(){
    echo 'indexAction';

    $jobs = Job::all();

    $project1 = new Project('Project 1', 'Description 1');
    $projects = [
        $project1
    ];


    $lastName= 'Iza';
    $name ="Fabian $lastName";

    return $this->renderHTML('index.twig',[
      'name'=> $name,
      'jobs'=> $jobs
    ]);

  }

}


 ?>
