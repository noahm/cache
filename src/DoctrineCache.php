<?php

namespace Onoi\Cache;

use Doctrine\Common\Cache\Cache as DoctrineCacheClient;

/**
 * Doctrine Cache decorator
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class DoctrineCache implements Cache {

	/**
	 * @var DoctrineCacheClient
	 */
	private $cache = null;

	/**
	 * @since 1.0
	 *
	 * @param DoctrineCacheClient $cache
	 */
	public function __construct( DoctrineCacheClient $cache ) {
		$this->cache = $cache;
	}

	/**
	 * {@inheritDoc}
	 */
	public function fetch( $id ) {
		return $this->cache->fetch( $id );
	}

	/**
	 * {@inheritDoc}
	 */
	public function contains( $id ) {
		return $this->cache->contains( $id );
	}

	/**
	 * {@inheritDoc}
	 */
	public function save( $id, $data, $ttl = 0 ) {
		$this->cache->save( $id, $data, $ttl );
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete( $id ) {
		return $this->cache->delete( $id );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStats() {
		return $this->cache->getStats();
	}

}
