<?php

it('envoie un visiteur non connecté vers la connexion', function () {
    $this->get('/')->assertRedirect(route('login'));
});

it('envoie un utilisateur connecté vers son tableau de bord', function () {
    $user = App\Models\User::factory()->create();

    $this->actingAs($user)->get('/')->assertRedirect(route('dashboard'));
});
