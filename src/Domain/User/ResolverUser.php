<?php

namespace App\Domain\User;

use App\Entity\Customer;
use App\Entity\User;
use App\Entity\UserAddress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ResolverUser
 * @package App\Domain\User
 */
final class ResolverUser
{

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var SerializerInterface  */
    protected $serializer;

    /**
     * ResolverUser constructor.
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     */
    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ) {
        $this->em = $em;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * @param UserDTO $dto
     * @param Customer $customer
     * @return User
     */
    public function save(UserDTO $dto, Customer $customer): User
    {
        $user = $this->createUser($dto, $customer);
        $userAddress = $this->createUserAddress($dto);

        $user->addAddress($userAddress);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param UserDTO $dto
     * @param Customer $customer
     * @return User
     */
    public function createUser(UserDTO $dto, Customer $customer): User
    {
        $user = new User();
        $user
            ->setFirstName($dto->getFirstName())
            ->setLastName($dto->getLastName())
            ->setEmail($dto->getEmail())
            ->setCustomer($customer)
        ;

        return $user;
    }

    /**
     * @param UserDTO $dto
     * @return UserAddress
     */
    public function createUserAddress(UserDTO $dto): UserAddress
    {
        $userAddress = new UserAddress();
        $userAddress
            ->setStreet($dto->getStreet())
            ->setCity($dto->getCity())
            ->setRegion($dto->getRegion())
            ->setPhoneNumber($dto->getPhoneNumber())
            ->setPostalCode($dto->getPostalCode())
        ;

        return $userAddress;
    }

    /**
     * @param string $data
     * @return UserDTO | array
     */
    public function createUserDTO(string $data): UserDTO
    {
        return $this->serializer->deserialize($data, UserDTO::class, "json");
    }
}
