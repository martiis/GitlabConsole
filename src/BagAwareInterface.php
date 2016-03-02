<?php

namespace Martiis\GitlabCLI;

interface BagAwareInterface
{
    /**
     * Sets bag into class.
     *
     * @param BagInterface $bag
     */
    public function setBag(BagInterface $bag);
}
