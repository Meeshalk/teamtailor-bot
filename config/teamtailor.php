<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Teamtailor data
    |--------------------------------------------------------------------------
    |
    |
    */

    'keywords' => [
      'swed' => ['jobb', 'arbete', 'arbeta', 'arbeta', 'fungera', 'jobba', 'använda', 'låta arbeta', 'jobba här', 'manövrera', 'verk', 'rekrytera', 'söker du jobb?', 'rekrytering', 'karriär', 'karriärsida', 'utnämning', 'karriärsajt', 'jobba hos oss', 'teamtailor'],
      'eng' => ['job', 'work', 'jobs', 'jobba', 'recruit', 'karriar', 'recruitment', 'career', 'careers', 'career page', 'position', 'positions', 'appointment', 'looking for job?', 'career site', 'work with us', 'teamtailor'],
      'jobPage' => ['jobb', 'jobs', 'job', 'jobba', 'career', 'karriar', 'arbete', 'arbeta', 'använda', 'verk', 'karriär', 'work', 'recruit', 'recruitment'],
      'isTt' => ['karriärsida från teamtailor', 'career site by teamtailor', 'Karriärsida från Teamtailor', 'Career Site By Teamtailor', 'Career site by Teamtailor'],
      'templateSite' => ['example.com', 'domain.com'],
      'excludeKnown' => ['godaddy', 'hostgator', 'youtube', 'facebook', 'twitter', 'google', 'instagram', 'linkedin', 'login', 'logout', 'signin', 'signup'],
      'exclude' => ['godaddy', 'hostgator', 'youtube', 'facebook', '#', 'twitter', 'google', 'instagram', 'linkedin', 'login', 'logout', 'signin', 'signup', 'privat', 'private', 'campaign', 'search', 'logga', 'bli medlem', 'medium', 'demo', '\+', '\&', 'alla', 'all', 'tagg', 'tag', 'hos', 'domain', 'buy', 'sell', 'order']
    ],

    'curl' => [
      'errors' => [6, 404]
    ],

    'patterns' => [
      'relative_link' => '/^\/(?<relative_link>.+)$/',
      'full_url' => '/^(?<full_url>(https?:\/\/|www\.|https?:\/\/www\.|https?:\/\/).*)$/',
      'domain' => '/^(?<protocol>https?:\/\/)?(?<domain>[a-z0-9.-]+)(?<resource>\/.*)?/',
      'test' => '/^(?<protocol>https?:\/\/)?(?<domain>(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63})(?<hyphne>\/.*)?/',
      'pattTemp' => '/.*(?<match>##).*/i'
    ]

];
