<?php

namespace Lipe\Lib\Site;

use Lipe\Lib\Meta\Mutator_Trait;

/**
 * Interact with a single site on a multisite install.
 *
 * Gives quick access to the `blogmeta` table and any
 * other properties available in the `WP_Site` class.
 *
 * @requires WP version 5.1+
 *
 * @author   Mat Lipe
 * @since    2.8.0
 *
 * @property int $id
 * @property int $network_id
 * @property string $$archived
 * @property string $deleted
 * @property string $domain
 * @property string $lang_id
 * @property string $last_updated
 * @property string $mature
 * @property string $path
 * @property string $public
 * @property string $registered
 * @property string $site_id
 * @property string $spam
 * @property string $blogname
 * @property string $home
 * @property int $post_count
 * @property string $siteurl
 *
 */
trait Site_Trait {
	use Mutator_Trait;

	/**
	 * @var int
	 */
	protected $blog_id;

	/**
	 * @var \WP_Site
	 */
	protected $site;


	/**
	 * @param int|null|\WP_Site $site
	 *
	 */
	public function __construct( $site = null ) {
		if ( null === $site ) {
			$this->blog_id = \get_current_blog_id();
		} elseif ( is_a( $site, \WP_Site::class ) ) {
			$this->site = $site;
			$this->blog_id = $this->site->blog_id;
		} else {
			$this->blog_id = $site;
		}
	}


	/**
	 * @deprecated In favor of $this->get_object()
	 */
	public function get_site() : ?\WP_Site {
		\_deprecated_function( __METHOD__, '2.0.0', 'get_object' );
		return $this->get_object();
	}


	public function get_object() : ?\WP_Site {
		if ( null === $this->site ) {
			$this->site = \get_site( $this->blog_id );
		}

		return $this->site;
	}


	/**
	 * @return int
	 */
	public function get_id() : int {
		return (int) $this->blog_id;
	}


	/**
	 * @return string
	 */
	public function get_meta_type() : string {
		return 'blog';
	}


	/**
	 * Access to extended properties from WP_Site.
	 *
	 * @see \WP_Site::__get
	 * @see Mutator_Trait::__get
	 *
	 * @return array
	 */
	protected function get_extended_properties() : array {
		return [
			'id',
			'network_id',
			'blogname',
			'siteurl',
			'post_count',
			'home',
		];
	}

	/**
	 *
	 * @param int|null|\WP_Site $site
	 *
	 * @static
	 *
	 * @return static
	 */
	public static function factory( $site = null ) {
		return new static( $site );
	}

}
