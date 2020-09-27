<?php

trait AdminActions
{
    //checking if the current user is an admin or not.
    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
