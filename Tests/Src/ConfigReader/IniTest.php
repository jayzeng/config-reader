<?php
/**
 * Test Ini config class
 * @category testing
 * @package Test_Config
 * @author Jay Zeng (jayzeng@jay-zeng.com)
 */
namespace Test\ConfigReader;

use \ConfigReader\Ini;

/**
 * @todo
 *      - Missing capablity to add empty file on the fly. Once we have that,
 *        we need to clean up direct manipulation towards a physical file
 * @category testing
 * @package Test_Config
 */
class IniTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up a list of valid & invalid files for testing purpose
     * @todo
     *    - Categorize files to valid & invalid
     *    - Add more valid and invalid files
     */
    private function setupValidTestFiles() {
        return array(
                __DIR__.DIRECTORY_SEPARATOR.'_files/config.ini',
                );
    }

    /**
     * Set up invalid config files
     */
    private function setupInvalidTestFiles() {
        return __DIR__.DIRECTORY_SEPARATOR.'_files/empty.ini';
    }

    public function tearDown() {
        $file = $this->setupInvalidTestFiles();
        chmod($file, 0744);
    }

    /**
     * testUnreable
     *
     * @expectedException \ConfigReader\Exception\FilePermissionException
     * @return void
     */
    public function testUnreable() {
        $file = $this->setupInvalidTestFiles();
        chmod($file, 0000);

        Ini::factory($file);
    }

    /**
     * Provides an invalid file, expect the object to throw InvalidArgumentException
     * @expectedException \ConfigReader\Exception\InvalidArgumentException
     */
    public function testInvalidFile() {
        $invalidFiles = array('thisFileDoesNotExist','config.ini.dist');

        foreach($invalidFiles as $invalidFile) {
            $ini = new Ini($invalidFile);
        }
    }

    /**
     * Test an existing file with invalid file extension
     */
    public function testNonIniFile() {
        $file = __DIR__.DIRECTORY_SEPARATOR.'_files/config.ini.dist';
        $this->setExpectedException('\ConfigReader\Exception\InvalidArgumentException');
        $ini = new Ini($file);
    }

    /**
     * Exercise Ini class to ensure it returns an instance of Ini
     */
    public function testValidFileGetData() {
        $files = $this->setupValidTestFiles();
        foreach($files as $file) {
            $ini = new Ini($file);
            $this->assertTrue($ini instanceOf Ini);

            // Exercise factory method
            $this->assertTrue(Ini::factory($file) instanceOf Ini);
        }
    }

    public function testFactory() {

        $files = $this->setupValidTestFiles();
        foreach($files as $file) {
            $this->assertEquals('ConfigReader\Ini', get_class(Ini::factory($file)));
        }
    }

    public function testConstructorWithLabel() {
        $files = $this->setupValidTestFiles();
        foreach($files as $file) {
            $ini = Ini::factory($file, 'production');

            // Test with getKey
            $this->assertEquals('localhost', $ini->get('host'));

            // Test magic method
            $this->assertEquals('localhost', $ini->host);

            $this->assertEquals('production', $ini->getLabel());
        }
    }

    public function testSetAndGetLabel() {
        $files = $this->setupValidTestFiles();
        foreach($files as $file) {
            $ini = Ini::factory($file)->setLabel('production');

            // Test with getKey
            $this->assertEquals('localhost', $ini->get('host'));

            // Test magic method
            $this->assertEquals('localhost', $ini->host);

            $this->assertEquals('production', $ini->getLabel());

            // Retrieve non-existent value
            $this->assertFalse($ini->nonexistentvalue);

            // Switch over to another section
            $ini->setLabel('debug');
            $this->assertNotEquals('production', $ini->getLabel());
            $this->assertEquals('debug', $ini->getLabel());

            $this->assertEquals(1, $ini->isEnabled);
            $this->assertEquals(1, $ini->get('isEnabled'));

            // Retrieve non-existent value
            $this->assertFalse($ini->nonexistentvalue);
        }
    }

    /**
     * test toArray()
     */
    public function testToArray() {
        $files = $this->setupValidTestFiles();
        foreach($files as $file) {
            $ini = Ini::factory($file)->setLabel('production');

            $value = $ini->toArray();
            $this->assertInternalType('array', $value);
            $this->assertCount(3, $value);
            $this->assertContains('password', $value);
        }
    }

    /**
     * test getIterator() and cover()
     */
    public function testGetIteratorAndCover() {
        $files = $this->setupValidTestFiles();
        foreach($files as $file) {
            $ini = Ini::factory($file)->setLabel('production');

            $tempArray = array();

            foreach($ini as $k => $v) {
                $this->assertNotNull($k);
                $this->assertNotNull($v);
                $tempArray[$k] = $v;
            }
            $this->assertEquals('password', $ini->password);
            $this->assertCount(3, $ini);
            $this->assertCount(3, $tempArray);

            // Destroy tempArray
            unset($tempArray);

            // Switch over to debug section to verify we getting proper count
            $ini->setLabel('debug');

            foreach($ini as $k => $v) {
                $this->assertNotNull($k);
                $this->assertNotNull($v);
                $tempArray[$k] = $v;
            }
            $this->assertEquals('mypassword', $ini->password);

            // Parse_ini_file() uses semi-colon as delimeter and only returns the 1st matched component
            $this->assertNotEquals('192.168.1.2;10.1.2.2', $ini->allowedIp);
            $this->assertEquals('192.168.1.2', $ini->allowedIp);
            $this->assertCount(4, $ini);
            $this->assertCount(4, $tempArray);
        }
    }

    /**
     * test getLabels()
     */
    public function testGetLabels() {
        $files = $this->setupValidTestFiles();
        $labels = Ini::factory($files[0])->getLabels();
        $this->assertSame(array('production', 'debug'), $labels);
    }
}
?>
