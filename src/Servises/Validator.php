<?php

namespace App\Servises;

use App\Interfaces\DatabaseInterface;

class Validator
{

    public const VALIDATOR_EMAIL = "email";
    public const VALIDATOR_PASSWORD_CONFIRMED = "password_confirmation";
    public const VALIDATOR_NUMERIC = "numeric";
    public const VALIDATOR_MAX = "max";
    public const VALIDATOR_MIN = "min";
    public const VALIDATOR_REQUERED = "required";
    public const VALIDATOR_NOT_EMPTY = "not_empty";
    public const VALIDATOR_UNIQUE = "unique";

    public static function validate($data, $validators, DatabaseInterface $databaseInterface = null)
    {
        $required = [];
        $errors = [];
        foreach ($validators as $keyValidator => $validator) {
            $hadValue = false;
            $hadErrors = false;
            foreach ($data as $key => $value) {
                if ($key === $keyValidator) {
                    $hadValue = true;
                    if (in_array(self::VALIDATOR_NOT_EMPTY, $validator) && empty($value)) :
                        $errors[] = "$keyValidator must be not empty";
                        $hadErrors = true;
                    endif;
                    if (in_array(self::VALIDATOR_NUMERIC, $validator) && !is_numeric($value)) :
                        $errors[] = "$keyValidator must be numeric";
                        $hadErrors = true;
                    endif;
                    if (array_key_exists(self::VALIDATOR_MAX, $validator) && (!is_string($value) || strlen($value) > $validator[self::VALIDATOR_MAX])) :
                        $errors[] = "$keyValidator must be a string no longer than {$validator[self::VALIDATOR_MAX]} characters";
                        $hadErrors = true;
                    endif;
                    if (array_key_exists(self::VALIDATOR_MIN, $validator) && (!is_string($value) || strlen($value) < $validator[self::VALIDATOR_MIN])) :
                        $errors[] = "$keyValidator must be a string no shorter than {$validator[self::VALIDATOR_MIN]} characters";
                        $hadErrors = true;
                    endif;
                    if (in_array(self::VALIDATOR_EMAIL, $validator) && !preg_match("/^\S+@\S+\.\S+$/", $value)) :
                        $errors[] = "$keyValidator is not email";
                        $hadErrors = true;
                    endif;
                    if (
                        in_array(self::VALIDATOR_PASSWORD_CONFIRMED, $validator)
                        && (is_array($data['password']) || !($data['password'] === $value))
                    ) :
                        $errors[] = "$keyValidator is not equal password";
                        $hadErrors = true;
                    endif;
                    if (array_key_exists(self::VALIDATOR_UNIQUE, $validator)) {
                        $databaseInterface->connection();
                        $sql = "SELECT COUNT(*) 
                                FROM {$validator[self::VALIDATOR_UNIQUE][0]} 
                                WHERE {$validator[self::VALIDATOR_UNIQUE][1]} = '$value'";
                        $dbData = $databaseInterface->query($sql);
                        if ($dbData[0][0] > 0) :
                            $errors[] = "$keyValidator is already exists";
                            $hadErrors = true;
                        endif;
                    }



                    if (!$hadErrors)
                        $required[$key] = $value;
                    break;
                }
            }
            if (in_array(self::VALIDATOR_REQUERED, $validator) && !$hadValue)
                $errors[] = "$keyValidator is required";
        }


        if (!empty($errors)) Response::badRequest($errors);

        return $required;
    }
}
