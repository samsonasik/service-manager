<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ServiceManager;

use ServiceManager\Driver\FactoryFinderInterface;
use ServiceManager\Exception\RuntimeException;

/**
 * Implementation for a service locator
 */
class ServiceManager implements ServiceLocatorInterface
{
    /**
     * @var FactoryInterface[]
     */
    protected $factories = [];

    /**
     * @var bool[]
     */
    protected $shared = [];

    /**
     * @var object[]
     */
    protected $services = [];

    /**
     * @var FactoryFinderInterface|null
     */
    protected $factoryFinder;

    /**
     * @param array                       $config
     * @param FactoryFinderInterface|null $factoryFinder
     */
    public function __construct(array $config = [], FactoryFinderInterface $factoryFinder = null)
    {
        $this->factoryFinder = $factoryFinder;
    }

    /**
     * {@inheritDoc}
     */
    public function get($className, array $options = [])
    {
        if (isset($this->services[$className])) {
            return $this->services[$className];
        }

        $object = $this->doCreate($className);

        if (null === $object) {
            throw new RuntimeException(sprintf(
                'Impossible to create object for class name "%s"',
                $className
            ));
        }

        if (isset($this->shared[$className]) && !$this->shared[$className]) {
            return $object;
        }

        return $this->services[$className] = $object;
    }

    /**
     * @param  string $className
     * @param  array  $options
     * @return object
     */
    public function doCreate($className, array $options = [])
    {
        if (isset($this->factories[$className])) {
            $factory = $this->factories[$className];
            $factory = is_string($factory) ? new $factory() : $factory;

            return $factory->createService($className, $this, $options);
        }

        if ($this->factoryFinder) {
            $factory = $this->factoryFinder->getFactory($className);

            if (null === $factory) {
                return null;
            }

            return $factory->createService($className, $this, $options);
        }

        return null;
    }
}
