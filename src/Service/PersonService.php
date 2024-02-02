<?php
/**
 * Created by PhpStorm.
 * User: samif
 * Date: 02/02/2024
 * Time: 02:17
 */

namespace App\Service;


class PersonService
{
    const MAX_AGE=150;

    public function checkAge(\DateTime $birthday):bool
    {

        $age = $birthday
            ->diff(new \DateTime('now'))
            ->y;
        return $age<self::MAX_AGE;
    }

}