<?php
/*
 * This file is part of the KleijnWeb\SwaggerBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KleijnWeb\SwaggerBundle\Response;

use KleijnWeb\SwaggerBundle\Document\ParameterRefBuilder;
use KleijnWeb\SwaggerBundle\Exception\InvalidParametersException;
use Ramsey\VndError\VndError;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class VndValidationErrorFactory
{
    const DEFAULT_MESSAGE = 'Input Validation Failure';

    /**
     * @var ParameterRefBuilder
     */
    private $refBuilder;

    /**
     * @param ParameterRefBuilder $refBuilder
     */
    public function __construct(ParameterRefBuilder $refBuilder)
    {
        $this->refBuilder = $refBuilder;
    }

    /**
     * @param Request                    $request
     * @param InvalidParametersException $exception
     * @param string|null                $logRef
     *
     * @return VndError
     */
    public function create(Request $request, InvalidParametersException $exception, $logRef = null)
    {
        $vndError = new VndError(self::DEFAULT_MESSAGE, $logRef);
        $vndError->addLink('help', '', ['title' => 'Api Specification']);
        $vndError->addLink('about', $request->getUri(), ['title' => 'Error Information']);

        foreach ($exception->getValidationErrors() as $errorSpec) {
            $vndError->addResource(
                $this->refBuilder->buildLink(
                    $request,
                    $errorSpec['property']
                ),
                new VndError($errorSpec['message'])
            );
        }

        return $vndError;
    }
}
