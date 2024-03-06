<?php

class Response
{
    protected $statusCode = 200;
    protected $headers = [];
    protected $body;

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function addHeader($header, $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function json($data)
    {
        $this->addHeader('Content-Type', 'application/json');
        $this->setBody(json_encode($data));
        return $this;
    }


    public function end()
    {
        $this->send();
    }


    public function send()
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $header => $value) {
            header("$header: $value");
        }

        // Convert the array to JSON
        // $jsonResponse = json_encode($result);
        // Set HTTP headers to indicate JSON response
        // header('Content-Type: application/json');
        // Output the JSON response
        // if ($result != null) {
        // echo $jsonResponse;

        if (!is_string($this->body)) {
            $this->setBody(json_encode($this->body));
        }

        echo $this->body;
        exit;
    }
}
