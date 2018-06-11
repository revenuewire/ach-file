<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-11
 * Time: 09:49
 */

namespace RW\ACH;


abstract class ComponentCollection
{
    /** @var bool */
    protected $isOpen = true;
    /** @var FileComponent */
    protected $headerRecord;
    /** @var FileComponent[] */
    protected $collection = [];
    /** @var FileComponent */
    protected $controlRecord;

    /**
     * Batch constructor.
     *
     * @param FileComponent $headerRecord
     */
    public function __construct($headerRecord)
    {
        $this->headerRecord = $headerRecord;
    }

    protected abstract function getControlRecord(): FileComponent;

    public abstract function getBlockCount(): int;

    /**
     * @param EntryDetailRecord $component
     * @return ComponentCollection for clean method chaining.
     */
    public function addComponent($component): ComponentCollection
    {
        if (!$this->isOpen) {
            throw new \BadMethodCallException('Unable to add entries to a closed collection');
        }

        $this->collection[] = $component;

        return $this;
    }

    /**
     * Finalize the batch and generate the Batch Control Record
     *
     * @return ComponentCollection for clean method chaining
     */
    public function close(): ComponentCollection
    {
        $this->isOpen = false;
        $this->controlRecord = $this->getControlRecord();

        return $this;
    }

    public function toString()
    {
        if ($this->isOpen) {
            throw new \BadMethodCallException('Unable to convert an open batch to a string');
        }

        $content = "{$this->headerRecord->toString()}\n";
        foreach ($this->collection as $component) {
            $content .= "{$component->toString()}\n";
        }
        // Don't add an extra new line here, let the caller decide if it is required.
        $content .= "{$this->controlRecord->toString()}";

        return $content;
    }
}