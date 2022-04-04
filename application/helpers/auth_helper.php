<?php
Require 'authorization_helper.php';

function getAuthUser($token)
{
    return authorization::validateToken($token);
}