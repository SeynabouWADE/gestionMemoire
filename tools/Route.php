<?php

class Route { //TODO à supprimé

    public function __construct(){}
    // url, controller, routeName
    public static $views = array();
    public static $posts = array();
    public static $gets  = array();
    public static $names = array();

    public static function post($url, $ctrl, $name=""){
        self::$posts[$url] = $ctrl;
        if($name != "")
            self::$names[$name] = $url;
    }
    public static function get($url, $ctrl, $name=""){
        self::$gets[$url] = $ctrl;
        if($name != "")
        self::$names[$name] = $url;
    }

    public static function view($url, $view, $name=""){
        self::$views[$url] = $view;
        if($name != "")
            self::$names[$name] = $url;
    }

    public static function route($name){
        echo self::racine().self::$posts[self::$names[$name]];        
    }
    
    public static function routeName($url, $name){
        self::$names[$name] = $url;       
    }
    public static function racine(){
        return str_replace("index.php", "", $_SERVER["PHP_SELF"]);
    }
}
