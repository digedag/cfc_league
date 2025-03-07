<?php

namespace System25\T3sports\Controller\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sys25\RnBase\Backend\Utility\BackendUtility;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\LanguageTool;
use Sys25\RnBase\Utility\TYPO3;
use TYPO3\CMS\Core\Http\Response;

/**
 * Handling requirejs client requests.
 */
class AjaxTicker
{
    private $languageTool;

    public function __construct(LanguageTool $languageTool)
    {
        $this->languageTool = $languageTool;
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $tickerMessage = trim(strip_tags($parsedBody['value'] ?? ''));
        $t3Time = (int) ($parsedBody['t3time'] ?? 0);
        $t3match = (int) ($parsedBody['t3match'] ?? 0);

        if (!is_object(TYPO3::getBEUser())) {
            return $this->createResponse('No BE user found!', 401);
        }

        if (!$tickerMessage || !$t3match) {
            return $this->createResponse('Invalid request!', 400);
        }
        $matchRecord = BackendUtility::getRecord('tx_cfcleague_games', $t3match);

        $record = [
            'comment' => $tickerMessage,
            'game' => $t3match,
            'type' => 100,
            'minute' => $t3Time,
            'pid' => $matchRecord['pid'],
        ];
        $data = [
            'tx_cfcleague_match_notes' => [
                'NEW1' => $record,
            ],
        ];
        $tce = Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();

        $response = $this->languageTool->sL('LLL:EXT:cfc_league/Resources/Private/Language/locallang.xlf:msg_sendInstant');

        return $this->createResponse($response, 200);
    }

    /**
     * @param array|null $configuration
     * @param int $statusCode
     *
     * @return Response
     */
    protected function createResponse($message, int $statusCode): Response
    {
        $response = (new Response())
            ->withStatus($statusCode)
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
        ;

        if (!empty($message)) {
            $response->getBody()->write($message);
            $response->getBody()->rewind();
        }

        return $response;
    }
}
