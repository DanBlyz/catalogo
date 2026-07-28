<?php

test('public registration is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});
