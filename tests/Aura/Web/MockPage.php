<?php
namespace Aura\Web;
class MockPage extends AbstractPage
{
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
}
