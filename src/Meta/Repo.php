<?php

namespace Lipe\Lib\Meta;

use Lipe\Lib\CMB2\Field;
use Lipe\Lib\Traits\Singleton;

/**
 * Repo to hold the different field types for our meta keys
 * and return the appropriate data based on field type.
 *
 * @author Mat Lipe
 * @since  2.0.0
 *
 */
class Repo extends Translate_Abstract {
	use Singleton;

	public const CHECKBOX          = 'checkbox';
	public const DEFAULT           = 'default';
	public const FILE              = 'file';
	public const GROUP             = 'group';
	public const TAXONOMY          = 'taxonomy';
	public const TAXONOMY_SINGULAR = 'taxonomy-singular';


	/**
	 * Store a field's id mapped to the field object
	 *
	 * @param Field $field
	 *
	 * @return void
	 */
	public function register_field( Field $field ) : void {
		$this->fields[ $field->get_id() ] = $field;
	}


	/**
	 * Get a registered field by an id.
	 *
	 * @param string $field_id
	 *
	 * @return null|Field
	 */
	protected function get_field( string $field_id ) : ?Field {
		return $this->fields[ $field_id ] ?? null;
	}


	/**
	 * Get the data type of registered field by an id.
	 *
	 * @param string $field_id
	 *
	 * @return string
	 */
	protected function get_field_data_type( string $field_id ) : string {
		$field = $this->get_field( $field_id );
		if ( null !== $field ) {
			return $field->data_type;
		}
		return static::DEFAULT;
	}


	/**
	 * Get a field's value
	 *
	 * Use the registered fields and registered types to determine the appropriate method to
	 * return the data.
	 *
	 * @param int|string $object_id - id of post, term, user, <custom>
	 * @param string     $field_id  - field id to return
	 * @param string     $meta_type - user, term, post, <custom> (defaults to 'post')
	 *
	 * @since 2.4.0 - Will return term objects for taxonomy field within options.
	 * @since 2.4.0 - Support singular taxonomy fields which return a single term.
	 *
	 * @return mixed
	 */
	public function get_value( $object_id, string $field_id, string $meta_type = 'post' ) {
		switch ( $this->get_field_data_type( $field_id ) ) {
			case self::CHECKBOX:
				return $this->get_checkbox_field_value( $object_id, $field_id, $meta_type );
			case self::FILE:
				return $this->get_file_field_value( $object_id, $field_id, $meta_type );
			case self::GROUP:
				return $this->get_group_field_value( $object_id, $field_id, $meta_type );
			case self::TAXONOMY:
				return $this->get_taxonomy_field_value( $object_id, $field_id, $meta_type );
			case self::TAXONOMY_SINGULAR:
				return $this->get_taxonomy_singular_field_value( $object_id, $field_id, $meta_type );
		}

		return $this->get_meta_value( $object_id, $field_id, $meta_type );

	}


	/**
	 * Update a field's value
	 *
	 * Use the registered fields and registered types to determine the appropriate method to
	 * set the data.
	 *
	 * @param int|string $object_id - id of post, term, user, <custom>
	 * @param string     $field_id  - field id to set.
	 * @param mixed      $value     - Value to save.
	 * @param string     $meta_type - user, term, post, <custom> (defaults to 'post')
	 *
	 * @since 2.5.0
	 *
	 * @return void
	 */
	public function update_value( $object_id, string $field_id, $value, string $meta_type = 'post' ) : void {
		switch ( $this->get_field_data_type( $field_id ) ) {
			case self::CHECKBOX:
				$this->update_checkbox_field_value( $object_id, $field_id, $value, $meta_type );
				break;
			case self::FILE:
				$this->update_file_field_value( $object_id, $field_id, (int) $value, $meta_type );
				break;
			case self::GROUP:
				$this->update_group_field_values( $object_id, $field_id, (array) $value, $meta_type );
				break;
			case self::TAXONOMY:
			case self::TAXONOMY_SINGULAR:
				$this->update_taxonomy_field_value( $object_id, $field_id, (array) $value, $meta_type );
				break;
			default:
				$this->update_meta_value( $object_id, $field_id, $value, $meta_type );
		}

	}


	/**
	 * Delete a field's value
	 *
	 * Use the registered fields and registered types to determine the appropriate method to
	 * delete the data.
	 *
	 * @param int|string $object_id - id of post, term, user, <custom>
	 * @param string     $field_id  - field id to set.
	 * @param string     $meta_type - user, term, post, <custom> (defaults to 'post')
	 *
	 * @since 2.5.0
	 *
	 * @return void
	 */
	public function delete_value( $object_id, string $field_id, string $meta_type ) : void {
		switch ( $this->get_field_data_type( $field_id ) ) {
			case self::FILE:
				$this->delete_file_field_value( $object_id, $field_id, $meta_type );
				break;
			case self::TAXONOMY:
			case self::TAXONOMY_SINGULAR:
				$this->delete_taxonomy_field_value( $object_id, $field_id, $meta_type );
				break;
			case self::CHECKBOX:
			case self::GROUP:
			default:
				$this->delete_meta_value( $object_id, $field_id, $meta_type );

		}
	}

}
