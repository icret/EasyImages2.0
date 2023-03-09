<?php

/**
 * FTP - access to an FTP server.
 *
 * @author     David Grudl
 * @copyright  Copyright (c) 2008 David Grudl
 * @license    New BSD License
 * @link       http://phpfashion.com/
 * @link 	   https://github.com/dg/ftp-php
 * @version    1.2
 *
 * @method void alloc(int $filesize, string & $result) - Allocates space for a file to be uploaded
 * @method void cdUp() - Changes to the parent directory
 * @method void chDir(string $directory) - Changes the current directory on a FTP server
 * @method int chMod(int $mode, string $filename) - Set permissions on a file via FTP
 * @method void close() - Closes an FTP connection
 * @method void connect(string $host, int $port = 21, int $timeout = 90) - Opens an FTP connection
 * @method void delete(string $path) - Deletes a file on the FTP server
 * @method bool exec(string $command) - Requests execution of a command on the FTP server
 * @method void fGet(resource $handle, string $remote_file, int $mode, int $resumepos = 0) - Downloads a file from the FTP server and saves to an open file
 * @method void fPut(string $remote_file, resource $handle, int $mode, int $startpos = 0) - Uploads from an open file to the FTP server
 * @method mixed getOption(int $option) - Retrieves various runtime behaviours of the current FTP stream
 * @method void get(string $local_file, string $remote_file, int $mode, int $resumepos = 0) - Downloads a file from the FTP server
 * @method void login(string $username, string $password) - Logs in to an FTP connection
 * @method int mdTm(string $remote_file) - Returns the last modified time of the given file
 * @method string mkDir(string $directory) - Creates a directory
 * @method int nbContinue() - Continues retrieving/sending a file(non-blocking)
 * @method int nbFGet(resource $handle, string $remote_file, int $mode, int $resumepos = 0) - Retrieves a file from the FTP server and writes it to an open file(non-blocking)
 * @method int nbFPut(string $remote_file, resource $handle, int $mode, int $startpos = 0) - Stores a file from an open file to the FTP server(non-blocking)
 * @method int nbGet(string $local_file, string $remote_file, int $mode, int $resumepos = 0) - Retrieves a file from the FTP server and writes it to a local file(non-blocking)
 * @method int nbPut(string $remote_file, string $local_file, int $mode, int $startpos = 0) - Stores a file on the FTP server(non-blocking)
 * @method array nList(string $directory) - Returns a list of files in the given directory
 * @method void pasv(bool $pasv) - Turns passive mode on or off
 * @method void put(string $remote_file, string $local_file, int $mode, int $startpos = 0) - Uploads a file to the FTP server
 * @method string pwd() - Returns the current directory name
 * @method void quit() - Closes an FTP connection(alias of close)
 * @method array raw(string $command) - Sends an arbitrary command to an FTP server
 * @method mixed rawList(string $directory, bool $recursive = false) - Returns a detailed list of files in the given directory
 * @method void rename(string $oldname, string $newname) - Renames a file or a directory on the FTP server
 * @method void rmDir(string $directory) - Removes a directory
 * @method bool setOption(int $option, mixed $value) - Set miscellaneous runtime FTP options
 * @method void site(string $command) - Sends a SITE command to the server
 * @method int size(string $remote_file) - Returns the size of the given file
 * @method void sslConnect(string $host, int $port = 21, int $timeout = 90) - Opens an Secure SSL-FTP connection
 * @method string sysType() - Returns the system type identifier of the remote FTP server
 */
class Ftp
{
	/**#@+ FTP constant alias */
	const ASCII = FTP_ASCII;
	const TEXT = FTP_TEXT;
	const BINARY = FTP_BINARY;
	const IMAGE = FTP_IMAGE;
	const TIMEOUT_SEC = FTP_TIMEOUT_SEC;
	const AUTOSEEK = FTP_AUTOSEEK;
	const AUTORESUME = FTP_AUTORESUME;
	const FAILED = FTP_FAILED;
	const FINISHED = FTP_FINISHED;
	const MOREDATA = FTP_MOREDATA;
	/**#@-*/

	private static $aliases = array(
		'sslconnect' => 'ssl_connect',
		'getoption' => 'get_option',
		'setoption' => 'set_option',
		'nbcontinue' => 'nb_continue',
		'nbfget' => 'nb_fget',
		'nbfput' => 'nb_fput',
		'nbget' => 'nb_get',
		'nbput' => 'nb_put',
	);

	/** @var resource|\FTP\Connection */
	private $resource;

	/** @var array */
	private $state;

	/** @var string */
	private $errorMsg;


	/**
	 * @param  string  URL ftp://...
	 * @param  bool
	 */
	public function __construct($url = NULL, $passiveMode = TRUE)
	{
		if (!extension_loaded('ftp')) {
			throw new Exception('PHP extension FTP is not loaded.');
		}
		if ($url) {
			$parts = parse_url($url);
			if (!isset($parts['scheme']) || !in_array($parts['scheme'], array('ftp', 'ftps', 'sftp'))) {
				throw new InvalidArgumentException('Invalid URL.');
			}
			$func = $parts['scheme'] === 'ftp' ? 'connect' : 'ssl_connect';
			if (empty($parts['port'])) {
				$this->$func($parts['host']);
			} else {
				$this->$func($parts['host'], (int) $parts['port']);
			}
			$this->login(urldecode($parts['user']), urldecode($parts['pass']));
			$this->pasv((bool) $passiveMode);
			if (isset($parts['path'])) {
				$this->chdir($parts['path']);
			}
		}
	}


	/**
	 * Magic method (do not call directly).
	 * @param  string  method name
	 * @param  array   arguments
	 * @return mixed
	 * @throws Exception
	 * @throws FtpException
	 */
	public function __call($name, $args)
	{
		$name = strtolower($name);
		$silent = strncmp($name, 'try', 3) === 0;
		$func = $silent ? substr($name, 3) : $name;
		$func = 'ftp_' . (isset(self::$aliases[$func]) ? self::$aliases[$func] : $func);

		if (!function_exists($func)) {
			throw new Exception("Call to undefined method Ftp::$name().");
		}

		$this->errorMsg = NULL;
		set_error_handler(array($this, '_errorHandler'));

		if ($func === 'ftp_connect' || $func === 'ftp_ssl_connect') {
			$this->state = array($name => $args);
			$this->resource = call_user_func_array($func, $args);
			$res = NULL;

		} elseif (!is_resource($this->resource) && !$this->resource instanceof \FTP\Connection) {
			restore_error_handler();
			throw new FtpException("Not connected to FTP server. Call connect() or ssl_connect() first.");

		} else {
			if ($func === 'ftp_login' || $func === 'ftp_pasv') {
				$this->state[$name] = $args;
			}

			array_unshift($args, $this->resource);
			$res = call_user_func_array($func, $args);

			if ($func === 'ftp_chdir' || $func === 'ftp_cdup') {
				$this->state['chdir'] = array(ftp_pwd($this->resource));
			}
		}

		restore_error_handler();
		if (!$silent && $this->errorMsg !== NULL) {
			if (ini_get('html_errors')) {
				$this->errorMsg = html_entity_decode(strip_tags($this->errorMsg));
			}

			if (($a = strpos($this->errorMsg, ': ')) !== FALSE) {
				$this->errorMsg = substr($this->errorMsg, $a + 2);
			}

			throw new FtpException($this->errorMsg);
		}

		return $res;
	}


	/**
	 * Internal error handler. Do not call directly.
	 */
	public function _errorHandler($code, $message)
	{
		$this->errorMsg = $message;
	}


	/**
	 * Reconnects to FTP server.
	 * @return void
	 */
	public function reconnect()
	{
		@ftp_close($this->resource); // intentionally @
		foreach ($this->state as $name => $args) {
			call_user_func_array(array($this, $name), $args);
		}
	}


	/**
	 * Checks if file or directory exists.
	 * @param  string
	 * @return bool
	 */
	public function fileExists($file)
	{
		return (bool) $this->nlist($file);
	}


	/**
	 * Checks if directory exists.
	 * @param  string
	 * @return bool
	 */
	public function isDir($dir)
	{
		$current = $this->pwd();
		try {
			$this->chdir($dir);
		} catch (FtpException $e) {
		}
		$this->chdir($current);
		return empty($e);
	}


	/**
	 * Recursive creates directories.
	 * @param  string
	 * @return void
	 */
	public function mkDirRecursive($dir)
	{
		$parts = explode('/', $dir);
		$path = '';
		while (!empty($parts)) {
			$path .= array_shift($parts);
			try {
				if ($path !== '') $this->mkdir($path);
			} catch (FtpException $e) {
				if (!$this->isDir($path)) {
					throw new FtpException("Cannot create directory '$path'.");
				}
			}
			$path .= '/';
		}
	}


	/**
	 * Recursive deletes path.
	 * @param  string
	 * @return void
	 */
	public function deleteRecursive($path)
	{
		if (!$this->tryDelete($path)) {
			foreach ((array) $this->nlist($path) as $file) {
				if ($file !== '.' && $file !== '..') {
					$this->deleteRecursive(strpos($file, '/') === FALSE ? "$path/$file" : $file);
				}
			}
			$this->rmdir($path);
		}
	}

}



class FtpException extends Exception
{
}
