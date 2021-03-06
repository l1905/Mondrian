<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Parser\PhpDumper;
use Trismegiste\Mondrian\Parser\PhpFile;
use Trismegiste\Mondrian\Tests\Fixtures\MockSplFileInfo;

/**
 * VirtualPhpDumper is a stub for virtual php dumper
 */
class VirtualPhpDumper extends PhpDumper implements \IteratorAggregate
{

    protected $storage;
    protected $testCase;
    protected $directory;
    protected $invocationMocker;

    /**
     * Init VFS
     */
    public function __construct(\PHPUnit_Framework_TestCase $testCase, $baseDir)
    {
        $this->testCase = $testCase;
        $this->directory = $baseDir;
    }

    public function init(array $fileSystem, \PHPUnit_Framework_MockObject_Matcher_Invocation $cptWrite)
    {
        $this->invocationMocker = new \PHPUnit_Framework_MockObject_InvocationMocker();
        $this->invocationMocker
                ->expects($cptWrite)
                ->method('write')
                ->withAnyParameters();

        $this->storage = array();
        foreach ($fileSystem as $name) {
            $absolute = $this->directory . $name;
            $this->storage[$name] = $this->getMockFile($absolute, file_get_contents($absolute));
        }
    }

    protected function getMockFile($absolute, $content)
    {
        return new MockSplFileInfo($absolute, $content);
    }

    /**
     * Stub for writes
     *
     * @param \Trismegiste\Mondrian\Parser\PhpFile $file
     */
    public function write(PhpFile $file)
    {
        $fch = $file->getRealPath();
        $stmts = iterator_to_array($file->getIterator());
        $prettyPrinter = new \PHPParser_PrettyPrinter_Default();
        $this->storage[basename($fch)] = $this->getMockFile(
                $fch, "<?php\n\n" . $prettyPrinter->prettyPrint($stmts)
        );

        $this->invocationMocker->invoke(
                new \PHPUnit_Framework_MockObject_Invocation_Object(
                'VirtualPhpDumper', 'write', array(basename($fch)), $this
                )
        );
    }

    /**
     * Compile VFS
     */
    public function compileStorage()
    {
        $generated = '';
        foreach ($this->storage as $fch) {
            $str = preg_replace('#^<\?php#', '', $fch->getContents());
            if (!empty($generated)) {
                $str = preg_replace('#^namespace.+$#m', '', $str);
            }
            $generated .= $str;
        }

        eval($generated);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->storage);
    }

    public function verifyCalls()
    {
        $this->invocationMocker->verify();
    }

}
