<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{
    // Tu n'as rien besoin d'ajouter ici pour le moment.
    // L'entité hérite automatiquement des propriétés du bundle (token, username, valid).
}