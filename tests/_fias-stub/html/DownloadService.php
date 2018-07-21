<?php

header("Content-Type: text/xml; charset=utf-8");

if (isset($_REQUEST['WSDL'])) {


    header("Content-Type: text/xml; charset=utf-8");

    echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

    echo file_get_contents('wsdl.xml');

} else {

    ini_set("soap.wsdl_cache_enabled", 0);


    class DownloadService
    {

        private $ver1 = [
            'VersionId'          => 1,
            'TextVersion'        => '1',
            'FiasCompleteDbfUrl' => '',
            'FiasCompleteXmlUrl' => 'http://fias-stub/ver1-full.rar',
            'FiasDeltaDbfUrl'    => '',
            'FiasDeltaXmlUrl'    => 'http://fias-stub/ver1-delta.rar',
            'Kladr4ArjUrl'       => '',
            'Kladr47ZUrl'        => ''
        ];

        private $ver2 = [
            'VersionId'          => 2,
            'TextVersion'        => '2',
            'FiasCompleteDbfUrl' => '',
            'FiasCompleteXmlUrl' => 'http://fias-stub/ver2-full.rar',
            'FiasDeltaDbfUrl'    => '',
            'FiasDeltaXmlUrl'    => 'http://fias-stub/ver2-delta.rar',
            'Kladr4ArjUrl'       => '',
            'Kladr47ZUrl'        => ''
        ];

        private $ver3 = [
            'VersionId'          => 3,
            'TextVersion'        => '3',
            'FiasCompleteDbfUrl' => '',
            'FiasCompleteXmlUrl' => 'http://fias-stub/ver3-full.rar',
            'FiasDeltaDbfUrl'    => '',
            'FiasDeltaXmlUrl'    => 'http://fias-stub/ver3-delta.rar',
            'Kladr4ArjUrl'       => '',
            'Kladr47ZUrl'        => ''
        ];


        public function GetLastDownloadFileInfo()
        {

            return [
                'GetLastDownloadFileInfoResult' => $this->ver3
            ];

        }


        public function GetAllDownloadFileInfo()
        {
            return [
                'GetAllDownloadFileInfoResult' => [
                    $this->ver1,
                    $this->ver2,
                    $this->ver3
                ]
            ];
        }

    }


    $server = new SoapServer('http://fias-stub/wsdl.xml');
    $server->setClass(DownloadService::class);
    $server->handle();

}


