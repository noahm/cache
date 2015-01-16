<?php

namespace Onoi\Cache\Tests;

use Onoi\Cache\FixedInMemoryCache;

/**
 * @covers \Onoi\Cache\FixedInMemoryCache
 *
 * @group onoi-cache
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class FixedInMemoryCacheTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\Onoi\Cache\FixedInMemoryCache',
			new FixedInMemoryCache()
		);
	}

	public function testItemRemoval() {

		$instance = new FixedInMemoryCache( 5 );

		$instance->save( 'foo', array( 'foo' ) );
		$instance->save( 42, null );

		$this->assertTrue( $instance->contains( 'foo' ) );
		$this->assertTrue( $instance->contains( 42 ) );

		$stats = $instance->getStats();
		$this->assertEquals(
			2,
			$stats['count']
		);

		$instance->delete( 'foo' );

		$this->assertFalse( $instance->contains( 'foo' ) );
		$this->assertFalse( $instance->delete( 'foo' ) );

		$stats = $instance->getStats();

		$this->assertEquals(
			1,
			$stats['count']
		);
	}

	public function testLeastRecentlyUsedShiftForLimitedCacheSize() {

		$instance = new FixedInMemoryCache( 5 );
		$instance->save( 'abcde', array( 'abcde' ) );

		$this->assertEquals(
			array( 'abcde' ),
			$instance->fetch( 'abcde' )
		);

		foreach ( array( 'éèêë', 'アイウエオ', 'АБВГД', 'αβγδε', '12345' ) as $alphabet ) {
			$instance->save( $alphabet, array( $alphabet ) );
		}

		// 'éèêë' was added and removes 'abcde' from the cache
		$this->assertFalse( $instance->fetch( 'abcde' ) );

		$stats = $instance->getStats();

		$this->assertEquals(
			5,
			$stats['count']
		);

		// 'éèêë' moves to the top (last postion as most recently used) and
		// 'アイウエオ' becomes the next LRU candidate
		$this->assertEquals(
			array( 'éèêë' ),
			$instance->fetch( 'éèêë' )
		);

		$instance->save( '@#$%&', '@#$%&' );
		$this->assertFalse( $instance->fetch( 'アイウエオ' ) );

		// АБВГД would be the next LRU slot but setting it again will move it to MRU
		// and push αβγδε into the next LRU position
		$instance->save( 'АБВГД', 'АБВГД' );

		$instance->save( '-+=<>', '-+=<>' );
		$this->assertFalse( $instance->fetch( 'αβγδε' ) );

		$stats = $instance->getStats();

		$this->assertEquals(
			5,
			$stats['count']
		);
	}

}
