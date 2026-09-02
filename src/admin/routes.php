<?php
declare(strict_types=1);

/**
 * Admin routes. "METHOD /path" => [controller file in controllers/, function].
 *
 * Authentication and capability checks live in the handlers, not here, so that
 * a route can never be exposed by being added to this list alone.
 */

return [
    // Authentication
    'GET /login'   => ['auth', 'show_login'],
    'POST /login'  => ['auth', 'do_login'],
    'POST /logout' => ['auth', 'do_logout'],

    // Dashboard
    'GET /' => ['dashboard', 'show_dashboard'],

    // Inbox
    'GET /inbox'                    => ['inbox', 'index'],
    'GET /inbox/{id}'               => ['inbox', 'show'],
    'POST /inbox/{id}/assign'       => ['inbox', 'assign'],
    'POST /inbox/{id}/status'       => ['inbox', 'set_status'],
    'POST /inbox/{id}/note'         => ['inbox', 'add_note'],

    // Listings
    'GET /listings'             => ['listings', 'index'],
    'GET /listings/new'         => ['listings', 'create'],
    'POST /listings'            => ['listings', 'store'],
    'GET /listings/{id}'        => ['listings', 'edit'],
    'POST /listings/{id}'       => ['listings', 'update'],
    'POST /listings/{id}/state' => ['listings', 'set_state'],
    'POST /listings/{id}/images/attach'         => ['listings', 'attach_images'],
    'POST /listings/{id}/images/{media}/cover'  => ['listings', 'set_cover'],
    'POST /listings/{id}/images/{media}/remove' => ['listings', 'remove_image'],

    // Activity
    'GET /activity' => ['activity', 'index'],

    // Sections scheduled for later phases. Listed so the sidebar leads somewhere
    // honest instead of a 404; each renders the "not built yet" page until its
    // controller lands.
    'GET /pages'    => ['pages', 'index'],
    'GET /blog'     => ['blog', 'index'],
    'GET /careers'  => ['careers', 'index'],
    'GET /services' => ['services', 'index'],
    'GET /media'             => ['media', 'index'],
    'POST /media/upload'     => ['media', 'upload'],
    'GET /media/{id}'        => ['media', 'show'],
    'POST /media/{id}'       => ['media', 'update'],
    'POST /media/{id}/delete'=> ['media', 'destroy'],
    'GET /settings'  => ['settings', 'index'],
    'POST /settings' => ['settings', 'save'],
    'GET /users'    => ['users', 'index'],
];
