<?php

// require_once($CFG->dirroot.'/mod/widgetspace/lib/gadget.php');

class GadgetContainer {
  function __construct($id) {
    $this->id = $id;
  }
  
  function build() {
    // http://graaasp.epfl.ch/gadget/demo/pad3d_ext/gadget.xml
    // http://iamac71.epfl.ch/viewer.xml
  
    global $CFG, $DB;

    // get shindig script to build gadgets
    $output = '<script src="' . $this->get_shindig_url() . '/gadgets/js/shindig-container:rpc.js?c=1&debug=1&nocache=1" type="text/javascript" charset="utf-8"></script>';
    
    // load html file for gadget
    // javascript builder for gadget
    $output.= $this->get_include_contents($CFG->dirroot . "/mod/widgetspace/lib/container.html");

    // get all gadgets for widgetspace
    $this->gadgets = $this->getGadgets();
    $output.= $this->get_include_contents($CFG->dirroot . "/mod/widgetspace/lib/data.html");

    // build the view
    echo $output;
  }  
  
  function getColumnsNumber() {
    global $DB;
    $ws = $DB->get_record('widgetspace', array('id'=>$this->id), '*', MUST_EXIST);
    return ($ws->numbercolumn+1); // number of columns in db is 0,1,2; so we have to add 1!
  }
  
  function getGadgets() {
    global $DB;
    
    $gadgets = $DB->get_records('widgetspace_gadgets',array('widgetspaceid'=>$this->id));
    
    return $gadgets;
  }
  
  function getGadgetToken($gadget) {
    // TODO: token should be encoded properly for security reasons
    
    // var token = ""+gadget.owner_id+":"+gadget.viewer_id+":"+gadget.gadget_id+
    // ":default:"+escape("http://"+gadget_url)+":"+gadget.gadget_id+":1";
      global $USER, $COURSE, $CFG;
      
      $token = "";
      // owner of a gadget is the current widgetspace, we use prefix for spaces extension
      $token.= "s_".$this->id.":"; 
      // viewer of a gadget, we use prefix for spaces extension
      $token.= $USER->id.":"; 
      // application_id (current gadget)
      $token.= $gadget->id.":"; // module_id
      $token.= "default:";
      $token.= urlencode($gadget->url) . ":"; // escape("http://"+gadget_url)
      $token.= $gadget->id.":"; // module_id
      $token.= "1"; 
              
      return $token;
  }
  
  function getGadgetHeight($gadget) {
    return ($gadget->height) ? $gadget->height : 200;
  }
  
  function getGadgetName($gadget) {
    return ($gadget->name) ? $gadget->name : " ";
  }
  
  function get_shindig_url() {
    global $CFG;
    
    // return $CFG->block_shindig_url;
    // return "http://shindig.epfl.ch";
    // return "http://localhost:8080";
    return "http://iamac71.epfl.ch:8080";
  }
  
  // executes php statements and
  // returns file as a string instead of including it
  function get_include_contents($file) {
    if (!is_file($file) || !file_exists($file) || !is_readable($file)) return false;
    ob_start();
    include($file);
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }
}

?>