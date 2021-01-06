<?php namespace App\apirenjakl;


class listbox
{
    function __construct ()
    {
        global $self;
        $self->ses->authenticate('public');
        $self->takeAll("components");
        $self->scrollInterval=100;
    }

    function index($vars)
    {
        global $self, $doc;
        $self->gov2nav->setDefaultNavCustom();
        $doc->body("pageTitle", 'Listbox Kementerian | BKN');
        $GLOBALS['vueData']['selected'] = null;
        $self->content();
    }

    function getRef($vars) {
        global $self, $doc;
        $data = $self->getRef($vars['id']);
        return $doc->responseGet($data);
    }

    function getRefSelect($vars) {
        global $self, $doc;
        $data = $self->getRefSelect($vars['id']);
        return $doc->responseGet($data);
    }
}