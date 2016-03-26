<?php

namespace Core\Views;

/**
*
*/
class View
{

    private $templatePath = ROOT_DIR.'src/Views/';
    public $defaultLayot = 'layout';

    private function fetchPartial($template, $params = [])
    {
        extract($params);
        ob_start();
        include $this->templatePath.$template.'.php';
        return ob_get_clean();
    }

    private function renderPartial($template, $params = [])
    {
        echo $this->fetchPartial($template, $params);
    }

    private function fetch($template, $params = [])
    {
        $content = $this->fetchPartial($template, $params);
        return $this->fetchPartial($this->defaultLayot, ['content' => $content]);
    }

    public function render($template, $params = [])
    {
        echo $this->fetch($template, $params);
    }
}
