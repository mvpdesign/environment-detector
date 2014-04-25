<?php

class EnvironmentDetector {

    /**
     * Detect the application's current environment.
     *
     * @param  array|string  $environments
     * @return string
     */
    public function detect($environments)
    {
        return $this->detectWebEnvironment($environments);
    }

    /**
     * Set the application environment for a web request.
     *
     * @param  array|string  $environments
     * @return string
     */
    protected function detectWebEnvironment($environments)
    {
        $webHost = $this->getHost();

        foreach ($environments as $environment => $hosts)
        {
            // To determine the current environment, we'll simply iterate through the possible
            // environments and look for the host that matches the host for this request we
            // are currently processing here, then return back these environment's names.
            foreach ((array) $hosts as $host)
            {
                if ($this->str_is($host, $webHost) or $this->isMachine($host))
                {
                    return $environment;
                }
            }
        }

        return 'production';
    }

    /**
     * Get the actual host for the web request.
     *
     * @return string
     */
    protected function getHost()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Determine if the name matches the machine name.
     *
     * @param  string  $name
     * @return bool
     */
    public function isMachine($name)
    {
        return $this->str_is($name, (strnatcmp(phpversion(), '5.3.0') >= 0) ? gethostname() : php_uname('n'));
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string  $pattern
     * @param  string  $value
     * @return bool
     */
    public function str_is($pattern, $value)
    {
        if ($pattern == $value) return true;

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        if ($pattern !== '/')
        {
             $pattern = str_replace('\*', '.*', $pattern).'\z';
        }
        else
        {
            $pattern = '/$';
        }

        return (bool) preg_match('#^'.$pattern.'#', $value);
    }
}