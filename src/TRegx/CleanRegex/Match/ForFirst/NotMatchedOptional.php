<?php
namespace TRegx\CleanRegex\Match\ForFirst;

use TRegx\CleanRegex\Exception\CleanRegex\SubjectNotMatchedException;
use TRegx\CleanRegex\Internal\UnknownSignatureExceptionFactory;
use TRegx\CleanRegex\Match\Details\NotMatched;
use Throwable;

class NotMatchedOptional implements Optional
{
    /** @var array */
    private $matches;
    /** @var string */
    private $subject;

    public function __construct(array $matches, string $subject)
    {
        $this->matches = $matches;
        $this->subject = $subject;
    }

    /**
     * @param string $exceptionClassName
     * @throws Throwable
     */
    public function orThrow(string $exceptionClassName = SubjectNotMatchedException::class)
    {
        $exception = $this->getException($exceptionClassName);
        throw $exception;
    }

    private function getException(string $exceptionClassName): Throwable
    {
        return (new UnknownSignatureExceptionFactory($exceptionClassName))->create($this->subject);
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function orReturn($default)
    {
        return $default;
    }

    /**
     * @param callable $producer
     * @return mixed
     */
    public function orElse(callable $producer)
    {
        return call_user_func($producer, new NotMatched($this->matches, $this->subject));
    }
}