<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 17.08.20
 * Time: 0:56
 */

namespace App\Heplers;


use App\Entity\NewsEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class FrontendResponse
{
    protected $serializer;
    protected $message;
    protected $code;

    protected $page;

    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }

    public function setList(?array $repositoryResult, int $page) {
        $this->code = Response::HTTP_NOT_FOUND;
        $this->message = [
            'status' => Response::HTTP_NOT_FOUND,
            'page' => $page,
            'articles' => []
        ];
        if(!empty($repositoryResult)) {
            $this->message['status'] = Response::HTTP_OK;
            $this->message['page'] = $page;
            $this->message['articles'] = $repositoryResult;
        }
        return $this;
    }

    public function setEntityRresponse(?NewsEntity $entity) {
        $this->code = Response::HTTP_NOT_FOUND;
        $this->message = [
            'status' => Response::HTTP_NOT_FOUND,
            'article' => 'Статья не найдена'
        ];
        if(!empty($entity)) {
            $this->message = [
                'status' => Response::HTTP_OK,
                'article' => $entity
            ];
            $this->code = Response::HTTP_OK;
        }
        return $this;
    }

    public function flush() : JsonResponse {
        $json = $this->serializer->serialize($this->message, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ]));
        return new JsonResponse($json, $this->code, [], true);
    }

}