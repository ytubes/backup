<?php
namespace ytubes\backup\databases;

class MySql implements DatabaseInterface
{
    /** @var string */
    protected $dbName;
    /** @var string */
    protected $userName;
    /** @var string */
    protected $password;
    /** @var string */
    protected $host = 'localhost';
    /** @var int */
    protected $port = 3306;
    /** @var array */
    protected $extraOptions = [];
    /** @var bool */
    protected $enableCompression = false;
    /** @var bool */
    protected $useSingleTransaction = false;
    /** @var string */
    protected $defaultCharacterSet = '';
	/**
	 * Задает конфиг для базы.
	 * @param array $config
	 */
	public function setConfig($config)
	{
		if (empty($config['user'])      ||
			empty($config['password'])  ||
			empty($config['dbName'])
		) {
            throw new \Exception('Connection config error');
        }

        $this->dbName = $config['dbName'];
        $this->userName = $config['user'];
        $this->password = $config['password'];

		if (!empty($config['host'])) {
            $this->host = $this->config['host'];
        }

		if (!empty($config['port'])) {
            $this->port = (int) $this->config['port'];
        }

		if (!empty($config['defaultCharacterSet'])) {
            $this->defaultCharacterSet = $config['defaultCharacterSet'];
        }

		if (!empty($config['useSingleTransaction'])) {
            $this->useSingleTransaction = $config['useSingleTransaction'];
        }

		if (!empty($config['enableCompression'])) {
            $this->enableCompression = $config['enableCompression'];
        }
	}
    /**
     * @param $outputPath
     * @return string
     */
    public function getDumpCommandLine($outputPath)
    {
        $arguments = [];

        if ($this->useSingleTransaction) {
            $arguments[] = '--single-transaction';
        }

        if ($this->defaultCharacterSet !== '') {
            $arguments[] = sprintf('--default-character-set=%s', escapeshellarg($this->defaultCharacterSet));
        }

        foreach ($this->extraOptions as $extraOption) {
            $arguments[] = $extraOption;
        }

    	$command = 'mysqldump --routines '.implode(' ', $arguments).' --host=%s --port=%s --user=%s --password=%s %s';

    	if ($this->enableCompression) {
    		$command .= ' | gzip > %s';
    		$outputPath .= '.gz';
    	} else {
    		$command .= ' > %s';
    	}

        return sprintf($command,
            escapeshellarg($this->host),
            escapeshellarg($this->port),
            escapeshellarg($this->userName),
            escapeshellarg($this->password),
            escapeshellarg($this->dbName),
            escapeshellarg($outputPath)
        );
    }
    /**
     * @param $inputPath
     * @return string
     */
    public function getRestoreCommandLine($inputPath)
    {
        $quote = $this->determineQuote();
        $arguments = [];

        if (! empty($this->defaultCharacterSet)) {
            $arguments[] = '--default-character-set='.$this->defaultCharacterSet;
        }

    	if ($this->enableCompression) {
	        return sprintf("gunzip < {$quote}%s{$quote} | mysql --host=%s --port=%s --user=%s --password=%s " . implode(' ', $arguments) . " %s",
	            $inputPath,
	            escapeshellarg($this->host),
	            escapeshellarg($this->port),
	            escapeshellarg($this->userName),
	            escapeshellarg($this->password),
	            escapeshellarg($this->dbName)
	        );
    	} else {
	        return sprintf('mysql --host=%s --port=%s --user=%s --password=%s '.implode(' ', $arguments).' %s -e "source %s"',
	            escapeshellarg($this->host),
	            escapeshellarg($this->port),
	            escapeshellarg($this->userName),
	            escapeshellarg($this->password),
	            escapeshellarg($this->dbName),
	            $inputPath
	        );
    	}
    }

    protected function determineQuote()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '"' : "'";
    }
    /**
     * @param string $extraOption
     *
     * @return $this
     */
    public function addExtraOption($extraOption)
    {
        if (! empty($extraOption)) {
            $this->extraOptions[] = $extraOption;
        }
        return $this;
    }
}