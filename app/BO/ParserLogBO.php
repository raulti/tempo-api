<?php

namespace App\BO;

use File;

/**
 * Resonsável por implementar as regras de negócio de 'Parse do Log'
 */
class ParserLogBO
{
   
    public function parse($file)
    {
        
        $filename = storage_path($file);
        $content = File::get($filename);
        foreach($content as $line) {
            echo $line;
        }
        
        echo "<pre>";
        print_r($file);die;
        echo "</pre>";
        
       
    }
}
