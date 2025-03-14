<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials", "true");

require_once __DIR__ . '/../services/UserService.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::set('user_service', new UserService());

Flight::group('/users', function() {

    Flight::route('POST /add', function() {
        $data = Flight::request()->data->getData();

        if (!isset($data['email']) || !isset($data['password']) || !isset($data['repeat_password_signup'])) {
            Flight::halt(400, 'Email, password and repeat password are required.');
        }

        if (trim($data['email']) == "" || trim($data['password']) == "" || trim($data['repeat_password_signup']) == "" || trim($data['address']) == "" ) {
            Flight::halt(400, 'Email, password, repeat password, and address cannot be empty.');
        }

        if ($data['password'] !== $data['repeat_password_signup']) {
            Flight::halt(400, 'Password and repeat password do not match');
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['repeat_password_signup']);

        $data['role_id'] = 1;

        $user = Flight::get('user_service')->add_user($data);

        Flight::json(
            $user
        );
    });

    Flight::route('GET /user', function () {
        $body = Flight::request()->query;
        $user_service = new UserService();
        $user = $user_service->get_user_by_id(12);
        Flight::json($user, 200);
    });

    Flight::route('GET /current', function() {
        $current_user_id = Flight::get('user');
        $user = Flight::get('user_service')->get_user_by_id($current_user_id);
        Flight::json(
            $user
        );
    });

});