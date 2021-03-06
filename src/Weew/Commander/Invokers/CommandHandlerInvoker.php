<?php

namespace Weew\Commander\Invokers;

use Weew\Commander\Exceptions\InvalidCommandHandlerException;
use Weew\Commander\ICommandHandlerInvoker;
use Weew\Commander\IDefinition;

class CommandHandlerInvoker implements ICommandHandlerInvoker {
    /**
     * @param IDefinition $definition
     *
     * @return bool
     */
    public function supports(IDefinition $definition) {
        $handler = $definition->getHandler();

        if ($this->isValidHandler($handler)) {
            return true;
        }

        if ($this->isValidHandlerClass($handler)) {
            return true;
        }

        return false;
    }

    /**
     * @param IDefinition $definition
     * @param $command
     *
     * @return mixed
     * @throws InvalidCommandHandlerException
     */
    public function invoke(IDefinition $definition, $command) {
        $handler = $definition->getHandler();

        if ($this->isValidHandlerClass($handler)) {
            $handler = $this->createHandler($handler);
        }

        if ($this->isValidHandler($handler)) {
            return $this->invokeHandler($handler, $command);
        }

        throw new InvalidCommandHandlerException(s(
            'Handler "%s" must implement method "handle($command)".',
            is_string($handler) ? $handler : get_type($handler)
        ));
    }

    /**
     * @param $class
     *
     * @return mixed
     */
    protected function createHandler($class) {
        return new $class();
    }

    /**
     * @param $handler
     * @param $command
     *
     * @return mixed
     */
    protected function invokeHandler($handler, $command) {
        return $handler->handle($command);
    }

    /**
     * @param $handler
     *
     * @return bool
     */
    protected function isValidHandlerClass($handler) {
        return is_string($handler) &&
            class_exists($handler) &&
            method_exists($handler, 'handle');
    }

    /**
     * @param $handler
     *
     * @return bool
     */
    protected function isValidHandler($handler) {
        return is_object($handler) && method_exists($handler, 'handle');
    }
}
