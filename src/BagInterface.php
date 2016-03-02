<?php

namespace Martiis\GitlabCLI;

interface BagInterface extends \ArrayAccess, \Iterator, \Countable
{
    /**
     * Checks if value is set in bag.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Sets value in bag.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value);

    /**
     * Retrieves value from the bag.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key);
}
