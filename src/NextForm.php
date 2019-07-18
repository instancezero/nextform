<?php

namespace Abivia;

use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Form\Form;
use Abivia\NextForm\Contracts\Access as Access;
use Abivia\NextForm\Contracts\DataStore as DataStore;
use Abivia\NextForm\Contracts\Renderer as Renderer;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 *
 */
class NextForm {

    public const SEGMENT_DELIM = '/';

    protected $access;
    protected $form;
    protected $render;
    protected $schema;
    protected $translate;

    public function __construct(Renderer $render = null, Translator $translate = null, Access $access = null) {
        $this -> render = $render;
        $this -> translate = $translate;
        if ($access === null) {
            $this -> access = new \Abivia\NextForm\Access\BasicAccess;
        } else {
            $this -> access = $access;
        }
    }

    public function generate($route) {
        // OKAY, HERE'S WHAT NEEDS TO HAPPEN:
        // fields in the form get connected to data definitions in the store
        // put stuff into $this -> form, $this -> schema
        // the store and a data provider need to be connected in some way
        // if there's existing data, the store needs to be populated with it
        // elements in the form get rendered, after being filtered/transformed to meet access rules
        // return script (JS) and markup (HTML)
    }

    public function setForm($form) {
        $this -> form = $form;
    }

}
