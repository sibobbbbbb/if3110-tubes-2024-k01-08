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
            $fieldInMessage = ucfirst(str_replace("-", " ", $field));

            if (!isset($data[$field])) {
                $this->addError($field, ucfirst("$fieldInMessage is required"));
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
                            $message = ucfirst("$fieldInMessage is required");
                            $this->addError($field, $message);
                        }
                        break;
                    case 'min':
                        if (strlen($value) < $param) {
                            // echo "in min";
                            $message = ucfirst("$fieldInMessage must be at least $param characters");
                            $this->addError($field, $message);
                        }
                        break;
                    case 'max':
                        if (strlen($value) > $param) {
                            // echo "in max";
                            $message = ucfirst("$fieldInMessage must be no more than $param characters");
                            $this->addError($field, $message);
                        }
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            // echo "in email";
                            $message = ucfirst("$fieldInMessage$ must be a valid email address");
                            $this->addError($field, $message);
                        }
                        break;
                    case 'enum':
                        if (!in_array($value, $param)) {
                            // echo "in enum";
                            $message = ucfirst("$fieldInMessage must be one of " . implode(', ', $param));
                            $this->addError($field, $message);
                        }
                        break;
                    case 'requiredFile':
                        foreach ($value['error'] as $key => $error) {
                            if ($error == 4) {
                                $message = ucfirst("$fieldInMessage is required");
                                $this->addError($field, $message);
                            }
                        }
                        break;
                    case 'file':
                        // allowed types
                        if (!in_array($value['type'], $param['allowedTypes'])) {
                            $message = ucfirst("$fieldInMessage must be one of " . implode(', ', $param['allowedTypes']));
                            $this->addError($field, $message);
                        }
                        // max size
                        if ($value['size'] > $param['maxSize']) {
                            $message = ucfirst("$fieldInMessage must be no more than " . $param['maxSize'] . " bytes");
                            $this->addError($field, $message);
                        }
                        break;
                    case 'files':
                        // allowed types
                        foreach ($value['type'] as $key => $type) {
                            if (!in_array($type, $param['allowedTypes'])) {
                                $message = ucfirst("$fieldInMessage must be one of " . implode(', ', $param['allowedTypes']));
                                $this->addError($field, $message);
                            }
                        }
                        // max size
                        foreach ($value['size'] as $key => $size) {
                            if ($size > $param['maxSize']) {
                                $sizeMb = floor($param['maxSize'] / (1024 * 1024));
                                $message = ucfirst("$fieldInMessage must be no more than " . $sizeMb . " MB");
                                $this->addError($field, $message);
                            }
                        }
                        break;
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
