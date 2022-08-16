<?php


namespace Wepesi\Controller;


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
    function get_users(){
        print_r($this->users);
    }

    function get_user_detail($id){
        $detail = array_filter($this->users,function($user) use ($id){
            return $user["id"] == (int)$id;
        });
        print_r($detail);
    }
    function delete_user($id){
//        $index = array_search($id, array_column($this->users,"id"));
        $detail = array_filter($this->users, function ($user) use ($id) {
            return $user['id'] != (int)$id;
        });
        print_r($detail);
    }
}