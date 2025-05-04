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

use PHPUnit\Framework\TestCase;
use Wikimedia\RelPath;

/**
 * @covers \Wikimedia\RelPath
 */
class RelPathTest extends TestCase {
	public function testSplitPath() {
		$this->assertEquals(
			[ '', 'foo', 'bar', 'baz.txt' ],
			RelPath::splitPath( '/foo/bar/baz.txt' ),
			'simple'
		);

		$this->assertEquals(
			[ '', 'foo', 'bar', 'baz.txt' ],
			RelPath::splitPath( '/foo/bar/./baz.txt' ),
			'dot'
		);

		$this->assertEquals(
			[ '', 'foo', 'baz.txt' ],
			RelPath::splitPath( '/foo/bar/../baz.txt' ),
			'dotdot'
		);
	}

	public static function provideRelPathTestCases() {
		return [
			'parent slash from subdir'      => [ '/foo/bar/', '/foo/bar/baz/', '..' ],
			'parent noslash from subdir'    => [ '/foo/bar', '/foo/bar/baz/', '..' ],
			'parent slash from file'        => [ '/foo/bar/', '/foo/bar/baz.txt', '..' ],
			'parent noslash from file'      => [ '/foo/bar', '/foo/bar/baz.txt', '..' ],
			'parent containing backslash'   => [ '/foo\\bar/', '/foo\\bar/baz/', '..' ],
			'root from subdir'              => [ '/', '/foo/', '..' ],
			'root from file'                => [ '/', '/foo/bar.txt', '../..' ],
			'root from nested file'         => [ '/', '/foo/bar/baz.txt', '../../..' ],
			'root from nested dir'          => [ '/', '/foo/bar/bat', '../../..' ],
			'sibling dir slash'             => [ '/foo/bar/', '/foo/baz/', '../bar' ],
			'sibling dir slash from file'   => [ '/foo/bar/', '/foo/baz.txt', '../bar' ],
			'subdir from parent slash'      => [ '/foo/bar/baz/', '/foo/bar/', 'baz' ],
			'subdir from parent noslash'    => [ '/foo/bar/baz', '/foo/bar', 'baz' ],
			'sibling dir file from dir'     => [ '/foo/bar.txt', '/foo/baz/', '../bar.txt' ],
			'sibling dir file from file'    => [ '/foo/bar.txt', '/foo/baz.txt', '../bar.txt' ],
			'sibling dir noslash'           => [ '/foo/bar', '/foo/baz/', '../bar' ],
			'sibling dir noslash from file' => [ '/foo/bar', '/foo/baz.txt', '../bar' ],
			'via root deep from deep'       => [ '/foo/bar/bat', '/x/y/z', '../../../foo/bar/bat' ],
			'via root deep from sub'        => [ '/foo/bar/bat', '/x', '../foo/bar/bat' ],
			'via root sub from deep'        => [ '/x', '/foo/bar/bat', '../../../x' ],
			'nested from root'              => [ '/foo/bar/bat', '/', 'foo/bar/bat' ],
			'noop root'                     => [ '/', '/', '.' ],
			'noop subdir'                   => [ '/a', '/a', '.' ],
			'noop nested'                   => [ '/a/b', '/a/b', '.' ],
			'base invalid'                  => [ 'foo/bar', 'quux', false ],
			'intermediate two'              => [ '/a/bat/cat/dog/boy/../../assets/img.png',
				'/a/bat/cat', 'assets/img.png'
			],
			'intermediate three'            => [ '/a/bat/cat/dog/boy/../../../assets/x.txt',
				'/a/bat', 'assets/x.txt'
			],
			'intermediate overflow'         => [ '/a/bat/cat/dog/../../../../eagle/x.txt',
				'/a/bat', '../../eagle/x.txt'
			],
			'intermediate back and forth'   => [ '/a/bat/cat/dog/eagle/egg1/../egg2/../egg3/../../../assets/x.txt',
				'/a/bat', 'cat/assets/x.txt'
			],
		];
	}

	public static function provideJoinPathTestCases() {
		return [
			'dot relative'                    => [ '/foo/bar', './baz', '/foo/bar/baz' ],
			'absolute'                        => [ '/foo/bar', '/tmp/file/', '/tmp/file/' ],
			'zero'                            => [ '/foo/0/bar', '/tmp/0/file/', '/tmp/0/file/' ],
			'absolute combo dot zero dotfile' => [ '/foo/./bar', '/tmp/0/.file/', '/tmp/0/.file/' ],
			'relative combo dot zero dotfile' => [ '/foo/./bar', 'tmp/0/.file/', '/foo/bar/tmp/0/.file' ],
			'solo filename'                   => [ '/foo/bar', 'file', '/foo/bar/file' ],
			'solo dotfile'                    => [ '/foo/bar', '.file', '/foo/bar/.file' ],
			'one parent'                      => [ '/foo/bar', '../baz', '/foo/baz' ],
			'two parents '                    => [ '/foo/bar', '../../baz', '/baz' ],
			'double-slash parent'             => [ '/foo//bar', '../baz', '/foo/baz' ],
			'double-slash two parents'        => [ '/foo//bar', '../../baz', '/baz' ],
			'double-slash many parents'       => [ '/foo//bar/baz//quux', '../../../baz', '/foo/baz' ],
			'noop dots'                       => [ '/foo/bar', '../././baz/.', '/foo/baz' ],
			'ignore too far by one'           => [ '/foo/bar', '../../../baz', '/baz' ],
			'ignore too far by two'           => [ '/foo/bar', '../../../../baz', '/baz' ],
			'ignore too far from root'        => [ '/', '../../../baz', '/baz' ],
			'back and forth'                  => [ '/foo/bar/baz/quux',
				'../../back/under/../../../file.txt', '/foo/file.txt'
			],
			'base with dotdot'                => [ '/foo/bar/../baz', 'quux', '/foo/baz/quux' ],
			'base invalid'                    => [ 'foo/bar', 'quux', false ],
		];
	}

	/**
	 * @dataProvider provideRelPathTestCases
	 */
	public function testRelPath( $path, $start, $expected ) {
		$this->assertEquals( $expected, RelPath::getRelativePath( $path, $start ) );
	}

	/**
	 * @dataProvider provideJoinPathTestCases
	 */
	public function testJoinPath( $base, $path, $expected ) {
		$this->assertEquals( $expected, RelPath::joinPath( $base, $path ) );
	}
}
