<?php
declare( strict_types = 1 );

// Benchmark for RelPath::joinPath() and RelPath::getRelativePath().

require_once __DIR__ . '/../vendor/autoload.php';

use Wikimedia\RelPath;

$iterations = 100_000;

echo "PHP " . PHP_VERSION . "\n";
echo "Iterations: " . number_format( $iterations ) . "\n\n";

// Benchmark joinPath
$base = '/data/example/resources/lib';
$rel = '../.././resources/lib/foo/foo.js';

$startTime = hrtime( true );

for ( $i = 0; $i < $iterations; $i++ ) {
	RelPath::joinPath( $base, $rel );
}

$endTime = hrtime( true );
$totalTimeMs = ( $endTime - $startTime ) / 1_000_000;
$avgTimeMs = $totalTimeMs / $iterations;

echo sprintf( "%-40s %.6f ms\n", "RelPath::joinPath average time:", $avgTimeMs );

// Benchmark getRelativePath
$path = '/data/example/resources/lib/foo/foo.js';
$start = '/data/example/resources/src';

$startTime = hrtime( true );

for ( $i = 0; $i < $iterations; $i++ ) {
	RelPath::getRelativePath( $path, $start );
}

$endTime = hrtime( true );
$totalTimeMs = ( $endTime - $startTime ) / 1_000_000;
$avgTimeMs = $totalTimeMs / $iterations;

echo sprintf( "%-40s %.6f ms\n", "RelPath::getRelativePath average time:", $avgTimeMs );
