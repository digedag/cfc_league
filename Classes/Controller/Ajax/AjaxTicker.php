<?php
namespace System25\T3sports\Controller\Ajax;


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

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
            $ajaxObj->addContent('message', 'No BE user found!');
            return;
        }
        
        if (! $tickerMessage || ! $t3match) {
            $ajaxObj->addContent('message', 'Invalid request!');
            return;
        }
        $matchRecord = \Tx_Rnbase_Backend_Utility::getRecord('tx_cfcleague_games', $t3match);
        
        $record = [
            'comment' => $tickerMessage,
            'game' => $t3match,
            'type' => 100,
            'minute' => $t3Time,
            'pid' => $matchRecord['pid']
        ];
        $data = array(
            'tx_cfcleague_match_notes' => array(
                'NEW1' => $record
            )
        );
        $tce = & \Tx_Rnbase_Database_Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();
        
        $GLOBALS['LANG']->includeLLFile('EXT:cfc_league/mod1/locallang.xml');
        $ajaxObj->addContent('message', $GLOBALS['LANG']->getLL('msg_sendInstant'));
        
    }
}
