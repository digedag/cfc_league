<?php
namespace System25\T3sports\Controller\Ajax;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\Response;

/**
 * Handling requirejs client requests.
 */
class AjaxTicker
{
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $tickerMessage = trim(strip_tags(\Tx_Rnbase_Utility_T3General::_POST('value')));
        $t3Time = (int) \Tx_Rnbase_Utility_T3General::_POST('t3time');
        $t3match = (int) \Tx_Rnbase_Utility_T3General::_POST('t3match');

        if (! is_object($GLOBALS['BE_USER'])) {
            return $this->createResponse('No BE user found!', 401);
        }

        if (! $tickerMessage || ! $t3match) {
            return $this->createJsonResponse('Invalid request!', 400);
        }
        $matchRecord = \Tx_Rnbase_Backend_Utility::getRecord('tx_cfcleague_games', $t3match);
        
        $record = [
            'comment' => $tickerMessage,
            'game' => $t3match,
            'type' => 100,
            'minute' => $t3Time,
            'pid' => $matchRecord['pid']
        ];
        $data = [
            'tx_cfcleague_match_notes' => [
                'NEW1' => $record
            ]
        ];
        $tce = \Tx_Rnbase_Database_Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();

        $GLOBALS['LANG']->includeLLFile('EXT:cfc_league/mod1/locallang.xml');
        return $this->createResponse($GLOBALS['LANG']->getLL('msg_sendInstant'), 200);
    }

    /**
     * @param array|null $configuration
     * @param int $statusCode
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
