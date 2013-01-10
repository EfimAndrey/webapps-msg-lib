<?php

class Mail_Storage_Folder implements RecursiveIterator
{
    /**
     * subfolders of folder array(localName => Mail_Storage_Folder folder)
     * @var array
     */
    protected $_folders;

    /**
     * local name (name of folder in parent folder)
     * @var string
     */
    protected $_localName;

    /**
     * global name (absolute name of folder)
     * @var string
     */
    protected $_globalName;

    /**
     * folder is selectable if folder is able to hold messages, else it's just a parent folder
     * @var bool
     */
    protected $_selectable = true;

    /**
     * create a new mail folder instance
     *
     * @param string $localName  name of folder in current subdirectory
     * @param string $globalName absolute name of folder
     * @param bool   $selectable if true folder holds messages, if false it's just a parent for subfolders
     * @param array  $folders    init with given instances of Mail_Storage_Folder as subfolders
     */
    public function __construct($localName, $globalName = '', $selectable = true, array $folders = array())
    {
        $this->_localName  = $localName;
        $this->_globalName = $globalName ? $globalName : $localName;
        $this->_selectable = $selectable;
        $this->_folders    = $folders;
    }

    /**
     * implements RecursiveIterator::hasChildren()
     *
     * @return bool current element has children
     */
    public function hasChildren()
    {
        $current = $this->current();
        return $current && $current instanceof Mail_Storage_Folder && !$current->isLeaf();
    }

    /**
     * implements RecursiveIterator::getChildren()
     *
     * @return Mail_Storage_Folder same as self::current()
     */
    public function getChildren()
    {
        return $this->current();
    }

    /**
     * implements Iterator::valid()
     *
     * @return bool check if there's a current element
     */
    public function valid()
    {
        return key($this->_folders) !== null;
    }

    /**
     * implements Iterator::next()
     *
     * @return null
     */
    public function next()
    {
        next($this->_folders);
    }

    /**
     * implements Iterator::key()
     *
     * @return string key/local name of current element
     */
    public function key()
    {
        return key($this->_folders);
    }

    /**
     * implements Iterator::current()
     *
     * @return Mail_Storage_Folder current folder
     */
    public function current()
    {
        return current($this->_folders);
    }

    /**
     * implements Iterator::rewind()
     *
     * @return null
     */
    public function rewind()
    {
        reset($this->_folders);
    }

    /**
     * get subfolder named $name
     *
     * @param  string $name wanted subfolder
     * @return Mail_Storage_Folder folder named $folder
     * @throws Mail_Storage_Exception
     */
    public function __get($name)
    {
        if (!isset($this->_folders[$name])) {
            /**
             * @see Mail_Storage_Exception
             */
            throw new Mail_Storage_Exception("no subfolder named $name");
        }

        return $this->_folders[$name];
    }

    /**
     * add or replace subfolder named $name
     *
     * @param string $name local name of subfolder
     * @param Mail_Storage_Folder $folder instance for new subfolder
     * @return null
     */
    public function __set($name, Mail_Storage_Folder $folder)
    {
        $this->_folders[$name] = $folder;
    }

    /**
     * remove subfolder named $name
     *
     * @param string $name local name of subfolder
     * @return null
     */
    public function __unset($name)
    {
        unset($this->_folders[$name]);
    }

    /**
     * magic method for easy output of global name
     *
     * @return string global name of folder
     */
    public function __toString()
    {
        return (string)$this->getGlobalName();
    }

    /**
     * get local name
     *
     * @return string local name
     */
    public function getLocalName()
    {
        return $this->_localName;
    }

    /**
     * get global name
     *
     * @return string global name
     */
    public function getGlobalName()
    {
        return $this->_globalName;
    }

    /**
     * is this folder selectable?
     *
     * @return bool selectable
     */
    public function isSelectable()
    {
        return $this->_selectable;
    }

    /**
     * check if folder has no subfolder
     *
     * @return bool true if no subfolders
     */
    public function isLeaf()
    {
        return empty($this->_folders);
    }
}