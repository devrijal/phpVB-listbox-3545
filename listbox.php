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
        global $self, $doc, $config;
        $self->gov2nav->setDefaultNavCustom();
        $doc->body("pageTitle", 'Listbox Kementerian | BKN');
        $GLOBALS['vueData']['selected'] = [338, 2723, 2735];
        $GLOBALS['vueData']['selectedSelect'] = 2723;
        $GLOBALS['vueData']['kl_id'] = (int)$config->domain->attr['id'];
        $self->content();
    }

    function getRef($vars) {
        global $self, $doc;
        $data = $self->getRef($vars['id']);
        return $doc->responseGet($data);
    }

    function getSelectedRef($vars) {
        global $self, $doc;
        $data = $vars['data'];
        $data = $self->getSelectedRef($data);
        return $doc->responseGet($data);
    }

    function getRefSelect($vars) {
        global $self, $doc;
        $data = $self->getRefSelect($vars['id']);
        return $doc->responseGet($data);
    }
}