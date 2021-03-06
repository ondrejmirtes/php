<?php

/**
 * Jyxo PHP Library
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/jyxo/php/blob/master/license.txt
 */

namespace Jyxo\Beholder\TestCase;

/**
 * Tests SMTP server availability.
 *
 * @category Jyxo
 * @package Jyxo\Beholder
 * @copyright Copyright (c) 2005-2011 Jyxo, s.r.o.
 * @license https://github.com/jyxo/php/blob/master/license.txt
 * @author Jaroslav Hanslík
 */
class Smtp extends \Jyxo\Beholder\TestCase
{
	/**
	 * Hostname.
	 *
	 * @var string
	 */
	private $host;

	/**
	 * Recipient.
	 *
	 * @var string
	 */
	private $to;

	/**
	 * Sender.
	 *
	 * @var string
	 */
	private $from;

	/**
	 * Timeout.
	 *
	 * @var integer
	 */
	private $timeout;

	/**
	 * Constructor.
	 *
	 * @param string $description Test description
	 * @param string $host Hostname
	 * @param string $to Recipient
	 * @param string $from Sender
	 * @param integer $timeout Timeout
	 */
	public function __construct($description, $host, $to, $from, $timeout = 2)
	{
		parent::__construct($description);

		$this->host = (string) $host;
		$this->to = (string) $to;
		$this->from = (string) $from;
		$this->timeout = (int) $timeout;
	}

	/**
	 * Performs the test.
	 *
	 * @return \Jyxo\Beholder\Result
	 */
	public function run()
	{
		// The \Jyxo\Mail\Sender\Smtp class is required
		if (!class_exists('\Jyxo\Mail\Sender\Smtp')) {
			return new \Jyxo\Beholder\Result(\Jyxo\Beholder\Result::NOT_APPLICABLE, 'Class \Jyxo\Mail\Sender\Smtp missing');
		}

		try {
			$header = 'From: ' . $this->from . "\n";
			$header .= 'To: ' . $this->to . "\n";
			$header .= 'Subject: Beholder' . "\n";
			$header .= 'Date: ' . date(DATE_RFC822) . "\n";

			$smtp = new \Jyxo\Mail\Sender\Smtp($this->host, 25, $this->host, $this->timeout);
			$smtp->connect()
				->from($this->from)
				->recipient($this->to)
				->data($header, 'Beholder SMTP Test')
				->disconnect();
		} catch (\Exception $e) {
			$smtp->disconnect();
			return new \Jyxo\Beholder\Result(\Jyxo\Beholder\Result::FAILURE, sprintf('Send error %s', $this->host));
		}

		// OK
		return new \Jyxo\Beholder\Result(\Jyxo\Beholder\Result::SUCCESS, $this->host);
	}
}
