<?php
namespace Core\Utils;

class DublicateException extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null, $field = null)
    {
        $this->_field = $field;
        parent::__construct($message, $code, $previous);
    }
}
