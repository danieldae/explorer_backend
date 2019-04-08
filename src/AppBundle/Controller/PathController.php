<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;


class PathController extends FOSRestController
{

    public function getPathAction(Request $request)
    {
        $path = $request->get('path');
        $filesystem = new Filesystem();
        $exists = $filesystem->exists($path);
        $fileType = "dir";

        if ($exists) {
            if (is_dir($path)) {
                $fileNameList = scandir($path);
                foreach ($fileNameList as $fileName) {
                    if($fileName != "." && $fileName != "..") {
                        $filePath = $path . "\\" . $fileName;
                        $data[] = array(
                            "filePath" => $filePath,
                            "fileType" => filetype($filePath),
                            "fileName" => $fileName,
                            "collapsed" => true,
                            "children" => [],
                            "mimeType" => mime_content_type($filePath)
                        );
                    }
                }
            }
            else if(is_file($path)) {
                $file = fopen($path,"r");
                $source = "";
                while(!feof($file))
                {
                    $source .= fgets($file). "\n";
                }
                fclose($file);
                $fileType = fileType($path);
                $data = $source;
            }
        }
        else {
            $fileType = "invalid";
            $data = false;
        }

        $view = $this->view(array("fileType"=> $fileType, "data" =>$data), 200)
            ->setHeader("Access-Control-Allow-Credentials", true)
            ->setHeader("Access-Control-Allow-Headers", 'x-requested-with, access-control-allow-origin')
            ->setHeader("Access-Control-Allow-Methods", 'OPTIONS, GET, HEAD, POST')
            ->setHeader("Access-Control-Allow-Origin", "*")
        ;

        return $this->handleView($view);
    }

    public function optionsPathAction() {
        $view = $this->view(null, 200)
            ->setHeader("Access-Control-Allow-Credentials", true)
            ->setHeader("Access-Control-Allow-Headers", 'x-requested-with, access-control-allow-origin')
            ->setHeader("Access-Control-Allow-Methods", 'OPTIONS, GET, HEAD, POST')
            ->setHeader("Access-Control-Allow-Origin", "*")
        ;
        return $this->handleView($view);
    }
}

