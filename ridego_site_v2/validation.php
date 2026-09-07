<?php

function validateRegisterInput($input)
{
    $errors = [];
    $data = [];

    $data['full_name'] = trim($input['full_name'] ?? '');
    $data['email'] = trim($input['email'] ?? '');
    $data['phone'] = trim($input['phone'] ?? '');
    $data['password'] = $input['password'] ?? '';

    if ($data['full_name'] === '') {
        $errors[] = 'Full name is required.';
    }

    if ($data['email'] === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }

    if ($data['phone'] === '') {
        $errors[] = 'Phone number is required.';
    }

    if ($data['password'] === '') {
        $errors[] = 'Password is required.';
    }

    return [
        'data' => $data,
        'errors' => $errors
    ];
}