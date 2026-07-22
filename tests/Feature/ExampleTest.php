<?php

test('the application redirects guest from root to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
