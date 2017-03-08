<?php

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
/**
 * Class One
 */
class One
{
    public $arg1;
    public $arg2;

    /**
     * One constructor.
     * @param null $arg1
     * @param null $arg2
     */
    public function __construct($arg1 = null, $arg2 = null)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
/**
 * Class Swift_DependencyContainerTest
 */
class Swift_DependencyContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Swift_DependencyContainer
     */
    private $_container;

    protected function setUp()
    {
        $this->_container = new Swift_DependencyContainer();
    }

    public function testRegisterAndLookupValue()
    {
        $this->_container->register('foo')->asValue('bar');
        self::assertSame('bar', $this->_container->lookup('foo'));
    }

    public function testHasReturnsTrueForRegisteredValue()
    {
        $this->_container->register('foo')->asValue('bar');
        self::assertTrue($this->_container->has('foo'));
    }

    public function testHasReturnsFalseForUnregisteredValue()
    {
        self::assertFalse($this->_container->has('foo'));
    }

    public function testRegisterAndLookupNewInstance()
    {
        $this->_container->register('one')->asNewInstanceOf('One');
        self::assertInstanceOf('One', $this->_container->lookup('one'));
    }

    public function testHasReturnsTrueForRegisteredInstance()
    {
        $this->_container->register('one')->asNewInstanceOf('One');
        self::assertTrue($this->_container->has('one'));
    }

    public function testNewInstanceIsAlwaysNew()
    {
        $this->_container->register('one')->asNewInstanceOf('One');
        $a = $this->_container->lookup('one');
        $b = $this->_container->lookup('one');
        self::assertEquals($a, $b); // but not the same
    }

    public function testRegisterAndLookupSharedInstance()
    {
        $this->_container->register('one')->asSharedInstanceOf('One');
        self::assertInstanceOf('One', $this->_container->lookup('one'));
    }

    public function testHasReturnsTrueForSharedInstance()
    {
        $this->_container->register('one')->asSharedInstanceOf('One');
        self::assertTrue($this->_container->has('one'));
    }

    public function testMultipleSharedInstancesAreSameInstance()
    {
        $this->_container->register('one')->asSharedInstanceOf('One');
        $a = $this->_container->lookup('one');
        $b = $this->_container->lookup('one');
        self::assertSame($a, $b);
    }

    public function testNewInstanceWithDependencies()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('one')->asNewInstanceOf('One')
            ->withDependencies(array('foo'));
        $obj = $this->_container->lookup('one');
        self::assertSame('FOO', $obj->arg1);
    }

    public function testNewInstanceWithMultipleDependencies()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('bar')->asValue(42);
        $this->_container->register('one')->asNewInstanceOf('One')
            ->withDependencies(array('foo', 'bar'));
        $obj = $this->_container->lookup('one');
        self::assertSame('FOO', $obj->arg1);
        self::assertSame(42, $obj->arg2);
    }

    public function testNewInstanceWithInjectedObjects()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('one')->asNewInstanceOf('One');
        $this->_container->register('two')->asNewInstanceOf('One')
            ->withDependencies(array('one', 'foo'));
        $obj = $this->_container->lookup('two');
        self::assertEquals($this->_container->lookup('one'), $obj->arg1);
        self::assertSame('FOO', $obj->arg2);
    }

    public function testNewInstanceWithAddConstructorValue()
    {
        $this->_container->register('one')->asNewInstanceOf('One')
            ->addConstructorValue('x')
            ->addConstructorValue(99);
        $obj = $this->_container->lookup('one');
        self::assertSame('x', $obj->arg1);
        self::assertSame(99, $obj->arg2);
    }

    public function testNewInstanceWithAddConstructorLookup()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('bar')->asValue(42);
        $this->_container->register('one')->asNewInstanceOf('One')
            ->addConstructorLookup('foo')
            ->addConstructorLookup('bar');

        $obj = $this->_container->lookup('one');
        self::assertSame('FOO', $obj->arg1);
        self::assertSame(42, $obj->arg2);
    }

    public function testResolvedDependenciesCanBeLookedUp()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('one')->asNewInstanceOf('One');
        $this->_container->register('two')->asNewInstanceOf('One')->withDependencies(array('one', 'foo'));
        $deps = $this->_container->createDependenciesFor('two');
        self::assertEquals(array($this->_container->lookup('one'), 'FOO'), $deps);
    }

    public function testArrayOfDependenciesCanBeSpecified()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('one')->asNewInstanceOf('One');
        $this->_container->register('two')->asNewInstanceOf('One')
            ->withDependencies(array(array('one', 'foo'), 'foo'));

        $obj = $this->_container->lookup('two');
        self::assertEquals(array($this->_container->lookup('one'), 'FOO'), $obj->arg1);
        self::assertSame('FOO', $obj->arg2);
    }

    public function testAliasCanBeSet()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('bar')->asAliasOf('foo');

        self::assertSame('FOO', $this->_container->lookup('bar'));
    }

    public function testAliasOfAliasCanBeSet()
    {
        $this->_container->register('foo')->asValue('FOO');
        $this->_container->register('bar')->asAliasOf('foo');
        $this->_container->register('zip')->asAliasOf('bar');
        $this->_container->register('button')->asAliasOf('zip');

        self::assertSame('FOO', $this->_container->lookup('button'));
    }
}
