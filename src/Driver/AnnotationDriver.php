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

namespace ServiceManager\Driver;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ServiceManager\Annotation;
use ServiceManager\Exception\RuntimeException;
use ServiceManager\Factory\InvokableFactory;
use ServiceManager\Factory\ReflectionFactory;
use ServiceManager\FactoryInterface;
use ServiceManager\ServiceLocatorInterface;

/**
 * Reader
 */
class AnnotationDriver implements FactoryFinderInterface
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var FactoryInterface[]
     */
    private $cacheFactories = [];

    /**
     * @var InvokableFactory
     */
    private $invokableFactory;

    /**
     * @var ReflectionFactory
     */
    private $reflectionFactory;

    /**
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * {@inheritDoc}
     */
    public function getFactory($className)
    {
        if (isset($this->cacheFactories[$className])) {
            return $this->cacheFactories[$className];
        }

        $reflectionClass       = new ReflectionClass($className);
        $reflectionConstructor = $reflectionClass->getConstructor();

        $annotation = $this->annotationReader->getMethodAnnotation($reflectionConstructor, Annotation\Inject::class);

        if (null === $annotation) {
            throw new RuntimeException(sprintf(
                'Impossible to create "%s" because constructor is not injectable. Are you sure you properly
                added the @Inject annotation?',
                $className
            ));
        }

        $factory = null;

        if ($reflectionConstructor->getNumberOfRequiredParameters() === 0) {
            if (!$this->invokableFactory) {
                $this->invokableFactory = new InvokableFactory();
            }
            $factory = $this->invokableFactory;
        } else {
            if (!$this->reflectionFactory) {
                $this->reflectionFactory = new ReflectionFactory();
            }
            $factory = $this->reflectionFactory;
        }

        return $this->cacheFactories[$className] = $factory;
    }
}
