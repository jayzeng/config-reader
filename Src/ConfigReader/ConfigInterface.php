<?php
/**
 * @author Jay Zeng (jayzeng@jay-zeng.com)
 * @category ConfigReader
 * @package ConfigReader
 * @subpackage Exception
 * @since 02/02/2013
 * @version 0.1
 */
namespace ConfigReader;

/**
 * @category ConfigReader
 * @package ConfigReader
 */
interface ConfigInterface
{
    /**
     * Return internal data structure in array
     *
     * @return Array
     */
    public function toArray();
}
