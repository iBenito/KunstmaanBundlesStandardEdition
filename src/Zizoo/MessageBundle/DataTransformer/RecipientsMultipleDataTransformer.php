<?php
namespace Zizoo\MessageBundle\DataTransformer;

use Zizoo\UserBundle\Form\DataTransformer\UserToUsernameTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Transforms collection of UserInterface into strings separated with coma
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class RecipientsMultipleDataTransformer implements DataTransformerInterface
{
    /**
     * @var UserToUsernameTransformer
     */
    private $userToUsernameTransformer;

    /**
     * @param UserToUsernameTransformer $userToUsernameTransformer
     */
    public function __construct(UserToUsernameTransformer $userToUsernameTransformer)
    {
        $this->userToUsernameTransformer = $userToUsernameTransformer;
    }

    /**
     *
     * @param Collection $recipients
     *
     * @return string
     */
    public function transform($recipients)
    {
        if ($recipients->count() == 0) {
            return array();
        }

        $usernames = array();

        foreach ($recipients as $recipient) {
            $usernames[] = $this->userToUsernameTransformer->transform($recipient);
        }

        return $usernames;
    }

    /**
     * Transforms an array of recipient strings into an array of UserInterface
     *
     * @param array $usernames
     *
     * @throws UnexpectedTypeException
     * @throws TransformationFailedException
     * @return Collection $recipients
     */
    public function reverseTransform($usernames)
    {

        if (count($usernames) == 0) {
            return "";
        }

        $users = array();
        foreach ($usernames as $username) {
            $users[] = $this->userToUsernameTransformer->reverseTransform($username);
        }

        return $users;
    }
}