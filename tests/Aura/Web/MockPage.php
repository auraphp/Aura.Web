<?php
namespace Aura\Web;

use Aura\Web\Controller\AbstractPage;

class MockPage extends AbstractPage
{
    public $hooks = [];
    
    public function actionIndex()
    {
        $this->data->action_method = 'actionIndex';
    }
    
    public function actionParams($foo, $bar, $baz = 'dib')
    {
        $this->data->foo = $foo;
        $this->data->bar = $bar;
        $this->data->baz = $baz;
    }
    
    public function preExec()
    {
        parent::preExec();
        $this->hooks[__FUNCTION__] = true;
    }
    
    public function preAction()
    {
        parent::preAction();
        $this->hooks[__FUNCTION__] = true;
    }
    
    public function postAction()
    {
        parent::postAction();
        $this->hooks[__FUNCTION__] = true;
    }
    
    public function preRender()
    {
        parent::preRender();
        $this->hooks[__FUNCTION__] = true;
    }
    
    public function postRender()
    {
        parent::postRender();
        $this->hooks[__FUNCTION__] = true;
    }
    
    public function postExec()
    {
        parent::postExec();
        $this->hooks[__FUNCTION__] = true;
    }
}
