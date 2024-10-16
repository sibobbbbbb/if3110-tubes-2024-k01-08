<?php

namespace src\utils;

class Validator
{
    // Error fields
    private $errorFields = [];

    /**
     * Validate the data (associative array) against the rules
     */
    public function validate($data, $rules)
    {
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) {
                $this->addError($field, "$field is required");
                continue;
            }

            $value = $data[$field];
            foreach ($rule as $validation => $param) {
                // If doesn't receive param
                if (is_numeric($validation)) {
                    $validation = $param;
                    $param = null;
                }

                switch ($validation) {
                    case 'required':
                        if (empty($value)) {
                            // echo "in required";
                            $message = ucfirst("$field is required");
                            $this->addError($field, $message);
                        }
                        break;
                    case 'min':
                        if (strlen($value) < $param) {
                            // echo "in min";
                            $message = ucfirst("$field must be at least $param characters");
                            $this->addError($field, $message);
                        }
                        break;
                    case 'max':
                        if (strlen($value) > $param) {
                            // echo "in max";
                            $message = ucfirst("$field must be no more than $param characters");
                            $this->addError($field, $message);
                        }
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            // echo "in email";
                            $message = ucfirst("$field must be a valid email address");
                            $this->addError($field, $message);
                        }
                        break;
                        // Add more validation rules as needed
                }
            }
        }

        return empty($this->errorFields);
    }

    private function addError($field, $message)
    {
        $this->errorFields[$field][] = $message;
    }

    public function getErrorFields()
    {
        return $this->errorFields;
    }
}
