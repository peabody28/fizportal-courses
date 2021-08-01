<?php


class Validator
{
    public function valid_user_data(User $user): array
    {
        if($user->email=="" or $user->name=="" or $user->password=="")
            return ["status"=>"ERROR", "error"=>"Заполни все поля"];
        if(preg_match("/[^a-z0-9._-]+/iu", $user->name))
            return ["status"=>"ERROR", "error"=>"В имени содержаться запрещенные символы"];

        $status = preg_match('/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u', $user->email);
        if(!$status)
            return ["status"=>"ERROR", "error"=>"Неверный формат email"];
        if(strlen($user->password)<6)
            return ["status"=>"ERROR", "error"=>"Пароль слишком короткий"];
        return ["status"=>"OK"];

    }
}