<?php


class Validator
{
    public function valid_user_data(User $user): array
    {
        if($user->name=="" or $user->password=="")
            return ["status"=>"ERROR", "error"=>"Заполни поля"];
        if(preg_match("/[^a-z0-9._-]+/iu", $user->name))
            return ["status"=>"ERROR", "error"=>"В имени содержаться запрещенные символы"];
        if(strlen($user->password)<6)
            return ["status"=>"ERROR", "error"=>"Пароль слишком короткий"];
        return ["status"=>"OK"];
    }
}