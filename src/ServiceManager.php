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

namespace Exp\ServiceManager;

use ReflectionClass;

/**
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
class ServiceManager implements ServiceManagerInterface
{
    /**
     * @var ReflectionClass[]
     */
    protected $reflCache = [];

    /**
     * @var FactoryInterface[]
     */
    protected $factories = [];

    /**
     * @var array
     */
    protected $factoriesPath = [];

    /**
     * @var object[]
     */
    protected $services = [];

    /**
     * {@inheritDoc}
     */
    public function get($className)
    {
        if (!isset($this->services[$className])) {
            $this->services[$className] = $this->doCreate($className);
        }

        return $this->services[$className];
    }

    /**
     * Create the instance
     *
     * @param  string $className
     * @return object
     */
    protected function doCreate($className)
    {
        if (!isset($this->reflCache[$className])) {
            $this->reflCache[$className] = new ReflectionClass($className);
        }

        $reflClass   = $this->reflCache[$className];
        $factoryPath = $this->factoriesPath[$reflClass->getNamespaceName()];
        $factory     = $factoryPath . '\\' . $reflClass->getName() . 'Factory';

        if (class_exists($factory)) {
            if (!isset($this->factories[$factory])) {
                $factory = $this->factories[$factory] = new $factory();
            }

            return $factory->createService($this);
        }

        return new $className();
    }
}
