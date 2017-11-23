<?php
/**
 * @author Klyachin Andrew <sfdiem5@gmail.com>
 */

namespace Orfogrammka\Support\Data;


class DataWrapper
{
    /**
     * @var array|float|int|string|null
     */
    private $data;

    /**
     * @param string|array|integer|float|boolean $data
     */
    public function __construct($data)
    {
        if (!is_scalar($data) && !is_array($data) && !is_null($data)) {
            throw new \InvalidArgumentException(
                sprintf("Input data is not valid: %s", serialize($data))
            );
        }

        $this->data = $data;
    }

    /**
     * @param string     $key
     * @param null|mixed $default
     *
     * @return DataWrapper
     */
    public function get($key, $default = null)
    {
        if (!is_array($this->data)) {
            throw new \LogicException("The internal data is not array. Key: {$key}");
        }

        if (array_key_exists($key, $this->data)) {
            return new self($this->data[$key]);
        } else {
            return new self($default);
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return is_array($this->data) && array_key_exists($key, $this->data);
    }

    /**
     * @return int
     */
    public function toInteger()
    {
        return (int)$this->data;
    }

    /**
     * @return float
     */
    public function toFloat()
    {
        return (float)$this->data;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return (string)$this->data;
    }

    /**
     * @return bool
     */
    public function toBoolean()
    {
        return (boolean)$this->data;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return (array)$this->data;
    }
}