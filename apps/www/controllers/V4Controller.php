<?php
/**
 * User: donny
 * Date: 2020/6/10
 * Time: 下午2:11
 */
namespace Qing\Api\Controllers;
use Kuga\Core\Api\ApiService;
use Kuga\Core\Api\Request\JwtRequest as KugaRequest;
use Kuga\Module\Acc\Model\AppModel;

class V4Controller extends ControllerBase{
    /**
     * 刷新app list 缓存
     * @return bool
     */
    public function refreshApplistCacheAction(){
        $appModel = new AppModel();
        $appModel->freshCache();
        return false;
    }
    /**
     * API服务网关地址，这种书写方式有利于nginx的rewrite分发
     * http://api.xxx.com/v4/gateway/console.user.login
     * @param string $method
     */
    public function gatewayAction($method=''){
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
        $this->response->setHeader("Access-Control-Allow-Headers","Content-Type,Access-Token-Type,Authorization,Version,Locale,Appkey");
        $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
        $requestMethod = $this->request->getMethod();
        if($requestMethod!='POST' && $requestMethod!='GET'){
            return;
        }
        $contentType = $this->request->getContentType();
        //post json格式时
        if(preg_match('/application\/json/',$contentType)){
            $requestData = file_get_contents('php://input');
            $requestData = json_decode($requestData,true);
            $requestData = \Qing\Lib\Utils::objectToArray($requestData);
        }else{
            //post 表单格式时
            $requestData = $this->request->getPost();
        }
        $headers = $this->request->getHeaders();
        //$locale = $this->request->get('locale','string','zh_CN');
        $locale = isset($headers['locale'])?$headers['locale']:'zh_CN';
        if($locale=='zh'){
            $locale='zh_CN';
        }
        $this->getDI()->getShared('translator')->setLocale(LC_MESSAGES, $locale,$this->config->system->charset);
        $requestObject = new KugaRequest($requestData);
        $requestObject->setOrigRequest($this->request);
        $requestObject->setMethod($method);
        $requestObject->setHeaders($headers);
        $requestObject->setDI($this->getDI());
        ApiService::setDi($this->getDI());
        ApiService::initApiJsonConfigFile(QING_ROOT_PATH.'/config/api/api.json');
        $result = ApiService::response($requestObject);
        switch($requestObject->getFormat()){
            case 'text':
                $this->response->setContent($result);
                break;
            case 'json':
            default:
                $this->setJsonResponse($result);
        }
    }
}
