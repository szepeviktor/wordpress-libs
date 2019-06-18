<?php

namespace Lipe\Lib\CMB2;

use Lipe\Lib\CMB2\Group\Layout;
use Lipe\Lib\Meta\Repo;

/**
 * Group
 *
 * @author  Mat Lipe
 * @since   7/27/2017
 *
 * @package Lipe\Lib\CMB2
 */
class Group extends Field {
	use Shorthand_Fields;

	/**
	 * ONLY APPLIES TO GROUPS
	 *
	 * These allow you to add arbitrary text/markup at different points in the field markup.
	 * These also accept a callback.
	 * The callback will receive $field_args as the first argument,
	 * and the CMB2_Field $field object as the second argument
	 *
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Parameters#before_group-after_group-before_group_row-after_group_row
	 *
	 * @var callable|string
	 */
	public $before_group;

	/**
	 * ONLY APPLIES TO GROUPS
	 *
	 * These allow you to add arbitrary text/markup at different points in the field markup.
	 * These also accept a callback.
	 * The callback will receive $field_args as the first argument,
	 * and the CMB2_Field $field object as the second argument
	 *
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Parameters#before_group-after_group-before_group_row-after_group_row
	 *
	 * @var callable|string
	 */
	public $after_group;

	/**
	 * ONLY APPLIES TO GROUPS
	 *
	 * These allow you to add arbitrary text/markup at different points in the field markup.
	 * These also accept a callback.
	 * The callback will receive $field_args as the first argument,
	 * and the CMB2_Field $field object as the second argument
	 *
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Parameters#before_group-after_group-before_group_row-after_group_row
	 *
	 * @var callable|string
	 */
	public $before_group_row;

	/**
	 * ONLY APPLIES TO GROUPS
	 *
	 * These allow you to add arbitrary text/markup at different points in the field markup.
	 * These also accept a callback.
	 * The callback will receive $field_args as the first argument,
	 * and the CMB2_Field $field object as the second argument
	 *
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Parameters#before_group-after_group-before_group_row-after_group_row
	 *
	 * @var callable|string
	 */
	public $after_group_row;

	/**
	 * Display format for the group
	 *
	 * block (default), row, table
	 *
	 * @var string
	 */
	protected $layout = 'block';

	/**
	 * box
	 *
	 * @var Box
	 */
	protected $box;


	/**
	 * Group constructor.
	 *
	 * @param string               $id
	 * @param string               $title
	 * @param Box|Shorthand_Fields $box
	 * @param string               $group_title    - include a {#} to have replace with number
	 * @param string               $add_button_text
	 * @param string               $remove_button_text
	 * @param bool                 $sortable
	 * @param bool                 $closed
	 * @param string               $remove_confirm - @since 2.7.0 -
	 *                                             A message to display when a user attempts
	 *                                             to delete a group.
	 *                                             (Defaults to null/false for no confirmation)
	 *
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Types#group
	 */
	public function __construct( $id, $title, Box $box, $group_title = null, $add_button_text = null, $remove_button_text = null, $sortable = true, $closed = false, ?string $remove_confirm = null ) {
		$this->box = $box;

		$this->type()->group( $group_title, $add_button_text, $remove_button_text, $sortable, $closed, $remove_confirm );

		parent::__construct( $id, $title );
	}


	/**
	 * Display format for the group
	 *
	 * block (default), row, table
	 *
	 * @param string $layout
	 *
	 * @since 1.10.0
	 *
	 * @return Group
	 */
	public function layout( string $layout ) : Group {
		Layout::init_once();

		$this->render_row_cb = [ Layout::in(), 'render_group_callback' ];
		$this->layout        = $layout;

		return $this;
	}


	/**
	 * Assign a field to a group, then register it.
	 *
	 * @param Field $field
	 *
	 * @return void
	 * @throws \LogicException
	 */
	public function add_field( Field $field ) : void {
		if ( null === $this->box->cmb ) {
			throw new \LogicException( 'You must add the group to the box before you add fields to the group.' );
		}

		$field->group = $this->get_id();
		$box = $this->box->get_box();
		$box->add_group_field( $this->id, $field->get_field_args(), $field->position );

		Repo::in()->register_field( $field );
	}


	/**
	 * Retrieve an array of this fields args to be
	 * submitted to CMB2 by way of
	 *
	 * @see Box::add_field()
	 *
	 * @throws \LogicException
	 *
	 * @return array
	 */
	public function get_field_args() : array {
		$args = parent::get_field_args();
		unset( $args['box'], $args['fields'] );

		return $args;
	}


	/**
	 * @override
	 *
	 * @throws \LogicException
	 */
	public function group() : void {
		throw new \LogicException( 'You cannot add a group to another group.' );
	}

}
