<?php

namespace Martiis\GitlabCLI;

class Bag implements BagInterface
{
    /**
     * @var array
     */
    private $bag = [];

    /**
     * Bag constructor.
     *
     * @param array $bag
     */
    public function __construct($bag = [])
    {
        $this->bag = $bag;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return isset($this->bag[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->bag[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->has($key) ? $this->bag[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->bag[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->bag);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->bag);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->bag);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->has($this->key());
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return reset($this->bag);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->bag);
    }
}
