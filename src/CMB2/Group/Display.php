<?php

namespace Lipe\Lib\CMB2\Group;

use Lipe\Lib\CMB2\Group;
use Lipe\Lib\Traits\Singleton;

/**
 * @author Mat Lipe
 * @since  1.10.0
 *
 */
class Display {
	use Singleton;

	/**
	 * Cmb instance of this particular meta box
	 *
	 * @var \CMB2
	 */
	private $cmb;


	protected function hook() : void {
		add_action( 'cmb2_before_form', [ $this, 'init_group' ], 10, 4 );
	}


	/**
	 *
	 * @param string     $cmb_id
	 * @param string|int $object_id
	 * @param string     $object_type
	 * @param \CMB2      $cmb
	 *
	 * @return void
	 */
	public function init_group( $cmb_id, $object_id, $object_type, \CMB2 $cmb ) : void {
		$this->cmb = $cmb;
	}


	/**
	 * Copied mostly from CMB2::render_group_callback()
	 *
	 * @param  array       $field_args  Array of field arguments for the group field parent.
	 * @param  \CMB2_Field $field_group The CMB2_Field group object.
	 *
	 * @see Group::display()
	 * @see \CMB2::render_group_callback()
	 *
	 * @return \CMB2_Field|null Group field object.
	 */
	public function render_group_callback( $field_args, \CMB2_Field $field_group ) : ?\CMB2_Field {
		// If field is requesting to be conditionally shown.
		if ( ! $field_group || ! $field_group->should_show() ) {
			return null;
		}

		$field_group->index = 0;

		$field_group->peform_param_callback( 'before_group' );

		$desc      = $field_group->args( 'description' );
		$label     = $field_group->args( 'name' );
		$is_table  = ( 'table' === $field_group->args( 'display' ) );
		$group_val = (array) $field_group->value();

		echo '<div class="cmb-row cmb-repeat-group-wrap cmb-group-' . esc_attr( $field_group->args( 'display' ) ) . ' ' . esc_attr( $field_group->row_classes() ), '" data-fieldtype="group"><div class="cmb-td"><div data-groupid="' . esc_attr( $field_group->id() ) . '" id="' . esc_attr( $field_group->id() ) . '_repeat" ' . $this->cmb->group_wrap_attributes( $field_group ) . '>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $desc || $label ) {
			$class = $desc ? ' cmb-group-description' : '';
			echo '<div class="cmb-row' . esc_attr( $class ) . '"><div class="cmb-th">';
			if ( $label ) {
				echo '<h2 class="cmb-group-name">' . esc_html( $label ) . '</h2>';
			}
			if ( $desc ) {
				echo '<p class="cmb2-metabox-description">' . esc_html( $desc ) . '</p>';
			}
			echo '</div></div>';
		}

		if ( $is_table ) {
			echo '<table class="cmb-table">';
			if ( $field_group->args( 'show_names' ) ) {
				$this->render_group_table_header( $field_group );
			}
		}

		if ( ! empty( $group_val ) ) {
			foreach ( $group_val as $group_key => $field_id ) {
				if ( $is_table ) {
					$this->render_group_table_row( $field_group );
				}
				$field_group->index ++;
			}
		} elseif ( $is_table ) {
			$this->render_group_table_row( $field_group );
		}

		if ( $is_table ) {
			echo '</table>';
		}

		if ( $field_group->args( 'repeatable' ) ) {
			$title = $is_table ? '{#}' : $field_group->options( 'group_title' );
			echo '<div class="cmb-row"><div class="cmb-td"><p class="cmb-add-row"><button type="button" data-selector="' . esc_attr( $field_group->id() ) . '_repeat" data-grouptitle="' . esc_attr( $title ) . '" class="cmb-add-group-row button-secondary">' . esc_html( $field_group->options( 'add_button' ) ) . '</button></p></div></div>';
		}

		echo '</div></div></div>';

		$this->styles();
		$field_group->peform_param_callback( 'after_group' );

		return $field_group;

	}


	/**
	 * Render a repeatable group row table header
	 *
	 * @param  \CMB2_Field $field_group CMB2_Field group field object.
	 *
	 */
	public function render_group_table_header( $field_group ) : void {
		?>
		<tr class="cmb-row">
			<th>&nbsp;</th>
			<?php
			foreach ( $field_group->args( 'fields' ) as $_field ) {
				echo '<th>' . esc_html( $_field['name'] ) . '</th>';
			}
			?>
			<th>&nbsp;</th>
		</tr>
		<?php
	}


	/**
	 * Render a repeatable group row as a table
	 *
	 * Used when a group is given the 'display_as_table' property.
	 *
	 * @since  2.5.0
	 *
	 * @param  \CMB2_Field $field_group CMB2_Field group field object.
	 *
	 * @return \CMB2
	 */
	public function render_group_table_row( $field_group ) : \CMB2 {
		$field_group->peform_param_callback( 'before_group_row' );
		?>
		<tr class="cmb-row cmb-repeatable-grouping" data-iterator="<?= esc_attr( $field_group->index ); ?>">
			<td class="cmb-group-table-control">
				<h3 class="cmb-group-title cmbhandle-title">
					<span><?= esc_html( $field_group->replace_hash( '{#}' ) ); ?></span>
				</h3>
			</td>
			<?php
			// Loop and render repeatable group fields.
			foreach ( array_values( $field_group->args( 'fields' ) ) as $field_args ) {
				?>
				<td class="inside cmb-nested cmb-field-list">
					<?php
					if ( 'hidden' === $field_args['type'] ) {
						// Save rendering for after the metabox.
						$this->cmb->add_hidden_field( $field_args, $field_group );
					} else {
						$field_args['show_names'] = false;
						$this->cmb->get_field( $field_args, $field_group )->render_field();
					}
					?>
				</td>
				<?php
			}
			if ( $field_group->args( 'repeatable' ) ) {
				?>
				<td class="cmb-remove-field-row cmb-group-table-control">
					<div class="cmb-remove-row">
						<a href="javascript:void(0)" type="button"
						   data-selector="<?= esc_attr( $field_group->id() ); ?>_repeat"
						   class="cmb-remove-group-row cmb-remove-group-row-button button-secondary cmb-shift-rows"
						   title="<?= esc_attr( $field_group->options( 'remove_button' ) ); ?>">
							<span class="dashicons dashicons-no-alt"/>
						</a>
					</div>
				</td>
				<?php
			}
			?>
		</tr>
		<?php

		$field_group->peform_param_callback( 'after_group_row' );

		return $this->cmb;
	}


	private function styles() : void {
		static $displayed = false;
		if ( $displayed ) {
			return;
		}
		$displayed = true;
		?>
		<style>
			.cmb-group-table table {
				width: 100%;
			}

			.cmb-group-table th {
				border-top: #DFDFDF solid 1px;
			}

			.cmb-group-table td:last-child,
			.cmb-group-table th:last-child {
				border-right: #DFDFDF solid 1px;
			}

			.cmb-group-table th,
			.cmb-group-table td {
				border-left: #DFDFDF solid 1px;
				border-bottom: #DFDFDF solid 1px;
				padding: 8px !important;
				vertical-align: top;
				text-align: left;
			}

			.cmb-group-table-control {
				width: 16px;
				padding: 0 !important;
				vertical-align: middle !important;
				text-align: center !important;
				background: #f4f4f4;
				text-shadow: #fff 0 1px 0;
			}

			.cmb-group-table .cmb-group-table-control a {
				float: none !important;
				display: block !important;
				margin: 5px 0 !important;
				text-align: center !important;
			}

			.cmb-group-table-control a,
			.cmb-group-table-control h3 {
				color: #aaa !important;
			}

			.cmb-group-table-control a:hover,
			.cmb-group-table-control:hover h3 {
				color: #23282d !important;
			}

			.cmb-group-table .cmb-group-table-control .cmb-remove-group-row {
				color: #F55E4F !important;
				margin-top: 12px !important;
			}

			.cmb-repeatable-group.sortable .cmb-group-table-control:first-child {
				cursor: move;
			}

			.cmb-group-table .cmb-group-title {
				position: relative;
				background: none;
				margin: 0 !important;
				padding: 0 !important;
			}

			.cmb-group-table th {
				width: auto;
			}

			.cmb-group-table .cmb-repeat-group-field {
				padding: 0 !important;
			}


		</style>
		<?php

	}
}
