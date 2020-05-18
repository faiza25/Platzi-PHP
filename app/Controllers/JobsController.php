<?php

namespace App\Controllers;

use App\Models\Job;
use Respect\Validation\Validator as v;


class JobsController extends BaseController{
    public function getAddJobAction($request) {
          $responseMessage=null;


        if ($request->getMethod()== 'POST') {
          $postdata = $request->getParsedBody();
          $jobValidator = v::key('title',v::stringType()->notEmpty())
                          ->key('description',v::stringType()->notEmpty());

                try {
                  $jobValidator->assert($postdata);
                  $postdata = $request->getParsedBody();

                  $files = $request->getUploadedFiles();
                                 $logo = $files['logo'];

                   if($logo->getError() == UPLOAD_ERR_OK) {
                       $fileName = $logo->getClientFilename();
                       $logo->moveTo("uploads/$fileName");
                   }


                  $job = new Job();
                  $job->title = $postdata['title'];
                  $job->description = $postdata['description'];
                  $job->visible=true;
                  $job->save();

                  $responseMessage = 'Saved';
                } catch (\Exception $e) {

                  $responseMessage =$e->getMessage();

                }







        }

        return $this->renderHTML("addJob.twig",[
          'responseMessage' =>$responseMessage

        ]);
    }
}
 ?>
