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

use ServiceManager\FactoryInterface;
use ServiceManager\ServiceLocatorInterface;

/**
 * Default factory for creating a plugin manager
 */
class DefaultPluginManagerFactory implements FactoryInterface
{
    const CONFIG_KEY_SEPARATOR = '.';

    /**
     * {@inheritDoc}
     */
    public function createService($className, ServiceLocatorInterface $serviceLocator, array $options = [])
    {
        $config    = [];
        $configKey = isset($options['config_key']) ? $options['config_key'] : null;

        if (!empty($configKey)) {
            $config = $this->extractConfig($serviceLocator, $configKey);
        }

        return new $className($config);
    }

    /**
     * Extract configuration from the global config
     *
     * It will use a dot as a separator
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string $configKey
     * @return array
     */
    protected function extractConfig(ServiceLocatorInterface $serviceLocator, $configKey)
    {
        $parts  = explode(static::CONFIG_KEY_SEPARATOR, $configKey);
        $config = $serviceLocator->get('Config')[array_shift($parts)];

        foreach ($parts as $part) {
            $config = $config[$part];
        }

        return $config;
    }
}
