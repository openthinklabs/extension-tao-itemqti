<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
namespace oat\taoQtiItem\controller;

use oat\taoQtiItem\controller\QtiPreview;
use oat\taoQtiItem\helpers\QtiFile;
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\model\qti\Item;
use \taoItems_actions_ItemPreview;
use \tao_helpers_Uri;
use \core_kernel_classes_Resource;
use \common_Exception;
use \taoQtiCommon_helpers_VariableFiller;
use \common_Logger;
use \taoQtiCommon_helpers_ResultTransmissionException;
use \taoQtiCommon_helpers_StateOutput;
use \common_ext_ExtensionsManager;

use qtism\runtime\common\State;
use qtism\runtime\tests\AssessmentItemSession;
use qtism\runtime\tests\AssessmentItemSessionException;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\StorageException;

/**
 * Qti Item Runner Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoQTI
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class QtiPreview extends taoItems_actions_ItemPreview
{

    public function getPreviewUrl($item, $options = array()){
        $code = base64_encode($item->getUri());
        return _url('render/'.$code.'/index.php', 'QtiPreview', 'taoQtiItem', $options);
    }

    public function submitResponses(){

        $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('itemUri'));

        if(!empty($itemUri)){
            $this->processResponses(new core_kernel_classes_Resource($itemUri), $this->getPostedResponses());
        }else{
            throw new common_Exception('missing required itemUri');
        }
    }

    protected function getPostedResponses(){
        return $this->hasRequestParameter("responseVariables") ? $this->getRequestParameter("responseVariables") : array();
    }

    /**
     * Item's ResponseProcessing.
     *
     * @param core_kernel_classes_Resource $item The Item you want to apply ResponseProcessing.
     * @param array $responses Client-side responses.
     * @throws RuntimeException If an error occurs while processing responses or transmitting results
     */
    protected function processResponses(core_kernel_classes_Resource $item, array $responses){
        try{
            $qtiXmlFilePath = QtiFile::getQtiFilePath($item);
            $qtiXmlDoc = new XmlDocument();
            $qtiXmlDoc->load($qtiXmlFilePath);
        }catch(StorageException $e){
            $msg = "An error occured while loading QTI-XML file at expected location '${qtiXmlFilePath}'.";
            throw new RuntimeException($msg, 0, $e);
        }

        $itemSession = new AssessmentItemSession($qtiXmlDoc->getDocumentComponent());
        $itemSession->beginItemSession();

        $variables = array();

        // Convert client-side data as QtiSm Runtime Variables.
        foreach($responses as $identifier => $response){
            $filler = new taoQtiCommon_helpers_VariableFiller($qtiXmlDoc->getDocumentComponent());
            try{
                $variables[] = $filler->fill($identifier, $response);
            }catch(OutOfRangeException $e){
                // A variable value could not be converted, ignore it.
                // Developer's note: QTI Pairs with a single identifier (missing second identifier of the pair) are transmitted as an array of length 1,
                // this might cause problem. Such "broken" pairs are simply ignored.
                common_Logger::d("Client-side value for variable '${identifier}' is ignored due to data malformation.");
            }
        }

        try{
            $itemSession->beginAttempt();
            $itemSession->endAttempt(new State($variables));

            // Return the item session state to the client-side.
            echo json_encode(array('success' => true, 'displayFeedback' => true, 'itemSession' => self::buildOutcomeResponse($itemSession)));
        }catch(AssessmentItemSessionException $e){
            $msg = "An error occured while processing the responses.";
            throw new RuntimeException($msg, 0, $e);
        }catch(taoQtiCommon_helpers_ResultTransmissionException $e){
            $msg = "An error occured while transmitting variable '${identifier}' to the target Result Server.";
            throw new RuntimeException($msg, 0, $e);
        }
    }

    /**
     * Get the ResultServer API call to be used by the item.
     *
     * @return string A string representing JavaScript instructions.
     */
    protected function getResultServer(){
        $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
        return array(
            'module'    => 'taoQTI/QtiPreviewResultServerApi',
            'endpoint'  => ROOT_URL . 'taoQTI/QtiPreview/',
            'params'    => $itemUri
        );
    }


    protected static function buildOutcomeResponse(AssessmentItemSession $itemSession){
        $stateOutput = new taoQtiCommon_helpers_StateOutput();

        foreach($itemSession->getOutcomeVariables(false) as $var){
            $stateOutput->addVariable($var);
        }

        $output = $stateOutput->getOutput();
        return $output;
    }

    protected function getRenderedItem($item){

        $qtiItem = Service::singleton()->getDataItemByRdfItem($item);
        $rubricBlocks = $this->getRubricBlocks($qtiItem);
        
        $qtifolder = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getConstant('BASE_WWW');
        $xhtml = $qtiItem->toXHTML(array(
            'contentVariableElements' => $rubricBlocks,
            'js' => array($qtifolder.'js/preview/qtiViewSelector.js'),
            'js_var' => array('view' => $this->getRequestView()),
            'css' => array($qtifolder.'css/preview/qtiViewSelector.css')
        ));

        return $xhtml;
    }

    protected function getRequestView(){
        $returnValue = 'candidate';
        if($this->hasRequestParameter('view')){
            $returnValue = tao_helpers_Uri::decode($this->getRequestParameter('view'));
        }
        return $returnValue;
    }

    protected function getRubricBlocks(Item $qtiItem){

        $returnValue = array();

        $currentView = $this->getRequestView();
        if(!is_null($qtiItem)){
            $rubricBlocks = $qtiItem->getRubricBlocks();
            foreach($rubricBlocks as $rubricBlock){
                $view = $rubricBlock->attr('view');
                if(!empty($view) && in_array($currentView, $view)){
                    $returnValue[$rubricBlock->getSerial()] = $rubricBlock->toArray();
                }
            }
        }

        return $returnValue;
    }

    public function getTemplateElements(Item $qtiItem){

        throw new common_Exception('qti template elments, to be implemented');
        //1. process templateRules
        //2. return the template values
    }

}