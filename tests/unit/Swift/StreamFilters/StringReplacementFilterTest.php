<?php

class Swift_StreamFilters_StringReplacementFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicReplacementsAreMade()
    {
        $filter = $this->_createFilter('foo', 'bar');
        $this->assertSame('XbarYbarZ', $filter->filter('XfooYfooZ'));
    }

    public function testShouldBufferReturnsTrueIfPartialMatchAtEndOfBuffer()
    {
        $filter = $this->_createFilter('foo', 'bar');
        $this->assertTrue($filter->shouldBuffer('XfooYf'),
            '%s: Filter should buffer since "foo" is the needle and the ending '.
            '"f" could be from "foo"'
        );
    }

    public function testFilterCanMakeMultipleReplacements()
    {
        $filter = $this->_createFilter(array('a', 'b'), 'foo');
        $this->assertSame('XfooYfooZ', $filter->filter('XaYbZ'));
    }

    public function testMultipleReplacementsCanBeDifferent()
    {
        $filter = $this->_createFilter(array('a', 'b'), array('foo', 'zip'));
        $this->assertSame('XfooYzipZ', $filter->filter('XaYbZ'));
    }

    public function testShouldBufferReturnsFalseOnEmptySearch()
    {
        $filter = $this->_createFilter(array(), "\n");
        $this->assertFalse($filter->shouldBuffer("foo\r\nbar"));
    }

    public function testShouldBufferReturnsFalseOnEmptySearchAndReplacer()
    {
        $filter = $this->_createFilter(array(), '');
        $this->assertFalse($filter->shouldBuffer("foo\r\nbar"));

        $filter = $this->_createFilter(array(), '');
        $this->assertFalse($filter->shouldBuffer(''));
    }

    public function testShouldBufferReturnsFalseOnEmptyReplacer()
    {
        $filter = $this->_createFilter(array('foo'), '');
        $this->assertFalse($filter->shouldBuffer("foo\r\nbar"));

        $filter = $this->_createFilter(array('foo'), '');
        $this->assertFalse($filter->shouldBuffer(''));
    }

    public function testShouldBufferReturnsFalseIfPartialMatchNotAtEndOfString()
    {
        $filter = $this->_createFilter("\r\n", "\n");
        $this->assertFalse($filter->shouldBuffer("foo\r\nbar"),
            '%s: Filter should not buffer since x0Dx0A is the needle and is not at EOF'
        );
    }

    public function testShouldBufferReturnsTrueIfAnyOfMultipleMatchesAtEndOfString()
    {
        $filter = $this->_createFilter(array('foo', 'zip'), 'bar');
        $this->assertTrue($filter->shouldBuffer('XfooYzi'),
            '%s: Filter should buffer since "zip" is a needle and the ending '.
            '"zi" could be from "zip"'
        );
    }

    public function testShouldBufferReturnsFalseOnEmptyBuffer()
    {
        $filter = $this->_createFilter("\r\n", "\n");
        $this->assertFalse($filter->shouldBuffer(''));
    }

    private function _createFilter($search, $replace)
    {
        return new Swift_StreamFilters_StringReplacementFilter($search, $replace);
    }
}
