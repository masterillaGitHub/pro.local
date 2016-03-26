<?php
namespace Core\Utils;

class FieldException extends \Exception
{
    protected $_field;
    public function __construct($message = "", $code = 0, \Exception $previous = null, $field = null)
    {
        $this->_field = $field;
        parent::__construct($message, $code, $previous);
    }
    public function getField()
    {
        return $this->_field;
    }
}
