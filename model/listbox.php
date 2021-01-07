<?php namespace App\apirenjakl\model;


class listbox extends \Gov2lib\crudHandler
{
    function __construct () {
        global $doc, $config;
        $this->templateDir=__DIR__."/../view";
        $path=explode("\\",__CLASS__);
        $this->className=$path[sizeof($path)-1];
        $doc->body("className",$this->className);
        parent::__construct($config->domain->attr['dsn']);
        $this->tbl->table=$this->tbl->kementerian;
    }

    function getRef($id) {
        \DB::useDB('kl_bkn');
        $q = "SELECT id, parent_id, nama, children as children_count, kode, level_label  
                FROM {$this->tbl->table} WHERE parent_id=%i";
        $result = [];

        try {
            $result = \DB::query($q, (int)$id);
        } catch (\MeekroDBException $e) {
            $this->exceptionHandler($e->getMessage());
        }
        return $result;
    }

    function getRefSelect($id) {
        \DB::useDB('kl_bkn');
        $fields = "id, parent_id, nama, kode, level_label";
        $q = "SELECT {$fields}  
                FROM {$this->tbl->table} WHERE parent_id=%i";
        $result = [];

        try {
            $result = \DB::query($q, (int)$id);

            foreach ($result as $i => $row) {
                $result[$i]['children'] = \DB::query($q, $row['id']);
            }
        } catch (\MeekroDBException $e) {
            $this->exceptionHandler($e->getMessage());
        }
        return $result;
    }
}