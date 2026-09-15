<?php

function validateRegisterInput($input)
{
    $errors = [];
    $data = [];

    $data['full_name'] = trim($input['full_name'] ?? '');
    $data['email'] = trim($input['email'] ?? '');
    $data['phone'] = trim($input['phone'] ?? '');
    $data['password'] = $input['password'] ?? '';
    $confirm_password = $input['confirm_password'] ?? '';


    /* FULL NAME */
    if ($data['full_name'] === '') {
        $errors[] = 'Full name is required.';
    }


    /* EMAIL */
    if ($data['email'] === '') {

        $errors[] = 'Email is required.';

    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {

        $errors[] = 'Invalid email address.';
    }


    /* PHONE NUMBER */
    if ($data['phone'] === '') {

        $errors[] = 'Phone number is required.';

    } elseif (!preg_match('/^09[0-9]{9}$/', $data['phone'])) {

        $errors[] = 'Phone number must start with 09 and contain exactly 11 digits.';
    }


    /* PASSWORD */
    if ($data['password'] === '') {

        $errors[] = 'Password is required.';

    } elseif (
        strlen($data['password']) < 8 ||
        !preg_match('/[A-Z]/', $data['password']) ||
        !preg_match('/[a-z]/', $data['password']) ||
        !preg_match('/[0-9]/', $data['password']) ||
        !preg_match('/[^A-Za-z0-9]/', $data['password'])
    ) {

        $errors[] = 'Password must be at least 8 characters and contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 symbol.';
    }


    /* CONFIRM PASSWORD */
    if ($confirm_password === '') {

        $errors[] = 'Please confirm your password.';

    } elseif ($data['password'] !== $confirm_password) {

        $errors[] = 'Passwords do not match.';
    }


    return [
        'data' => $data,
        'errors' => $errors
    ];
}