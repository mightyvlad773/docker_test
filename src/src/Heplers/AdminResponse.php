<?php
namespace App\Heplers;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class AdminResponse
{

    protected $serializer;
    protected $message;
    protected $code;

    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }


    public function setConstraitError(ConstraintViolationListInterface $constraits, int $httpErrorCode = 400) : self {
        $this->message = ['status' => $httpErrorCode,
            'message' =>  call_user_func(function($constraits) {
                $listErrors = [];
                foreach($constraits as $error) {
                    $listErrors[] = $error->getMessage();
                }
                return $listErrors;
            }, $constraits)
        ];
        $this->code = $httpErrorCode;
        return $this;
    }

    public function setMessage(string $message, int $httpSuccessCode = 200) : self {
        $this->message = ['status' => $httpSuccessCode, 'message' => [$message]];
        $this->code = $httpSuccessCode;
        return $this;
    }

    public function flush() {
        $json = $this->serializer->serialize($this->message, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ]));
        return new JsonResponse($json, $this->code, [], true);
    }

}