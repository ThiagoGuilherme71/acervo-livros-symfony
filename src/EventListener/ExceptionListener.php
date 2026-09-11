<?php

namespace App\EventListener;

use App\Exception\AssuntoDuplicadoException;
use App\Exception\AssuntoPossuiLivroVinculadoException;
use App\Exception\AutorDuplicadoException;
use App\Exception\AutorPossuiLivroVinculadoException;
use App\Exception\LivroSemAutorException;
use App\Exception\LivroValorInvalidoException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
class ExceptionListener
{
    private const MENSAGENS_POR_EXCEPTION = [
        AutorDuplicadoException::class => null,
        AssuntoDuplicadoException::class => null,
        AutorPossuiLivroVinculadoException::class => null,
        AssuntoPossuiLivroVinculadoException::class => null,
        LivroSemAutorException::class => null,
        LivroValorInvalidoException::class => null,
    ];

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $mensagem = $this->resolverMensagem($exception);

        if ($mensagem === null) {
            return;
        }

        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add('error', $mensagem);

        $event->setResponse(new RedirectResponse($this->router->generate('app_home')));
    }

    private function resolverMensagem(\Throwable $exception): ?string
    {
        if (array_key_exists($exception::class, self::MENSAGENS_POR_EXCEPTION)) {
            return $exception->getMessage();
        }

        if ($exception instanceof ForeignKeyConstraintViolationException) {
            return 'Não é possível concluir a operação: o registro possui vínculo com outro dado.';
        }

        if ($exception instanceof UniqueConstraintViolationException) {
            return 'Já existe um registro com esses dados.';
        }

        return null;
    }
}