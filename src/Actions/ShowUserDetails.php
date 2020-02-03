<?php

namespace App\Actions;

use App\Domain\Services\SerializerService;
use App\Repository\UserRepository;
use App\Responder\JsonResponder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowUserDetails
 * @package App\Actions
 *
 * @Route("api/customers/{idCustomer}/users/{idUser}", name="api_show_users_details", methods={"GET"})
 */
final class ShowUserDetails
{

    /** @var UserRepository */
    protected $userRepo;

    /** @var SerializerService */
    protected $serializer;

    /**
     * ShowUserDetails constructor.
     * @param UserRepository $userRepo
     * @param SerializerService $serializer
     */
    public function __construct(UserRepository $userRepo, SerializerService $serializer)
    {
        $this->userRepo = $userRepo;
        $this->serializer = $serializer;
    }

    public function __invoke(JsonResponder $responder, int $idCustomer, int $idUser)
    {
        $user = $this->userRepo->findOneBy(
            [
                'customer' => $idCustomer,
                'id' => $idUser
            ]
        );
        $data = $this->serializer->serializer($user, ['groups' => ['showUserDetails']]);

        return $responder($data, Response::HTTP_OK);
    }
}