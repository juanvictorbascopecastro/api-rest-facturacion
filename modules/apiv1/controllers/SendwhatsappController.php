<?php

namespace app\modules\apiv1\controllers;

use Yii;
use yii\web\Controller;
use app\modules\apiv1\models\Whatsapp;

class SendwhatsappController extends Controller
{
    public $enableCsrfValidation = false;
    private $tokenWhatsapp;
    private $urlWhatsapp;

    public function __construct($id, $module, $config = [])
    {
        $whatsappParams = Yii::$app->params['whatsapp'];
        $this->tokenWhatsapp = $whatsappParams['token'];
        $this->urlWhatsapp = $whatsappParams['url'];

        parent::__construct($id, $module, $config);
    }

    /**
     * Enviar mensajes de plantilla de WhatsApp.
     * 
     * @return array
     */
    public function actionIndex()
    {
        $whatsappModel = new Whatsapp();

        if ($whatsappModel->load(Yii::$app->request->post(), '') && $whatsappModel->validate()) {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $whatsappModel->to,
                "type" => "template",
                "template" => [
                    "name" => 'hello_world',
                    "language" => [
                        "code" => 'en_US'
                    ]
                ]
            ];

            return $this->sendRequest($data);
        } else {
            return $this->sendResponse([
                'statusCode' => 422,
                'message' => 'Datos de mensaje inválidos',
                'errors' => $whatsappModel->errors
            ]);
        }
    }

    public function actionFactura()
    {
        $whatsappModel = new Whatsapp();

        if ($whatsappModel->load(Yii::$app->request->post(), '') && $whatsappModel->validate()) {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $whatsappModel->to,
                "type" => "template",
                "template" => [
                    "name" => "factura",
                    "language" => [
                        "code" => "es"
                    ],
                    "components" => [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type" => "document",
                                    "document" => [
                                        "link" => "https://siatanexo.impuestos.gob.bo/images/archivos_tecnicos/archivos_apoyo/formatoGrafico.pdf",
                                        "filename" => "cod-2387283.pdf",
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => "12606658000"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "FACTURA: 12"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "35E9521AAF25B131708C6264741DFAE07E C8896CF83238B40E95CB8E74"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "Victor Bascope"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "340000"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "17"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "19/06/2024 15:38 PM"
                                ],
                                [
                                    "type" => "text",
                                    "text" => "200.00 Bs."
                                ],
                                [
                                    "type" => "text",
                                    "text" => "200.00 Bs."
                                ],
                            ]
                        ]
                    ]
                ]
            ];

            return $this->sendRequest($data);
        } else {
            return $this->sendResponse([
                'statusCode' => 422,
                'message' => 'Datos de mensaje inválidos',
                'errors' => $whatsappModel->errors
            ]);
        }
    }
    
    public function actionPrueba()
    {
        $whatsappModel = new Whatsapp();
    
        if ($whatsappModel->load(Yii::$app->request->post(), '') && $whatsappModel->validate()) {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $whatsappModel->to,
                "type" => "template",
                "template" => [
                    "name" => 'prueba_2',
                    "language" => [
                        "code" => 'es'
                    ],
                    "components" => [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => "ENTEC - SUCRE"
                                ]
                            ]
                        ],
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => "9302903"
                                ],
                            ]
                        ],
                        [
                            "type" => "button",
                            "sub_type" => "url",
                            "index" => "0",
                            "parameters" => [
                                // [
                                //     "type" => "text",
                                //     "text" => "Ver Factura"
                                // ],
                                [
                                    "type" => "payload",
                                    "payload" => "https://apirest.app.io.ioox.io/web/site/index"
                                ]
                            ],
                        ]
                        // "buttons" => [
                        //     [
                        //         "type" => "URL",
                        //         "text" => "Ver Factura",
                        //         "url" => "https://github.com/juanvictorbascopecastro"
                        //     ]
                        // ],
                    ]
                ]
            ];
    
            return $this->sendRequest($data);
        } else {
            return $this->sendResponse([
                'statusCode' => 422,
                'message' => 'Datos de mensaje inválidos',
                'errors' => $whatsappModel->errors
            ]);
        }
    }
    
    /**
     * Enviar mensajes de texto plano de WhatsApp.
     * 
     * @return array
     */
    public function actionText()
    {
        $whatsappModel = new Whatsapp();

        if ($whatsappModel->load(Yii::$app->request->post(), '') && $whatsappModel->validate()) {
            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $whatsappModel->to,
                "type" => "text",
                "text" => [
                    "body" => $whatsappModel->message
                ]
            ];

            return $this->sendRequest($data);
        } else {
            return $this->sendResponse([
                'statusCode' => 422,
                'message' => 'Datos de mensaje inválidos',
                'errors' => $whatsappModel->errors
            ]);
        }
    }

    /**
     * Envía una solicitud a la API de WhatsApp Business.
     * 
     * @param array $data
     * @return array
     */
    private function sendRequest($data)
    {
        $data_string = json_encode($data);

        try {
            $curl = curl_init($this->urlWhatsapp);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->tokenWhatsapp,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
            ]);

            $result = curl_exec($curl);
            $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $response = json_decode($result, true);

            if ($status_code == 200) {
                return $this->sendResponse([
                    'statusCode' => 200,
                    'message' => 'El mensaje ha sido enviado',
                    'data' => $response
                ]);
            } else {
                return $this->sendResponse([
                    'statusCode' => 500,
                    'message' => 'El mensaje no pudo ser enviado',
                    'errors' => $response
                ]);
            }
        } catch (\Exception $e) {
            return $this->sendResponse([
                'statusCode' => 500,
                'message' => "El mensaje no pudo ser enviado. Error: {$e->getMessage()}"
            ]);
        }
    }

    /**
     * Envía una respuesta JSON.
     * 
     * @param array $response
     * @return array
     */
    protected function sendResponse($response)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = $response['statusCode'];

        $responseData = ['message' => $response['message']];

        if (isset($response['name'])) {
            $responseData['name'] = $response['name'];
        }

        if (isset($response['errors'])) {
            $responseData['errors'] = $response['errors'];
        }

        if (isset($response['data'])) {
            $responseData['data'] = $response['data'];
        }

        return $responseData;
    }
}
