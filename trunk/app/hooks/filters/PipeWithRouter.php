<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class PipeWithRouter extends Pipe {

    var $dir_name;

    function PipeWithRouter($filter,$router) {
        $this->dir_name = trim($router->fetch_directory(),'/');
        parent::Pipe($filter,$router->fetch_class(),$router->fetch_method());
    }

    function _matches($path)
    {
        switch($path) {
            case '/':
            case '*': return parent::_matches($path);
            default:

                /**
                 * Possible cases:
                 * 1. ctlr/*
                 * 2. ctlr/method
                 * 3. ctlr/method1,method2
                 * 4. folder/*
                 * 5. folder/ctlr/*
                 * 6. folder/ctlr/method
                 * 7. folder/ctlr/mthod1,method2
                 */

                //Get the segments
                $parts = explode('/', $path);

                //Pad to always have at least 3 array pieces
                $parts = array_pad($parts,3,false);

                switch (true) {
                    case (
                        empty($this->dir_name) // Current controller isn't in a subfolder
                        && $parts[0] == $this->controller_name // Controller name matches
                        && $parts[1] == '*') : // Method is wildcard
                         return true; // Case 1

                    case (
                      empty($this->dir_name)  // Current controller isn't in a subfolder
                      && $parts[0] == $this->controller_name // Controller name matches
                      && $parts[1] == $this->method_name) : // Method Name matches
                          return true; // Case 2

                    case (
                      empty($this->dir_name)  // Current controller isn't in a subfolder
                      && $parts[0] == $this->controller_name // Controller name matches
                      && strpos($parts[1],',')!==false) : // Method segment has a comma
                          $subparts = explode(',',$parts[1]);
                          if(in_array($this->method_name,$subparts))
                              return true; // Case 3
                          break;

                    case (
                      $parts[0] == $this->dir_name // Folder name matches
                      && $parts[1] == '*') : // Controller name is a wildcard
                          return true; // Case 4

                    case (
                        $parts[0] == $this->dir_name // Folder Name matches
                        && $parts[1] == $this->controller_name // Controller name matches
                        && $parts[2] == '*') : // Method is wildcard
                            return true; // Case 5

                    case (
                        $parts[0] == $this->dir_name // Folder Name matches
                        && $parts[1] == $this->controller_name // Controller name matches
                        && $parts[2] == $this->method_name) : // Method name matches
                            return true; // Case 6

                    case (
                        $parts[0] == $this->dir_name // Folder Name matches
                        && $parts[1] == $this->controller_name // Controller name matches
                        && strpos($parts[2],',') !== false) : // Method segment has a comma
                            $subparts = explode(',',$parts[2]);
                            if(in_array($this->method_name,$subparts))
                                return true; // Case 7
                            break;

                }

        }
        return false;
    }
}

?>