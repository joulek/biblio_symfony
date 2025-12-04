<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // Vérifie si l'attribut est lié à l'administration
        return strpos($attribute, 'ADMIN_') === 0;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // L'utilisateur doit être connecté
        if (!$user instanceof User) {
            return false;
        }

        // Vérifie si l'utilisateur a le rôle ADMIN
        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
