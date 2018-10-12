<?php

namespace App\Controllers;

use App\Models\Users;
use Respect\Validation\Validator;

class User extends Controller
{
    public function Get(\Base $f3, array $routes)
    {
        $users = \App\Models\Users::instance()->GetAll();

        $f3->set('View.Users', $users);

        echo \Template::instance()->render('user.html');
    }

    public function Post(\Base $f3, array $routes)
    {
        if ($f3->exists('POST', $post)) {
            $messages = [];

            try {
                Validator::key('password_confirm', Validator::stringType()->equals($post['password']))
                    ->assert($post);

                // Create Normal User
                Users::instance()->New($post['name'], $post['password']);
            } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
                $messages = $e->findMessages([
                    'name' => 'Name must contain at least one caracter',
                    'password' => 'Password must contain at least six caracters',
                    'password_confirm' => 'Password confirmation is different from password',
                ]);
            } catch (\App\Models\Ex_Duplicate $e) {
                $messages['name'] = 'A user with same name already exists';
            }

            $f3->set('View.Form', [
                'Values' => $post,
                'Validation' => $messages
            ]);
        }

        $this->Get($f3, $routes);

    }

    public function DelUser(\Base $f3, array $routes)
    {
        if ($f3->exists('POST', $post)) {
            $username = $post['username'];
            try {
                \App\Models\Users::instance()->DelByName($username);
            } catch (\App\Models\Ex_InvalidData $e) {
                $messages['name'] = "$username is not found";
            }
        }
        $f3->reroute('@user');
    }
}