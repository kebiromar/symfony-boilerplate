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

namespace App\Tests\Controller\User;

use App\Fixtures\UserFixture;
use App\Test\TestCase;

final class LoginControllerTest extends TestCase
{
    protected array $fixtures = [
        UserFixture::class,
    ];

    public function testLogin(): void
    {
        $crawler = $this->browser->request('GET', '/user/login');

        self::assertResponseIsSuccessful();
        self::assertRouteSame('user_login');

        $title = $crawler->filter('h3.uk-card-title')->first();
        self::assertSame('Login', $title->text());

        $loginButton = $crawler->selectButton('Continue');
        $loginForm = $loginButton->form();

        $loginForm->setValues([
            'username' => 'jojo',
            'password' => '123456789',
        ]);

        $this->browser->submit($loginForm);

        self::assertResponseRedirects('/');
        $crawler = $this->browser->followRedirect();
        $username = $crawler->filter('.uk-navbar-right > ul:nth-child(1) > li:nth-child(1) > a:nth-child(1)');

        self::assertSame('jojo', $username->text());
    }

    public function testLoginWithUnregisteredUsername(): void
    {
        $crawler = $this->browser->request('GET', '/user/login');
        $loginButton = $crawler->selectButton('Continue');
        $loginForm = $loginButton->form();
        $loginForm->setValues([
            'username' => 'jojo2',
            'password' => '123456789',
        ]);

        $this->browser->submit($loginForm);

        self::assertResponseRedirects('/user/login');
        $crawler = $this->browser->followRedirect();
        $username = $crawler->filter('.uk-alert-danger');

        self::assertSame('Username could not be found.', $username->text());
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $crawler = $this->browser->request('GET', '/user/login');
        $loginButton = $crawler->selectButton('Continue');
        $loginForm = $loginButton->form();
        $loginForm->setValues([
            'username' => 'jojo',
            'password' => 'password',
        ]);

        $this->browser->submit($loginForm);

        self::assertResponseRedirects('/user/login');
        $crawler = $this->browser->followRedirect();
        $username = $crawler->filter('.uk-alert-danger');

        self::assertSame('Invalid credentials.', $username->text());
    }
}
