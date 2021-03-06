<?php

class Mail_Message extends Mail_Part implements Mail_Message_Interface
{
    /**
     * flags for this message
     * @var array
     */
    protected $_flags = array();

    /**
     * Public constructor
     *
     * In addition to the parameters of Mail_Part::__construct() this constructor supports:
     * - file  filename or file handle of a file with raw message content
     * - flags array with flags for message, keys are ignored, use constants defined in Mail_Storage
     *
     * @param  string $rawMessage  full message with or without headers
     * @throws Mail_Exception
     */
    public function __construct(array $params)
    {
        if (isset($params['file'])) {
            if (!is_resource($params['file'])) {
                $params['raw'] = @file_get_contents($params['file']);
                if ($params['raw'] === false) {
                    /**
                     * @see Mail_Exception
                     */
                    throw new Mail_Exception('could not open file');
                }
            } else {
                $params['raw'] = stream_get_contents($params['file']);
            }
        }

        if (!empty($params['flags'])) {
            // set key and value to the same value for easy lookup
            $this->_flags = array_combine($params['flags'], $params['flags']);
        }

        parent::__construct($params);
    }

    /**
     * return toplines as found after headers
     *
     * @return string toplines
     */
    public function getTopLines()
    {
        return $this->_topLines;
    }

    /**
     * check if flag is set
     *
     * @param mixed $flag a flag name, use constants defined in Mail_Storage
     * @return bool true if set, otherwise false
     */
    public function hasFlag($flag)
    {
        return isset($this->_flags[$flag]);
    }

    /**
     * get all set flags
     *
     * @return array array with flags, key and value are the same for easy lookup
     */
    public function getFlags()
    {
        return $this->_flags;
    }
}
