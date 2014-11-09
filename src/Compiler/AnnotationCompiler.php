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

namespace ServiceManager\Compiler;

use Doctrine\Common\Annotations\Reader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Compiler for annotations
 *
 * This class takes a list of paths to compile, and will generate an optimized config file
 */
final class AnnotationCompiler implements CompilerInterface
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * The dir where the config file must be generated
     *
     * @var string|null
     */
    private $cacheDir;

    /**
     * The cache key
     *
     * @var string|null
     */
    private $cacheKey;

    /**
     * The optimized config
     *
     * @var array
     */
    private $config = [];

    /**
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = (string) $cacheDir;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @param string $cacheKey
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = (string) $cacheKey;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * {@inheritDoc}
     */
    public function compile(array $basePaths)
    {
        foreach ($basePaths as $basePath) {
            $this->compilePath($basePath);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function export()
    {
        $path = rtrim($this->cacheDir, '/') . '/' . trim($this->cacheKey, '/') . '.php';

        file_put_contents($path, var_export($this->config, true));
    }

    /**
     * @param  string $path
     * @return void
     */
    private function compilePath($path)
    {
        $directory = new RecursiveDirectoryIterator($path);
        $iterator  = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);

        /** @var \DirectoryIterator $file */
        foreach ($iterator as $file) {
            $file->isFile();
        }
    }
}
