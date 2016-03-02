<?php

/**
 * @copyright C UAB NFQ Technologies 2015
 *
 * This Software is the property of NFQ Technologies
 * and is protected by copyright law – it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact UAB NFQ Technologies:
 * E-mail: info@nfq.lt
 * http://www.nfq.lt
 */

namespace Martiis\GitlabCLI\Command;

use Martiis\GitlabCLI\Bag;
use Symfony\Component\Console\Command\Command;

abstract class AbstractBagAwareCommand extends Command
{
    /**
     * @var Bag
     */
    private $bag;

    /**
     * @return Bag
     */
    public function getBag()
    {
        return $this->bag;
    }

    /**
     * @param Bag $bag
     */
    public function setBag($bag)
    {
        $this->bag = $bag;
    }
}
