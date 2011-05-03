<?php
namespace aura\web;
class MockPage extends Page
{
    private $_pre_action = false;
    
    private $_pre_exec = false;
    
    private $_post_action = false;
    
    private $_post_exec = false;
    
    public function __get($key)
    {
        return $this->$key;
    }
    
    public function preExec()
    {
        parent::preExec();
        $this->_pre_exec = true;
    }
    
    public function preAction()
    {
        parent::preAction();
        $this->_pre_action = true;
    }
    
    public function postAction()
    {
        parent::postAction();
        $this->_post_action = true;
    }
    
    public function postExec()
    {
        parent::postExec();
        $this->_post_exec = true;
    }
    
    public function actionIndex()
    {
        $this->response->view_data['action_method'] = 'actionIndex';
    }
    
    public function actionParams($foo, $bar, $baz = 'dib')
    {
        $this->response->view_data->foo = $foo;
        $this->response->view_data->bar = $bar;
        $this->response->view_data->baz = $baz;
    }
}
