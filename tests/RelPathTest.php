<?php
/**
 * Copyright (c) 2015 Ori Livneh <ori@wikimedia.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @file
 * @author Ori Livneh <ori@wikimedia.org>
 */

class RelPathTest extends \PHPUnit\Framework\TestCase {

	public function provideRelPathTestCases() {
		return [
			[ '/foo/bar/', '/foo/bar/baz/', '..' ],
			[ '/foo/bar/', '/foo/bar/baz.txt', '..' ],
			[ '/foo/bar/', '/foo/bar/', '.' ],
			[ '/foo/bar/', '/foo/bar/', '.' ],
			[ '/', '/foo/', '..' ],
			[ '/', '/foo/bar.txt', '../..' ],
			[ '/', '/foo/bar/baz.txt', '../../..' ],
			[ '/foo/bar/', '/foo/baz/', '../bar' ],
			[ '/foo/bar/', '/foo/baz.txt', '../bar' ],
			[ '/foo/bar/baz/', '/foo/bar/', 'baz' ],
			[ '/foo/bar/baz', '/foo/bar', 'baz' ],
			[ '/foo/bar.txt', '/foo/baz/', '../bar.txt' ],
			[ '/foo/bar.txt', '/foo/baz.txt', '../bar.txt' ],
			[ '/foo/bar', '/foo/baz/', '../bar' ],
			[ '/foo/bar', '/foo/baz.txt', '../bar' ],
			[ '/foo/bar', '/foo/bar/baz/', '..' ],
			[ '/foo/bar', '/foo/bar/baz.txt', '..' ],
			[ '/foo/bar/bat', '/x/y/z', '../../../foo/bar/bat' ],
			[ '/foo/bar/bat', '/foo/bar', 'bat' ],
			[ '/foo/bar/bat', '/', 'foo/bar/bat' ],
			[ '/', '/foo/bar/bat', '../../..' ],
			[ '/foo/bar/bat', '/x', '../foo/bar/bat' ],
			[ '/x', '/foo/bar/bat', '../../../x' ],
			[ '/', '/', '.' ],
			[ '/a', '/a', '.' ],
			[ '/a/b', '/a/b', '.' ],
			[ 'foo/bar', 'quux', false ],
			[ '/foo\\bar/', '/foo\\bar/baz/', '..' ],
		];
	}

	public function provideJoinPathTestCases() {
		return [
			[ '/foo/bar', './baz', '/foo/bar/baz' ],
			[ '/foo/bar', '/tmp/file/', '/tmp/file/' ],
			[ '/foo/0/bar', '/tmp/0/file/', '/tmp/0/file/' ],
			[ '/foo/./bar', '/tmp/0/.file/', '/tmp/0/.file/' ],
			[ '/foo/./bar', 'tmp/0/.file/', '/foo/bar/tmp/0/.file' ],
			[ '/foo/bar', 'file', '/foo/bar/file' ],
			[ '/foo/bar', '.file', '/foo/bar/.file' ],
			[ '/foo//bar', '../baz', '/foo/baz' ],
			[ '/foo/bar', '../././baz/.', '/foo/baz' ],
			[ '/foo//bar', '../../baz', '/baz' ],
			[ '/foo//bar', '../../../baz', '/baz' ],
			[ '/', '../../../baz', '/baz' ],
			[ '/foo/bar/../baz', 'quux', '/foo/baz/quux' ],
			[ '/foo/bar', '../quux/../baz.txt', '/foo/baz.txt' ],
			[ 'foo/bar', 'quux', false ],
		];
	}

	/**
	 * @dataProvider provideRelPathTestCases
	 */
	public function testRelPath( $path, $start, $expected ) {
		$this->assertEquals( $expected, \Wikimedia\RelPath::getRelativePath( $path, $start ) );
	}

	/**
	 * @dataProvider provideJoinPathTestCases
	 */
	public function testJoinPath( $base, $path, $expected ) {
		$this->assertEquals( $expected, \Wikimedia\RelPath::joinPath( $base, $path ) );
	}
}
