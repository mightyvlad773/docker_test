<?php

namespace App\Controller;

use App\Entity\NewsEntity;
use App\Heplers\AdminResponse;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param AdminResponse $response
     * @return JsonResponse
     * @Route("/article", name="create_article", methods={"PUT"})
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, AdminResponse $response): JsonResponse
    {
        $entityForm = $serializer->deserialize($request->getContent(), NewsEntity::class, 'json');
        if(count($errors = $validator->validate($entityForm)) > 0) {
            $response->setConstraitError($errors, Response::HTTP_BAD_REQUEST);
        } else {
            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($entityForm);
            $eManager->flush();
            $response->setMessage("Новость #{$entityForm->getId()} добавлена", Response::HTTP_OK);
        }
        return $response->flush();
    }

    /**
     * @param $id
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param AdminResponse $response
     * @return JsonResponse
     * @Route("/article/{id}/edit", name="update_article", methods={"POST"})
     */
    public function update($id, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, AdminResponse $response): JsonResponse
    {
        $form = $serializer->deserialize($request->getContent(), NewsEntity::class, 'json');

        $eManager = $this->getDoctrine()->getManager();
        $article = $eManager->getRepository(NewsEntity::class)->find($id);

        if(!empty($article)) {

            if(!is_null($form->getTitle())) {
                $article->setTitle($form->getTitle());
            }
            if(!is_null($form->getShortDescription())) {
                $article->setShortDescription($form->getShortDescription());
            }
            if(!is_null($form->getDescription())) {
                $article->setDescription($form->getDescription());
            }
            if(!is_null($form->getPublishedAt())) {
                $article->setPublishedAt($form->getPublishedAt()->format('Y-m-d H:i:s') ?? null);
            }
            if(!is_null($form->getIsActive())) {
                $article->setIsActive($form->getIsActive());
            }
            if(!is_null($form->getIsHide())) {
                $article->setIsHide($form->getIsHide());
            }

            if(count($errors = $validator->validate($article)) > 0) {
                $response->setConstraitError($errors, Response::HTTP_BAD_REQUEST);
            } else {
                $eManager->persist($article);
                $eManager->flush();
                $response->setMessage("Новость #{$article->getId()} обновлена", Response::HTTP_OK);
            }
        } else {
            $response->setMessage("Новость #{$id} не найдена", Response::HTTP_NOT_FOUND);
        }

        return $response->flush();
    }

    /**
     * @param $id
     * @param AdminResponse $response
     * @return JsonResponse
     * @Route("/article/{id}", name="delete_article", methods={"DELETE"})
     */
    public function delete($id, AdminResponse $response): JsonResponse
    {
        $eManager = $this->getDoctrine()->getManager();
        $article = $eManager->getRepository(NewsEntity::class)->find($id);
        if(!empty($article)) {
           $articleId = $article->getId();
           $eManager->remove($article);
           $eManager->flush();
           $response->setMessage("Новость #{$articleId} удалена", Response::HTTP_OK);
        } else {
            $response->setMessage("Новость не найдена", Response::HTTP_NOT_FOUND);
        }
        return $response->flush();
    }
}
