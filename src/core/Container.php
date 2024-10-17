<?php

namespace src\core;

use Exception;

interface ContainerInterface
{
    public function bind(string $key, callable $value);
    public function get(string $key);
}

/**
 * Dependency injection container
 */
class Container implements ContainerInterface
{
    // Maps class names to factory functions (lazy initialization)
    private $bindings = [];

    // Maps class names to singleton instances
    private $instances = [];

    /**
     * Register a factory function for a class
     */
    public function bind(string $key, callable $factory): void
    {
        if (!is_callable($factory)) {
            throw new Exception('factory must be a callable factory function');
        }

        $this->bindings[$key] = $factory;
    }

    /**
     * Get an instance of a class (singleton)
     */
    public function get(string $key)
    {
        // If instance is already created, return it
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        // If factory is not registered, throw an exception
        if (!isset($this->bindings[$key])) {
            throw new Exception("Factory function not found (check if $key class is registered in the container)");
        }

        // Call the factory function to create an instance
        $instance = $this->bindings[$key]($this);
        $this->instances[$key] = $instance;

        return $instance;
    }
}
