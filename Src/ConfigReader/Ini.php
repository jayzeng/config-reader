<?php
/**
 * Ini config parser
 * @example
 * <pre>
 * <code>
 * <?php
 * $ini = Ini::factory('config.ini')->setLabel('database');
 * echo $ini->host;
 *
 * // Alternatively
 * echo $ini->get('host');
 * $ini->setLabel('debug');
 * echo $ini->isEnabled;
 * ?>
 * </code>
 * </pre>
 * @author Jay Zeng (jayzeng@jay-zeng.com)
 * @category ConfigReader
 * @package ConfigReader
 * @subpackage Exception
 * @since 02/02/2013
 * @version 0.1
 */
namespace ConfigReader;

/**
 * Ini parser that provides an OO style getters to
 * read Ini values
 * @category Framework
 * @package Framework_Config
 */
class Ini implements \IteratorAggregate, \Countable
{
    /**
     * @var Array
     */
    protected $_data = array();

    /**
     * @var String|null Ini evaluated section
     */
    protected $_loadedSection;

    /**
     * @param String      $file
     * @param String|null $label
     */
    public function __construct($file, $label = null) {
        $this->_data = $this->loadFile($file);
        if(isset($label)) {
            $this->_loadedSection = $label;
        }
    }

    /**
     * Factory
     *
     * @param string      $file
     * @param string|null $label
     * @return self
     */
    public static function factory($file, $label = null) {
        return new self($file,$label);
    }

    /**
     * return all labels
     *
     * @return Array
     */
    public function getLabels() {
        return array_keys($this->_data);
    }

    /**
     * Evaluate a valid ini file
     *
     * @param String $file
     * @return array multi-dimensional array
     * @throws Exception\InvalidArgumentException    Invalid input, not a file
     * @throws Exception\FilePermissionException     Insufficient permissions to write a file
     * @throws Exception\InvalidArgumentException    Input file is not an ini
     */
    private function loadFile($file) {
        if(!is_file($file)) {
            throw new Exception\InvalidArgumentException(
                    sprintf('%s is not a valid file', $file)
                    );
        }

        if(!is_readable($file)) {
            throw new Exception\FilePermissionException(
                    sprintf('Insufficient permission to read file: %s', $file)
                    );
        }

        $fileInfo = new \SplFileInfo($file);
        if('ini' !== $fileInfo->getExtension()) {
            throw new Exception\InvalidArgumentException(sprintf('%s is not an ini file', $file));
        }

        return parse_ini_file($file, TRUE);
    }

    /**
     * Retrieve key associated with key within selected loaded section
     *
     * @param String $key
     * @return mixed
     */
    public function get($key) {
        $label = $this->getLabel();

        if( isset($this->_data[$label]) && array_key_exists($key,$this->_data[$label]) ) {
            return $this->_data[$label][$key];
        }

        return false;
    }

    /**
     * Override magic method to enable $obj->key
     *
     * @param String $key
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Switched over to another section from ini stored in memory
     *
     * @param String $label
     * @return self
     */
    public function setLabel($label) {
        $this->_loadedSection = $label;
        return $this;
    }

    /**
     * @return String|null Section being parsed
     */
    public function getLabel() {
        return $this->_loadedSection;
    }

    /**
     * @return Array ini data in array
     */
    public function toArray() {
        return $this->_data[$this->_loadedSection];
    }

    /**
     * Enable user to directly loop through the object
     * @example
     * <pre>
     * db.ini
     * [master]
     * host = localhost
     * username = user
     * password = mypassword
     * </pre>
     *
     * <code>
     * <?php
     * $ini = Ini::factory('./db.ini')->setLabel('master')->getIterator();
     * foreach($ini as $key => $val) {
     *    printf('key: %s => value: %s', $key, $val);
     *    echo PHP_EOL;
     * }
     * ?>
     * </code>
     *
     * <pre>
     * Returns:
     * key: host => value: localhost
     * key: username => value: user
     * key: password => value: mypassword
     * </pre>
     *
     * @see \IteratorAggregate::getIterator()
     * @return \ArrayObject
     */
    public function getIterator() {
        return new \ArrayObject($this->_data[$this->_loadedSection]);
    }

    /**
     * Enable config countable
     *
     * @see \Countable::count()
     */
    public function count() {
        return count($this->_data[$this->_loadedSection]);
    }
}
?>
