<?php
/*
 * Copyright (c) 2023. Wepesi inc.
 */

namespace Example\Controller;


class UserController
{
    /**
     * @var array[]
     */
    private array $users;

    function __construct(){
        $this->users = [
            ['id' => 1, 'name' => 'alfa'],
            ['id' => 2, 'name' => 'beta'],
            ['id' => 3, 'name' => 'cygma'],
        ];
    }
    public function get_users(){
        print_r($this->users);
    }
    private function filterUserByID(int $id){
        return array_filter($this->users, function ($user) use ($id) {
            return $user['id'] == (int)$id;
        });
    }
    public function get_user_detail($id){
        $detail = $this->filterUserByID($id);
        print_r($detail);
    }

    public function userExist(int $id){
        $user = count($this->filterUserByID($id));
        print($user>0?'Exist<br>'.PHP_EOL:'user not found<br>'.PHP_EOL);
    }
    public function delete_user($id){
//        $index = array_search($id, array_column($this->users,"id"));
        $detail = array_filter($this->users, function ($user) use ($id) {
            return $user['id'] != (int)$id;
        });
        print_r($detail);
    }
}