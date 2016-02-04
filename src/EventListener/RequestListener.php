<?php
/*
 * This file is part of the KleijnWeb\SwaggerBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KleijnWeb\SwaggerBundle\EventListener;

use KleijnWeb\SwaggerBundle\Document\DocumentRepository;
use KleijnWeb\SwaggerBundle\Request\RequestProcessor;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class RequestListener
{
    /**
     * @var DocumentRepository
     */
    private $schemaRepository;

    /**
     * @var RequestProcessor
     */
    private $processor;

    /**
     * @param DocumentRepository $schemaRepository
     * @param RequestProcessor   $processor
     */
    public function __construct(DocumentRepository $schemaRepository, RequestProcessor $processor)
    {
        $this->schemaRepository = $schemaRepository;
        $this->processor = $processor;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        if (!$request->get('_definition')) {
            return;
        }
        if (!$request->get('_swagger_path')) {
            throw new \LogicException("Request does not contain reference to Swagger path");
        }
        $swaggerDocument = $this->schemaRepository->get($request->get('_definition'));

        $operationDefinition = $swaggerDocument
            ->getOperationDefinition(
                $request->get('_swagger_path'),
                $request->getMethod()
            );

        $this->processor->process(
            $request,
            $operationDefinition
        );
    }
}