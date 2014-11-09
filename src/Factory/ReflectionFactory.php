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

namespace ServiceManager\Factory;

use ReflectionClass;
use ServiceManager\FactoryInterface;
use ServiceManager\ServiceLocatorInterface;

/**
 * Factory that uses reflection to create an object
 */
class ReflectionFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService($className, ServiceLocatorInterface $serviceLocator, array $options = [])
    {
        $reflectionClass       = new ReflectionClass($className);
        $reflectionConstructor = $reflectionClass->getConstructor();
        $constructorParameters = $reflectionConstructor->getParameters();
        $arguments             = [];

        foreach ($constructorParameters as $constructorParameter) {
            $class       = $constructorParameter->getClass();
            $arguments[] = $serviceLocator->get($class->getName());
        }

        return new $className(...$arguments, $options);
    }
}
