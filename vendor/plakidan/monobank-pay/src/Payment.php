<?php


namespace MonoPay;


class Payment extends RequestBuilder
{
    private \MonoPay\Client $client;

    public function __construct(\MonoPay\Client $client)
    {
        $this->client = $client;
    }

    /**
     * Створення рахунку
     * Створення рахунку для оплати
     * @param int $amount Сума оплати у мінімальних одиницях (копійки для гривні)
     * @param array $options Додаткові параметри (Див. посилання)
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @link https://api.monobank.ua/docs/acquiring.html#/paths/~1api~1merchant~1invoice~1create/post
     */
    public function create(int $amount, array $options=[]): array
    {
        if($amount < 1){
            throw new \Exception('Amount must be a natural number',500);
        }
        $options['amount']=$amount;
        $response = $this->client->getClient()->request('POST','/api/merchant/invoice/create',[
            \GuzzleHttp\RequestOptions::JSON => $options
        ]);

        return $this->getDataFromGuzzleResponse($response);
    }

    /**
     * Статус рахунку
     * Метод перевірки статусу рахунку при розсинхронізації з боку продавця або відсутності webHookUrl при створенні рахунку.
     * @param string $invoiceId ID рахунку
     * @link https://api.monobank.ua/docs/acquiring.html#/paths/~1api~1merchant~1invoice~1status?invoiceId=%7BinvoiceId%7D/get
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function info(string $invoiceId): array
    {
        $response = $this->client->getClient()->request('GET','/api/merchant/invoice/status',[
            \GuzzleHttp\RequestOptions::QUERY => [
                'invoiceId' => $invoiceId
            ]
        ]);

        return $this->getDataFromGuzzleResponse($response);
    }

    /**
     * Скасування оплати
     * Скасування успішної оплати рахунку
     * @param string $invoiceId ID рахунку
     * @param array $options Додаткові параметри (Див. посилання)
     * @link https://api.monobank.ua/docs/acquiring.html#/paths/~1api~1merchant~1invoice~1cancel/post
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function refund(string $invoiceId, array $options=[]): array
    {
        $options['invoiceId'] = $invoiceId;

        $response = $this->client->getClient()->request('POST','/api/merchant/invoice/cancel',[
            \GuzzleHttp\RequestOptions::JSON => $options
        ]);

        return $this->getDataFromGuzzleResponse($response);
    }

    /**
     * Інвалідація рахунку
     * Інвалідація рахунку, якщо за ним ще не було здіснено оплати
     * @param string $invoiceId ID рахунку
     * @link https://api.monobank.ua/docs/acquiring.html#/paths/~1api~1merchant~1invoice~1remove/post
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public  function cancel(string $invoiceId): array
    {
        $response = $this->client->getClient()->request('POST','/api/merchant/invoice/remove',[
            \GuzzleHttp\RequestOptions::JSON => [
                'invoiceId' => $invoiceId
            ]
        ]);
        return $this->getDataFromGuzzleResponse($response);
    }

    /**
     * Розширена інформація про успішну оплату
     * Дані про успішну оплату, якщо вона була здійснена
     * @param string $invoiceId Ідентифікатор рахунку
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     *@link https://api.monobank.ua/docs/acquiring.html#/paths/~1api~1merchant~1invoice~1payment-info?invoiceId=%7BinvoiceId%7D/get
     */
    public function successDetails(string $invoiceId): array
    {
        $response = $this->client->getClient()->request('GET','/api/merchant/invoice/payment-info',[
            \GuzzleHttp\RequestOptions::QUERY => [
                'invoiceId' => $invoiceId
            ]
        ]);

        return $this->getDataFromGuzzleResponse($response);
    }

    /**
     * Фіналізація суми холду
     * Фінальна сумма списання має бути нижчою або дорівнювати суммі холду
     * @param string $invoiceId Ідентифікатор рахунку
     * @param int|null $amount Сума у мінімальних одиницях, якщо бажаєте змінити сумму списання
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @link https://api.monobank.ua/docs/acquiring.html#/paths/~1api~1merchant~1invoice~1finalize/post
     */
    public function captureHold(string $invoiceId, int $amount = null): array
    {
        $body = [
            'invoiceId' => $invoiceId
        ];
        if(isset($amount)){
            $body['amount'] = $amount;
        }
        $response = $this->client->getClient()->request('POST','/api/merchant/invoice/finalize',[
            \GuzzleHttp\RequestOptions::JSON => $body
        ]);

        return $this->getDataFromGuzzleResponse($response);
    }

    /**
     * Виписка за період
     * Список платежів за вказаний період
     * @param int $fromTimestamp UTC Unix timestamp
     * @param int|null $toTimestamp UTC Unix timestamp
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @link https://api.monobank.ua/docs/acquiring.html#/paths/~1api~1merchant~1statement/get
     */
    public function items(int $fromTimestamp, int $toTimestamp=null): array
    {
        $query = [
            'from' => $fromTimestamp
        ];
        if(isset($toTimestamp)){
            $query['to'] = $toTimestamp;
        }
        $response = $this->client->getClient()->request('GET','/api/merchant/statement',[
            \GuzzleHttp\RequestOptions::QUERY => $query
        ]);

        $data = $this->getDataFromGuzzleResponse($response);
        return $data['list']??[];
    }

}