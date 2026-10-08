<?php

test('the home page redirects to the login page', function () {
    $this->get('/')
        ->assertRedirect(route('login'));
});

test('the login page loads successfully', function () {
    $this->get('/login')
        ->assertOk();
});

test('the application health endpoint responds successfully', function () {
    $this->get('/up')
        ->assertOk();
});
