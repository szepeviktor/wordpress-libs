<?php

namespace Lipe\Lib\User;

use Lipe\Lib\Meta\Mutator_Trait;

/**
 * @property string $nickname
 * @property string $description
 * @property string $user_description
 * @property string $first_name
 * @property string $user_firstname
 * @property string $last_name
 * @property string $user_lastname
 * @property string $user_login
 * @property string $user_pass
 * @property string $user_nicename
 * @property string $user_email
 * @property string $user_url
 * @property string $user_registered
 * @property string $user_activation_key
 * @property string $user_status
 * @property int    $user_level
 * @property string $display_name
 * @property string $spam
 * @property string $deleted
 * @property string $locale
 * @property string $rich_editing
 * @property string $syntax_highlighting
 * @property string[] $roles
 *
 * @method bool exists()
 * @method bool[] get_role_caps()
 * @method void add_role( string $role )
 * @method void remove_role( string $role )
 * @method void set_role( string $role )
 * @method int level_reduction( int $max, string $item )
 * @method void update_user_level_from_caps()
 * @method void add_cap( string $cap, bool $grant = true )
 * @method void remove_cap( string $cap )
 * @method void remove_all_caps()
 * @method bool has_cap( string $cap, ...$args )
 * @method string translate_level_to_cap( int $level )
 * @method void for_site( int $site_id = '' )
 * @method int get_site_id()
 *
 *
 */
trait User_Trait {
	use Mutator_Trait;

	/**
	 * user_id
	 *
	 * @var int
	 */
	protected $user_id;

	/**
	 * user
	 *
	 * @var \WP_User
	 */
	protected $user;


	/**
	 * User_Trait constructor.
	 *
	 * @param int|null|\WP_User $user
	 */
	public function __construct( $user = null ) {
		if ( null === $user ) {
			$this->user_id = get_current_user_id();
		} elseif ( is_a( $user, \WP_User::class ) ) {
			$this->user = $user;
			$this->user_id = $user->ID;
		} else {
			$this->user_id = $user;
		}
	}


	public function get_id() : int {
		return (int) $this->user_id;
	}


	/**
	 * @return int
	 * @deprecated
	 *
	 */
	public function get_user_id() : int {
		\_deprecated_function( 'get_user_id', '2.5.0', 'get_id' );

		return $this->get_id();
	}


	/**
	 * @deprecated In favor of $this->get_object()
	 */
	public function get_user() {
		return $this->get_object();
	}


	public function get_object() : ?\WP_User {
		if ( null === $this->user ) {
			$this->user = get_user_by( 'id', $this->user_id );
		}

		return $this->user;
	}


	public function get_meta_type() : string {
		return 'user';
	}


	/**
	 *
	 * @param int|null|\WP_User $user
	 *
	 * @static
	 *
	 * @return static
	 */
	public static function factory( $user = null ) {
		return new static( $user );
	}

}
