<?php

namespace Lipe\Lib\Cron;

/**
 * @author Mat Lipe
 * @since  1.19.0
 *
 */
abstract class Cron_Abstract {

	/**
	 * Run the Task
	 *
	 * @return void
	 */
	abstract public function run() : void;


	/**
	 * How often the event should reoccur.
	 * hourly, twicedaily, daily
	 *
	 * If going to be using custom recurrence intervals, it is best
	 * to create a class for managing them with interval constants to return
	 * with this method.
	 *
	 * @return string
	 */
	abstract public function get_recurrence() : string;


	/**
	 * You must call init() to register the task!
	 *
	 * @return void
	 */
	public function init() : void {
		$this->schedule_task();
		$this->hook();
	}


	protected function hook() : void {
		add_action( static::NAME, [ $this, 'run_task' ] );
	}


	/**
	 * Store a last run stamp and run abstract method
	 *
	 * @see Cron_Abstract::run()
	 *
	 * @return void
	 */
	public function run_task() : void {
		update_option( static::NAME . '/last-run', time() );
		$this->run();
	}


	/**
	 * Get the timestamp this cron will run next.
	 *
	 * @return float
	 */
	public function get_next_run() : float {
		return \wp_next_scheduled( static::NAME );
	}


	/**
	 * Get the timestamp of the last time this cron was run.
	 *
	 * @return int
	 */
	public function get_last_run() : int {
		return get_option( static::NAME . '/last-run', 0 );
	}


	/**
	 * Schedule the task
	 *
	 * @return void
	 */
	protected function schedule_task() : void {
		if ( ! \wp_next_scheduled( static::NAME ) ) {
			\wp_schedule_event( time() - 1, $this->get_recurrence(), static::NAME );
		}
	}
}
