<?php
    class JsonUtils{
        public static function toArray($object) {
            $reflectionClass = new \ReflectionClass($object);
        
            $properties = $reflectionClass->getProperties();
        
            $array = [];
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($object);
                if (is_object($value)) {
                    $array[$property->getName()] = self::toArray($value);
                } else {
                    $array[$property->getName()] = $value;
                }
            }
            return $array;
        }
    }
?>