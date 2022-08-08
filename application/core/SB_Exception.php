<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SB_Exception extends Exception {

    public function __construct($options = 
        [
             'message'      => ''
            ,'title'        => ''
            ,'typeMsg'      => ''
            ,'class'        => ''
        ]
        ,$code=0
        ,Exception $previous = null
    ) {
        //SET DATA TO ALERT
        $this->title    = isset($options['title'])      ? $options['title']       : '';
        $this->typeMsg  = isset($options['typeMsg'])    ? $options['typeMsg']     : '';
        $this->class    = isset($options['class'])      ? $options['class']       : '';
        $saveTrace      = isset($options['saveTrace'])  ? $options['saveTrace']   : TRUE;
        $message        = isset($options['message']) && $options['message'] ? $options['message'] : lang('general_throw_exception');
        $log_message    = isset($options['log_message'])? $options['log_message'] : '';
        
        //SET DATA EXCEPTION
        parent::__construct($message, $code, $previous);
        !$saveTrace   OR log_message('error', 'SB_Exception Trace - ' . $this->getTraceAsString());
        !$log_message OR log_message('error', 'SB_Exception Message - ' . $log_message);

        log_message('info', 'SB_Exception Class Initialized');
    }

    // representación de cadena personalizada del objeto
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    /**
     * Retornamos ¿Qué tipo de mensaje es?
     * Esto servira para mostrarlo el un alerta, ya sea sweet|toats|etc...
     * @return String $typeMsg
     */
    public function getTypeMessage() {
        return $this->typeMsg ? $this->typeMsg: 'error';
    }

    /**
     * Obtenemos el titulo del mensaje para la alerta
     * @return String $title
     */
    public function getTitle() {
        return $this->title ? $this->title : lang('general_error');
    }

    public function getClass() {
        return $this->class;
    }
}

/* End of file SB_Exception.php */
/* Location: ./application/core/SB_Exception.php */