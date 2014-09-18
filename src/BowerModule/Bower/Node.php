<?php

namespace BowerModule\Bower;


class Node
{
	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var int
	 */
	public $level;
	/**
	 * @var Node
	 */
	public $prev;
	/**
	 * @var Node
	 */
	public $next;

	public function __construct($name, $level)
	{
		$this->name = $name;
		$this->level = $level;
		$this->prev = null;
		$this->next = null;
	}


} 