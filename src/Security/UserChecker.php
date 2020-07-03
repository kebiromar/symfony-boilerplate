<?php

/*
 * This file is part of Sanyu.
 *
 * (c) Saif Eddin Gmati
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Security;

use App\Entity\Suspension;
use App\Entity\User;
use App\Exception\UserSuspendedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isSuspended()) {
            /** @var Suspension */
            $suspension = $user->getSuspensions()->last();

            throw UserSuspendedException::create($user, $suspension);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
    }
}
