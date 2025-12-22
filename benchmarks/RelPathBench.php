<?php
declare( strict_types = 1 );

// Benchmark for RelPath::joinPath().

require_once __DIR__ . '/../vendor/autoload.php';

use Wikimedia\RelPath;

$iterations = 1000;

$base = '/data/example/resources/lib';
$rel = '../.././resources/lib/foo/foo.js';

$startTime = hrtime( true );

for ( $i = 0; $i < $iterations; $i++ ) {
	RelPath::joinPath( $base, $rel );
}

$endTime = hrtime( true );
$totalTimeMs = ( $endTime - $startTime ) / 1_000_000;
$avgTimeMs = $totalTimeMs / $iterations;

echo "PHP " . PHP_VERSION . "\n";
echo "RelPath::joinPath average time: " . sprintf( '%.4f', $avgTimeMs ) . " ms\n";
