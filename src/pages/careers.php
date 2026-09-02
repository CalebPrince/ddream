<?php
declare(strict_types=1);

/** Careers page band order. Every band is editable in Page contents. */

section('careers-hero', ['openRoles' => count(data_set('careers'))]);
section('careers');
